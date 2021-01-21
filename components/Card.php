<?php namespace Cjkpl\Tiles\Components;

use Cms\Classes\ComponentBase;
use Cjkpl\Tiles\Classes\CardMaker;

class Card extends ComponentBase
{

    /**
     * @var \Cjkpl\Tiles\Models\Card the Card model to display
     */
    public $card;

    /**
     * @var string $REF last visited page
     */
    public $REF;

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
        $this->card = CardMaker::getCard($this->param('id'));

        $this->prepareVars();
    }

    protected function prepareVars()
    {
        $this->REF = $_SERVER['HTTP_REFERER'] ?? '/';
    }
}
