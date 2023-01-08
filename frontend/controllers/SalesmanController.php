<?php
namespace frontend\controllers;

use common\models\CommonHelpers;
use Yii;
use yii\web\Controller;
use common\models\Salesman;
use common\models\SalesmanSearch;
use common\models\Distributor;
use common\models\States;
use common\models\User;
use Exception;

class SalesmanController extends Controller{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
            if(in_array(Yii::$app->user->identity->role_id,[User::DISTRIBUTOR])){
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
        $model=new Salesman();
        $searchmodel=new SalesmanSearch();
        $searchdata=$searchmodel->search(Yii::$app->request->queryParams);
        return $this->render('index',[
            'searchmodel'=>$searchmodel,
            'searchdata'=>$searchdata,
            'model'=>$model,
        ]);
    }

    public function actionChangeStatus($id){
        $model=$this->findone($id);
        if($model->status==Salesman::STATUS_ACTIVE){
            $model->status=Salesman::STATUS_INACTIVE;
        }else{
            $model->status=Salesman::STATUS_ACTIVE;
        }
        if($model->save()){
            Yii::$app->session->setFlash("sucess","Status changed Successfully");
            return $this->redirect(Yii::$app->getUrlManager()->createUrl('salesman/index'));
        }
    }

    public function actionDelete($id){
        $model=$this->findone($id);
        $model->status=Salesman::STATUS_DELETED;
        if($model->save()){
            Yii::$app->session->setFlash("sucess","Salesman Deleted Successfully");
            return $this->redirect(Yii::$app->getUrlManager()->createUrl('salesman/index'));
        }
    }

    public function actionCreate(){
        $model=new Salesman();
        $usermodel=new User();

        $transaction=Yii::$app->db->beginTransaction();
        
            try{
                if(Yii::$app->request->isPost){
                    if($model->load(Yii::$app->request->post()) && $usermodel->load(Yii::$app->request->post())){
                        $usermodel->username=$model->name;
                        
                        if($model->validate() && $usermodel->validate()){
                            $usermodel->role_id=User::SALES_PERSON;
                            $rndm_password=CommonHelpers::randomStringGenerate(8);
                            $usermodel->password_hash=Yii::$app->security->generatePasswordHash($rndm_password);
                            if($usermodel->save(false)){
                                $model->user_id=$usermodel->id;
                            }
                            if($model->save(false)){
                                // CommonHelpers::sendOtp($usermodel,$rndm_password);
                            }
                            $transaction->commit();
                            Yii::$app->session->setFlash('success','Salesman Created Successfully');
                            return $this->redirect(Yii::$app->getUrlManager()->createUrl('salesman/index'));
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
                        return $this->redirect(Yii::$app->getUrlManager()->createUrl('salesman/index'));
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
        return Salesman::findOne($id);
    }

}

?>