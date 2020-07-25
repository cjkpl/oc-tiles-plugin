<?php namespace Cjkpl\Tiles;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Cjkpl\Tiles\Components\Section' => 'section',
        ];
    }

    public function registerPageSnippets()
    {
        return [
            'Cjkpl\Tiles\Components\Section' => 'section',
        ];
    }

    public function registerSettings()
    {
    }
}
