<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int|null $balance
 * @property int $created_at
 * @property int $updated_at
 * @property int $last_active_at
 * @property string $password
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $password;

    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DELETED = 3;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_LOGIN = 'login';

    //Set behaviour to set user columns automatically
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'password'], 'required', 'on' => self::SCENARIO_CREATE],
            [['email', 'password'], 'required', 'on' => self::SCENARIO_LOGIN],
            [['status', 'balance', 'created_at', 'updated_at', 'last_active_at'], 'integer'],
            [['first_name', 'last_name', 'password_hash', 'password_reset_token', 'email', 'password'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['access_token'], 'string'],
            [['first_name', 'last_name', 'email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'balance' => 'Balance',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\UserQuery(get_called_class());
    }

    public static function findIdentity($id)
    {
        return static::find()
            ->active()
            ->andWhere(['id' => $id])
            ->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->active()
            ->andWhere(['access_token' => $token, 'status' => self::STATUS_ACTIVE])
            ->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }


    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }


    public static function findByEmail($email)
    {
        return self::find()->andWhere(['status' => self::STATUS_ACTIVE])->andWhere(['email' => $email])->one();
    }

    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    public function getData()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'firstname' => $this->first_name,
            'lastname' => $this->last_name,
            'member_since' => Yii::$app->formatter->asDate($this->created_at, 'short'),
            'balance' => Yii::$app->formatter->asDecimal($this->balance, 2),
            'last_active_at' => $this->last_active_at,
            'last_active_formatted' => Yii::$app->formatter->asDate($this->last_active_at)
        ];
    }

}
