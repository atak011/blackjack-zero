<?php

namespace App\Services;

use App\Model\Card;

class CardService
{

    /**
     *This function preparing gaming card total card : 312 total deck : 6
     */
    public static function prepareCards(): array
    {
        $deck = null;
        $types = ['Clubs', 'Hearts', 'Spades', 'Diamonds'];
        $numbers = ['Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King'];
        foreach ($numbers as $number) {
            foreach ($types as $type) {
                $label = "$number of $type";
                $card = new Card($number, $type, $label);
                $deck[] = $card;
            }
        }

        $copyDeck = $deck;
        // 6 Decks Shuffle
        for ($i = 0; $i <= 4; $i++) {
            $deck = array_merge($deck, $copyDeck);
        }
        shuffle($deck);
        return $deck;
    }

    // This function calculating hands and return total value
    public static function calculateHand(array $hand): int
    {
        $value = 0;
        foreach ($hand as $card) {
            switch ($card->value) {
                case 'Ace':
                    $tempHand = $hand;
                    if (($key = array_search($card, $hand)) !== false) {
                        unset($tempHand[$key]);
                    }
                    if (self::calculateHand($tempHand) <= 10) {
                        $value += 11;
                    } else {
                        $value += 1;
                    }
                    break;
                case 'King':
                case 'Queen':
                case 'Jack':
                    $value += 10;
                    break;
                default:
                    $value += (int)$card->value;
                    break;
            }
        }
        return $value;
    }


}