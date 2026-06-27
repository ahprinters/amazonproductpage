<?php

class Auth
{
    private $conn;
    private $sessionKey = 'admin_user';

    public function __construct($connection)
    {
        $this->conn = $connection;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function attemptAdminLogin($email, $password)
    {
        $email = trim($email);
        $password = trim($password);

        if ($email === '' || $password === '') {
            throw new Exception("Email and password are required.");
        }

        $stmt = $this->conn->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            AND role = 'admin'
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            throw new Exception("Invalid admin credentials.");
        }

        if ($user['status'] !== 'active') {
            throw new Exception("This admin account is not active.");
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid admin credentials.");
        }

        $_SESSION[$this->sessionKey] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        return true;
    }

    public function admin()
    {
        return $_SESSION[$this->sessionKey] ?? null;
    }

    public function checkAdmin()
    {
        return isset($_SESSION[$this->sessionKey]);
    }

    public function requireAdmin()
    {
        if (!$this->checkAdmin()) {
            header("Location: login.php");
            exit;
        }
    }

    public function logout()
    {
        unset($_SESSION[$this->sessionKey]);
    }
}