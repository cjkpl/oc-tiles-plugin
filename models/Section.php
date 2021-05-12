<?php namespace Cjkpl\Tiles\Models;

use Model;
use Url;
use Cms\Classes\Theme;
use Cms\Classes\Page as CmsPage;

/**
 * Model
 */
class Section extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SimpleTree;
    use \October\Rain\Database\Traits\Sortable;
    use \October\Rain\Database\Traits\Sluggable;

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

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
     * - cmsPages - a list of CMS pages (objects of the Cms\Classes\Page class), if the item type requires a CMS page reference to
     *   resolve the item URL.
     * @param string $type Specifies the menu item type
     * @return array Returns an array
     */
    public static function getMenuTypeInfo($type)
    {
        $result = [];

        if ($type == 'tiles-section') {

            $references = [];

            $sections = Section::orderBy('name')
                ->where('is_visible',true)
                ->get();
            foreach ($sections as $section) {
                $references[$section->id] = $section->name;
            }

            $result = [
                'references'   => $references,
                'dynamicItems' => true,
                'nesting'      => true,
            ];

        }

        if ($type == 'all-tiles-sections') {
            $result = [
                'dynamicItems' => true
            ];
        }

        if ($result) {
            $theme = Theme::getActiveTheme();

            $pages = CmsPage::listInTheme($theme, true);
            $cmsPages = [];
            foreach ($pages as $page) {
                if (!$page->hasComponent('section')) {
                    continue;
                }

                $cmsPages[] = $page;
            }

            $result['cmsPages'] = $cmsPages;
        }

        return $result;
    }

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
     * @param \RainLab\Pages\Classes\MenuItem $item Specifies the menu item.
     * @param \Cms\Classes\Theme $theme Specifies the current theme.
     * @param string $url Specifies the current page URL, normalized, in lower case
     * The URL is specified relative to the website root, it includes the subdirectory name, if any.
     * @return mixed Returns an array. Returns null if the item cannot be resolved.
     */
    public static function resolveMenuItem($item, $url, $theme)
    {
        $result = null;

        if ($item->type == 'tiles-section') {
            if (!$item->reference || !$item->cmsPage) {
                return;
            }

            // show current section, rather than all its children
            if (!$item->replace) {
                $section = self::find($item->reference);
                if (!$section) {
                    return;
                }

                $pageUrl = self::getSectionPageUrl($item->cmsPage, $section, $theme);
                if (!$pageUrl) {
                    return;
                }

                $pageUrl = Url::to($pageUrl);

                $result = [];
                $result['url'] = $pageUrl;
                $result['isActive'] = $pageUrl == $url;
                $result['mtime'] = $section->updated_at;
            } else {
                $result = [
                    'items' => []
                ];

                $sections = self::orderBy('name')
                    ->where('is_visible',true)
                    ->where('parent_id', $item->reference)
                    ->get();
                foreach ($sections as $section) {
                    $sectionItem = [
                        'title' => $section->name,
                        'url'   => self::getSectionPageUrl($item->cmsPage, $section, $theme),
                        'mtime' => $section->updated_at
                    ];

                    $sectionItem['isActive'] = $sectionItem['url'] == $url;

                    $result['items'][] = $sectionItem;
                }

            }
        }
        elseif ($item->type == 'all-tiles-sections') {
            $result = [
                'items' => []
            ];

            $sections = self::orderBy('name')
                ->where('is_visible',true)
                ->get();
            foreach ($sections as $section) {
                $sectionItem = [
                    'title' => $section->name,
                    'url'   => self::getSectionPageUrl($item->cmsPage, $section, $theme),
                    'mtime' => $section->updated_at
                ];

                $sectionItem['isActive'] = $sectionItem['url'] == $url;

                $result['items'][] = $sectionItem;
            }
        }

        return $result;
    }

    /**
     * Returns URL of a section page.
     *
     * @param $pageCode
     * @param $section
     * @param $theme
     */
    protected static function getSectionPageUrl($pageCode, $section, $theme)
    {
        $page = CmsPage::loadCached($theme, $pageCode);
        if (!$page) {
            return;
        }

        // as of 2021-02-05, only ID is supported,
        // TODO: add slug to editor form and support for slug elsewhere
        $params = [
            'slug' => $section->slug
        ];

        $url = CmsPage::url($page->getBaseFileName(), $params);

        return $url;
    }
}
