<?php 

namespace frontend\controllers;

use common\models\CommonHelpers;
use common\models\TaxMaster;
use common\models\TaxMasterSearch;
use common\models\User;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class TaxMasterController extends Controller{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
            if(!in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){
                Yii::$app->session->setFlash('error','Permission Denied');
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/index'));
                return false;
            }
            $this->enableCsrfValidation = false;
            return true;
        }
        return false;
    }

    public function actionIndex(){
       $searchmodel=new TaxMasterSearch();
       $data=$searchmodel->search(Yii::$app->request->queryParams);
       
       return $this->render('index',[
        'searchmodel'=>$searchmodel,
        'data'=>$data,
       ]);

    }

    public function actionCreate(){
        $model = new TaxMaster();
        if(Yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post()) && $model->validate()){
                if($model->save()){
                    Yii::$app->session->setFlash('success','Tax is created');
                    return $this->redirect(Url::to(['tax-master/index']));
                }
            }
        }
        return $this->render('create',[
            'model'=>$model,
        ]);
    }

    public function actionUpdate($id){
        $model = $this->findone($id);
        if(Yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post()) && $model->validate()){
                if($model->save()){
                    Yii::$app->session->setFlash('success','Tax is created');
                    return $this->redirect(Url::to(['tax-master/index']));
                }
            }
        }
        return $this->render('update',[
            'model'=>$model,
        ]);
    }

    public function actionDelete($id){
        $model=$this->findone($id);
        $model->status=TaxMaster::STATUS_DELETED;
        if($model->save(false)){
            Yii::$app->session->setFlash('success','Deleted successfully');
            return $this->redirect(Url::to(['tax-master/index']));
        }
    }

    public function actionChangeStatus($id){
        $model=$this->findone($id);
        if($model->status==TaxMaster::STATUS_ACTIVE){
            $model->status=TaxMaster::STATUS_INACTIVE;
        }else{
            $model->status=TaxMaster::STATUS_ACTIVE;
        }
        if($model->save()){
            Yii::$app->session->setFlash("sucess","Status changed Successfully");
            return $this->redirect(Url::to(['tax-master/index']));
        }
    }

    private function findone($id){
        return TaxMaster::findOne($id);
    }
}


?>