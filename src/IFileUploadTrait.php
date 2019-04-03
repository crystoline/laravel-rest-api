<?php
/**
 * Created by PhpStorm.
 * User: cryst
 * Date: 6/7/2018
 * Time: 4:40 PM
 */

namespace Crystoline\LaraRestApi;

use Illuminate\Http\Request;

trait IFileUploadTrait
{

    /**
     * @param Request $request
     * @return string
     */
    public static function fileBasePath(Request $request){
        return '';
    }
}