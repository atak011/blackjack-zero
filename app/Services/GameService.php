<?php

namespace App\Services;

use App\Commands\GameCommand;

class GameService
{

    protected $cardService;

    public function __construct()
    {
        $this->cardService = CardService::class;
    }

    // This function preparing init cards
    public static function initPlayerAndDealerCards($deck):array{
        $playerHand = null;
        $dealerHand = null;
        for ($i=0; $i <=1 ; $i++) {
            $playerHand[] = array_pop($deck);
            $dealerHand[] = array_pop($deck);
        }
        return [$playerHand,$dealerHand];
    }

    // This function printing normalized string for hand
    public static function getHandDetailedArray($hand):string{
        $labelArray = [];
        foreach ($hand as $card){
            $labelArray[] = $card->labeledValue;
        }
        return implode('-',$labelArray);
    }

}