<?php
/**
 * Created by PhpStorm.
 * User: mei
 * Date: 2018/3/30
 * Time: 16:06
 */
namespace Msx\BestSignSdk\Facades;

use Illuminate\Support\Facades\Facade;

class BestSignSdk extends Facade {
    protected static function getFacadeAccessor ()
    {
        return 'bestSignSdk';
    }
}