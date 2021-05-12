<?php


namespace Cjkpl\Tiles\Classes;

use Cjkpl\Tiles\Models\Card as CardModel;
use Illuminate\Support\Facades\Request;
use Event;
use Config;
use Illuminate\Support\Facades\Schema;

/**
 * Class CardMaker
 * @package Cjkpl\Tiles\Classes
 */

class CardMaker
{

    /**
     * retrieves cardModel instance from provided
     * slug parameter, and if none, attempts to infer
     * the parameter from last segment of URL
     * @param  string|null  $slug
     */
    public static function getCardBySlug(?string $slug = null, bool $forSeo = false, string $columns = '*') : ?CardModel
    {
        if (!$slug) {
            $seg = Request::segments();
            $slug = end($seg);
        }
        $id = 0;
        return self::getCardFromSlugOrId($id, $slug, $forSeo, $columns);
    }


    /**
     * retrieves cardModel instance from provided
     * parameter, and if none, attempts to infer
     * the parameter from last segment of URL
     * @param  int|null  $id
     */
    public static function getCardById(?int $id = null, bool $forSeo = false, string $columns = '*') : ?CardModel
    {
        if (!$id) {
            $seg = Request::segments();
            $id = intval(end($seg));
        }
        $slug = '';
        return self::getCardFromSlugOrId($id, $slug, $forSeo, $columns);
    }

    /**
     * Retrieves cardModel by id.
     * Takes into account only visible cards in visible sections
     * @param  int  $id record id
     * @param string $slug record slug - takes precedence over id, if present
     * @param bool $forSeo if true, requires that the record has is_seo=true
     * @param string $columns list of column names to retrieve or * for all
     * @return CardModel|null
     */
    protected static function getCardFromSlugOrId(int $id = 0, string $slug = '', bool $forSeo = false, string $columns = '*') : ?CardModel
    {
        // can't pass raw list of columns, to avoid sql attacks
        // config may define a list of available columns - if not, use all from table

        if (Config::get('cjkpl.tiles::TILES_API_ALLOWED_COLUMNS') == '*') {
            $available_cols = \October\Rain\Support\Facades\Schema
                ::getColumnListing(app(\Cjkpl\Tiles\Models\Card::class)->getTable());
        } else {
            $available_cols = explode(',', Config::get('cjkpl.tiles::TILES_API_ALLOWED_COLUMNS'));
        }
        $requested_cols = ($columns == '*')
            ? $available_cols
            : explode(',', $columns);

        $final_columns = array_intersect($available_cols, $requested_cols);

        // protect against wrong columns / none matching
        if (sizeof($final_columns) == 0) {
            $final_columns = ['id'];
        }

        if ($id < 1 && $slug == '') {
            return null;
        }

        $card = CardModel::whereHas('section', function ($q) {
                $q->where('is_visible', true);
            })
            ->where('is_visible', true);
        
        if ($slug) {
            $card = $card->where('slug', '=' ,$slug);
        } else {
            $card = $card->where('id', '=' ,$id);
        }
        
        if ($forSeo) {
            $card = $card->where('is_seo', true);
        }
        return $card->first($final_columns);
    }

}
