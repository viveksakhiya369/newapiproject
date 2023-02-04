<?php

namespace frontend\controllers;

use common\models\CommonHelpers;
use common\models\CustomModel;
use common\models\Orders;
use common\models\OrdersSearch;
use common\models\Products;
use common\models\User;
use Exception;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\Controller;

class SellorderController extends Controller{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
            if(!in_array(Yii::$app->user->identity->role_id,[User::DEALER])){
                Yii::$app->session->setFlash('error','Permission Denied');
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/index'));
                return false;
            }
                $this->enableCsrfValidation = false;
                return true;
        }
        return false;
    }

    public function actionIndex($sell_order){
        $searchmodel=new OrdersSearch();
        $data=$searchmodel->search(Yii::$app->request->queryParams);
        $all_datas=$data->getModels();
        foreach($all_datas as $i => $val){
            // echo'<pre>';print_r($val);exit();
            $item_ids_array=explode(',',$val->all_item_id);
            $discount_arary=explode(',',$val->all_discount);
            $amt_array=explode(',',$val->all_amount);
            $qty_array=explode(',',$val->all_qty);
            $rate_array=explode(',',$val->all_rate);
            $discount_total_amount=0;
            $total_amt_order=0;
            $total_order_points=0;
            foreach($discount_arary as $key => $value){
                $discount_total_amount+=(($qty_array[$key]*$rate_array[$key])*$discount_arary[$key])/100;
                $total_amt_order+=$amt_array[$key];
                $total_order_points+=($qty_array[$key])*((int)Products::findOne($item_ids_array[$key])->point);
            }
            $val->all_discount=$discount_total_amount;
            $val->all_amount=$total_amt_order;
            $val->total_points=$total_order_points;
        }
        // echo'<pre>';print_r($all_datas);exit();
        $array_data_provider=new ArrayDataProvider([
            'allModels'=>$data->getModels(),
        ]);
        // echo'<pre>';print_r($searchdata->getModels());exit();
        return $this->render('index', [
            'searchmodel' => $searchmodel,
            'data' => $array_data_provider,
        ]);
    }

    public function actionCreate()
    {
        $model = [new Orders()];
        try {
            $transaction = Yii::$app->db->beginTransaction();
            if (Yii::$app->request->isPost) {
                // echo'<pre>';print_r(Yii::$app->request->post());exit();
                $karigar_parent_id=Yii::$app->request->post('karigar_parent_id');
                $arr = Yii::$app->request->post('Orders');
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
                        $onemodel->parent_id = $karigar_parent_id;
                        $onemodel->order_no = $order_no;
                        $onemodel->status = Orders::STATUS_APPROVED;
                        if (CommonHelpers::AddGodownStock($onemodel) == false) {
                            return $this->redirect(Url::to(['sellorder/index', 'sell_order' => true]));
                        }
                        if (!($flag = $onemodel->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if ($flag) {
                        if (CommonHelpers::addPoints($model, $order_no) == false) {
                            return $this->redirect(Url::to(['sellorder/index', 'sell_order' => true]));
                        }
                        $transaction->commit();
                        Yii::$app->session->setFlash("success", "Your order placed successfully");
                        return $this->redirect(Url::to(['sellorder/index', 'sell_order' => true]));
                    }
                }
            }
        } catch (Exception $e) {
            Yii::$app->session->setFlash("error", $e->getMessage());
            return $this->redirect(Url::to(['sellorder/index', 'sell_order' => true]));
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

}




?>