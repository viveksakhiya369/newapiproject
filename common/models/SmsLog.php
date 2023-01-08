<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sms_log".
 *
 * @property int $id
 * @property string $message
 * @property string $status
 * @property int $user_id
 */
class SmsLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message', 'status', 'user_id','body'], 'required'],
            [['message'], 'string'],
            [['user_id'], 'integer'],
            [['status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Message',
            'status' => 'Status',
            'user_id' => 'User ID',
        ];
    }
}
