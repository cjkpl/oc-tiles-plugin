<?php namespace Cjkpl\Tiles;

use Cjkpl\Tiles\Classes\CardMaker;
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
            $twig->addExtension(new \Twig\Extension\StringLoaderExtension());
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

    public function boot()
    {
        /**
         * Add card tags as meta keywords in response to Cjkpl.Seo plugin event
         */
        Event::listen(
            'cjkpl.seo.prepare',
            function (string &$title, string &$description, \October\Rain\Support\Collection &$keywords) {

                // retrieve card by last URI segment (should be card ID)
                $card = CardMaker::getCard(null, true);
                // if no card found, return without any action
                if (!$card) return;

                // append card title to page title
                $title = $card->title . " - " . $title;

                // if card has a description, use it (removing any html tags)
                // but only if it does not contain any twig!
                // TODO: figure out how to process twig in desc for seo purposes
                $txt_description = trim(strip_tags($card->description));
                // dd((strpos($txt_description, '{{') === false));
                if (
                (strlen($txt_description) > 3) &&
                (strpos($txt_description, '{{') === false)) {
                    $description = $txt_description;
                }

                // remove empty elements
                $tags = array_filter(explode(',', $card->tags));

                // if no tags, return without any action
                if (count($tags) < 1) return;

                $keywords = $keywords->merge($tags);
            }
        );
    }
}
