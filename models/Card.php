<?php namespace Cjkpl\Tiles\Models;

use Model;
use Url;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;


/**
 * Model
 */
class Card extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'cjkpl_tiles_cards';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $jsonable = ['tags','custom_set', 'tags_url'];

    public function getLanguageOptions($value, $formData)
    {
        return \Cjkpl\Tiles\Classes\Languages::getCommonLanguageOptions();
    }

    public $belongsTo = [
        'section' => ['Cjkpl\Tiles\Models\Section', 'key' => 'section_id', 'otherKey' => 'id']
    ];

    //
    // Scopes
    //

    public function scopeIsVisible($query)
    {
        return $query
            ->where('is_visible', true);
    }

    /**
     * @param $query
     * @return mixed
     * Used to include/exclude from seo and menu item list
     */
    public function scopeIsSeo($query)
    {
        return $query
            ->where('is_seo', true);
    }

    /**
     * Handler for the pages.menuitem.getTypeInfo event.
     * Returns a menu item type information. The type information is returned as array
     * with the following elements:
     * - references - a list of the item type reference options. The options are returned in the
     *   ["key"] => "title" format for options that don't have sub-options, and in the format
     *   ["key"] => ["title"=>"Option title", "items"=>[...]] for options that have sub-options. Optional,
     *   required only if the menu item type requires references.
     * - nesting - Boolean value indicating whether the item type supports nested items. Optional,
     *   false if omitted.
     * - dynamicItems - Boolean value indicating whether the item type could generate new menu items.
     *   Optional, false if omitted.
     * - cmsPages - a list of CMS pages (objects of the Cms\Classes\Page class),
     *   if the item type requires a CMS page reference to
     *   resolve the item URL.
     *
     * @param string $type Specifies the menu item type
     * @return array Returns an array
     */
    public static function getMenuTypeInfo($type)
    {
        $result = [];

        if ($type == 'tile') {
            $references = [];

            $tiles = self::orderBy('title')->get();
            foreach ($tiles as $tile) {
                $references[$tile->id] = $tile->title;
            }

            $result = [
                'references'   => $references,
                'nesting'      => false,
                'dynamicItems' => false
            ];
        }

        if ($type == 'all-tiles') {
            $result = [
                'dynamicItems' => true
            ];
        }

        if ($type == 'one-section-tiles') {
            $references = [];

            $sections = Section::orderBy('name')->get();
            foreach ($sections as $section) {
                $references[$section->id] = $section->name;
            }

            $result = [
                'references'   => $references,
                'dynamicItems' => true
            ];
        }

        if ($result) {
            // retrieve pages with 'Card' component
            $theme = Theme::getActiveTheme();

            $pages = CmsPage::listInTheme($theme, true);
            $cmsPages = [];

            foreach ($pages as $page) {
                if (!$page->hasComponent('card')) {
                    continue;
                }

                $cmsPages[] = $page;
            }

            $result['cmsPages'] = $cmsPages;

            return $result;
        }
    } // end getMenuItemTypeInfo

    /**
     * Handler for the pages.menuitem.resolveItem event.
     * Returns information about a menu item. The result is an array
     * with the following keys:
     * - url - the menu item URL. Not required for menu item types that return all available records.
     *   The URL should be returned relative to the website root and include the subdirectory, if any.
     *   Use the Url::to() helper to generate the URLs.
     * - isActive - determines whether the menu item is active. Not required for menu item types that
     *   return all available records.
     * - items - an array of arrays with the same keys (url, isActive, items) + the title key.
     *   The items array should be added only if the $item's $nesting property value is TRUE.
     *
     * @param \RainLab\Pages\Classes\MenuItem $item Specifies the menu item.
     * @param \Cms\Classes\Theme $theme Specifies the current theme.
     * @param string $url Specifies the current page URL, normalized, in lower case
     * The URL is specified relative to the website root, it includes the subdirectory name, if any.
     * @return mixed Returns an array. Returns null if the item cannot be resolved.
     */
    public static function resolveMenuItem($item, $url, $theme)
    {

        $result = null;

        if ($item->type == 'tile') {
            if (!$item->reference || !$item->cmsPage) {
                return;
            }

            $category = self::find($item->reference);
            if (!$category) {
                return;
            }

            $pageUrl = self::getTilePageUrl($item->cmsPage, $category, $theme);
            if (!$pageUrl) {
                return;
            }

            $pageUrl = Url::to($pageUrl);

            $result = [];
            $result['url'] = $pageUrl;
            $result['isActive'] = $pageUrl == $url;
            $result['mtime'] = $category->updated_at;
        }
        elseif ($item->type == 'all-tiles') {
            $result = [
                'items' => []
            ];

            $tiles = self::isVisible()
                ->isSeo() //seo scope excludes from both menu item list and sitemap.xml
                ->orderBy('title')
                ->get();

            foreach ($tiles as $tile) {
                $tileItem = [
                    'title' => $tile->title,
                    'url'   => self::getTilePageUrl($item->cmsPage, $tile, $theme),
                    'mtime' => $tile->updated_at
                ];

                $tileItem['isActive'] = $tileItem['url'] == $url;

                $result['items'][] = $tileItem;
            }
        }
        elseif ($item->type == 'one-section-tiles') {
            if (!$item->reference || !$item->cmsPage) {
                return;
            }

            $section = Section::find($item->reference);
            if (!$section) {
                return;
            }

            $result = [
                'items' => []
            ];

            $query = self::isVisible()
                ->isSeo() //seo scope excludes from both menu item list and sitemap.xml
                ->orderBy('title');

            $query->whereHas('section', function($q) use ($item) {
                $q->where('id', $item->reference);
            });

            $tiles = $query->get();

            foreach ($tiles as $tile) {
                $tileItem = [
                    'title' => $tile->title,
                    'url'   => self::getTilePageUrl($item->cmsPage, $tile, $theme),
                    'mtime' => $tile->updated_at
                ];

                $tileItem['isActive'] = $tileItem['url'] == $url;

                $result['items'][] = $tileItem;
            }
        }

        return $result;
    } // end resolveMenuItem

    /**
     * Returns URL of a tile page.
     *
     * @param $pageCode
     * @param $category
     * @param $theme
     */
    protected static function getTilePageUrl($pageCode, $category, $theme)
    {
        $page = CmsPage::loadCached($theme, $pageCode);
        if (!$page) {
            return;
        }

        $params = [
            'id' => $category->id
        ];
        $url = CmsPage::url($page->getBaseFileName(), $params);

        return $url;
    }
}
