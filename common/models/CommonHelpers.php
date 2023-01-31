<?php

namespace common\models;

use PHPUnit\Framework\Constraint\Count;
use Yii;
use Twilio\Rest\Client;
use yii\web\Controller;

class CommonHelpers
{

    const TWILIO_ACCOUNT_SID = "ACcb088bccc531816553b72d21fa0f068d";
    const TWILIO_TOKEN = "3f3d3bb8c4ac8a494d046a9bde3d0525";
    const TWILIO_FROM_NUMBER = "+15736224181";

    public static function CheckLogin()
    {
        $session = Yii::$app->session;
        if ($session->get('userId') && is_numeric($session->get('userId')) && !Yii::$app->user->isGuest) {
            return true;
        } else {
            return false;
        }
    }

    public static function getTitle($controllername, $actionname)
    {

        $titlename = '';
        switch ($actionname) {
            case 'index':
                $titlename = "Manage " . ucfirst($controllername);
                break;
            case 'create':
                $titlename = "Create " . ucfirst($controllername);
                break;
            case 'update':
                $titlename = "Update " . ucfirst($controllername);
                break;
            case 'delete':
                $titlename = "Delete " . ucfirst($controllername);
                break;
        }
        return $titlename;
    }

    public static function randomStringGenerate($length = 32, $onlynumbers = false)
    {
        $characters = '0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ';
        if ($onlynumbers == true) {
            $characters = '0123456789';
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function GenerateOrderNo()
    {
        $orders = Orders::find()->all();
        return count($orders) + 1;
    }

    public static function sendOtp($model, $plain_password)
    {
        $message = "Welcome to convex Your password is : " . $plain_password . " Please do not share with anyone -Team convex";
        $otp = new Client(self::TWILIO_ACCOUNT_SID, self::TWILIO_TOKEN);
        $modelotp = $otp->messages->create(
            '+91' . $model->mobile_num,
            [
                'from' => self::TWILIO_FROM_NUMBER,
                'body' => $message
            ]
        );
        if (isset($modelotp) && !empty($modelotp)) {
            $response['sid'] = $modelotp->sid;
            $response['numSegments'] = $modelotp->numSegments;
            $response['direction'] = $modelotp->direction;
            $response['from'] = $modelotp->from;
            $response['to'] = $modelotp->to;
            $response['status'] = $modelotp->status;
            $response['apiVersion'] = $modelotp->apiVersion;

            $sms = new SmsLog();
            $sms->message = $message;
            $sms->status = $modelotp->status;
            $sms->user_id = $model->id;
            $sms->body = json_encode($response);
            $sms->save();
        }
    }

    public static function CheckCreateAccessforSuperAdmin($actionname)
    {
        if (($actionname == "create") && in_array(Yii::$app->user->identity->role_id, [User::SUPER_ADMIN])) {
            return true;
        } else {
            return false;
        }
    }

    public static function StockInwardCheck($order_no)
    {
        $stock = GodownStock::find()->where(['order_no' => $order_no])->asArray()->one();
        $stock = ShopStock::find()->where(['order_no' => $order_no])->asArray()->one();
        return (isset($stock) && !empty($stock)) ? false : true;
    }

    public static function SavePendingOrders($model, $value = [])
    {
        // echo'<pre>';print_r("hello");exit();
        $value['qty'] = $model->qty - (isset($value['qty']) ? $value['qty'] : 0);
        $value['total_pack'] = $model->total_pack - (isset($value['total_pack']) ? $value['total_pack'] : 0);
        $value['amount'] = $model->amount - (isset($value['amount']) ? $value['amount'] : 0);
        $pending_order = new PendingOrders();
        $pending_order->old_order_id = $model->id;
        $pending_order->parent_id = $model->id;
        $pending_order->order_no = $model->order_no;
        $pending_order->item_id = $model->item_id;
        $pending_order->item_name = $model->item_name;
        $pending_order->qty = $value['qty'];
        $pending_order->total_pack = $value['total_pack'];
        $pending_order->order_qty = $model->qty;
        $pending_order->pack = $model->pack;
        $pending_order->rate = $model->rate;
        $pending_order->tax = $model->tax;
        $pending_order->discount = $model->discount;
        $pending_order->amount = $value['amount'];
        $pending_order->salesman_id = $model->salesman_id;
        $pending_order->status = Orders::STATUS_REJECTED;
        if ($pending_order->save(false)) {
            return $pending_order;
        } else {
            return false;
        }
    }

    public static function AddGodownStock($order_model)
    {
        //insert quantity in user godown
        // echo'<pre>';print_r($order_model);exit();
        $current_godown = (new GodownStockSearch())->searchItemWise($order_model->item_id)->one();
        if (isset($current_godown)) {
            if ($current_godown->total_qty >= $order_model->qty) {
                $god_model = new GodownStock();
                $god_model->parent_id = $order_model->parent_id;
                $god_model->order_id = $order_model->id;
                $god_model->order_no = $order_model->order_no;
                $god_model->item_id = $order_model->item_id;
                $god_model->item_name = $order_model->item_name;
                $god_model->barcode = $order_model->barcode;
                $god_model->qty = $order_model->qty;
                $god_model->rate = $order_model->rate;
                $god_model->amount = $order_model->amount;
                $god_model->status = GodownStock::STATUS_ACTIVE;
                if (!($god_model->save(false))) {
                    Yii::$app->session->setFlash('error', 'Something went wrong!!');
                    return false;
                }
                //delete quantity from supplier godown
                //delete the old ids
                $old_stock_ids = explode(',', $current_godown->all_ids);
                foreach ($old_stock_ids as $i => $old_id) {
                    $old_god_model = GodownStock::findOne($old_id);
                    $old_god_model->status = GodownStock::STATUS_DELETED;
                    if (!($old_god_model->save(false))) {
                        Yii::$app->session->setFlash('error', 'Something went wrong!!');
                        return false;
                    }
                }
                //enter the new stock entry
                $rem_god_model = new GodownStock();
                $rem_god_model->parent_id = $current_godown->parent_id;
                $rem_god_model->order_id = $current_godown->id;
                $rem_god_model->order_no = $current_godown->order_no;
                $rem_god_model->item_id = $current_godown->item_id;
                $rem_god_model->item_name = $current_godown->item_name;
                $rem_god_model->barcode = $current_godown->barcode;
                $rem_god_model->qty = ($current_godown->total_qty - $god_model->qty);
                $rem_god_model->rate = $current_godown->rate;
                $rem_god_model->amount = ($current_godown->total_amount - $god_model->amount);
                $rem_god_model->status = GodownStock::STATUS_ACTIVE;
                if (!($rem_god_model->save(false))) {
                    Yii::$app->session->setFlash('error', 'Something went wrong!!');
                    return false;
                }
                // Yii::$app->session->setFlash('error','Godown stock is insufficient');
                return true;
            } else{
                Yii::$app->session->setFlash('error','Godown stock is insufficient');
                return false;
            }
        }else {
            Yii::$app->session->setFlash('error', 'The item can not be found in godown please insert the item');
            return false;
        }
    }

    public static function addPoints($order_models,$order_number){
        $total_points=0;
        $total_qty=0;
        $all_items=[];
        foreach($order_models as $i => $order_model){
            $product=Products::find()->andWhere(['!=','status',Products::STATUS_DELETED])->andWhere(['id'=>$order_model->item_id])->one();
            $total_points=$total_points+(($product->point)*($order_model->qty));
            $total_qty=$total_qty+$order_model->qty;
            $all_items[]=$order_model->item_id;
        }
        $point=new Points();
        $point->sender_id=Yii::$app->user->identity->id;
        $point->receiver_id=$order_model->parent_id;
        $point->order_id=$order_number;
        $point->item_id=implode(",",$all_items);
        $point->quantity=$total_qty;
        $point->points=$total_points;
        $point->status=Points::STATUS_ACTIVE;
        if(!($point->save(false))){
            return false;
        }
        return true;
        
    }

    public static function getSumOfPoints($arr_datas){
        // return 'hello';
        $total_points=0;
        foreach($arr_datas as $i => $arr_data){
            if($arr_data->sender_id==Yii::$app->user->identity->id){
                $total_points=$total_points-$arr_data->points;
            }else{
                $total_points=$total_points+$arr_data->points;
            }
        }
        return $total_points;
    }
}
