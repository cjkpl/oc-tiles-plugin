<?php

/**
 * Return card for id; optional parameter 'columns' allows limiting retrieval to selected columns only
 * For all columns, empty or '*'; otherwise comma-separated list of column names.
 */
Route::get('/api/tile/{id}/{columns?}', function($id, $columns = '*')
{
    return Response::json(\Cjkpl\Tiles\Classes\CardMaker::getCard($id, false, $columns));
});
