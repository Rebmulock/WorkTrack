<?php

namespace App\Auth;

use App\Core\IAuthenticator;
use App\Models\User;
use Exception;

/**
 * Class DBAuthenticator
 * Basic implementation of user authentication
 * @package App\Auth
 */
class DBAuthenticator implements IAuthenticator
{
    /**
     * DBAuthenticator constructor
     */
    public function __construct()
    {
        session_start();
    }

    /**
     * Verify, if the user is in DB and has his password is correct
     * @param $login
     * @param $password
     * @return bool
     * @throws Exception
     */
    public function login($login, $password): bool
    {
        if ($this->usernameExists($login)) {
            $user = User::getAll('`username` = ?', [$login])[0];
            $real_password = $user->getPassword();
            $user_id = $user->getId();
            $position = $user->getPosition();

            if (password_verify($password, $real_password)) {
                $_SESSION['user'] = $login;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['position'] = $position;
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Logout the user
     */
    public function logout(): void
    {
        if (isset($_SESSION["user"])) {
            unset($_SESSION["user"]);
            session_destroy();
        }
    }

    /**
     * @throws Exception
     */
    public function usernameExists(string $username): bool
    {
        return User::getAll('`username` = ?', [$username]) != null;
    }

    /**
     * Get the name of the logged-in user
     * @return string
     * @throws Exception
     */
    public function getLoggedUserName(): string
    {
        return $_SESSION['user'] ?? throw new Exception("User not logged in");
    }

    /**
     * Get the context of the logged-in user
     * @return string
     */
    public function getLoggedUserContext(): mixed
    {
        if ($this->isLogged())
        {
            return [
                'username' => $_SESSION['user'],
                'id' => $_SESSION['user_id'],
                'position' => $_SESSION['position']
            ];
        }

        return null;
    }

    /**
     * Return if the user is authenticated or not
     * @return bool
     */
    public function isLogged(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user'] != null;
    }

    /**
     * Return the id of the logged-in user
     * @return mixed
     */
    public function getLoggedUserId(): mixed
    {
        return $_SESSION['user_id'] ?? null;
    }
}
