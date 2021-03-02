<?php namespace Cjkpl\Tiles\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Input;
use Event;

class Section extends ComponentBase
{
    /**
     * @var cards to display
     */
    public $cards;

    /**
     * @var section(s) to display
     */
    public $sections;

    /**
     * Reference to the page name for linking to card content
     *
     * @var string
     */
    public $contentPage;

    /**
     * @var string $REF last visited page
     */
    public $REF;

    public function componentDetails()
    {
        return [
            'name'        => 'Section of Cards (Tiles)',
            'description' => 'Uses "Tiles" tile in top menu to edit cards'
        ];
    }

    public function defineProperties()
    {
        return [
            'columns' => [
                'title'       => 'Columns',
                'description' => 'Number of columns in one row; additional cards will wrap',
                'type'        => 'dropdown',
                'default'     => '3',
                'placeholder' => 'Number of columns (3)',
                'options'     => [1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6']
            ],
            'layout' => [
                'title'       => 'Layout',
                'description' => 'Leave empty to use default;
                                 If entered, OctoberCMS will use a partial tiles/+layout_name',
                'type'        => 'dropdown',
                'placeholder' => '-default-'
            ],
            'language' => [
                'title'       => 'Language filter',
                'description' => 'Filter out all cards with languages other than selected',
                'default'     => '',
                'type'        => 'dropdown',
            ],
            'section' => [
                'title'   => 'Section',
                'description' => 'Select section of tiles/cards to display',
                'type'    => 'dropdown'
            ],
            'flipeven' => [
                'title'       => 'Alternate odd/even',
                'description' => 'Alternate layout for odd/even cards (if defined in template)',
                'type'        => 'checkbox'
            ],
            'contentPage' => [
                'title'       => 'Content page',
                'description' => 'Optional page to show `Content` field of any card',
                'type'        => 'dropdown',
                'group'       => 'Links',
            ],
        ];
    }

    public function onRun()
    {
        $this->prepareVars();

        // if param('slug') defined, it overrides 'section' property

        $section_ids = [$this->property('section')]; // default from property

        if ($this->param('slug')) { // override from url
            // allow multiple sections - if slug contains a comma, break it into tokens
            $section_ids = explode(',',$this->param('slug'));
        }

        $this->cards =
            \Cjkpl\Tiles\Models\Card
                ::whereIn('section_id',$section_ids)
                ->where('is_visible',true)
                ->orderBy('section_id','asc')
                ->orderBy('sort_order','asc')->get();

        // TODO! Fix this so that cards in child sections do not ignore is_visible flag!!!

        $this->sections =
            \Cjkpl\Tiles\Models\Section::whereIn('id', $section_ids)
                                        ->where('is_visible',true)
                                        ->with(['cards' => function($query){
                                            $query->where('is_visible',true);
                                        }])
                                        ->orderBy('id','asc')
                                        ->get(['id','name','is_visible','layout']);

        // notify extending plugins (e.g. SQLTiles) of the retrieved card contents
        Event::fire('cjkpl.tiles.section.display', [&$this]);

        /*
         * Add a "url" helper attribute for linking to each card detail
         */
        $this->cards->each(function($card) {
            if (
                ($card['autolink_content'] == 1) &&  // ok to autolink
                (strlen($card['url']) == 0 &&        // but only if url is empty
                 strlen($card['content'])>3)         // and there is some content defined
                )
                {
                $card->url = $this->controller->pageUrl($this->property('contentPage'), ["id" => $card['id']]);
                }
        });

    }

    public function getContentPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    protected function prepareVars()
    {
        /*
         * Page links
         */
        $this->contentPage = $this->page['contentPage'] = $this->property('contentPage');

        $this->REF = $_SERVER['HTTP_REFERER'] ?? '/';
    }

    /**
     * @return array list of dropdown options for Section Component+Snippet
     */
    public function getSectionOptions()
    {
        $sections = \Cjkpl\Tiles\Models\Section::select('id','name')->where('is_visible',true)->get();
        $options = [];
        foreach ($sections as $section) {
            $options[$section['id']] = $section['name'];
        }
        return $options;
    }

    public function getLanguageOptions()
    {
        $l = \Cjkpl\Tiles\Classes\Languages::getCommonLanguageOptions();
        return (['' => '- Any -'] + $l);
    }

    /**
     * Get all partials in the "tiles" subfolder
     *
     * @return array
     */
    public function getLayoutOptions()
    {
        $theme = \Cms\Classes\Theme::getActiveTheme();

        $path = $theme->getPath() . '/partials/tiles';

        if (file_exists($path) === false) {
            return [];
        }

        $files = array_diff(scandir($path), array('.', '..'));

        $options = [
            '-default-' => 'Default Layout', // use components/section/tileset1.htm
            '-section-' => 'Section (inline)' // use inline layout specified in Section config (e.g. from FateFactory)
        ];
        foreach ($files as $file) {
            if (substr($file, 0, 1) !== '_') {
                $name = $file;
                $options[$file] = $name;
            }
        }

        return $options;
    }
}
