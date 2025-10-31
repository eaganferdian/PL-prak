<div class="dashboard">
    <!-- Welcome Section -->
    <div class="welcome-section section">
        <div class="royal-card card-md text-center">
            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['user']['full_name']) ?>!</h2>
            <p class="text-muted">Kelola peminjaman ruangan kerajaan Anda dengan mudah</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-section section">
        <h3>Ringkasan Hari Ini</h3>
        <div class="grid grid-4">
            <div class="stat-card">
                <div class="stat-icon">🏛️</div>
                <div class="stat-info">
                    <h3><?= $stats['totalRooms'] ?? 0 ?></h3>
                    <p>Ruangan Tersedia</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-info">
                    <h3><?= $stats['todayBookings'] ?? 0 ?></h3>
                    <p>Booking Hari Ini</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-info">
                    <h3><?= $stats['pendingBookings'] ?? 0 ?></h3>
                    <p>Menunggu Persetujuan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <h3><?= $stats['approvedBookings'] ?? 0 ?></h3>
                    <p>Disetujui</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions section">
        <h3>Akses Cepat</h3>
        <div class="grid grid-4">
            <a href="?c=room&a=availability" class="action-card">
                <div class="action-icon">🔍</div>
                <span>Cek Ketersediaan</span>
                <small>Lihat jadwal kosong</small>
            </a>
            <a href="?c=booking&a=create" class="action-card">
                <div class="action-icon">➕</div>
                <span>Booking Baru</span>
                <small>Pesan ruangan</small>
            </a>
            <a href="?c=booking&a=calendar" class="action-card">
                <div class="action-icon">📆</div>
                <span>View Calendar</span>
                <small>Lihat jadwal</small>
            </a>
            <?php if ($isAdmin): ?>
            <a href="?c=room&a=create" class="action-card">
                <div class="action-icon">⚙️</div>
                <span>Kelola Ruangan</span>
                <small>Admin only</small>
            </a>
            <?php else: ?>
            <a href="?c=booking" class="action-card">
                <div class="action-icon">📋</div>
                <span>Booking Saya</span>
                <small>Lihat history</small>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="activity-section section">
        <div class="royal-card card-md">
            <div class="card-header">
                <h3>Aktivitas Terbaru</h3>
                <a href="?c=booking" class="royal-link">Lihat Semua →</a>
            </div>
            <?php if (empty($recentBookings)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📝</div>
                    <p>Belum ada aktivitas booking</p>
                    <a href="?c=booking&a=create" class="btn btn-primary">Buat Booking Pertama</a>
                </div>
            <?php else: ?>
                <div class="activity-list">
                    <?php foreach ($recentBookings as $booking): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <?= $booking['status'] === 'approved' ? '✅' : 
                               ($booking['status'] === 'pending' ? '⏳' : '📅') ?>
                        </div>
                        <div class="activity-content">
                            <strong><?= htmlspecialchars($booking['room_name']) ?></strong>
                            <span><?= date('d M Y', strtotime($booking['booking_date'])) ?> • 
                                  <?= date('H:i', strtotime($booking['start_time'])) ?>-<?= date('H:i', strtotime($booking['end_time'])) ?></span>
                        </div>
                        <div class="activity-status status-<?= $booking['status'] ?>">
                            <?= ucfirst($booking['status']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>