<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\Responses\Response;
use App\Core\IAuthenticator;

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

    public function getUser() : Response
    {
        $userContext = $this->app->getAuth()->getLoggedUserContext();

        if ($userContext)
        {
            return $this->json($userContext);
        }

        return $this->json(['error' => 'Pouzivatel nie je prihlaseny!']);
    }

    public function profile() : Response
    {
        return $this->html();
    }
}