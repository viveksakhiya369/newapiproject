<?php

namespace frontend\controllers;

use common\models\City;
use common\models\CommonHelpers;
use common\models\Products;
use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;

class AjaxController extends Controller{

    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false;
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
                return true;
        }
        return false; 
    }

    public function actionGetCityName(){
        $cities=ArrayHelper::map(City::find()->where(['state_id'=>Yii::$app->request->post('state_id')])->asArray()->all(),'id','name');
        $str= "<option value=''>Select City</option>";
        foreach($cities as $key => $value){
            $str .= "<option value='".$key."'>".$value."</option>";
        }
        return $str;
    }

    public function actionGetProductDetails(){
        return json_encode(Products::find()->where(['id'=>Yii::$app->request->post('product_id')])->asArray()->one());
    }

}
