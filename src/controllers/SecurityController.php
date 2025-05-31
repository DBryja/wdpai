<?php
namespace controllers;
use models\Role;
use repository\UserRepository;
use utils\LoginSecurity;

class SecurityController extends AppController
{
    public function adminLogin()
    {
        if (!$this->isPost()) {
            return $this->render("admin-login");
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        $userRepository = new UserRepository();

        // Check if there are any existing admin users
        if ($userRepository->countAdmins() === 0) {
            // No admin exists, create a new admin user
            $userRepository->create($email, $password, ROLE::ADMIN->value);

            $this->messages[] = "Admin user created successfully. Please log in.";
            return $this->render("admin-login", ['messages' => $this->messages]);
        }

        if ($userRepository->comparePassword($email, $password)) {
            $user = $userRepository->getUser($email);

            $token = uniqid(); // Generate a unique session token
            $userRepository->saveSessionToken($user->getId(), $token);

            setcookie('adminEmail', $email, time() + (86400 * 30), "/");
            setcookie('sessionToken', $token, time() + (86400 * 30), "/");

            LoginSecurity::setLoginSession($user->getId());
            header("Location: /admin/dashboard");
        } else {
            $this->messages[] = "Invalid email or password.";
            return $this->render("admin-login", ['messages' => $this->messages]);
        }

        header("Location: /admin");
        return null;
    }

    public function logout() {
        LoginSecurity::logout();
    }
}