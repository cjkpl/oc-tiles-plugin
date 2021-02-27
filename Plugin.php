<?php namespace Cjkpl\Tiles;

use Cjkpl\Tiles\Classes\CardMaker;
use System\Classes\PluginBase;
use Illuminate\Support\Facades\Event;
use Cjkpl\Tiles\Models\Section;
use Cjkpl\Tiles\Models\Card;
use Backend;

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
        return [
// NOT USED YET
//            'config' => [
//                'label'       => 'Tiles',
//                'description' => 'Manage settings for Tiles',
//                'icon'        => 'icon-trello',
//                'class'       => 'Cjkpl\Tiles\Models\Settings',
//                'order'       => 500,
//                'keywords'    => 'tiles settings'
//            ]
        ];
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
// NOT USED YET
//            'cjkpl.tiles.settings' => [
//                'tab'   => 'Tiles',
//                'label' => 'Manage Tiles plugin settings'
//            ],
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

        /*
         * Register menu items for the RainLab.Pages plugin, and for sitemap
         */
        Event::listen('pages.menuitem.listTypes', function() {
            return [
                'tiles-section'         => 'cjkpl.tiles::lang.menuitem.tiles-section',
                'all-tiles-sections'    => 'cjkpl.tiles::lang.menuitem.all-tiles-sections',
                'tile'                  => 'cjkpl.tiles::lang.menuitem.tile',
                'all-tiles'             => 'cjkpl.tiles::lang.menuitem.all-tiles',
                'one-section-tiles'         => 'cjkpl.tiles::lang.menuitem.one-section-tiles',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'tiles-section' || $type == 'all-tiles-sections') {
                return Section::getMenuTypeInfo($type);
            }
            elseif ($type == 'tile' || $type == 'all-tiles' || $type == 'one-section-tiles') {
                return Card::getMenuTypeInfo($type);
            }
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'tiles-section' || $type == 'all-tiles-sections') {
                return Section::resolveMenuItem($item, $url, $theme);
            }
            elseif ($type == 'tile' || $type == 'all-tiles' || $type == 'one-section-tiles') {
                return Card::resolveMenuItem($item, $url, $theme);
            }
        });

    } //end boot
}
