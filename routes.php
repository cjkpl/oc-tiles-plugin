<?php

/**
 * Return card for id; optional parameter 'columns' allows limiting retrieval to selected columns only
 * For all columns, empty or '*'; otherwise comma-separated list of column names.
 */
if (Config::get('cjkpl.tiles::TILES_API_ENABLED')) {
    Route::get(
        Config::get('cjkpl.tiles::TILES_API_ROUTE'),
        function ($id, $columns = '*') {
            return Response::json(\Cjkpl\Tiles\Classes\CardMaker::getCard($id, false, $columns));
        }
    );
}
