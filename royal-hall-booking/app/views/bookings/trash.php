<div class="bookings-page">
    <div class="page-header">
        <h2>Royal Archives</h2>
        <p>View and manage archived chamber reservations</p>
    </div>

    <?php if (empty($trashedBookings)): ?>
        <div class="empty-state">
            <div class="empty-icon">📜</div>
            <p>The Royal Archives are empty</p>
            <a href="?c=booking&a=index" class="btn btn-royal">Return to Bookings</a>
        </div>
    <?php else: ?>
        <div class="bookings-table royal-card">
            <table class="royal-table">
                <thead>
                    <tr>
                        <th>Chamber</th>
                        <th>Date & Time</th>
                        <th>Purpose</th>
                        <th>Auto-Delete In</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trashedBookings as $booking): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($booking['room_name']) ?></strong>
                        </td>
                        <td>
                            <div>📅 <?= date('M j, Y', strtotime($booking['booking_date'])) ?></div>
                            <div>🕐 <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></div>
                        </td>
                        <td><?= htmlspecialchars($booking['purpose']) ?></td>
                        <td><?= $booking['days_until_deletion'] ?> days</td>
                        <td>
                            <div class="action-buttons-small">
                                <form action="?c=booking&a=restore" method="POST" class="inline-form">
                                    <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                                    <button type="submit" class="btn btn-small btn-success">Restore</button>
                                </form>
                                <form action="?c=booking&a=permanentDelete" method="POST" class="inline-form" 
                                      onsubmit="return confirm('Are you absolutely certain? This action cannot be undone!');">
                                    <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Delete Permanently</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>