<?php


namespace app\controllers;

use app\models\LoginForm;
use app\models\User;
use frontend\modules\api\v1\helpers\UserHelper;
use Yii;
use yii\rest\ActiveController;

/**
 * Class UserController
 */
class UserController extends BaseController
{
    public $modelClass = 'models\EmptyModel';

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'get-balance' => ['GET']
        ];
    }


    public function actionGetBalance()
    {
        $user = Yii::$app->user->identity;
        return [
            'success' => true,
            'balance' => Yii::$app->formatter->asDecimal($user->balance, 2)
        ];
    }

}