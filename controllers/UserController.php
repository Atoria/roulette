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
            'get-balance' => ['GET'],
            'add-balance' => ['POST']
        ];
    }


    public function actionGetBalance()
    {
        $user = Yii::$app->user->identity;
        return [
            'success' => true,
            'balance' => Yii::$app->formatter->asDecimal($user->balance / 100, 2)
        ];
    }


    //Additional method add balance.
    public function actionAddBalance()
    {
        $amount = Yii::$app->request->post('amount'); //Lets say we get amount in dollar
        $user = Yii::$app->user->identity;

        $user->balance += floatval($amount) * 100; //store in cents


        if (!$user->save()) {
            return [
                'success' => false,
                'error' => $user->error
            ];
        }

        return ['success' => true];

    }

}