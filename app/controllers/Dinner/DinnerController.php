<?php

namespace App\Controllers\Dinner;

use App\Controllers\BaseController;
use FintechFab\Models\DinnerMenuItem;
use FintechFab\Models\DinnerMenuSection;

class DinnerController extends BaseController
{

    public $layout = 'dinner';

    public function getDinner()
    {

	    // Формируем данные для заказа
	    $sections = DinnerMenuSection::all();
	    $menu = DinnerMenuItem::all();

        return $this->make('dinner' , array('sections' => $sections, 'menu' => $menu,));
    }

}