<?php
namespace frontend\controllers;

use common\models\CommonHelpers;
use Yii;
use yii\web\Controller;
use common\models\Karigar;
use common\models\KarigarSearch;
use common\models\Dealer;
use common\models\States;
use common\models\User;
use Exception;

class KarigarController extends Controller{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
            if(in_array(Yii::$app->user->identity->role_id,[User::KARIGAR,User::DISTRIBUTOR])){
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
        $model=new Karigar();
        $searchmodel=new KarigarSearch();
        $searchdata=$searchmodel->search(Yii::$app->request->queryParams);
        return $this->render('index',[
            'searchmodel'=>$searchmodel,
            'searchdata'=>$searchdata,
            'model'=>$model,
        ]);
    }

    public function actionChangeStatus($id){
        $model=$this->findone($id);
        if($model->status==Karigar::STATUS_ACTIVE){
            $model->status=Karigar::STATUS_INACTIVE;
        }else{
            $model->status=Karigar::STATUS_ACTIVE;
        }
        if($model->save()){
            Yii::$app->session->setFlash("sucess","Status changed Successfully");
            return $this->redirect(Yii::$app->getUrlManager()->createUrl('karigar/index'));
        }
    }

    public function actionDelete($id){
        $model=$this->findone($id);
        $model->status=Karigar::STATUS_DELETED;
        if($model->save()){
            Yii::$app->session->setFlash("sucess","Karigar Deleted Successfully");
            return $this->redirect(Yii::$app->getUrlManager()->createUrl('karigar/index'));
        }
    }

    public function actionCreate(){
        $model=new Karigar();
        $usermodel=new User();
        $usermodel->scenario="newpass";

        $transaction=Yii::$app->db->beginTransaction();
        
            try{
                if(Yii::$app->request->isPost){
                    if($model->load(Yii::$app->request->post()) && $usermodel->load(Yii::$app->request->post())){
                        $usermodel->username=$model->name;
                        
                        if($model->validate() && $usermodel->validate()){
                            $usermodel->role_id=User::KARIGAR;
                            // $rndm_password=CommonHelpers::randomStringGenerate(8);
                            $usermodel->password_hash=Yii::$app->security->generatePasswordHash($usermodel->password);
                            if($usermodel->save(false)){
                                $model->user_id=$usermodel->id;
                                $model->parent_id=Dealer::getDealerId(Yii::$app->user->identity->id);
                            }
                            if($model->save(false)){
                                // CommonHelpers::sendOtp($usermodel,$usermodel->password);
                            }
                            $transaction->commit();
                            Yii::$app->session->setFlash('success','Karigar Created Successfully');
                            return $this->redirect(Yii::$app->getUrlManager()->createUrl('karigar/index'));
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
                        return $this->redirect(Yii::$app->getUrlManager()->createUrl('karigar/index'));
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
        return Karigar::findOne($id);
    }

}

?>