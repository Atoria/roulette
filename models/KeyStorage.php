<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "key_storage".
 *
 * @property int $id
 * @property string|null $key
 * @property string|null $value
 */
class KeyStorage extends \yii\db\ActiveRecord
{

    const JACKPOT_KEY = 'jackpot';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'key_storage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
            [['key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\KeyStorageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\KeyStorageQuery(get_called_class());
    }
}
