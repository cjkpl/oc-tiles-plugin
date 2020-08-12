<?php namespace Cjkpl\Tiles\Components;

use Cms\Classes\ComponentBase;

class Card extends ComponentBase
{

    /**
     * @var \Cjkpl\Tiles\Models\Card the Card model to display
     */
    public $card;

    public function componentDetails()
    {
        return [
            'name'        => 'Card Content',
            'description' => 'Individual card content'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->card = 
            \Cjkpl\Tiles\Models\Card
                ::where('id','=',$this->param('id'))
                ->where('is_visible',true)
                ->first();
    }
}
