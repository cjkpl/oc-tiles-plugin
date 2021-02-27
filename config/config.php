<?php

return [
    'TILES_API_ENABLED' => env('TILES_API_ENABLED', false),
    'TILES_API_ROUTE'   => env('TILES_API_ROUTE', '/api/tile/{id}/{columns?}'),
    'TILES_API_ALLOWED_COLUMNS' => env('TILES_API_ALLOWED_COLUMNS', '*')
];
