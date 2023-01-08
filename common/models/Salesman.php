<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sales_officer".
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $city
 * @property string $state
 * @property int $user_id
 * @property int $parent_id distributor_id
 * @property int $status
 * @property string $created_dt
 * @property int $created_by
 * @property string $updated_dt
 * @property int $updated_by
 */
class Salesman extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=1;
    const STATUS_DELETED=2;
    const STATUS_INACTIVE=3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sales_officer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'address', 'city','state'], 'required'],
            [['name', 'address', 'city','state'], 'string', 'max' => 255],
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
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'user_id' => 'User ID',
            'parent_id' => 'Parent ID',
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

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getCity(){
        return $this->hasOne(City::className(),['id'=>'city']);
    }

    public function getState(){
        return $this->hasOne(States::className(),['id'=>'state']);
    }

    public function getDealer(){
        return $this->hasMany(Dealer::className(),['parent_id'=>'id']);
    }

    public static function getDistributorId($user_id){
        return Distributor::find()->where(['user_id'=>$user_id])->one()->id;
    }
}
