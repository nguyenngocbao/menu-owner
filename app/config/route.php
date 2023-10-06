<?php

/**
 * @author
 */
use App\Controllers\IndexController;
use App\Controllers\AuthencController;
use App\Controllers\store\StoreController;
use App\Controllers\store\MenuController;
use App\Controllers\store\ItemController;

return [
    ['GET', '', [IndexController::class, 'index']],
    ['GET', '/auth', [IndexController::class, 'auth']],
    ['GET', '/', [IndexController::class, 'index']],
    ['POST', '/login', [IndexController::class, 'login']],
    ['GET', '/logout', [IndexController::class, 'logout']],

    ['GET', '/store', [StoreController::class, 'index']],
    ['GET', '/menu', [StoreController::class, 'index']],

    ['POST', '/menu/update', [MenuController::class, 'update']],
    ['POST', '/menu/delete', [MenuController::class, 'delete']],
    ['POST', '/menu/get', [MenuController::class, 'get']],

    ['POST', '/item/update', [ItemController::class, 'update']],
    ['POST', '/item/delete', [ItemController::class, 'delete']],
    ['POST', '/item/get', [ItemController::class, 'get']],
];
