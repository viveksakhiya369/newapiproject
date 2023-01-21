<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $parent_id
 * @property int $order_no
 * @property int $item_id
 * @property string $item_name
 * @property int $qty
 * @property string $pack
 * @property int $rate
 * @property int $amount
 * @property int $status
 * @property string $created_dt
 * @property int $created_by
 * @property string $updated_dt
 * @property int $updated_by
 */
class Orders extends \yii\db\ActiveRecord
{

    const STATUS_QUEUED=1;
    const STATUS_INPROGRESS=2;
    const STATUS_APPROVED=3;
    const STATUS_DELIVERED=4;
    const STATUS_REJECTED=5;
    const STATUS_DELETED=9;

    const STATUS_QUEUED_LABEL="Queued";
    const STATUS_INPROGRESS_LABEL="Inprogress";
    const STATUS_APPROVED_LABEL="Approved";
    const STATUS_DELIVERED_LABEL="Delivered";
    const STATUS_REJECTED_LABEL="Rejected";
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'qty','tax','rate', 'discount', 'amount'], 'required'],
            // [['item_new_id'],'required','message'=>'Please select Product'],
            [['parent_id', 'order_no', 'item_id', 'qty', 'rate', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_dt','overall_discount', 'updated_dt'], 'safe'],
            [['item_name', 'pack'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'order_no' => 'Order No',
            'item_id' => 'Item ID',
            'item_name' => 'Item Name',
            'qty' => 'Qty',
            'pack' => 'Pack',
            'rate' => 'Rate',
            'tax'=>'Tax%',
            'discount'=>'Discount%',
            'amount' => 'Amount',
            'status' => 'Status',
            'item_new_id'=>'Item Id',
            'created_dt' => 'Created Dt',
            'created_by' => 'Created By',
            'updated_dt' => 'Updated Dt',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->updated_dt = date('Y-m-d H:i:s');
            $this->updated_by = (isset(Yii::$app->user->identity->id) && !empty(Yii::$app->user->identity->id)) ? Yii::$app->user->identity->id : 0;
            if ($insert) {
                $this->created_dt = date('Y-m-d H:i:s');
                $this->created_by = (isset(Yii::$app->user->identity->id) && !empty(Yii::$app->user->identity->id)) ? Yii::$app->user->identity->id : 0;
            }
            return true;
        } else {
            return false;
        }
    }

    public function getUser(){
        return $this->hasOne(User::className(),['user_id'=>'parent_id']);
    }


    public function getSalesman(){
        return $this->hasOne(Salesman::className(),['user_id'=>'parent_id']);
    }

    public function getDealer(){
        return $this->hasOne(Dealer::className(),['user_id'=>'parent_id']);
    }

    public static function getDistributorId($user_id){
        return Distributor::find()->where(['user_id'=>$user_id])->one()->dist_name;
    }
    public static function getDistributorAddress($user_id){
        return Distributor::find()->where(['user_id'=>$user_id])->one()->address;
    }

}
