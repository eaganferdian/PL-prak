<div class="form-page">
    <div class="page-header">
        <h2>Book Royal Chamber</h2>
        <p>Reserve a hall for your royal event</p>
    </div>

    <div class="parchment-form large">
        <form method="POST" action="?c=booking&a=store" class="royal-form">
            <div class="form-group">
                <label for="room_id" class="form-label">🏛️ Select Chamber</label>
                <select id="room_id" name="room_id" class="form-select" required>
                    <option value="">Choose a royal chamber...</option>
                    <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['id'] ?>" 
                            <?= (isset($room) && $room['id'] == ($_POST['room_id'] ?? $room['id'] ?? '')) ? 'selected' : '' ?>
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
                           value="<?= htmlspecialchars($_POST['booking_date'] ?? $date) ?>" 
                           min="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label for="start_time" class="form-label">🕐 Start Time</label>
                    <input type="time" id="start_time" name="start_time" class="form-input" 
                           value="<?= htmlspecialchars($_POST['start_time'] ?? $startTime) ?>" required>
                </div>

                <div class="form-group">
                    <label for="end_time" class="form-label">🕔 End Time</label>
                    <input type="time" id="end_time" name="end_time" class="form-input" 
                           value="<?= htmlspecialchars($_POST['end_time'] ?? $endTime) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="purpose" class="form-label">🎯 Purpose of Booking</label>
                <textarea id="purpose" name="purpose" class="form-textarea" rows="4" 
                          placeholder="Describe the purpose of your event..." required><?= htmlspecialchars($_POST['purpose'] ?? '') ?></textarea>
            </div>

            <div class="cost-estimate royal-card">
                <h4>💰 Cost Estimate</h4>
                <div class="estimate-details">
                    <div class="estimate-item">
                        <span>Selected Chamber:</span>
                        <span id="chamber-name">-</span>
                    </div>
                    <div class="estimate-item">
                        <span>Hourly Rate:</span>
                        <span id="hourly-rate">- gold/hour</span>
                    </div>
                    <div class="estimate-item">
                        <span>Duration:</span>
                        <span id="duration">- hours</span>
                    </div>
                    <div class="estimate-item total">
                        <span>Estimated Total:</span>
                        <span id="total-cost">- gold coins</span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-royal">📖 Submit Booking</button>
                <a href="?c=room&a=availability" class="btn btn-secondary">📅 Check Availability First</a>
                <a href="?c=booking&a=index" class="btn btn-secondary">↩️ Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// Simple cost calculation without external JS
document.addEventListener('DOMContentLoaded', function() {
    function calculateCost() {
        const roomSelect = document.getElementById('room_id');
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        
        if (roomSelect.value && startTime && endTime) {
            const hourlyRate = roomSelect.selectedOptions[0].getAttribute('data-rate');
            const start = new Date('2000-01-01 ' + startTime);
            const end = new Date('2000-01-01 ' + endTime);
            const hours = (end - start) / (1000 * 60 * 60);
            
            if (hours > 0) {
                document.getElementById('chamber-name').textContent = roomSelect.selectedOptions[0].text.split(' - ')[0];
                document.getElementById('hourly-rate').textContent = hourlyRate + ' gold/hour';
                document.getElementById('duration').textContent = hours.toFixed(1) + ' hours';
                document.getElementById('total-cost').textContent = (hourlyRate * hours).toFixed(2) + ' gold coins';
                return;
            }
        }
        
        // Reset if invalid
        document.getElementById('chamber-name').textContent = '-';
        document.getElementById('hourly-rate').textContent = '- gold/hour';
        document.getElementById('duration').textContent = '- hours';
        document.getElementById('total-cost').textContent = '- gold coins';
    }
    
    // Add event listeners
    document.getElementById('room_id').addEventListener('change', calculateCost);
    document.getElementById('start_time').addEventListener('change', calculateCost);
    document.getElementById('end_time').addEventListener('change', calculateCost);
    
    // Initial calculation
    calculateCost();
});
</script>