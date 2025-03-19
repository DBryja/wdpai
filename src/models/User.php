<?php

namespace models;

class User {
    private $id;
    private $email;
    private $passwordHash;
    private $role;

    public function __construct($email, $passwordHash, $role, $id=null) {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPasswordHash() {
        return $this->passwordHash;
    }

    public function getRole() {
        return $this->role;
    }
}