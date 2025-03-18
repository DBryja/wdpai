<?php

namespace models;

enum Role: string{
    case ADMIN = 'admin';
    case USER = 'user';
    case GUEST = 'guest';
}
class User
{
    private $email;
    private $password;
    private $role;

    public function __construct(string $email, string $password, Role $role)
    {
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

}