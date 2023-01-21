<?php

namespace frontend\controllers;

use common\models\CommonHelpers;
use common\models\CustomModel;
use common\models\Orders;
use common\models\OrdersSearch;
use common\models\PendingOrders;
use common\models\User;
use Exception;
use Yii;
use yii\base\Model;
use yii\bootstrap5\Modal;
use yii\helpers\Url;
use yii\web\Controller;

class OrderController extends Controller
{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
            if (CommonHelpers::CheckCreateAccessforSuperAdmin(Yii::$app->controller->action->id)) {
                Yii::$app->session->setFlash('error', 'Permission Denied');
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/index'));
                return false;
            }
            $this->enableCsrfValidation = false;
            return true;
        }
        return false;
    }

    public function actionCreate()
    {
        $model = [new Orders()];
        try {
            $transaction = Yii::$app->db->beginTransaction();
            if (Yii::$app->request->isPost) {
                // echo'<pre>';print_r(Yii::$app->request->post());exit();
                $arr = Yii::$app->request->post('Orders');
                $new_arr = array();
                foreach ($arr as $item) {
                    if (isset($new_arr[$item['item_id']])) {
                        $new_arr[$item['item_id']]['qty'] += $item['qty'];
                        $new_arr[$item['item_id']]['amount'] += $item['amount'];
                        continue;
                    }

                    $new_arr[$item['item_id']] = $item;
                }

                $arr = array_values($new_arr);
                $post_data[$model[0]->formName()] = $arr;
                $model = CustomModel::createMultipleWithCustomArr(Orders::className(), $arr);
                CustomModel::loadMultiple($model, $post_data);
                // echo'<pre>';print_r($model);exit();
                if (CustomModel::validateMultiple($model)) {
                    $order_no = CommonHelpers::GenerateOrderNo();
                    // echo'<pre>';print_r($order_no);exit();
                    foreach ($model as $key => $onemodel) {
                        $onemodel->parent_id = Yii::$app->user->identity->id;
                        $onemodel->order_no = $order_no;
                        $onemodel->status = Orders::STATUS_QUEUED;
                        if (!($flag = $onemodel->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->session->setFlash("success", "Your order placed successfully");
                        return $this->redirect(Url::to(['order/index', 'sent' => true]));
                    }
                }
            }
        } catch (Exception $e) {
            Yii::$app->session->setFlash("error", $e->getMessage());
            return $this->redirect(Url::to(['order/index', 'sent' => true]));
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionIndex()
    {
        $searchmodel = new OrdersSearch();
        $searchdata = $searchmodel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchmodel' => $searchmodel,
            'searchdata' => $searchdata,
        ]);
    }

    public function actionDelete($order_no)
    {
        $models = Orders::find()->where(['order_no' => $order_no])->all();
        foreach ($models as $key => $model) {
            $model->status = Orders::STATUS_DELETED;
            $model->save(false);
        }
        Yii::$app->session->setFlash('success', 'your order has been deleted successfully');
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function actionView($order_no)
    {
        $orders = Orders::find();
        if (in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::DEALER])) {
            $orders->joinWith(['dealer', 'dealer.distributor']);
        }
        $result = $orders->where(['orders.order_no' => $order_no])->all();
        $oneorder = Orders::find()->joinWith(['dealer', 'dealer.distributor'])->where(['order_no' => $order_no])->groupBy('order_no')->one();
        // echo'<pre>';print_r($oneorder);exit();
        return $this->render('_view', [
            'result' => $result,
            'order_details' => $oneorder,
        ]);
    }

    public function actionUpdate($order_no)
    {
        $model = $this->findAllOrders($order_no);
        if (Yii::$app->request->isPost) {
            $new_order = Yii::$app->request->post('Orders');
            foreach ($new_order as $new_key => $new_val) {
                if ($new_val['qty'] != $model[$new_key]->qty) {
                    $new_val['qty'] = $model[$new_key]->qty - $new_val['qty'];
                    $new_val['amount'] = $model[$new_key]->amount - $new_val['amount'];
                    $pending_order = new PendingOrders();
                    $pending_order->old_order_id = $model[$new_key]->id;
                    $pending_order->parent_id = $model[$new_key]->id;
                    $pending_order->order_no = $model[$new_key]->order_no;
                    $pending_order->item_id = $model[$new_key]->item_id;
                    $pending_order->item_name = $model[$new_key]->item_name;
                    $pending_order->qty = $new_val['qty'];
                    $pending_order->order_qty = $model[$new_key]->qty;
                    $pending_order->pack = $model[$new_key]->pack;
                    $pending_order->rate = $model[$new_key]->rate;
                    $pending_order->amount = $new_val['amount'];
                    $pending_order->salesman_id = $model[$new_key]->salesman_id;
                    $pending_order->status = Orders::STATUS_REJECTED;
                    $pending_order->save(false);
                    $model[$new_key]->qty = $model[$new_key]->qty - $pending_order->qty;
                    $model[$new_key]->amount = $model[$new_key]->amount - $pending_order->amount;
                }
                else if($new_val['amount'] != $model[$new_key]->amount){
                    $model[$new_key]->amount=isset($new_val['amount']) ? $new_val['amount'] : 0;
                    $model[$new_key]->overall_discount=isset($new_val['overall_discount']) ? $new_val['overall_discount'] : 0;
                    $model[$new_key]->discount=isset($new_val['discount']) ? $new_val['discount'] : 0;
                }
                $model[$new_key]->status = Orders::STATUS_APPROVED;
                $model[$new_key]->save(false);
            }
            Yii::$app->session->setFlash("success", "order has been updated successfully");
            return $this->redirect(Url::to(['order/index', 'receieved' => true]));
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSendUpdate($order_no)
    {
        $model = $this->findAllOrders($order_no);
        try {
            $transaction = Yii::$app->db->beginTransaction();
            if (Yii::$app->request->isPost) {
                // echo'<pre>';print_r(Yii::$app->request->post());exit();
                $old_status=$model[0]->status;
                foreach ($model as $key => $v) {
                    $v->status = Orders::STATUS_DELETED;
                    $v->save(false);
                }
                $arr = Yii::$app->request->post('Orders');
                $new_arr = array();
                foreach ($arr as $item) {
                    if (isset($new_arr[$item['item_id']])) {
                        $new_arr[$item['item_id']]['qty'] += $item['qty'];
                        $new_arr[$item['item_id']]['amount'] += $item['amount'];
                        continue;
                    }

                    $new_arr[$item['item_id']] = $item;
                }

                $arr = array_values($new_arr);
                $post_data[$model[0]->formName()] = $arr;
                $new_model = CustomModel::createMultipleWithCustomArr(Orders::className(), $arr);
                CustomModel::loadMultiple($new_model, $post_data);
                // echo'<pre>';print_r($model);exit();
                if (CustomModel::validateMultiple($new_model)) {
                    // echo'<pre>';print_r($order_no);exit();
                    foreach ($new_model as $key => $onemodel) {
                        $onemodel->parent_id = $model[0]->parent_id;
                        $onemodel->status = $old_status;
                        $onemodel->order_no = $order_no;
                        if (!($flag = $onemodel->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->session->setFlash("success", "Your order placed successfully");
                        return $this->redirect(Url::to(['order/index', 'sent' => true]));
                    }
                }
            }
        } catch (Exception $e) {
            echo'<pre>';print_r($e->getMessage());exit();
            // Yii::$app->session->setFlash("error", $e->getMessage());
            // return $this->redirect(Url::to(['order/index', 'sent' => true]));
        }

        return $this->render('send_update_order', [
            'model' => $model,
        ]);
    }

    private function findAllOrders($order_no)
    {
        return Orders::find()->where(['order_no' => $order_no])->andWhere(['!=','status',Orders::STATUS_DELETED])->all();
    }
}
