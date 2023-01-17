<?php
namespace frontend\controllers;

use common\models\CommonHelpers;
use Yii;
use yii\web\Controller;
use common\models\Distributor;
use common\models\DistributorSearch;
use common\models\States;
use common\models\Transport;
use common\models\User;
use Exception;
use yii\helpers\Url;

class TransportController extends Controller{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
            if(in_array(Yii::$app->user->identity->role_id,[User::DEALER])){
                Yii::$app->session->setFlash('error','Permission Denied');
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/index'));
                return false;
            }
                $this->enableCsrfValidation = false;
                return true;
        }
        return false;
    }

    // public function actionIndex(){
    //     $model=new Distributor();
    //     $searchmodel=new DistributorSearch();
    //     $searchdata=$searchmodel->search(Yii::$app->request->queryParams);
    //     return $this->render('index',[
    //         'searchmodel'=>$searchmodel,
    //         'searchdata'=>$searchdata,
    //         'model'=>$model,
    //     ]);
    // }

    // public function actionChangeStatus($id){
    //     $model=$this->findone($id);
    //     if($model->status==Distributor::STATUS_ACTIVE){
    //         $model->status=Distributor::STATUS_INACTIVE;
    //     }else{
    //         $model->status=Distributor::STATUS_ACTIVE;
    //     }
    //     if($model->save()){
    //         Yii::$app->session->setFlash("sucess","Status changed Successfully");
    //         return $this->redirect(Yii::$app->getUrlManager()->createUrl('distributor/index'));
    //     }
    // }

    // public function actionDelete($id){
    //     $model=$this->findone($id);
    //     $model->status=Distributor::STATUS_DELETED;
    //     if($model->save()){
    //         Yii::$app->session->setFlash("sucess","Distributor Deleted Successfully");
    //         return $this->redirect(Yii::$app->getUrlManager()->createUrl('distributor/index'));
    //     }
    // }

    public function actionCreate($order_no=''){

        $model= new Transport();
            if(Yii::$app->request->isPost){
                if($model->load(Yii::$app->request->post()) && $model->validate()){
                    $model->order_no=$order_no;
                    $model->transpotation_id=$order_no;
                    $model->status=Transport::STATUS_ACTIVE;
                    if($model->save()){
                        Yii::$app->session->setFlash('success','Transportation details are saved!');
                        return $this->redirect(Url::to(['order/index','receieved'=>true]));
                    }
                }
            }
        return $this->renderAjax('create',[
            'model'=>$model,
        ]);
        
    }
    
    public function actionUpdate($order_no){
        $model=Transport::find()->where(['order_no'=>$order_no])->one();
        if(Yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post()) ){
                if($model->validate()){
                    if($model->save(false)){
                        Yii::$app->session->setFlash("success","Transportation details has been updated");
                        return $this->redirect(Url::to(['order/index','receieved'=>true]));
                    }
                }
            }
        }
        return $this->renderAjax('update',[
            'model'=>$model,
        ]);
    }

    private function findone($id){
        return Distributor::findOne($id);
    }

}

?>