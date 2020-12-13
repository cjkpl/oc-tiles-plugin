<?php namespace Cjkpl\Tiles;

use System\Classes\PluginBase;
use Illuminate\Support\Facades\Event;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Cjkpl\Tiles\Components\Section' => 'section',
            'Cjkpl\Tiles\Components\Card' => 'card',
        ];
    }

    public function registerPageSnippets()
    {
        return [
            'Cjkpl\Tiles\Components\Section' => 'section',
            'Cjkpl\Tiles\Components\Card' => 'card',
        ];
    }

    public function registerSettings()
    {
    }

    public function register()
    {
        Event::listen('cms.page.beforeRenderPage', function($controller, $page) {

            $twig = $controller->getTwig();
            $twig->addExtension(new \Twig_Extension_StringLoader());
        });
    }

    public function registerPermissions()
    {
        return [
            'cjkpl.tiles.cards' => [
                'tab'   => 'Tiles',
                'label' => 'Manage cards'
            ],
            'cjkpl.tiles.sections' => [
                'tab'   => 'Tiles',
                'label' => 'Manage sections (with cards)'
            ],
        ];
    }
}
