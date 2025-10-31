<div class="availability-page">
    <div class="page-header">
        <h2>Check Chamber Availability</h2>
        <p>Find available royal halls for your event</p>
    </div>

    <!-- Search Form -->
    <div class="parchment-form">
        <form method="GET" class="royal-form">
            <input type="hidden" name="c" value="room">
            <input type="hidden" name="a" value="availability">
            <input type="hidden" name="check" value="1">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="date" class="form-label">📅 Date</label>
                    <input type="date" id="date" name="date" class="form-input" 
                           value="<?= htmlspecialchars($date) ?>" min="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label for="start_time" class="form-label">🕐 Start Time</label>
                    <input type="time" id="start_time" name="start_time" class="form-input" 
                           value="<?= htmlspecialchars($startTime) ?>" required>
                </div>

                <div class="form-group">
                    <label for="end_time" class="form-label">🕔 End Time</label>
                    <input type="time" id="end_time" name="end_time" class="form-input" 
                           value="<?= htmlspecialchars($endTime) ?>" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-royal">🔍 Check Availability</button>
            </div>
        </form>
    </div>

    <!-- Results -->
    <?php if (isset($_GET['check'])): ?>
        <?php if (empty($availableRooms)): ?>
            <div class="empty-state">
                <div class="empty-icon">😔</div>
                <h3>No Available Chambers</h3>
                <p>No royal halls are available for the selected date and time.</p>
                <p>Try adjusting your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="results-section">
                <h3>Available Royal Chambers</h3>
                <p class="results-info">Found <?= count($availableRooms) ?> available chamber(s) for <?= date('F j, Y', strtotime($date)) ?> from <?= date('g:i A', strtotime($startTime)) ?> to <?= date('g:i A', strtotime($endTime)) ?></p>

                <div class="rooms-grid compact">
                    <?php foreach ($availableRooms as $room): ?>
                    <div class="room-card royal-card available">
                        <div class="room-header">
                            <h4><?= htmlspecialchars($room['name']) ?></h4>
                            <div class="room-rate">💰 <?= number_format($room['hourly_rate'], 2) ?> gold/hour</div>
                        </div>
                        
                        <div class="room-details">
                            <div class="room-meta">
                                <span>👥 <?= $room['capacity'] ?> people</span>
                                <span>📍 <?= htmlspecialchars($room['location']) ?></span>
                            </div>

                            <?php if (!empty($room['facility_names'])): ?>
                            <div class="room-facilities">
                                <div class="facilities-tags">
                                    <?php 
                                    $facilities = explode(',', $room['facility_names']);
                                    $displayFacilities = array_slice($facilities, 0, 3);
                                    foreach ($displayFacilities as $facility): 
                                    ?>
                                    <span class="facility-tag small"><?= htmlspecialchars(trim($facility)) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($facilities) > 3): ?>
                                    <span class="facility-tag small">+<?= count($facilities) - 3 ?> more</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="room-actions">
                            <a href="?c=booking&a=create&room_id=<?= $room['id'] ?>&date=<?= urlencode($date) ?>&start_time=<?= urlencode($startTime) ?>&end_time=<?= urlencode($endTime) ?>" 
                               class="btn btn-royal btn-small">Book This Chamber</a>
                            <a href="?c=room&a=show&id=<?= $room['id'] ?>" class="btn btn-small">View Details</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>