<?php


namespace Cjkpl\Tiles\Classes;

use Cjkpl\Tiles\Models\Card as CardModel;
use Illuminate\Support\Facades\Request;
use Event;

/**
 * Class CardMaker
 * @package Cjkpl\Tiles\Classes
 */

class CardMaker
{
    /**
     * retrieves cardModel instance from provided
     * parameter, and if none, attempts to infer
     * the parameter from last segment of URL
     * @param  int|null  $id
     */
    public static function getCard(?int $id = null) : ?CardModel
    {
        if (!$id) {
            $seg = Request::segments();
            $id = intval(end($seg));
        }
        return self::getCardFromId($id);
    }

    /**
     * Retrieves cardModel by id.
     * Takes into account only visible cards.
     * @param  int  $id record id
     * @return CardModel|null
     */
    protected static function getCardFromId(int $id) : ?CardModel
    {
        if ($id < 1) return null;
        return(
            CardModel
                ::where('id','=',$id)
                ->where('is_visible',true)
                ->first()
        );
    }

}