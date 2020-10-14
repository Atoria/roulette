<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "spin_log".
 *
 * @property int $id
 * @property string|null $status
 * @property float|null $winning_number
 * @property float|null $won_amount
 * @property string|null $user_ip
 * @property int|null $created_by
 * @property int|null $created_at
 *
 * @property User $createdBy
 */
class SpinLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'spin_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['winning_number', 'won_amount'], 'number'],
            [['created_by', 'created_at'], 'integer'],
            [['status', 'user_ip'], 'string', 'max' => 255],
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
            'status' => 'Status',
            'winning_number' => 'Winning Number',
            'won_amount' => 'Won Amount',
            'user_ip' => 'User Ip',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
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
