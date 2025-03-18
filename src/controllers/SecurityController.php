<?php
namespace controllers;
use models\User;
use models\Role;
class SecurityController extends AppController
{
    public function login(){
        $user = new User(
            "test@test.com",
            "test123",
            Role::ADMIN
        );
        $email = $_POST['email'];
        $password = $_POST['password'];

        if($user->getEmail() != $email){
            return $this->render("login", ['messages' => ['Wrong email']]);
        }

        if ($user->getPassword() != $password){
            return $this->render("login", ['messages' => ['Wrong password']]);
        }

//        return $this->render("dashboard");
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/dashboard");
        return null;
    }
}