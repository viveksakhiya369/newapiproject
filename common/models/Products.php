<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string $item_group
 * @property string $item_name
 * @property string $size
 * @property string $pack
 * @property string $unit
 * @property int $point
 * @property string $hsn
 * @property string $barcode
 * @property int $tax
 * @property int $purchase_rate
 * @property int $wholesale_rate
 * @property int $dealer_rate
 * @property int $mrp
 * @property int $discount
 * @property int $minimum_stock
 * @property int $order_level
 * @property string $personal_code
 * @property int $status
 * @property string $created_dt
 * @property int $created_by
 * @property string $updated_dt
 * @property int $updated_by
 */
class Products extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE=1;
    const STATUS_DELETED=2;
    const STATUS_INACTIVE=3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_group', 'item_name', 'size', 'pack', 'unit', 'point', 'hsn', 'barcode', 'tax', 'purchase_rate','wholesale_rate','dealer_rate', 'mrp', 'discount', 'minimum_stock', 'order_level', 'personal_code'], 'required'],
            [['point', 'tax', 'purchase_rate','wholesale_rate','dealer_rate', 'mrp', 'discount', 'minimum_stock', 'order_level', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_dt', 'updated_dt'], 'safe'],
            [['item_group', 'item_name', 'size', 'pack', 'unit', 'hsn', 'barcode', 'personal_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_group' => 'Item Group',
            'item_name' => 'Item Name',
            'size' => 'Size',
            'pack' => 'Pack',
            'unit' => 'Unit',
            'point' => 'Point',
            'hsn' => 'Hsn',
            'barcode' => 'Barcode',
            'tax' => 'Tax%',
            'purchase_rate' => 'Purchase Rate',
            'wholesale_rate'=> 'Distributor Rate',
            'dealer_rate'=> 'Dealer Rate',
            'mrp' => 'Mrp',
            'discount' => 'Discount%',
            'minimum_stock' => 'Minimum Stock',
            'order_level' => 'Order Level',
            'personal_code' => 'Personal Code',
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

    public function getTaxName(){
        return $this->hasOne(TaxMaster::className(),['id'=>'tax']);
    }
}
