<?php

namespace app\models\query;

use app\models\User;

/**
 * This is the ActiveQuery class for [[\app\models\User]].
 *
 * @see \app\models\User
 */
class UserQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this
     */
    public function notDeleted()
    {
        $this->andWhere(['!=', 'status', User::STATUS_DELETED]);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleted()
    {
        $this->andWhere(['=', 'status', User::STATUS_DELETED]);
        return $this;
    }


    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => User::STATUS_ACTIVE]);
        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function byId($id)
    {
        return $this->andWhere(['id' => $id]);
    }
}
