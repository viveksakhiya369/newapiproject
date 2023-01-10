<?php

namespace frontend\controllers;

use common\models\CommonHelpers;
use common\models\CustomModel;
use common\models\Orders;
use common\models\OrdersSearch;
use common\models\User;
use Exception;
use Yii;
use yii\base\Model;
use yii\bootstrap5\Modal;
use yii\helpers\Url;
use yii\web\Controller;

class OrderController extends Controller{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
            if(CommonHelpers::CheckCreateAccessforSuperAdmin(Yii::$app->controller->action->id)){
                Yii::$app->session->setFlash('error','Permission Denied');
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/index'));
                return false;
            }
                $this->enableCsrfValidation = false;
                return true;
        }
        return false;
    }

    public function actionCreate(){
        $model=[new Orders()];
        try{
            $transaction=Yii::$app->db->beginTransaction();
            if(Yii::$app->request->isPost){
                $arr = Yii::$app->request->post('Orders');
                $new_arr = array();
                foreach($arr AS $item) {
                  if(isset($new_arr[$item['item_id']])) {
                    $new_arr[$item['item_id']]['qty'] += $item['qty'];
                    $new_arr[$item['item_id']]['amount'] += $item['amount'];
                    continue;
                  }
                
                  $new_arr[$item['item_id']] = $item;
                }
                
                $arr = array_values($new_arr);
                $post_data[$model[0]->formName()]=$arr;
                $model=CustomModel::createMultipleWithCustomArr(Orders::className(),$arr);
                CustomModel::loadMultiple($model,$post_data);
                if(CustomModel::validateMultiple($model)){
                    $order_no=CommonHelpers::GenerateOrderNo();
                    // echo'<pre>';print_r($order_no);exit();
                    foreach($model as $key=>$onemodel){
                        $onemodel->parent_id=Yii::$app->user->identity->id;
                        $onemodel->order_no=$order_no;
                        $onemodel->status=Orders::STATUS_QUEUED;
                        if(!($flag=$onemodel->save(false))){
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if($flag){
                        $transaction->commit();
                        Yii::$app->session->setFlash("success","Your order placed successfully");
                        return $this->redirect(Url::to(['order/index','sent'=>true]));
                    }
                }
            }
        }catch(Exception $e){
            Yii::$app->session->setFlash("error",$e->getMessage());
            return $this->redirect(Url::to(['order/index','sent'=>true]));
        }
        return $this->render('create',[
            'model'=>$model,
        ]);
    }

    public function actionIndex(){
        $searchmodel=new OrdersSearch();
        $searchdata=$searchmodel->search(Yii::$app->request->queryParams);
        return $this->render('index',[
            'searchmodel'=>$searchmodel,
            'searchdata'=>$searchdata,
        ]);

    }

    public function actionView($order_no){
        $orders=Orders::find();
        if(in_array(Yii::$app->user->identity->role_id,[User::DISTRIBUTOR,User::DEALER])){
            $orders->joinWith(['dealer','dealer.distributor']);
        }
        $result=$orders->where(['orders.order_no'=>$order_no])->all();
        $oneorder=Orders::find()->joinWith(['dealer','dealer.distributor'])->where(['order_no'=>$order_no])->groupBy('order_no')->one();
        // echo'<pre>';print_r($oneorder);exit();
        return $this->render('_view',[
            'result'=>$result,
            'order_details'=>$oneorder,
        ]);
    }

}

?>