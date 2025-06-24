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

    /**
     * Login a user
     * @return Response
     */
    public function login(): Response
    {
        $formData = $this->app->getRequest()->getPost();
        $logged = null;
        if (isset($formData['submit'])) {
            $logged = $this->app->getAuth()->login($formData['login'], $formData['password']);
            if ($logged) {
                return $this->redirect($this->url("admin.index"));
            }
        }

        $data = ($logged === false ? ['message' => 'Zlý login alebo heslo!'] : []);
        return $this->html($data);
    }

    /**
     * Logout a user
     * @return ViewResponse
     */
    public function logout(): Response
    {
        $this->app->getAuth()->logout();
        return $this->html();
    }

    /**
     * @throws JsonException|Exception
     */
    public function register(): Response
    {
        $rawBody = file_get_contents('php://input');

        if (empty($rawBody)) {
            $formData = [];
        } else {
            try {
                $formData = json_decode($rawBody, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                return $this->json(['success' => false, 'errors' => ['Neplatné JSON dáta!']]);
            }
        }

        $formData = (array)$formData;

        $errors = [];

        $username = $formData['username'] ?? '';
        $password = $formData['password'] ?? '';
        $confirm_password = $formData['confirm_password'] ?? '';
        $registered_at = $formData['registered_at'] ?? '';
        $position = $formData['position'] ?? '';

        if ($username === '') {
            $errors[] = 'Pouzivatelske meno je povinne!';
        } elseif (!preg_match('/^[a-zA-Z0-9]{5,}$/', $username)) {
            $errors[] = 'Pouzivatelske meno je neplatne!';
        } else {
            $existingUser = User::getAll('`username` = ?', [$username]);

            if ($existingUser) {
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

        if (empty($errors)) {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setRegisteredAt($registered_at);
            $user->setPosition($position);

            $user->save();

            $this->app->getAuth()->login($username, $password);
            return $this->json(['success' => true, 'message' => 'Registrácia prebehla úspešne.']);
        } elseif (!empty($rawBody))
        {
            return $this->json(['success' => false, 'errors' => $errors]);
        }

        return $this->html($errors);
    }
}
