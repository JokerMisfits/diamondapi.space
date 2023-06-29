<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id ID
 * @property string $username Имя пользователя
 * @property string $password Пароль
 * @property string $password_repeat Пароль
 * @property int|null $tg_user_id ID пользователя в telegram
 * @property string|null $email Email
 * @property string|null $phone Номер телефона
 * @property string|null $auth_key Кука
 * @property string $last_activity Дата последней активности
 * @property int|null $member_id ID пользователя
 * 
 */
class Users extends ActiveRecord implements IdentityInterface{
    public string $password_repeat = '';
    public bool $rememberMe = true;
    private static $_user = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName(){
        return 'users';
    }

    public function scenarios(){
        $scenarios = parent::scenarios();
        $scenarios['signup'] = ['username', 'password', 'password_repeat'];
        $scenarios['login'] = ['username', 'password', 'rememberMe'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['username', 'password'], 'required'],
            [['password_repeat'], 'required', 'on' => 'signup'],
            [['tg_user_id', 'member_id', 'role_id'], 'integer'],
            [['last_activity'], 'safe'],
            [['rememberMe'], 'boolean'],
            [['rememberMe'], 'required', 'on' => 'login'],
            [['username'], 'string', 'min' => 4],
            [['email', 'on' => 'update'], 'string', 'min' => 5],
            [['password', 'password_repeat'], 'string', 'min' => 6],
            [['username'], 'string', 'max' => 32],
            [['password', 'auth_key'], 'string', 'max' => 64],
            [['password_repeat'], 'string', 'max' => 64],
            [['phone'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 128],
            [['username'], 'unique', 'except' => 'login'],
            [['email'], 'unique', 'on' => 'update'],
            [['phone'], 'unique', 'except' => 'login'],
            [['username', 'password'], 'trim'],
            [['phone', 'email'], 'trim' , 'on' => 'update'],
            [['password'], 'validateModelPassword', 'on' => 'login'],
            ['username', 'match', 'pattern' => '/^[a-z]\w*$/i','message' => '{attribute} должно начинаться и содержать символы только латинского алфавита'],
            ['phone', 'match', 'pattern' => '/^((\+7|7|8)+([0-9]){10})$/', 'message' => 'Недействительный номер'],
            [['role_id'], 'exist', 'skipOnError' => false, 'targetClass' => Roles::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'password_repeat' => 'Подтвердить пароль',
            'tg_user_id' => 'ID пользователя в telegram',
            'email' => 'Email',
            'phone' => 'Номер телефона',
            'rememberMe' => 'Запомнить меня',
            'auth_key' => 'Кука',
            'last_activity' => 'Дата последней активности',
            'member_id' => 'ID пользователя',
            'role_id' => 'ID роли'
        ];
    }

    /** 
    * Gets query for [[Role]]. 
    * 
    * @return \yii\db\ActiveQuery|yii\db\ActiveQuery 
    */ 
   public function getRole(){ 
       return $this->hasOne(Roles::class, ['id' => 'role_id']); 
   }

    /**
     * {@inheritdoc}
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find() : UsersQuery{
        return new UsersQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */

    public static function findIdentity($id) : IdentityInterface|null{
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */

    public static function findIdentityByAccessToken($token, $type = null) : false{
        return false;
        //return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */

    private static function findByUsername($username) : static|null{
        return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId() : int|string{
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() : string|null{
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($auth_key) : bool{
        if($this->auth_key === $auth_key){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateModelPassword($attribute, $params){
        if(!$this->hasErrors()){
            self::getUser();
            if(!self::$_user || !self::validatePassword($this->password)){
                $this->addError($attribute, 'Неправильное имя пользователя или пароль.');
            }
            else{
                if($this->rememberMe){
                    self::generateAuthKey();
                }
                else{
                    self::$_user->auth_key = null;
                    self::$_user->save();
                }
            }
        }
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    private function validatePassword($password) : bool{
        return Yii::$app->security->validatePassword($password,self::$_user->password);
    }

    private static function generateAuthKey() : void{
        self::$_user->updateAttributes(['auth_key' => Yii::$app->security->generateRandomString(64)]);
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login(){
        if($this->validate()){
            self::getUser();
            if(self::$_user !== null){
                return Yii::$app->user->login(self::$_user, $this->rememberMe ? 3600*24*30 : 0);
            }
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     */
    private function getUser() : void{
        if(self::$_user === false){
            self::$_user = self::findByUsername($this->username);
            if(self::$_user === null){
                self::$_user = false;
            }
        }
    }
}