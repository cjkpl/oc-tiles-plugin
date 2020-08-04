<?php namespace Cjkpl\Tiles;

use System\Classes\PluginBase;
use Illuminate\Support\Facades\Event;

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

    public function register()
    {
        Event::listen('cms.page.beforeRenderPage', function($controller, $page) {

            $twig = $controller->getTwig();
            $twig->addExtension(new \Twig_Extension_StringLoader());
        });
    }
}
