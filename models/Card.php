<?php namespace Cjkpl\Tiles\Models;

use Model;

/**
 * Model
 */
class Card extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'cjkpl_tiles_cards';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public function getLanguageOptions($value, $formData)
    {
        return \Cjkpl\PubliCat\Classes\Languages::getCommonLanguageOptions();
    }

    public $belongsTo = [
        'section' => ['Cjkpl\Tiles\Models\Section', 'key' => 'section_id', 'otherKey' => 'id']
    ];
}
