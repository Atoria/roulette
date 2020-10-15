<?php


namespace app\controllers;

use app\core\CheckBets;
use app\models\KeyStorage;
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
            'bet' => ['POST'],
            'history' => ['GET']
        ];
    }


    public function actionBet()
    {
        $request = Yii::$app->request;
        $bet = $request->post('bet');
        $user = Yii::$app->user->identity;

        $transaction = Yii::$app->db->beginTransaction();
        $spin = new SpinLog();
        $spin->bet = $bet; //Store bet
        $spin->user_ip = Yii::$app->request->getUserIP(); //Get user Ip
        $checkInstance = new CheckBets();
        //Check Valid
        $validateBet = $checkInstance->IsValid($bet);
        $spin->status = $validateBet->getIsValid() ? SpinLog::STATUS_VALID : SpinLog::STATUS_INVALID;
        $spin->bet_amount = $validateBet->getBetAmount();

        $betAmountCents = floatval($spin->bet_amount) * 100; // convert dollars to cents

        if ($betAmountCents > $user->balance) {
            $transaction->rollBack();
            return [
                'success' => false,
                'error' => 'Not enough money on balance. Current Balance: ' . Yii::$app->formatter->asDecimal($user->balance / 100, 2) . ' betting: ' . Yii::$app->formatter->asDecimal($spin->bet_amount, 2)
            ];
        }

        //Get Estimate Win
        $spin->winning_number = rand(0, 36);
        $spin->won_amount = $checkInstance->EstimateWin($bet, $spin->winning_number);

        if (!$spin->save()) {
            $transaction->rollBack();
            return [
                'success' => false,
                'error' => $spin->errors
            ];
        }

        //update user balance based on result
        $user->balance += floatval($spin->won_amount) * 100 - $betAmountCents;

        if (!$user->save()) {
            $transaction->rollBack();
            return [
                'success' => false,
                'error' => $user->errors
            ];
        }


        $jackpot = KeyStorage::find()->andWhere(['key' => KeyStorage::JACKPOT_KEY])->one();
        if (!$jackpot) {
            $jackpot = new KeyStorage();
            $jackpot->key = KeyStorage::JACKPOT_KEY;
            $jackpot->value = 0;
        }


        $jackpot->value = (string)($spin->bet_amount + intval($jackpot->value));

        if (!$jackpot->save()) {
            $transaction->rollBack();
            return [
                'success' => false,
                'error' => $jackpot->errors
            ];
        }

        $ch = curl_init( Yii::$app->params['socketUrl'] );
        //Setup request to send json via POST.
        $payload = json_encode( ["jackpot"=> $jackpot->value / 100] );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        //return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        //Send request.
        $result = curl_exec($ch);
        curl_close($ch);

        $transaction->commit();
        return [
            'success' => true,
            'data' => $spin->getData()
        ];

    }

    public function actionHistory()
    {
        $request = Yii::$app->request;
        $limit = $request->get('limit', null);
        $offset = $request->get('offset', null);
        $user = Yii::$app->user->identity;

        $spins = SpinLog::find()->andWhere(['created_by' => $user->id]);
        $total = $spins->count();
        //If there is pagination from frontend paginate
        if ($limit && $offset) {
            $spins->limit($limit)->offset($offset);
        }
        $spins = $spins->orderBy('created_at desc')->all();

        $data = [];
        foreach ($spins as $spin) {
            $data[] = $spin->getData();
        }

        return [
            'success' => true,
            'total' => $total,
            'spins' => $data
        ];
    }

}