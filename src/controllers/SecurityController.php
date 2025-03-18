<?php
namespace controllers;
use repository\UserRepository;
class SecurityController extends AppController
{
    public function login(){
        $userRepository = new UserRepository();
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $userRepository->getUser($email);

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