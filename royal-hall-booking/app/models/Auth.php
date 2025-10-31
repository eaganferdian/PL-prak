<?php
class Auth
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Login user
    public function login(string $username, string $password): ?array
    {
        $stmt = $this->db->prepare("SELECT id, username, email, password, full_name, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }
        }
        return null;
    }

    // Register new user
    public function register(array $data): bool
    {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bind_param("ssss", $data['username'], $data['email'], $password, $data['full_name']);
        return $stmt->execute();
    }

    // Check if username/email exists
    public function userExists(string $username, string $email): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}