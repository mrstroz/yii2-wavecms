<?php

namespace mrstroz\wavecms\models;

use mrstroz\wavecms\models\query\UserQuery;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $auth_key
 * @property integer $status
 * @property integer $is_admin
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $roles
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const IS_ADMIN_YES = 1;
    const IS_ADMIN_NO = 0;

    const SCENARIO_MY_ACCOUNT = 'my_account';
    const SCENARIO_CREATE = 'create';

    public $password;
    public $password_repeat;

    public $roles;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios(); // TODO: Change the autogenerated stub

        $scenarios[self::SCENARIO_MY_ACCOUNT] = [
            'password',
            'password_repeat',
            'first_name',
            'last_name',
            'lang'
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique',
                'message' => Yii::t('wavecms/user', 'This email address has already been taken.')
            ],
            [['first_name', 'last_name', 'lang'], 'string'],
            [['password'], 'safe'],
            ['password', 'required','on' => self::SCENARIO_CREATE],
            ['password', 'compare', 'compareAttribute' => 'password_repeat', 'on' => self::SCENARIO_MY_ACCOUNT],
            [['is_admin'], 'boolean'],
            ['username', 'default', 'value' => function ($model, $attribute) {
                return $model->email;
            }]
        ];
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels(); // TODO: Change the autogenerated stub

        $labels['status'] = Yii::t('wavecms/user', 'Status');
        $labels['email'] = Yii::t('wavecms/user', 'Email');
        $labels['first_name'] = Yii::t('wavecms/user', 'First name');
        $labels['last_name'] = Yii::t('wavecms/user', 'Last name');
        $labels['password'] = Yii::t('wavecms/user', 'Password');
        $labels['password_repeat'] = Yii::t('wavecms/user', 'Repeat password');
        $labels['lang'] = Yii::t('wavecms/user', 'Language');
        $labels['is_admin'] = Yii::t('wavecms/user', 'Is admin ?');
        $labels['roles'] = Yii::t('wavecms/user', 'Roles');

        return $labels;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'is_admin' => 1, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'is_admin' => 1, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }
}
