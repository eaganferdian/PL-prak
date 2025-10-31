<?php
class Controller
{
    // Render view dengan layout
    public function view(string $template, array $data = []): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        extract($data, EXTR_SKIP);
        $flashes = $this->takeFlashes();
        $currentUser = $_SESSION['user'] ?? null;
        $baseUrl = APP_URL;

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/' . $template . '.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    // Redirect helper
    public function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    // Set flash message
    public function flash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flashes'][] = ['type' => $type, 'message' => $message];
    }

    // Ambil flash messages
    public function takeFlashes(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $flashes = $_SESSION['flashes'] ?? [];
        unset($_SESSION['flashes']);
        return $flashes;
    }

    // Check if user is logged in
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->flash('error', 'You must be logged in to access this page');
            $this->redirect('?c=auth&a=login');
        }
    }

    // Check if user is admin
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if ($_SESSION['user']['role'] !== 'admin') {
            $this->flash('error', 'Admin access required');
            $this->redirect('?c=dashboard');
        }
    }
}