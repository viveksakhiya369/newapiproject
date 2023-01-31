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
use yii\helpers\ArrayHelper;
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
                // echo'<pre>';print_r($arr);exit();
                $new_arr = array();
                foreach ($arr as $item) {
                    if (isset($new_arr[$item['item_id']])) {
                        $new_arr[$item['item_id']]['total_pack'] += $item['total_pack'];
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
        $orders = Orders::find()->andWhere(['!=', Orders::tableName() . '.status', Orders::STATUS_DELETED]);
        if (in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::DEALER])) {
            $orders->joinWith(['dealer', 'dealer.distributor']);
        }
        $result = $orders->andWhere([Orders::tableName() . '.order_no' => $order_no])->all();
        $oneorder = Orders::find()->joinWith(['dealer', 'dealer.distributor'])->andWhere(['order_no' => $order_no])->andWhere(['!=', Orders::tableName() . '.status', Orders::STATUS_DELETED])->groupBy('order_no')->one();
        // echo'<pre>';print_r($oneorder);exit();
        return $this->render('_view', [
            'result' => $result,
            'order_details' => $oneorder,
        ]);
    }

    public function actionUpdate($order_no)
    {
        $model = $this->findAllOrders($order_no);
        if (empty($model)) {
            Yii::$app->session->setFlash("error", "Order does not exist!");
            return $this->redirect(Url::to(Url::to(['order/index', 'receieved' => true])));
        }
        if (Yii::$app->request->isPost) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                $new_order = Yii::$app->request->post('Orders');
                // echo'<pre>';var_dump(isset($new_order));exit();
                if ((isset($new_order)) && (count($new_order) >= count($model))) {
                    foreach ($new_order as $new_key => $new_val) {
                        if (isset($model[$new_key]) && !empty($model[$new_key])) {
                            if ($new_val['qty'] < $model[$new_key]->qty) {
                                $pending_order = CommonHelpers::SavePendingOrders($model[$new_key], $new_val);
                                if ($pending_order == false) {
                                    $transaction->rollBack();
                                    break;
                                }
                                $model[$new_key]->qty = $model[$new_key]->qty - $pending_order->qty;
                                $model[$new_key]->amount = $model[$new_key]->amount - $pending_order->amount;
                                $model[$new_key]->total_pack = $model[$new_key]->total_pack - $pending_order->total_pack;
                            } else if ($new_val['amount'] != $model[$new_key]->amount) {
                                $model[$new_key]->qty = isset($new_val['qty']) ? $new_val['qty'] : 0;
                                $model[$new_key]->total_pack = isset($new_val['total_pack']) ? $new_val['total_pack'] : 0;
                                $model[$new_key]->amount = isset($new_val['amount']) ? $new_val['amount'] : 0;
                                $model[$new_key]->overall_discount = isset($new_val['overall_discount']) ? $new_val['overall_discount'] : 0;
                                $model[$new_key]->discount = isset($new_val['discount']) ? $new_val['discount'] : 0;
                            }
                            $model[$new_key]->barcode = isset($new_val['barcode']) ? $new_val['barcode'] : "";
                            $model[$new_key]->status = Orders::STATUS_APPROVED;

                            // echo'<pre>';print_r($model);exit();
                            $point_approved_order[] = $model[$new_key];
                            // if (CommonHelpers::addPoints($model[$new_key]) == false) {
                            //     return $this->redirect(Url::to(['order/index', 'receieved' => true]));
                            // }
                            if (CommonHelpers::AddGodownStock($model[$new_key]) == false) {
                                return $this->redirect(Url::to(['order/index', 'receieved' => true]));
                            }
                            if (!($model[$new_key]->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        } else {
                            $new_val['order_no'] = $order_no;
                            $new_val['status'] = Orders::STATUS_APPROVED;
                            $new_val['parent_id'] = $model[0]->parent_id;
                            $post_data[$model[0]->formName()] = $new_val;
                            $order_model = new Orders();
                            if ($order_model->load($post_data)) {
                                $point_approved_order[] = $order_model;
                                // if (CommonHelpers::addPoints($order_model) == false) {
                                //     return $this->redirect(Url::to(['order/index', 'receieved' => true]));
                                // }
                                if (CommonHelpers::AddGodownStock($order_model) == false) {
                                    return $this->redirect(Url::to(['order/index', 'receieved' => true]));
                                }
                                if (!($order_model->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    $oldIDs = ArrayHelper::map($model, 'id', 'id');
                    $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($new_order, 'id', 'id')));
                    if (isset($deletedIDs) && !empty($deletedIDs)) {
                        foreach ($model as $i => $val) {
                            if (in_array($model[$i]->id, $deletedIDs)) {
                                $pending_order = CommonHelpers::SavePendingOrders($model[$i]);
                                if ($pending_order == false) {
                                    $transaction->rollBack();
                                    break;
                                }
                                $model[$i]->status = Orders::STATUS_DELETED;
                                if (!($model[$i]->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                                unset($model[$i]);
                            }
                        }
                    } else {
                        Yii::$app->session->setFlash('error', 'something went worng!!');
                        return $this->redirect(Url::to(['order/index', 'receieved' => true]));
                    }
                    $model = array_values($model);
                    $new_ids = array_diff($oldIDs, $deletedIDs);
                    // echo'<pre>';print_r($new_ids);
                    // echo'<pre>';print_r(array_values($model));
                    // echo'<pre>';print_r($new_order);exit();
                    foreach ($model as $i => $val) {
                        if (array_key_exists($i, $new_order) && in_array($model[$i]->id, $new_ids) && in_array($new_order[$i]['id'], $new_ids) && ($model[$i]->item_id == $new_order[$i]['item_id'])) {
                            if ($new_order[$i]['qty'] < $model[$i]->qty) {
                                $pending_order = CommonHelpers::SavePendingOrders($model[$i], $new_order[$i]);
                                // echo'<pre>';print_r($pending_order);exit();
                                if ($pending_order == false) {
                                    $transaction->rollBack();
                                    break;
                                }
                                $model[$i]->qty = $model[$i]->qty - $pending_order->qty;
                                $model[$i]->total_pack = $model[$i]->total_pack - $pending_order->total_pack;
                                $model[$i]->amount = $model[$i]->amount - $pending_order->amount;
                            } else if ($model[$i]->amount != $new_order[$i]['amount']) {
                                $model[$i]->total_pack = isset($new_order[$i]['total_pack']) ? $new_order[$i]['total_pack'] : 0;
                                $model[$i]->qty = isset($new_order[$i]['qty']) ? $new_order[$i]['qty'] : 0;
                                $model[$i]->amount = isset($new_order[$i]['amount']) ? $new_order[$i]['amount'] : 0;
                                $model[$i]->overall_discount = isset($new_order[$i]['overall_discount']) ? $new_order[$i]['overall_discount'] : 0;
                                $model[$i]->discount = isset($new_order[$i]['discount']) ? $new_order[$i]['discount'] : 0;
                            }
                            $model[$i]->barcode = isset($new_order[$i]['barcode']) ? $new_order[$i]['barcode'] : 0;
                            $model[$i]->status = Orders::STATUS_APPROVED;
                            // echo'<pre>';print_r($model);exit();
                            $point_approved_order[] = $model[$i];
                            // if (CommonHelpers::addPoints($model[$i]) == false) {
                            //     return $this->redirect(Url::to(['order/index', 'receieved' => true]));
                            // }
                            if (CommonHelpers::AddGodownStock($model[$i]) == false) {
                                return $this->redirect(Url::to(['order/index', 'receieved' => true]));
                            }
                            if (!($model[$i]->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                }
                // echo'<pre>';print_r($point_approved_order);exit();
                if (CommonHelpers::addPoints($point_approved_order, $order_no) == false) {
                    return $this->redirect(Url::to(['order/index', 'receieved' => true]));
                }
                $transaction->commit();
                Yii::$app->session->setFlash("success", "order has been updated successfully");
                return $this->redirect(Url::to(['order/index', 'receieved' => true]));
            } catch (Exception $e) {
                // echo '<pre>';
                // print_r($e->getMessage());
                // exit();
                $transaction->rollBack();

                Yii::$app->session->setFlash("error", $e->getMessage());
                return $this->redirect(Url::to(['order/index', 'receieved' => true]));
            }
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
                $old_status = $model[0]->status;
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
            echo '<pre>';
            print_r($e->getMessage());
            exit();
            // Yii::$app->session->setFlash("error", $e->getMessage());
            // return $this->redirect(Url::to(['order/index', 'sent' => true]));
        }

        return $this->render('send_update_order', [
            'model' => $model,
        ]);
    }

    private function findAllOrders($order_no)
    {
        return Orders::find()->where(['order_no' => $order_no])->andWhere(['!=', 'status', Orders::STATUS_DELETED])->all();
    }
}
