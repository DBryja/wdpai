<?php
namespace controllers;
use repository\UserRepository;
use utils\LoginSecurity;

class SecurityController extends AppController
{
    public function adminLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userRepository = new UserRepository();
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;

            if ($email === null || $password === null) {
                error_log("Email or password missing");
                return $this->render("admin-login", ['messages' => ['Email and password are required']]);
            }

            $user = $userRepository->getUser($email);

            if ($user === null || $user->getPasswordHash() !== $password) {
                error_log("Invalid email or password");
                return $this->render("admin-login", ['messages' => ['Invalid email or password']]);
            }

            LoginSecurity::setLoginSession($user->getId());
            error_log("Login successful, redirecting to /admin");
            header("Location: /admin");
            exit();
        } else {
            return $this->render("admin-login");
        }
    }

    public function logout() {
        LoginSecurity::logout();
    }
}