<?php

namespace Crystoline\LaraRestApi;

use Illuminate\Http\Request;

interface ISchoolFileUpload
{
    /**
     * return the base path for file upload
     * @param Request $request
     * @return string
     */
    public static function fileBasePath(Request $request);
}