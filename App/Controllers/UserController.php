<?php

namespace App\Controllers;
use App\Core\AControllerBase;
use App\Core\Responses\Response;

class UserController extends AControllerBase
{
    public function authorize(string $action)
    {
        return $this->app->getAuth()->isLogged();
    }

    public function index() : Response
    {
        return $this->html();
    }

    public function profile() : Response
    {
        return $this->html();
    }
}