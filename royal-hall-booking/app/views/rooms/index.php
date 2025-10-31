<div class="rooms-page">
    <div class="page-header">
        <h2>Royal Chambers</h2>
        <p>Discover the finest halls in the kingdom</p>
    </div>

    <!-- Search and Actions -->
    <div class="page-actions">
        <form method="GET" class="search-form">
            <input type="hidden" name="c" value="room">
            <input type="hidden" name="a" value="index">
            <div class="search-group">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search chambers..." class="search-input">
                <button type="submit" class="btn btn-royal">🔍 Search</button>
            </div>
        </form>
        
        <div class="action-buttons">
            <a href="?c=room&a=availability" class="btn btn-secondary">📅 Check Availability</a>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="?c=room&a=create" class="btn btn-royal">🏛️ Add Chamber</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($rooms)): ?>
        <div class="empty-state">
            <div class="empty-icon">🏛️</div>
            <p>No royal chambers found</p>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="?c=room&a=create" class="btn btn-royal">Add First Chamber</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Rooms Grid -->
<!-- GANTI bagian rooms-grid dengan ini: -->
<div class="rooms-grid grid-3">
    <?php foreach ($rooms as $room): ?>
    <div class="room-card">
        <div class="room-image">
            <div class="room-placeholder">
                🏛️
            </div>
        </div>
        <div class="room-content">
            <div class="room-header">
                <h4><?= htmlspecialchars($room['name']) ?></h4>
                <div class="room-rate"><?= number_format($room['hourly_rate'], 0) ?> gold</div>
            </div>
            
            <p class="room-description"><?= htmlspecialchars($room['description']) ?></p>
            
            <div class="room-meta">
                <div class="meta-item">
                    <span class="meta-icon">👥</span>
                    <span><?= $room['capacity'] ?> people</span>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">📍</span>
                    <span><?= htmlspecialchars($room['location']) ?></span>
                </div>
            </div>

            <?php if (!empty($room['facility_names'])): ?>
            <div class="room-facilities">
                <div class="facilities-tags">
                    <?php 
                    $facilities = explode(',', $room['facility_names']);
                    foreach (array_slice($facilities, 0, 3) as $facility): 
                    ?>
                    <span class="facility-tag"><?= htmlspecialchars(trim($facility)) ?></span>
                    <?php endforeach; ?>
                    <?php if (count($facilities) > 3): ?>
                    <span class="facility-tag">+<?= count($facilities) - 3 ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="room-actions">
                <a href="?c=booking&a=create&room_id=<?= $room['id'] ?>" class="btn btn-primary btn-sm">
                    Book Now
                </a>
                <a href="?c=room&a=show&id=<?= $room['id'] ?>" class="btn btn-secondary btn-sm">
                    Details
                </a>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="?c=room&a=edit&id=<?= $room['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?c=room&a=index&page=<?= $i ?>&q=<?= urlencode($search) ?>" 
               class="pagination-link <?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>