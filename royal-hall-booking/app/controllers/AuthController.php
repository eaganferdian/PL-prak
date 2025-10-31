<?php
class AuthController extends Controller
{
    private Auth $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    // Show login form
    public function login(): void
    {
        if (isset($_SESSION['user'])) {
            $this->redirect('?c=dashboard');
            return;
        }

        $this->view('auth/login', ['title' => 'Royal Login']);
    }

    // Process login
    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=auth&a=login');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->flash('error', 'Please fill all fields');
            $this->redirect('?c=auth&a=login');
            return;
        }

        $user = $this->auth->login($username, $password);

        if ($user) {
            $_SESSION['user'] = $user;
            $this->flash('success', 'Welcome to Royal Hall Booking!');
            $this->redirect('?c=dashboard');
        } else {
            $this->flash('error', 'Invalid credentials');
            $this->redirect('?c=auth&a=login');
        }
    }

    // Show register form
    public function register(): void
    {
        if (isset($_SESSION['user'])) {
            $this->redirect('?c=dashboard');
            return;
        }

        $this->view('auth/register', ['title' => 'Royal Registration']);
    }

    // Process registration
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=auth&a=register');
            return;
        }

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'full_name' => trim($_POST['full_name'] ?? '')
        ];

        // Validation
        $errors = [];
        if (empty($data['username'])) $errors[] = 'Username is required';
        if (empty($data['email'])) $errors[] = 'Email is required';
        if (empty($data['password'])) $errors[] = 'Password is required';
        if (empty($data['full_name'])) $errors[] = 'Full name is required';
        if (strlen($data['password']) < 6) $errors[] = 'Password must be at least 6 characters';

        if ($this->auth->userExists($data['username'], $data['email'])) {
            $errors[] = 'Username or email already exists';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect('?c=auth&a=register');
            return;
        }

        if ($this->auth->register($data)) {
            $this->flash('success', 'Registration successful! Please login.');
            $this->redirect('?c=auth&a=login');
        } else {
            $this->flash('error', 'Registration failed. Please try again.');
            $this->redirect('?c=auth&a=register');
        }
    }

    // Logout
    public function logout(): void
    {
        session_destroy();
        $this->flash('success', 'You have been logged out');
        $this->redirect('?c=auth&a=login');
    }
}