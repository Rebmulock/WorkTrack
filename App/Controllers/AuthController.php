<?php

namespace App\Controllers;

use App\Config\Configuration;
use App\Core\AControllerBase;
use App\Core\Responses\Response;
use App\Core\Responses\ViewResponse;
use App\Models\User;
use DateTime;
use Exception;
use JsonException;

/**
 * Class AuthController
 * Controller for authentication actions
 * @package App\Controllers
 */
class AuthController extends AControllerBase
{
    /**
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->redirect(Configuration::LOGIN_URL);
    }

    public function redirectIfLogged(bool $ifLogged): ?Response
    {
        if (($ifLogged && $this->app->getAuth()->isLogged()) || (!$ifLogged && !$this->app->getAuth()->isLogged()))
        {
            return $this->redirect($this->url("home.index"));
        }

        return null;
    }

    /**
     * Login a user
     * @return Response
     * @throws JsonException
     */
    public function login(): Response
    {
        $redirectUser = $this->redirectIfLogged(true);

        if ($redirectUser !== null)
        {
            return $redirectUser;
        }

        $formData = $this->request()->getRawBodyJSON();
        $error = '';

        if (!empty($formData)) {
            if ($formData->username === '') {
                $error = 'Pouzivatelske meno je povinne!';

            } elseif (!preg_match('/^[a-zA-Z0-9]{5,}$/', $formData->username)) {
                $error = 'Pouzivatelske meno je neplatne!';

            } else {
                $logged = $this->app->getAuth()->login($formData->username, $formData->password);

                if ($logged) {
                    $redirect = null;

                    switch (User::getOne($this->app->getAuth()->getLoggedUserId())->getPosition())
                    {
                        case 0:
                            $redirect = $this->url("user.profile");
                            break;

                        case 1:
                            $redirect = $this->url("admin.panel");
                            break;

                        default:
                            break;
                    }

                    return $this->json([
                        'success' => true,
                        'message' => 'Prihlásenie úspešné.',
                        'redirect' => $redirect,
                    ]);
                }

                $error = 'Nesprávny username alebo heslo';
            }

            return $this->json([
                'success' => false,
                'message' => 'Prihlásenie neúspešné.',
                'error' => $error,
            ]);
        }

        return $this->html();
    }

    /**
     * Logout a user
     * @return ViewResponse
     */
    public function logout(): Response
    {
        $redirectUser = $this->redirectIfLogged(false);

        if ($redirectUser !== null)
        {
            return $redirectUser;
        }

        $this->app->getAuth()->logout();

        return $this->html();
    }

    /**
     * @throws JsonException|Exception
     */
    public function register(): Response
    {
        $redirectUser =  $this->redirectIfLogged(true);

        if ($redirectUser !== null)
        {
            return $redirectUser;
        }

        $formData = $this->request()->getRawBodyJSON();

        $errors = [];

        $username = $formData->username ?? '';
        $password = $formData->password ?? '';
        $confirm_password = $formData->confirm_password ?? '';
        $registered_at = $formData->registered_at ?? '';
        $position = $formData->position ?? '';

        if ($username === '') {
            $errors[] = 'Pouzivatelske meno je povinne!';
        } elseif (!preg_match('/^[a-zA-Z0-9]{5,}$/', $username)) {
            $errors[] = 'Pouzivatelske meno je neplatne!';
        } else {
            if ($this->app->getAuth()->usernameExists($username)) {
                $errors[] = 'Pouzivatelske meno uz existuje!';
            }
        }

        if (strlen($password) < 8) {
            $errors[]= 'Heslo musi mat aspon 8 znakov!';
        }

        if ($password !== $confirm_password) {
            $errors[] = 'Hesla sa nezhoduju!';
        }

        if ($registered_at === '')
        {
            $errors[] = 'Datum registracie je povinny!';
        } else {
            try {
                $registered_at = DateTime::createFromFormat('j. n. Y H:i:s', $registered_at)->format('Y-m-d H:i:s') ?? '';
            } catch (\DateMalformedStringException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($position !== 0 && $position !== 1)
        {
            $errors[] = 'Neplatna pozicia!';
        }

        if (empty($errors))
        {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setRegisteredAt($registered_at);
            $user->setPosition($position);

            $user->save();

            $this->app->getAuth()->login($username, $password);

            return $this->json([
                'success' => true,
                'message' => 'Registrácia prebehla úspešne.',
                'redirect' => $this->url("user.profile")
            ]);

        } elseif (!empty($formData))
        {
            return $this->json(['success' => false, 'error' => $errors]);
        }

        return $this->html();
    }
}
