<div class="bookings-page">
    <div class="page-header">
        <h2>Royal Bookings</h2>
        <p>Manage your chamber reservations</p>
    </div>

    <!-- Filters and Actions -->
    <div class="page-actions">
        <div class="filters">
            <form method="GET" class="filter-form">
                <input type="hidden" name="c" value="booking">
                <input type="hidden" name="a" value="index">
                
                <div class="search-box">
                    <input type="text" name="q" value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Search by purpose or room..." class="search-input">
                    <button type="submit" class="search-button">🔍</button>
                </div>

                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                    <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>✅ Approved</option>
                    <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>❌ Rejected</option>
                    <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>📋 Completed</option>
                </select>

                <select name="sort" class="filter-select" onchange="this.form.submit()">
                    <option value="">Sort By</option>
                    <option value="date_asc" <?= ($sort ?? '') === 'date_asc' ? 'selected' : '' ?>>Date (Oldest First)</option>
                    <option value="date_desc" <?= ($sort ?? '') === 'date_desc' ? 'selected' : '' ?>>Date (Newest First)</option>
                    <option value="cost_asc" <?= ($sort ?? '') === 'cost_asc' ? 'selected' : '' ?>>Cost (Low to High)</option>
                    <option value="cost_desc" <?= ($sort ?? '') === 'cost_desc' ? 'selected' : '' ?>>Cost (High to Low)</option>
                </select>
            </form>
        </div>
        
        <div class="action-buttons">
            <a href="?c=booking&a=create" class="btn btn-royal">➕ New Booking</a>
            <a href="?c=booking&a=calendar" class="btn btn-secondary">🗓️ Calendar View</a>
        </div>
    </div>

    <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <div class="empty-icon">📖</div>
            <p>No bookings found</p>
            <a href="?c=booking&a=create" class="btn btn-royal">Make Your First Booking</a>
        </div>
    <?php else: ?>
        <!-- Bookings Table -->
        <div class="bookings-table royal-card">
            <table class="royal-table">
                <thead>
                    <tr>
                        <th>Chamber</th>
                        <th>Date & Time</th>
                        <th>Purpose</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($booking['room_name']) ?></strong>
                        </td>
                        <td>
                            <div>📅 <?= date('M j, Y', strtotime($booking['booking_date'])) ?></div>
                            <div>🕐 <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></div>
                        </td>
                        <td><?= htmlspecialchars($booking['purpose']) ?></td>
                        <td>💰 <?= number_format($booking['total_cost'], 2) ?></td>
                        <td>
                            <span class="booking-status status-<?= $booking['status'] ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons-small">
                                <a href="?c=booking&a=show&id=<?= $booking['id'] ?>" class="btn btn-small">View</a>
                                <?php if ($booking['status'] === 'pending' && $booking['user_id'] == $_SESSION['user']['id']): ?>
                                <a href="?c=booking&a=edit&id=<?= $booking['id'] ?>" class="btn btn-small btn-secondary">Edit</a>
                                <?php endif; ?>
                                <?php if ($isAdmin && $booking['status'] === 'pending'): ?>
                                <form method="POST" action="?c=booking&a=updateStatus" class="inline-form">
                                    <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-small btn-success">Approve</button>
                                </form>
                                <form method="POST" action="?c=booking&a=updateStatus" class="inline-form">
                                    <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-small btn-danger">Reject</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?c=booking&a=index&page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>" 
               class="pagination-link <?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>