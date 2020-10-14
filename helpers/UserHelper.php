<?php

namespace frontend\modules\api\v1\helpers;


use app\models\User;

class UserHelper
{

    /**
     * Get current user by header Authorization
     * @return array|\yii\db\ActiveRecord|\yii\web\IdentityInterface
     */
    public static function getCurrentUser()
    {
        $request = \Yii::$app->request;
        $headers = $request->getHeaders();
        $header = $headers->get('Authorization');
        if (!$header){
            return null;
        }
        $split = explode(" ", $header);
        if (count($split) !== 2 || $split[0] !== 'Bearer'){
            return null;
        }
        $accessToken = $split[1];
        return User::findIdentityByAccessToken($accessToken);
    }
}