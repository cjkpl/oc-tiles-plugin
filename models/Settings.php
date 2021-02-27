<?php

declare(strict_types=1);

namespace Cjkpl\Tiles\Models;

use October\Rain\Database\Model;
use System\Behaviors\SettingsModel;

// SETTINGS ARE NOT USED YET!

class Settings extends Model
{
    // public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'cjkpl_tiles_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->implement = [SettingsModel::class];

        parent::__construct($attributes);
    }

    public static function isApiEnabled(): bool
    {
        return (bool) (new self)->get('api_enabled', false);
    }

}
