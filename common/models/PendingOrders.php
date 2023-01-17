<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pending_orders".
 *
 * @property int $id
 * @property int $old_order_id
 * @property int $parent_id
 * @property int $order_no
 * @property int $item_id
 * @property string $item_name
 * @property int $qty
 * @property int $order_qty
 * @property string $pack
 * @property int $rate
 * @property int $amount
 * @property int $salesman_id
 * @property int $status
 * @property string $created_dt
 * @property int $created_by
 * @property string $updated_dt
 * @property int $updated_by
 */
class PendingOrders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pending_orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['old_order_id', 'parent_id', 'order_no', 'item_id', 'item_name', 'qty', 'order_qty', 'pack', 'rate', 'amount', 'salesman_id', 'status', 'created_dt', 'created_by', 'updated_dt', 'updated_by'], 'required'],
            [['old_order_id', 'parent_id', 'order_no', 'item_id', 'qty', 'order_qty', 'rate', 'amount', 'salesman_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_dt', 'updated_dt'], 'safe'],
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
            'old_order_id' => 'Old Order ID',
            'parent_id' => 'Parent ID',
            'order_no' => 'Order No',
            'item_id' => 'Item ID',
            'item_name' => 'Item Name',
            'qty' => 'Qty',
            'order_qty' => 'Order Qty',
            'pack' => 'Pack',
            'rate' => 'Rate',
            'amount' => 'Amount',
            'salesman_id' => 'Salesman ID',
            'status' => 'Status',
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
}
