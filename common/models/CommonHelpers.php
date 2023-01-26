<?php

namespace common\models;

use PHPUnit\Framework\Constraint\Count;
use Yii;
use Twilio\Rest\Client;

class CommonHelpers{

    const TWILIO_ACCOUNT_SID="ACcb088bccc531816553b72d21fa0f068d";
    const TWILIO_TOKEN="3f3d3bb8c4ac8a494d046a9bde3d0525";
    const TWILIO_FROM_NUMBER="+15736224181";

    public static function CheckLogin(){
        $session=Yii::$app->session;
        if($session->get('userId') && is_numeric($session->get('userId')) && !Yii::$app->user->isGuest){
            return true;
        }else{
            return false;
        }
    }

    public static function getTitle($controllername,$actionname){

        $titlename='';
        switch($actionname){
            case 'index':
                $titlename= "Manage ".ucfirst($controllername);
                break;
            case 'create':
                $titlename= "Create ".ucfirst($controllername);
                break;
            case 'update':
                $titlename="Update ".ucfirst($controllername);
                break;
            case 'delete':
                $titlename="Delete ".ucfirst($controllername);
                break;
        }
        return $titlename;

    }

    public static function randomStringGenerate($length = 32,$onlynumbers=false){
        $characters = '0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ';
        if($onlynumbers==true){
            $characters='0123456789';
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function GenerateOrderNo(){
        $orders=Orders::find()->all();
        return count($orders)+1;
    }

    public static function sendOtp($model,$plain_password){
        $message="Welcome to convex Your password is : ".$plain_password." Please do not share with anyone -Team convex";
        $otp= new Client(self::TWILIO_ACCOUNT_SID,self::TWILIO_TOKEN);
        $modelotp=$otp->messages->create(
            '+91'.$model->mobile_num,
            [
                'from'=>self::TWILIO_FROM_NUMBER,
                'body'=>$message
            ]
        );
        if(isset($modelotp) && !empty($modelotp)){
            $response['sid']=$modelotp->sid;
            $response['numSegments']=$modelotp->numSegments;
            $response['direction']=$modelotp->direction;
            $response['from']=$modelotp->from;
            $response['to']=$modelotp->to;
            $response['status']=$modelotp->status;
            $response['apiVersion']=$modelotp->apiVersion;

            $sms=new SmsLog();
            $sms->message=$message;
            $sms->status=$modelotp->status;
            $sms->user_id=$model->id;
            $sms->body=json_encode($response);
            $sms->save();
        }
    }

    public static function CheckCreateAccessforSuperAdmin($actionname){
        if(($actionname=="create") && in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN]) ){
            return true;
        }else{
            return false;
        }
    }

    public static function StockInwardCheck($order_no){
        $stock=GodownStock::find()->where(['order_no'=>$order_no])->asArray()->one();
        $stock=ShopStock::find()->where(['order_no'=>$order_no])->asArray()->one();
        return (isset($stock) && !empty($stock)) ? false : true;
    }
}


?>