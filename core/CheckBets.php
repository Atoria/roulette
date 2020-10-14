<?php


namespace app\core;


/**
 * <p>Title: VirtualRoulette</p>
 *
 * <p>Description: VirtualRoulette CheckBets class</p>
 *
 * <p>Copyright: Copyright (c) 2013</p>
 *
 * <p>Company: Singular Group LLC</p>
 *
 * @author Shako Kakauridze
 * @version 1.0.0
 */
class CheckBets
{

    private static $VALID_BET_TYPES = array("n", "v", "h", "s", "c", "d", "row", "twelve", "half", "even", "odd", "red", "black");

    private static $BOARD_ROW1 = array(1, 4, 7, 10, 13, 16, 19, 22, 25, 28, 31, 34);
    private static $BOARD_ROW2 = array(2, 5, 8, 11, 14, 17, 20, 23, 26, 29, 32, 35);
    private static $BOARD_ROW3 = array(3, 6, 9, 12, 15, 18, 21, 24, 27, 30, 33, 36);

    private static $BOARD_TWELVE1 = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
    private static $BBOARD_TWELVE2 = array(13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24);
    private static $BOARD_TWELVE3 = array(25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);

    private static $BOARD_HALF1 = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18);
    private static $BOARD_HALF2 = array(19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);


    private static $BOARD_EVEN = array(2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36);
    private static $BOARD_ODD = array(1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29, 31, 33, 35);

    private static $BOARD_RED = array(1, 3, 5, 7, 9, 12, 14, 16, 18, 19, 21, 23, 25, 27, 30, 32, 34, 36);
    private static $BOARD_BLACK = array(2, 4, 6, 8, 10, 11, 13, 15, 17, 20, 22, 24, 26, 28, 29, 31, 33, 35);


    private static $BOARD_SQUARE = array(
        array(-1),
        array(0, 2, 3),
        array(2, 3, 5, 6),
        array(5, 6, 8, 9),
        array(8, 9, 11, 12),
        array(11, 12, 14, 15),
        array(14, 15, 17, 18),
        array(17, 18, 20, 21),
        array(20, 21, 23, 24),
        array(23, 24, 26, 27),
        array(26, 27, 29, 30),
        array(29, 30, 32, 33),
        array(32, 33, 35, 36),
        array(0, 1, 2),
        array(1, 2, 4, 5),
        array(4, 5, 7, 8),
        array(7, 8, 10, 11),
        array(10, 11, 13, 14),
        array(13, 14, 16, 17),
        array(16, 17, 19, 20),
        array(19, 20, 22, 23),
        array(22, 23, 25, 26),
        array(25, 26, 28, 29),
        array(28, 29, 31, 32),
        array(31, 32, 34, 35)
    );


    /**
     *
     * @param String $BetString
     */
    public function IsValid($BetString)
    {

        $responseObject = new IsValidResponse();

        $responseObject->setIsValid(false);
        $responseObject->setBetAmount(0);

        $JSONArray = (array)json_decode($BetString);

        if (!$JSONArray) {
            return $responseObject;
        }

        $hashTable = array();

        //Iterate Over JSONArray
        foreach ($JSONArray as $key => $value) {

            $value = (array)$value;

            if (!in_array($value['T'], CheckBets::$VALID_BET_TYPES)) {
                return $responseObject;
            }

            if (!$this->IsValidTypeId((string)$value['T'], (int)$value['I'])) {
                return $responseObject;
            }

            $bk = (int)$value['K'];
            $bc = (int)$value['C'];
            if ($bc < 1 || $bc > 200) {
                return $responseObject;
            }


            $k = "$value[T]_$value[I]";
            if (!isset($hashTable[$k])) {
                $hashTable[$k] = (int)$bc;
            } else {
                $bcc = (int)$hashTable[$k] + $bc;
                $hashTable[$k] = (int)$bcc;
            }
            $responseObject->setBetAmount($responseObject->getBetAmount() + ($bc * $bk));

            foreach ($hashTable as $key => $value) {
                $sKey = (string)$key;
                $splitedKey = explode("_", $key);
                if ($this->GetMinBetByType($splitedKey[0]) > (int)$value) {
                    return $responseObject;
                }

                if ($this->GetMaxBetByType($splitedKey[0], $splitedKey[1]) < (int)$value) {
                    return $responseObject;
                }

            }

        }


        $responseObject->setIsValid(true);
        return $responseObject;
    }


    public function EstimateWin($BetString, $winnum)
    {
        $win = 0;

        $JSONArray = (array)json_decode($BetString);

        foreach ($JSONArray as $key => $value) {
            $value = (array)$value;
            $bi = (int)$value['I'];
            $bk = (int)$value['K'];
            $bc = (int)$value['C'];
            $k;
            $win2 = 0;

            switch ((string)$value['T']) {
                case "n":
                    if ($bi == $winnum) {
                        $win2 = $bc * 36;
                    }
                    break;

                case "v":
                    $k = $bi - 3;
                    if ($k < 0)
                        $k = 0;
                    if ($bi == $winnum || $k == $winnum)
                        $win2 = $bc * 18;
                    break;

                case "h":
                    if ($bi == $winnum || $bi + 1 == $winnum)
                        $win2 = $bc * 18;
                    break;

                case "s":
                    if (in_array($winnum, CheckBets::$BOARD_SQUARE[$bi]))
                        $win2 = $bc * (36 / count(CheckBets::$BOARD_SQUARE[$bi]));
                    break;

                case "c":
                    $k = ($bi - 1) * 3;
                    if ($k + 1 == $winnum || $k + 2 == $winnum || $k + 3 == $winnum)
                        $win2 = $bc * 12;
                    break;
                case "d":
                    if ($bi == 1) {
                        if (0 <= $winnum && $winnum <= 3)
                            $win2 = $bc * 9;
                    } else {
                        $k = ($bi - 1) * 3;
                        if ($k - 2 <= $winnum && $winnum <= $k + 3)
                            $win2 = $bc * 6;
                    }
                    break;
                case "row":
                    switch ($bi) {
                        case 1:
                            if (in_array($winnum, CheckBets::$BOARD_ROW1))
                                $win2 = $bc * 3;
                            break;
                        case 2:
                            if (in_array($winnum, CheckBets::$BOARD_ROW2))
                                $win2 = $bc * 3;
                            break;
                        case 3:
                            if (in_array($winnum, CheckBets::$BOARD_ROW3))
                                $win2 = $bc * 3;
                            break;
                    }
                    break;

                case "twelve":
                    switch ($bi) {
                        case 1:
                            if (in_array($winnum, CheckBets::$BOARD_TWELVE1))
                                $win2 = $bc * 3;
                            break;
                        case 2:
                            if (in_array($winnum, CheckBets::$BOARD_TWELVE2))
                                $win2 = $bc * 3;
                            break;
                        case 3:
                            if (in_array($winnum, CheckBets::$BOARD_TWELVE3))
                                $win2 = $bc * 3;
                            break;
                    }
                    break;
                case "half":
                    switch ($bi) {
                        case 1:
                            if (in_array($winnum, CheckBets::$BOARD_HALF1))
                                $win2 = $bc * 2;
                            break;
                        case 2:
                            if (in_array(CheckBets::$BOARD_HALF2, $winnum))
                                $win2 = $bc * 2;
                            break;
                    }
                    break;
                case "even":
                    if (in_array($winnum, CheckBets::$BOARD_EVEN))
                        $win2 = $bc * 2;
                    break;
                case "odd":
                    if (in_array($winnum, CheckBets::$BOARD_ODD))
                        $win2 = $bc * 2;
                    break;
                case "red":
                    if (in_array($winnum, CheckBets::$BOARD_RED))
                        $win2 = $bc * 2;
                    break;
                case "black":
                    if (in_array($winnum, CheckBets::$BOARD_BLACK))
                        $win2 = $bc * 2;
                    break;
            }
            if ($win2 > 0)
                $win += $win2 * $bk;

        }
        return $win;


    }


    private function IsInArray($arrayObject, $id)
    {
        if (in_array($id, $arrayObject)) {
            return true;
        }
        return false;
    }


    public function IsValidTypeId($typeString, $id)
    {
        $id = (int)$id;

        switch ($typeString) {
            case "n":
                return $id >= 0 && $id <= 36;
            case "v":
                return $id >= 1 && $id <= 36;
            case "h":
                return $id >= 1 && $id <= 36 && $id % 3 != 0;
            case "s":
                return $id >= 1 && $id <= 24;
            case "c":
                return $id >= 1 && $id <= 12;
            case "d":
                return $id >= 1 && $id <= 12;
            case "row":
                return $id >= 1 && $id <= 3;
            case "twelve":
                return $id >= 1 && $id <= 3;
            case "half":
                return $id == 1 || $id == 2;
            case "even":
                return $id == 1;
            case "odd":
                return $id == 1;
            case "red":
                return $id == 1;
            case "black":
                return $id == 1;
            default:
                return false;
        }
    }


    private function GetMinBetByType($typeString)
    {
        switch ($typeString) {

            case "n":
            case "v":
            case "h":
            case "s":
            case "c":
            case "d":
                return 1;
                break;

            case "row":
            case "twelve":
                return 5;
                break;


            case "half":
            case "even":
            case "odd":
            case "red":
            case "black":
                return 10;
                break;

            default:
                return 1;
                break;
        }
    }


    private function GetMaxBetByType($typeString, $id)
    {
        switch ($typeString) {

            case 'n':
                return 20;
                break;


            case 'v':
            case 'h':
                return 40;
                break;


            case 's':
                if ($id == "1" || $id == "13") {
                    return 60;
                } else {
                    return 80;
                }
                break;

            case 'c':
                return 60;
                break;

            case 'd':
                if ($id == "1") {
                    return 80;
                } else {
                    return 120;
                }
                break;

            case "row":
            case "twelve":
                return 100;
                break;


            case "half":
            case "even":
            case "odd":
            case "red":
            case "black":
                return 200;
                break;

            default:
                return 0;
                break;
        }
    }

}
