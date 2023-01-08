<?php

namespace frontend\controllers;

use common\models\CommonHelpers;
use common\models\Products;
use common\models\ProductSearch;
use common\models\User;
use Exception;
use Yii;
use yii\web\Controller;

class ProductController extends Controller{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
            if(in_array(Yii::$app->user->identity->role_id,[User::DEALER,User::DISTRIBUTOR,User::SALES_PERSON,User::KARIGAR])){
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
        $model = new Products();

        try{

            if(Yii::$app->request->isPost){
                if($model->load(Yii::$app->request->post())){
                    if($model->validate()){
                        $model->status=Products::STATUS_ACTIVE;
                        if($model->save()){
                            
                            Yii::$app->session->setFlash('success','Product saved successfully');
                            $this->redirect(Yii::$app->getUrlManager()->createUrl('product/index'));
                        }
                    }
                }
            }
        }catch(Exception $e){
            Yii::$app->session->setFlash('error',$e->getMessage());
        }

        return $this->render('create',[
            'model'=>$model
        ]);

    }

    public function actionIndex(){
        $searchmodel = new ProductSearch();
        $searchdata = $searchmodel->search(Yii::$app->request->queryParams);
        return $this->render('index',[
            'searchdata'=>$searchdata,
            'searchmodel'=>$searchmodel,
        ]);

    }

    public function actionChangeStatus($id){
        $model=$this->findone($id);
        if($model->status==Products::STATUS_ACTIVE){
            $model->status=Products::STATUS_INACTIVE;
        }else{
            $model->status=Products::STATUS_ACTIVE;
        }
        if($model->save()){
            Yii::$app->session->setFlash("success","Status changed Successfully");
            return $this->redirect(Yii::$app->getUrlManager()->createUrl('product/index'));
        }
    }

    public function actionDelete($id){
        $model=$this->findone($id);
        $model->status=Products::STATUS_DELETED;
        if($model->save()){
            Yii::$app->session->setFlash("sucess","Product Deleted Successfully");
            return $this->redirect(Yii::$app->getUrlManager()->createUrl('product/index'));
        }
    }

    public function actionUpdate($id){
        $model=$this->findone($id);
        if(Yii::$app->request->isPost){
            if($model->load(Yii::$app->request->post())){
                if($model->validate()){
                    if($model->save(false)){
                        Yii::$app->session->setFlash("success","updated successfully");
                        return $this->redirect(Yii::$app->getUrlManager()->createUrl('product/index'));
                    }
                }
            }
        }
        return $this->render('update',[
            'model'=>$model,
        ]);
    }

    protected function findone($id){
        return Products::findOne($id);
    }
    

}


?>