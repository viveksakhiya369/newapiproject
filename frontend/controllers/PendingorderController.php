<?php

namespace frontend\controllers;

use common\models\CommonHelpers;
use common\models\CustomModel;
use common\models\Orders;
use common\models\OrdersSearch;
use common\models\PendingOrders;
use common\models\PendingOrdersSearch;
use common\models\Products;
use common\models\User;
use Exception;
use Yii;
use yii\base\Model;
use yii\bootstrap5\Modal;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\Controller;

class PendingorderController extends Controller
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

   

    public function actionIndex()
    {
        $searchmodel = new PendingOrdersSearch();
        $searchdata = $searchmodel->search(Yii::$app->request->queryParams);
        $all_datas=$searchdata->getModels();
        foreach($all_datas as $i => $val){
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
            'allModels'=>$searchdata->getModels(),
        ]);
        // echo'<pre>';print_r($searchdata->getModels());exit();
        return $this->render('index', [
            'searchmodel' => $searchmodel,
            'searchdata' => $array_data_provider,
        ]);
    }

    public function actionResubmit($order_no){
        $pending_model=PendingOrders::find()->joinWith('order')->where(['!=',PendingOrders::tableName().'.status',Orders::STATUS_DELETED])->andWhere([PendingOrders::tableName().'.order_no'=>$order_no])->all();
        if(isset($pending_model) && !empty($pending_model)){
            try{
                $transaction=Yii::$app->db->beginTransaction();
                $static_order=CommonHelpers::GenerateOrderNo();
                foreach($pending_model as $i => $value){
                    $new_order= new Orders();
                    $new_order->parent_id=$value->order->parent_id;
                    $new_order->order_no=$static_order;
                    $new_order->item_id=$value->item_id;
                    $new_order->item_name=$value->item_name;
                    $new_order->qty=$value->qty;
                    $new_order->total_pack=$value->total_pack;
                    $new_order->order_qty=$value->order_qty;
                    $new_order->pack=$value->pack;
                    $new_order->rate=$value->rate;
                    $new_order->tax=isset($value->tax) ? $value->tax :0;
                    $new_order->discount=isset($value->discount) ? $value->discount : 0;
                    $new_order->overall_discount=isset($value->overall_discount) ? $value->overall_discount : 0;
                    $new_order->amount=$value->amount;
                    $new_order->salesman_id=isset($value->salesman_id) ? $value->salesman_id : 0 ;
                    $new_order->status=Orders::STATUS_QUEUED;
                    if(!$new_order->save(false)){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash("error","Something got wrong!");
                        return $this->redirect(Url::to(['pendingorder/index']));
                    }
                    $value->status=Orders::STATUS_DELETED;
                    if(!$value->save(false)){
                        $transaction->rollBack();
                        Yii::$app->session->setFlash("error","Something got wrong!");
                        return $this->redirect(Url::to(['pendingorder/index']));
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash("success","Your order has resubmit successfully");
                return $this->redirect(Url::to(['pendingorder/index']));
            }catch(Exception $e){
                $transaction->rollBack();
                Yii::$app->session->setFlash("error",$e->getMessage());
                return $this->redirect(Url::to(['pendingorder/index']));
            }
        }
    }

    public function actionDelete($order_no)
    {
        $models = PendingOrders::find()->where(['order_no' => $order_no])->all();
        foreach ($models as $key => $model) {
            $model->status = Orders::STATUS_DELETED;
            $model->save(false);
        }
        Yii::$app->session->setFlash('success', 'your order has been deleted successfully');
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function actionView($order_no)
    {
        $orders = PendingOrders::find();
        if (in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::DEALER])) {
            // echo'<pre>';print_r("hello");exit();
            $orders->joinWith(['order.dealer', 'order.dealer.distributor']);
        }
        $result = $orders->where([PendingOrders::tableName().'.order_no' => $order_no])->all();
        $searchmodel = new PendingOrdersSearch();
        $params['order_no']=$order_no;
        $searchdata = $searchmodel->search($params);
        $all_datas=$searchdata->getModels();
        foreach($all_datas as $i => $val){
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
        $oneorder = PendingOrders::find()->joinWith(['order.dealer', 'order.dealer.distributor'])->where([PendingOrders::tableName().'.order_no' => $order_no])->groupBy('order_no')->one();
        return $this->render('_view', [
            'result' => $result,
            'order_details' => $all_datas[0],
        ]);
    }


}
