<?php namespace Cjkpl\Tiles\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Sections extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = ['cjkpl.tiles.sections'];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Cjkpl.Tiles', 'tiles-menu-item', 'sections-menu-item');
    }
}
