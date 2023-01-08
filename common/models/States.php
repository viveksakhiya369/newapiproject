<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "states".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 */
class States extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'states';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
        ];
    }

    public static function getStatenamefromId($id){
        return States::findOne($id)->name;
    }
}
