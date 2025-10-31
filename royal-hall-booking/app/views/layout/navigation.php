<?php
// Navigation data
$navItems = [
    'dashboard' => ['icon' => '', 'label' => 'Dashboard', 'url' => '?c=dashboard'],
    'room' => ['icon' => '', 'label' => 'Royal Chambers', 'url' => '?c=room'],
    'availability' => ['icon' => '', 'label' => 'Check Availability', 'url' => '?c=room&a=availability'],
    'booking' => ['icon' => '', 'label' => 'Bookings', 'url' => '?c=booking'],
    'calendar' => ['icon' => '', 'label' => 'Calendar', 'url' => '?c=booking&a=calendar'],
    'trash' => ['icon' => '📜', 'label' => 'Archives', 'url' => '?c=booking&a=trash']
];

// Admin only items
if (isset($currentUser) && $currentUser['role'] === 'admin') {
    $navItems['facility'] = ['icon' => '⚙️', 'label' => 'Facilities', 'url' => '?c=facility'];
}
?>

<nav class="royal-nav">
    <div class="container">
        <ul class="nav-menu">
            <?php foreach ($navItems as $key => $item): ?>
            <li>
                <a href="<?= $item['url'] ?>" 
                   class="nav-link <?= $controller === $key ? 'active' : '' ?>">
                    <?= $item['icon'] ?> <?= $item['label'] ?>
                </a>
            </li>
            <?php endforeach; ?>
            
            <?php if (isset($currentUser)): ?>
            <li class="nav-user">
                <span class="user-greeting">Hai, <?= htmlspecialchars($currentUser['full_name']) ?>!</span>
                <a href="?c=auth&a=logout" class="nav-link logout">⚔️ Logout</a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>