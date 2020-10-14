<?php


namespace app\controllers;

use app\core\CheckBets;
use app\models\LoginForm;
use app\models\SpinLog;
use app\models\User;
use frontend\modules\api\v1\helpers\UserHelper;
use Yii;
use yii\rest\ActiveController;

/**
 * Class SpinController
 */
class SpinController extends BaseController
{
    public $modelClass = 'models\SpinLog';

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'bet' => ['POST']
        ];
    }


    public function actionBet()
    {
        $request = Yii::$app->request;
        $bet = $request->post('bet');
        $user = Yii::$app->user->identity;

        $spin = new SpinLog();
        $spin->bet = $bet; //Store bet
        $spin->user_ip = Yii::$app->request->getUserIP(); //Get user Ip
        $checkInstance = new CheckBets();
        //Check Valid
        $validateBet = $checkInstance->IsValid($bet);
        $spin->status = $validateBet->getIsValid() ? SpinLog::STATUS_VALID : SpinLog::STATUS_INVALID;
        $spin->bet_amount = $validateBet->getBetAmount();

        $betAmountCents = floatval($spin->bet_amount) * 100;

        if ($betAmountCents > $user->balance) {
            return [
                'success' => false,
                'error' => 'Not enough money on balance. Current Balance: ' . Yii::$app->formatter->asDecimal($user->balance / 100, 2) . ' betting: ' . Yii::$app->formatter->asDecimal($spin->bet_amount, 2)
            ];
        }


        //Get Estimate Win
        $spin->winning_number = rand(0, 36);
        $spin->won_amount = $checkInstance->EstimateWin($bet, $spin->winning_number);

        if (!$spin->save()) {
            return [
                'success' => false,
                'error' => $spin->errors
            ];
        }


        $user->balance += floatval($spin->won_amount) * 100 - $betAmountCents;

        if (!$user->save()) {
            return [
                'success' => false,
                'error' => $user->errors
            ];
        }


        return [
            'success' => true,
            'data' => $spin->getData()
        ];

    }


}