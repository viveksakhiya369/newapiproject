<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "points".
 *
 * @property int $id
 * @property int $sender_id
 * @property int $receiver_id
 * @property int $item_id
 * @property int $quantity
 * @property int $points
 * @property int $status
 * @property string $created_dt
 * @property int $created_by
 * @property string $updated_dt
 * @property int $updated_by
 */
class Points extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=1;
    const STATUS_DELETED=2;
    const STATUS_INACTIVE=3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'points';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender_id', 'receiver_id', 'item_id', 'quantity', 'points', 'status', 'created_dt', 'created_by', 'updated_dt', 'updated_by'], 'required'],
            [['sender_id', 'receiver_id', 'item_id', 'quantity', 'points', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_dt', 'updated_dt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => 'Sender ID',
            'receiver_id' => 'Receiver ID',
            'item_id' => 'Item ID',
            'quantity' => 'Quantity',
            'points' => 'Points',
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
