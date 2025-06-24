<?php

namespace App\Models;

use App\Core\Model;
use DateTime;

class User extends Model
{
    protected int $id;
    protected string $username;
    protected string $password;
    protected string $registered_at;
    protected string $position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRegisteredAt(): string
    {
        return $this->registered_at;
    }

    public function setRegisteredAt(string $registered_at): void
    {
        $this->registered_at = $registered_at;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }
}