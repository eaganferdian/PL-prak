<div class="form-page">
    <div class="page-header">
        <h2>Edit Booking</h2>
        <p>Modify your chamber reservation</p>
    </div>

    <div class="parchment-form large">
        <form method="POST" action="?c=booking&a=update" class="royal-form">
            <input type="hidden" name="id" value="<?= $booking['id'] ?>">
            
            <div class="form-group">
                <label for="room_id" class="form-label">🏛️ Select Chamber</label>
                <select id="room_id" name="room_id" class="form-select" required>
                    <option value="">Choose a royal chamber...</option>
                    <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['id'] ?>" 
                            <?= $room['id'] == $booking['room_id'] ? 'selected' : '' ?>
                            data-rate="<?= $room['hourly_rate'] ?>">
                        <?= htmlspecialchars($room['name']) ?> - 
                        <?= number_format($room['hourly_rate'], 2) ?> gold/hour - 
                        Capacity: <?= $room['capacity'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="booking_date" class="form-label">📅 Date</label>
                    <input type="date" id="booking_date" name="booking_date" class="form-input" 
                           value="<?= htmlspecialchars($_POST['booking_date'] ?? $booking['booking_date']) ?>" 
                           min="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label for="start_time" class="form-label">🕐 Start Time</label>
                    <input type="time" id="start_time" name="start_time" class="form-input" 
                           value="<?= htmlspecialchars($_POST['start_time'] ?? date('H:i', strtotime($booking['start_time']))) ?>" required>
                </div>

                <div class="form-group">
                    <label for="end_time" class="form-label">🕔 End Time</label>
                    <input type="time" id="end_time" name="end_time" class="form-input" 
                           value="<?= htmlspecialchars($_POST['end_time'] ?? date('H:i', strtotime($booking['end_time']))) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="purpose" class="form-label">🎯 Purpose of Booking</label>
                <textarea id="purpose" name="purpose" class="form-textarea" rows="4" required><?= htmlspecialchars($_POST['purpose'] ?? $booking['purpose']) ?></textarea>
            </div>

            <div class="current-info royal-card">
                <h4>📋 Current Booking Details</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <span>Status:</span>
                        <span class="status-<?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span>
                    </div>
                    <div class="info-item">
                        <span>Current Cost:</span>
                        <span><?= number_format($booking['total_cost'], 2) ?> gold coins</span>
                    </div>
                    <div class="info-item">
                        <span>Created:</span>
                        <span><?= date('M j, Y', strtotime($booking['created_at'])) ?></span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-royal">💾 Update Booking</button>
                <a href="?c=booking&a=show&id=<?= $booking['id'] ?>" class="btn btn-secondary">↩️ Cancel</a>
            </div>
        </form>
    </div>
</div>