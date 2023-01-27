<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "godown_stock".
 *
 * @property int $id
 * @property int $parent_id
 * @property int $item_id
 * @property string $item_name
 * @property int $barcode
 * @property int $stock
 * @property int $rate
 * @property int $amount
 * @property int $status
 * @property string $created_dt
 * @property int $created_by
 * @property string $updated_dt
 * @property int $updated_by
 */
class GodownStock extends \yii\db\ActiveRecord
{
    const INWARD_TYPE_GODOWN=0;
    const INWARD_TYPE_SHOP=1;
    const STATUS_ACTIVE=1;
    const STATUS_DELETED=2;
    const STATUS_INACTIVE=3;
    public $tax;
    public $pack;
    public $discount;
    public $total_qty;
    public $inward_type;
    public $total_amount;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'godown_stock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'item_name', 'qty','inward_type'], 'required'],
            [['id', 'parent_id', 'item_id', 'stock', 'rate', 'amount', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_dt', 'updated_dt','tax','pack','discount','barcode','total_qty','inward_type','total_amount'], 'safe'],
            [['item_name'], 'string', 'max' => 255],
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
            'item_id' => 'Item Name',
            'item_name' => 'Item Name',
            'barcode' => 'Barcode',
            'stock' => 'Stock',
            'rate' => 'Rate',
            'amount' => 'Amount',
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

    public function getOrder(){
        return $this->hasOne(Orders::className(),['id'=>'parent_id']);
    }
}
