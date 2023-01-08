<?php

namespace common\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "dealer".
 *
 * @property int $id
 * @property string $dealer_name
 * @property string $address
 * @property string $city
 * @property string $taluka
 * @property string $state
 * @property string $gstin
 * @property string $pan
 * @property int $user_id
 * @property int $parent_id distributor_id
 * @property string $owner_name
 * @property int $status
 * @property string $created_dt
 * @property int $created_by
 * @property string $updated_dt
 * @property int $updated_by
 */
class Dealer extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=1;
    const STATUS_DELETED=2;
    const STATUS_INACTIVE=3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dealer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address', 'city', 'taluka', 'state', 'gstin', 'pan', 'owner_name'], 'required'],
            [['user_id', 'parent_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_dt', 'updated_dt'], 'safe'],
            [['dealer_name', 'address', 'city', 'taluka', 'state', 'gstin', 'pan', 'owner_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dealer_name' => 'Dealer Name',
            'address' => 'Address',
            'city' => 'City',
            'taluka' => 'Taluka',
            'state' => 'State',
            'gstin' => 'Gstin',
            'pan' => 'Pan',
            'user_id' => 'User ID',
            'parent_id' => 'Parent ID',
            'owner_name' => 'Owner Name',
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

    public function getDistributor(){
        return $this->hasOne(Distributor::className(),['id'=>'parent_id']);
    }

    public static function getDealerId($user_id){
        return Dealer::find()->where(['user_id'=>$user_id])->one()->id;
    }

}
