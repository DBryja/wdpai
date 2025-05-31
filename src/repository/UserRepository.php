<?php

namespace repository;
use models\User;
use models\Role;

class UserRepository extends Repository
{
    public function create($email, $password, $role)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->connect()->prepare('INSERT INTO public.users (email, password, role) VALUES (:email, :password, :role)');
        $stmt->execute([':email' => $email, ':password' => $hashedPassword, ':role' => $role]);
    }

    public function findAll(){
        $stmt = $this->db->connect()->prepare('SELECT * FROM public.users');
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getUser(string $email): ?User {
        $stmt = $this->db->connect()->prepare('SELECT * FROM public.users WHERE email = :email');
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);

        try {
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($user === false) {
                return null;
            }
            return new User(
                $user['email'],
                $user['password'],
                Role::from($user['role']),
                $user["id"]
            );
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function comparePassword($email, $password) {
        $stmt = $this->db->connect()->prepare('SELECT password FROM public.users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $hash = $stmt->fetchColumn();

        error_log("Password: " . $password);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        error_log("hashedPassword: " . $hashedPassword);
        error_log("Hash: " . $hash);
        
        return password_verify($password, $hash);
    }

    public function saveSessionToken($userId, $token) {
        $query = "UPDATE users SET session_token = :token WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':token' => $token, ':id' => $userId]);
    }

    public function getSessionToken($userId) {
        $query = "SELECT session_token FROM users WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchColumn();
    }

    public function getAllAdmins()
    {
        $query = "SELECT * FROM users WHERE role = :role";
        $stmt = $this->db->connect()->prepare($query);
        $roleName = Role::ADMIN->value;
        $stmt->execute([':role' => $roleName]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->db->connect()->prepare('DELETE FROM public.users WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function countAdmins()
    {
        $stmt = $this->db->connect()->prepare('SELECT COUNT(*) FROM public.users WHERE role = :role');
        $roleName = Role::ADMIN->value;
        $stmt->execute([':role' => $roleName]);
        return $stmt->fetchColumn();
    }
}