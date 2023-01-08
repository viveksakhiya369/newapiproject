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
                $model=CustomModel::createMultiple(Orders::className());
                CustomModel::loadMultiple($model,Yii::$app->request->post());
                if(CustomModel::validateMultiple($model)){
                    $order_no=CommonHelpers::randomStringGenerate(6,true);
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
                        return $this->redirect(Yii::$app->getUrlManager()->createUrl('order/index'));
                    }
                }
            }
        }catch(Exception $e){
            Yii::$app->session->setFlash("error",$e->getMessage());
            return $this->redirect(Yii::$app->getUrlManager()->createUrl('order/index'));
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
        echo'<pre>';print_r($oneorder);exit();
        return $this->render('_view',[
            'result'=>$result,
            'order_details'=>$oneorder,
        ]);
    }

}

?>