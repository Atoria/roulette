<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "spin_log".
 *
 * @property int $id
 * @property string|null $bet
 * @property string|null $status
 * @property int|null $bet_amount
 * @property int|null $winning_number
 * @property int|null $won_amount
 * @property string|null $user_ip
 * @property int|null $created_by
 * @property int|null $created_at
 *
 * @property User $createdBy
 */
class SpinLog extends \yii\db\ActiveRecord
{
    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'spin_log';
    }


    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'created_at', 'winning_number', 'won_amount', 'bet_amount'], 'integer'],
            [['bet'], 'string'],
            [['status', 'user_ip', ], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bet' => 'Bet',
            'status' => 'Status',
            'bet_amount' => 'Bet Amount',
            'winning_number' => 'Winning Number',
            'won_amount' => 'Won Amount',
            'user_ip' => 'User Ip',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }


    public function getData(){
        return [
            'id' => $this->id,
            'bet_amount' => $this->bet_amount,
            'won_amount' => $this->won_amount,
            'created_at' => Yii::$app->formatter->asDate($this->created_at)
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\SpinLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\SpinLogQuery(get_called_class());
    }
}
