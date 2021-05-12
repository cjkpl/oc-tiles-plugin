<?php namespace Cjkpl\Tiles\Components;

use Cms\Classes\ComponentBase;
use Cjkpl\Tiles\Classes\CardMaker;
use Event;

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
        return [
            'useIds' => [
                'title'       => 'Use card IDs',
                'description' => 'Allow Card IDs, not only slugs',
                'type'        => 'checkbox',
                'default'     => 1
            ],
        ];
    }

    public function onRun()
    {

        // either slug, or id
        $slugId = $this->param('slug');

        // check the param - if it is a positive integer, we have ID not slug
        $paramIsId = ($this->property('useIds')
                        && (is_int($slugId) || ctype_digit($slugId))
                        && (int)$slugId>0);

        if ($paramIsId && $this->property('useIds')) {
            $this->card = CardMaker::getCardById($slugId);
        } else { // param is slug
            $this->card = CardMaker::getCardBySlug($slugId);
        }
        // notify extending plugins (e.g. SQLTiles) of the retrieved card contents
        Event::fire('cjkpl.tiles.card.display', [&$this->card]);

        $this->prepareVars();
    }

    protected function prepareVars()
    {
        $this->REF = $_SERVER['HTTP_REFERER'] ?? '/';
    }
}
