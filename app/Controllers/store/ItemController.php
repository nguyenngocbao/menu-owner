<?php

namespace App\Controllers\store;

use App\Controllers\CrudController;

class ItemController extends CrudController
{
    public function url()
    {
        return "/item/";
    }

}