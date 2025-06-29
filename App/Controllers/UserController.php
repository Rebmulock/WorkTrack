<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\Responses\Response;
use App\Models\User;
use Exception;
use JsonException;

class UserController extends AControllerBase
{
    public function authorize(string $action): bool
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

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function profile() : Response
    {
        $formData = $this->request()->getRawBodyJSON();
        $error = '';

        if (!empty($formData) && $this->app->getAuth()->isLogged())
        {
            if (isset($formData->username))
            {
                if ($formData->username === '')
                {
                    $error = 'Pouzivatelske meno nesmie byt prazdne';

                } elseif (!preg_match('/^[a-zA-Z0-9]{5,}$/', $formData->username)) {
                    $error = 'Pouzivatelske meno je neplatne!';

                } else {
                    if ($formData->username === $this->app->getAuth()->getLoggedUserName())
                    {
                        $error = 'Nove pouzivatelske meno je zhodne so starym!';

                    } elseif (User::getAll('`username` = ?', [$formData->username]) != null) {
                        $error = 'Pouzivatelske meno uz existuje!';
                    } else {
                        $user = User::getOne($this->app->getAuth()->getLoggedUserId());
                        $user->setUsername($formData->username);
                        $user->save();

                        return $this->json([
                            'success' => true,
                            'message' => 'Zmena username úspešná.',
                        ]);
                    }
                }

                return $this->json([
                    'success' => false,
                    'error' => $error,
                ]);
            }

            if (isset($formData->new_password))
            {
                if (!isset($formData->confirm_password) || !isset($formData->password))
                {
                    $error = 'Nevyplnene overovacie alebo aktualne heslo!';

                } elseif ($formData->new_password !== $formData->confirm_password)
                {
                    $error = 'Hesla sa nezhoduju!';
                } else
                {
                    $user = User::getOne($this->app->getAuth()->getLoggedUserId());
                    $real_password = $user->getPassword();

                    if (password_verify($formData->password, $real_password))
                    {
                        $user->setPassword(password_hash($formData->new_password, PASSWORD_DEFAULT));
                        $user->save();

                        return $this->json([
                            'success' => true,
                            'message' => 'Zmena hesla úspešná.',
                        ]);

                    } else
                    {
                        $error = 'Aktualne heslo je nespravne!';
                    }
                }

                return $this->json([
                    'success' => false,
                    'error' => $error,
                ]);
            }
        }

        return $this->html();
    }
}