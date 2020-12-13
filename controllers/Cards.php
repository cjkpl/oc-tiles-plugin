<?php namespace Cjkpl\Tiles\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Cards extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public $requiredPermissions = ['cjkpl.tiles.cards'];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Cjkpl.Tiles', 'tiles-menu-item', 'cards-menu-item');
    }

    public function onDuplicate() {
        $checked_items_ids = input('checked');

        foreach ($checked_items_ids as $id) {
            $original = \Cjkpl\Tiles\Models\Card::where("id", $id)->first();

            $clone = $original->replicate();
            $clone->title = "COPY ".$clone->title;
            // we do not use slugs here $clone->slug = now()->timestamp."_".$clone->slug;
            $clone->is_visible = 0;
            $clone->id = \Cjkpl\Tiles\Models\Card::max('id') + 1;
            $clone->save();
        }

        \Flash::success('Card cloned');
        return $this->listRefresh();
    }
}
