<div class="booking-detail-page">
    <div class="page-header">
        <h2>Booking Details</h2>
        <p>Royal Chamber Reservation</p>
    </div>

    <div class="detail-layout">
        <div class="detail-main">
            <div class="booking-info royal-card">
                <div class="booking-header">
                    <h3><?= htmlspecialchars($booking['room_name']) ?></h3>
                    <span class="booking-status status-<?= $booking['status'] ?>">
                        <?= ucfirst($booking['status']) ?>
                    </span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">📅 Date:</span>
                        <span class="info-value"><?= date('F j, Y', strtotime($booking['booking_date'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">🕐 Time:</span>
                        <span class="info-value"><?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">💰 Total Cost:</span>
                        <span class="info-value"><?= number_format($booking['total_cost'], 2) ?> gold coins</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">👤 Booked By:</span>
                        <span class="info-value"><?= htmlspecialchars($booking['user_name']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">📍 Location:</span>
                        <span class="info-value"><?= htmlspecialchars($booking['location']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">👥 Capacity:</span>
                        <span class="info-value"><?= $booking['capacity'] ?> people</span>
                    </div>
                </div>

                <div class="purpose-section">
                    <h4>🎯 Purpose</h4>
                    <p><?= nl2br(htmlspecialchars($booking['purpose'])) ?></p>
                </div>

                <?php if (!empty($booking['admin_notes'])): ?>
                <div class="admin-notes-section">
                    <h4>📜 Royal Steward's Notes</h4>
                    <p><?= nl2br(htmlspecialchars($booking['admin_notes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-sidebar">
            <div class="action-widget royal-card">
                <h3>Booking Actions</h3>
                <div class="action-buttons-vertical">
                    <?php if ($booking['status'] === 'pending' && $booking['user_id'] == $_SESSION['user']['id']): ?>
                    <a href="?c=booking&a=edit&id=<?= $booking['id'] ?>" class="btn btn-secondary btn-block">✏️ Edit Booking</a>
                    <form method="POST" action="?c=booking&a=delete" onsubmit="return confirm('Cancel this booking?')">
                        <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-block">❌ Cancel</button>
                    </form>
                    <?php endif; ?>

                    <?php if ($isAdmin && $booking['status'] === 'pending'): ?>
                    <form method="POST" action="?c=booking&a=updateStatus" class="action-form">
                        <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                        <div class="form-group">
                            <label class="form-label">Admin Notes</label>
                            <textarea name="admin_notes" class="form-textarea" rows="3" placeholder="Optional notes..."><?= htmlspecialchars($booking['admin_notes'] ?? '') ?></textarea>
                        </div>
                        <div class="form-actions-vertical">
                            <button type="submit" name="status" value="approved" class="btn btn-success btn-block">✅ Approve</button>
                            <button type="submit" name="status" value="rejected" class="btn btn-danger btn-block">❌ Reject</button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <a href="?c=booking&a=index" class="btn btn-secondary btn-block">↩️ Back to List</a>
                </div>
            </div>

            <div class="info-widget royal-card">
                <h3>Booking Information</h3>
                <div class="widget-info">
                    <div class="widget-item">
                        <span class="widget-label">Created:</span>
                        <span class="widget-value"><?= date('M j, Y g:i A', strtotime($booking['created_at'])) ?></span>
                    </div>
                    <div class="widget-item">
                        <span class="widget-label">Booking ID:</span>
                        <span class="widget-value">#<?= $booking['id'] ?></span>
                    </div>
                    <div class="widget-item">
                        <span class="widget-label">Contact:</span>
                        <span class="widget-value"><?= htmlspecialchars($booking['user_email']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>