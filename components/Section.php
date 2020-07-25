<?php namespace Cjkpl\Tiles\Components;

use Cms\Classes\ComponentBase;

class Section extends ComponentBase
{
    /**
     * @var cards to display
     */
    public $cards;

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
                'description' => 'Leave empty to use default; If entered, OctoberCMS will use a partial tiles/+layout_name',
                'type'        => 'string',
                'placeholder' => 'default',
                'options'     => [1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6']
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
            ]
        ];
    }

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
        $l = \Cjkpl\PubliCat\Classes\Languages::getCommonLanguageOptions();
        return (['' => '- Any -'] + $l);
    }

    public function onRun()
    {
        $this->cards = \Cjkpl\Tiles\Models\Card::where('section_id','=',$this->property('section'))
            ->where('is_visible',true)->orderBy('sort_order','asc')->get();
    }
}
