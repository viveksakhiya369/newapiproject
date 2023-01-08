<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $mobile_number;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['mobile_number', 'password'], 'required'],
            [['mobile_number'],'integer'],
            ['mobile_number', 'match', 'pattern'=>"/^[0-9]{3}[0-9]{3}[0-9]{2}[0-9]{2}$/",'message'=> 'Mobile Number should have 10 digits.'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect Mobile Number or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            if(Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0)){
                $session=Yii::$app->session;
                $user=$this->getUser();
                $session->set('userId',$user->id);
                $session->set('userEmail',$user->email);
                $session->set('userMobilenum',$user->mobile_num);
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
        
        
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByMobilenum($this->mobile_number);
        }

        return $this->_user;
    }
}
