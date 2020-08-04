<?php namespace Cjkpl\Tiles\Models;

use Model;

/**
 * Model
 */
class Section extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'cjkpl_tiles_sections';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $jsonable = ['custom_labels'];


    public $hasMany = [
        'cards' => 'Cjkpl\Tiles\Models\Card'
    ];
}
