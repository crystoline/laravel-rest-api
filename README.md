<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

# laravel-rest-api
[![Build Status](https://scrutinizer-ci.com/g/crystoline/laravel-rest-api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/crystoline/laravel-rest-api/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/crystoline/laravel-rest-api/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Code Quality](https://scrutinizer-ci.com/g/crystoline/laravel-rest-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/crystoline/laravel-rest-api/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/crystoline/laravel-rest-api.svg?style=flat-square)](https://packagist.org/packages/crystoline/laravel-rest-api)
[![Total Downloads](https://img.shields.io/packagist/dt/crystoline/laravel-rest-api.svg?style=flat-square)](https://packagist.org/packages/crystoline/laravel-rest-api)
[![License](https://img.shields.io/packagist/l/crystoline/laravel-rest-api.svg?style=flat-square)](https://packagist.org/packages/crystoline/laravel-rest-api)

##Description
Provide rest apis in Laravel in easy steps. Avoid Repeated crud/Bread Process. This helps you to focus on the business logic

[![Say Thanks!](https://img.shields.io/badge/Say%20Thanks-!-1EAEDB.svg)](https://saythanks.io/to/crystoline)

##Installation 

```
composer require crystoline/laravel-rest-api
```

##Usage


#### Controller Sample
```php
<?php

namespace App\Http\Controllers\Api\v1\Admin;

use Crystoline\LaraRestApi\RestApiTrait;
use Crystoline\LaraRestApi\IRestApiAble;
use Crystoline\LaraRestApi\TestModel;

use App\Http\Controllers\Controller;

class TestController extends Controller implements IRestApiAble
{
    use RestApiTrait;
    
    /**
    * Define the Eloquent Model.
    * This line is required.   
    */
    public static function getModel() : string  {
        return TestModel::class;
    }
    
    
    public static function getValidationRules(): array
    {
        return [
            "name" => "required",
            "email" => "required|email",
            "password" => "required|password_confirmed"
        ];
    }


}
```

#### Route Sample

```php
<?php

    Route::prefix('users')->group( function () {
    Route::get('', 'TestController@index');
    Route::get('show/{id}', 'TestController@show');
    Route::post('store', 'TestController@store');
    Route::put('update/{id}', 'TestController@update');
    Route::put('delete/{id}', 'TestController@destroy');

});
```