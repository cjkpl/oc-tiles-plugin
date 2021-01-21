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
    public static function getCard(?int $id = null, bool $forSeo = false) : ?CardModel
    {
        if (!$id) {
            $seg = Request::segments();
            $id = intval(end($seg));
        }
        return self::getCardFromId($id, $forSeo);
    }

    /**
     * Retrieves cardModel by id.
     * Takes into account only visible cards in visible sections
     * @param  int  $id record id
     * @param bool $forSeo if true, requires that the record has is_seo=true
     * @return CardModel|null
     */
    protected static function getCardFromId(int $id, bool $forSeo = false) : ?CardModel
    {
        if ($id < 1) return null;
        $card = CardModel::whereHas('section', function ($q) {
            $q->where('is_visible',true);
            })
            ->where('id','=',$id)
            ->where('is_visible',true);

        if ($forSeo) {
            $card->where('is_seo', true);
        }
        return $card->first();
    }

}