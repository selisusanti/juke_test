<?php

namespace App\Services;


use App\Services\ApiHandler;
use App\Services\Implemen\TestServiceImpl;


class TestService implements TestServiceImpl{
    public function __construct() {
    }

    public function coba(){
        return ApiHandler::requestWithoutAccessToken("GET","/todos");
    }
}
