<div class="room-detail-page">
    <div class="page-header">
        <h2><?= htmlspecialchars($room['name']) ?></h2>
        <p><?= htmlspecialchars($room['location']) ?></p>
    </div>

    <div class="detail-layout">
        <div class="detail-main">
            <div class="room-info royal-card">
                <h3>Chamber Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">🏛️ Name:</span>
                        <span class="info-value"><?= htmlspecialchars($room['name']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">📍 Location:</span>
                        <span class="info-value"><?= htmlspecialchars($room['location']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">👥 Capacity:</span>
                        <span class="info-value"><?= $room['capacity'] ?> nobles</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">💰 Hourly Rate:</span>
                        <span class="info-value"><?= number_format($room['hourly_rate'], 2) ?> gold coins</span>
                    </div>
                </div>

                <div class="description-section">
                    <h4>Royal Description</h4>
                    <p><?= nl2br(htmlspecialchars($room['description'])) ?></p>
                </div>
            </div>

            <?php if (!empty($room['facility_names'])): ?>
            <div class="facilities-section royal-card">
                <h3>🏰 Royal Amenities</h3>
                <div class="facilities-grid">
                    <?php 
                    $facilities = explode(',', $room['facility_names']);
                    foreach ($facilities as $facility): 
                    ?>
                    <div class="facility-item">
                        <span class="facility-icon">⚜️</span>
                        <span><?= htmlspecialchars(trim($facility)) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="detail-sidebar">
            <div class="booking-widget royal-card">
                <h3>Book This Chamber</h3>
                <p>Reserve this royal hall for your event</p>
                <div class="widget-actions">
                    <a href="?c=booking&a=create&room_id=<?= $room['id'] ?>" class="btn btn-royal btn-block">
                        🗓️ Book Now
                    </a>
                    <a href="?c=room&a=availability&room_id=<?= $room['id'] ?>" class="btn btn-secondary btn-block">
                        📅 Check Availability
                    </a>
                </div>
            </div>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <div class="admin-actions royal-card">
                <h3>Chamber Management</h3>
                <div class="action-buttons">
                    <a href="?c=room&a=edit&id=<?= $room['id'] ?>" class="btn btn-secondary btn-block">✏️ Edit Chamber</a>
                    <form method="POST" action="?c=room&a=delete" onsubmit="return confirm('Archive this royal chamber?')">
                        <input type="hidden" name="id" value="<?= $room['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-block">🗄️ Archive</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>