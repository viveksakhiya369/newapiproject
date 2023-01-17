<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "transport".
 *
 * @property int $id
 * @property int $order_no
 * @property string $tranpotation_id
 * @property string $driver_name
 * @property string $vehicle_number
 * @property int $status
 * @property string $created_dt
 * @property int $created_by
 * @property string $updated_dt
 * @property int $updated_by
 */
class Transport extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=1;
    const STATUS_DELETED=2;
    const STATUS_INACTIVE=3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transport';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['driver_name', 'vehicle_number'], 'required'],
            [['order_no', 'status', 'created_by', 'updated_by'], 'integer'],
            [['driver_name', 'vehicle_number','order_no','transpotation_id','status','created_dt', 'updated_dt'], 'safe'],
            [['transpotation_id', 'driver_name', 'vehicle_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_no' => 'Order No',
            'transpotation_id' => 'Transpotation ID',
            'driver_name' => 'Driver Name',
            'vehicle_number' => 'Vehicle Number',
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
