<?php
namespace utils;

use JetBrains\PhpStorm\NoReturn;

class LoginSecurity
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            error_log("Session started");
        } else {
            error_log("Session already active");
        }
    }

    public static function setLoginSession($userId): void
    {
        self::startSession();
        $_SESSION['user_id'] = $userId;
        error_log("Session set for user_id: " . $userId);
    }

    public static function checkAdminAccess(): void
    {
        self::startSession();
        $isLoggedIn = isset($_SESSION['user_id']);
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        error_log("Checking admin access for path: " . $currentPath);
        error_log("User logged in: " . ($isLoggedIn ? "Yes" : "No"));

        if (str_starts_with($currentPath, '/admin') && !$isLoggedIn && $currentPath !== '/adminLogin') {
            error_log("Redirecting to /adminLogin");
            header('Location: /adminLogin');
            exit();
        }
    }

    #[NoReturn] public static function logout(): void {
        self::startSession();
        session_unset();
        session_destroy();
        error_log("Session destroyed, user logged out");
        header('Location: /');
        exit();
    }
}