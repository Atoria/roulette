<?php


namespace app\controllers;

use app\models\LoginForm;
use app\models\User;
use frontend\modules\api\v1\helpers\UserHelper;
use Yii;
use yii\rest\ActiveController;

/**
 * Class GuestController
 */
class GuestController extends BaseController
{
    public $modelClass = 'models\EmptyModel';

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'is-logged-in' => ['OPTIONS', 'GET'],
            'login' => ['OPTIONS', 'POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
            'register' => ['POST']
        ];
    }

    public function actionIsLoggedIn()
    {
        $user = UserHelper::getCurrentUser();
        $data = null;
        if ($user) {
            $data = $user->getData();
        }
        return [
            'success' => boolval($user),
            'data' => $data
        ];
    }

    public function actionLogin()
    {
        $request = \Yii::$app->request;


        $model = new LoginForm();
        if ($model->load($request->post(), '') && $model->login()) {
            return [
                'success' => true,
                'data' => \Yii::$app->user->identity->getData(),
                'accessToken' => \Yii::$app->user->identity->auth_key
            ];
        }

        return [
            'success' => false,
            'errors' => $model->errors
        ];
    }


    public function actionRegister()
    {
        $model = new User();
        $post = Yii::$app->request->post();

        if ($model->load($post, '') && $model->validate()) {
            $model->status = User::STATUS_ACTIVE;
            $model->setPassword($model->password);
            $model->generateAuthKey();

            if ($model->save()) {
                return ['success' => true];
            }
        }

        return ['success' => false, 'error' => $model->errors];
    }

}