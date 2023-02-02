<?php

namespace frontend\controllers;

use common\models\City;
use common\models\CommonHelpers;
use common\models\Karigar;
use common\models\Products;
use common\models\User;
use Twilio\TwiML\Voice\Prompt;
use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
        $products=Products::find()->joinWith('taxName')->where([Products::tableName().'.id'=>Yii::$app->request->post('product_id')])->asArray()->one();
        $products['current_rate']=(Yii::$app->user->identity->role_id==User::DISTRIBUTOR) ? $products['wholesale_rate'] : $products['dealer_rate'];
        return json_encode($products);
    }

    public function actionGetProductList(){
        return json_encode(Products::find()->where(['like','item_name',Yii::$app->request->post('item_val')])->asArray()->all());
    }

    public function actionTest(){
        return $this->render('test');
    }

    public function actionGetKarigarFromMobile(){   
        $karigar=Karigar::find()->joinWith('user')->andWhere(['!=',Karigar::tableName().'.status',User::STATUS_DELETED])->andWhere([User::tableName().'.mobile_num'=>Yii::$app->request->post('mobile_num')])->one();
        if(isset($karigar)){
            $response['name']=$karigar->name;
            $response['parent_id']=$karigar->user->id;
            return json_encode($response);
        }else{
            return "";
        }
    }

}
