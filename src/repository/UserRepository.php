<?php

namespace repository;
use models\User;
use models\Role;

class UserRepository extends Repository
{
    public function getUser(string $email): ?User{
        $stmt = $this->db->connect()->prepare('SELECT * FROM public.users WHERE email = :email');
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($user == false){
//            TODO: throw exception
            return null;
        }
        return new User($user['email'], $user['password'], Role::from($user['role']));
    }

}