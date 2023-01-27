<?php

namespace common\models;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use Yii;
use common\models\User;

/**
 * Password reset form
 */
class ChangePassword extends Model
{   
    public $old_password;
    public $new_password;
    public $repeat_password;
    /**
     * @var \common\models\User
     */
    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['old_password', 'new_password', 'repeat_password'], 'required', 'on' => 'changePwd'],
            ['new_password','match','pattern'=>'/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/','message'=>'New password must contain at least one lower and upper case character and a digit.','on' => 'changePwd'],
		    ['old_password', 'findPasswords', 'on' => 'changePwd'],
		    ['repeat_password', 'compare', 'compareAttribute'=>'new_password', 'on'=>'changePwd'],
            [['old_password', 'new_password', 'repeat_password'],'safe'],
        ];
    }
    public function findPasswords($attribute, $params)
	{
		$user = User::findIdentity(Yii::$app->user->identity->id);
		if (!(Yii::$app->security->validatePassword($this->old_password, Yii::$app->user->identity->password_hash)))
			$this->addError($attribute, 'Current password is incorrect.');
	}
    public function attributeLabels()
    {
        return [
            'old_password'=>'Current Password',
            'new_password'=>'New Password',
            'repeat_password' => 'Re-Enter New Password',
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {   
        
        $user = User::findOne(Yii::$app->user->identity->id);
        // echo '<pre>'; print_r($user);exit();
        $user->setPassword($this->new_password);
        // $user->removePasswordResetToken();
        // $user->generateAuthKey();

        return $user->save(false);
    }
}
