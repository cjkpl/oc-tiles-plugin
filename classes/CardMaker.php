<?php


namespace Cjkpl\Tiles\Classes;

use Cjkpl\Tiles\Models\Card as CardModel;
use Illuminate\Support\Facades\Request;
use Event;
use Illuminate\Support\Facades\Schema;

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
    public static function getCard(?int $id = null, bool $forSeo = false, string $columns = '*') : ?CardModel
    {
        if (!$id) {
            $seg = Request::segments();
            $id = intval(end($seg));
        }
        return self::getCardFromId($id, $forSeo, $columns);
    }

    /**
     * Retrieves cardModel by id.
     * Takes into account only visible cards in visible sections
     * @param  int  $id record id
     * @param bool $forSeo if true, requires that the record has is_seo=true
     * @param string $columns list of column names to retrieve or * for all
     * @return CardModel|null
     */
    protected static function getCardFromId(int $id, bool $forSeo = false, string $columns = '*') : ?CardModel
    {
        // can't pass raw list of columns, to avoid sql attacks
        $available_cols = \October\Rain\Support\Facades\Schema
            ::getColumnListing(app(\Cjkpl\Tiles\Models\Card::class)->getTable());
        $requested_cols = ($columns == '*')
            ? $available_cols
            : explode(',', $columns);

        $final_columns = array_intersect($available_cols, $requested_cols);

        // protect against wrong columns / none matching
        if (sizeof($final_columns) == 0) {
            $final_columns = ['id'];
        }

        if ($id < 1) {
            return null;
        }

        $card = CardModel::whereHas('section', function ($q) {
                $q->where('is_visible', true);
            })
            ->where('id', '=' ,$id)
            ->where('is_visible', true);

        if ($forSeo) {
            $card->where('is_seo', true);
        }
        return $card->first($final_columns);
    }

}
