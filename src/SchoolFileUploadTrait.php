<?php
/**
 * Created by PhpStorm.
 * User: cryst
 * Date: 6/7/2018
 * Time: 4:40 PM
 */

namespace Crystoline\LaraRestApi;

use Illuminate\Http\Request;

trait SchoolFileUploadTrait
{

    public static function fileBasePath(Request $request){
        $conf = session('client.configuration');
        //dd($conf->subdomain);

        if($conf and !empty($conf->subdomain)){
            return "school/{$conf->subdomain}";
        }

        return '';
    }
}