<?php namespace Cjkpl\Tiles\Components;

use Cms\Classes\ComponentBase;
use Event;

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

        // notify extending plugins (e.g. SQLTiles) of the retrieved card contents
        Event::fire('cjkpl.tiles.card.display', [&$this]);
    }
}
