<?php

namespace frontend\controllers;

use yii\web\Controller;
use Yii;
use common\models\CommonHelpers;
use common\models\Dealer;
use common\models\DealerSearch;
use common\models\Distributor;
use common\models\User;
use Exception;

class DealerController extends Controller{

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

    public function actionIndex(){
        $model = new Dealer();
        $searchmodel= new DealerSearch();
        $searchdata=$searchmodel->search(Yii::$app->request->queryParams);
        return $this->render('index',[
            'searchmodel'=>$searchmodel,
            'searchdata'=>$searchdata,
            'model'=>$model,
        ]);
    }

    public function actionDelete($id){
        $model=$this->findone($id);
        $model->status=Dealer::STATUS_DELETED;
        if($model->save()){
            Yii::$app->session->setFlash("sucess","Dealer Deleted Successfully");
            return $this->redirect(Yii::$app->getUrlManager()->createUrl('dealer/index'));
        }
    }

    public function actionChangeStatus($id){
        $model=$this->findone($id);
        if($model->status==Dealer::STATUS_ACTIVE){
            $model->status=Dealer::STATUS_INACTIVE;
        }else{
            $model->status=Dealer::STATUS_ACTIVE;
        }
        if($model->save()){
            Yii::$app->session->setFlash("success","Status changed Successfully");
            return $this->redirect(Yii::$app->getUrlManager()->createUrl('dealer/index'));
        }
    }

    public function actionCreate(){
        $model=new Dealer();
        $usermodel=new User();

        $transaction=Yii::$app->db->beginTransaction();
        
        try{
            if(Yii::$app->request->isPost){
                if($model->load(Yii::$app->request->post()) && $usermodel->load(Yii::$app->request->post())){
                    $usermodel->username=$model->dealer_name;
                    // echo'<pre>';print_r($model);exit();
                    if($model->validate() && $usermodel->validate()){
                        $usermodel->role_id=User::DEALER;
                        $rndm_password=CommonHelpers::randomStringGenerate(8);
                        $usermodel->password_hash=Yii::$app->security->generatePasswordHash($rndm_password);
                        if($usermodel->save(false)){
                            $model->user_id=$usermodel->id;
                            $model->parent_id=Distributor::getDistributorId(Yii::$app->user->identity->id);
                        }
                        if($model->save(false)){
                            //CommonHelpers::sendOtp($usermodel,$rndm_password);
                        }
                        $transaction->commit();
                        Yii::$app->session->setFlash('success','Dealer added successfully');
                        return $this->redirect(Yii::$app->getUrlManager()->createUrl('dealer/index'));
                    }
                }
            }
        }catch(Exception $e){
            Yii::$app->session->setFlash('error',$e->getMessage());
            $transaction->rollBack();
        }

        return $this->render('create',[
            'model'=>$model,
            'usermodel'=>$usermodel,
        ]);
    }

    public function actionUpdate($id){
        $model=$this->findone($id);
        $usermodel=User::findOne($model->user_id);
        if(Yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post()) && $usermodel->load(Yii::$app->request->post())){
                if($model->validate() && $usermodel->validate()){
                    if($model->save(false) && $usermodel->save(false)){
                        Yii::$app->session->setFlash("success","updated successfully");
                        return $this->redirect(Yii::$app->getUrlManager()->createUrl('dealer/index'));
                    }
                }
            }
        }
        return $this->render('update',[
            'model'=>$model,
            'usermodel'=>$usermodel
        ]);
    }

    private function findone($id){
        return Dealer::findOne($id);
    }
}

?>