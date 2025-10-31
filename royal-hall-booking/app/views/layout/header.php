<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Royal Hall Booking') ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/medieval.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/calendar.css">
</head>
<body class="medieval-theme">
    <div class="page-wrapper">
        <!-- Royal Banner -->
        <header class="royal-header">
            <div class="container">
                <div class="header-content">
                    <div class="royal-brand">
                        <h1 class="royal-title">Royal Hall Booking</h1>
                    </div>
                    <p class="royal-subtitle">Manage Your Kingdom's Chambers</p>
                </div>
            </div>
        </header>

        <!-- Include Navigation -->
        <?php require __DIR__ . '/navigation.php'; ?>

        <!-- Flash Messages -->
        <?php if (!empty($flashes)): ?>
        <div class="container">
            <?php foreach ($flashes as $flash): ?>
            <div class="flash-message flash-<?= $flash['type'] ?>">
                <span class="flash-icon">
                    <?= $flash['type'] === 'success' ? '✅' : ($flash['type'] === 'error' ? '❌' : 'ℹ️') ?>
                </span>
                <?= htmlspecialchars($flash['message']) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="royal-main">
            <div class="container">