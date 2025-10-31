<div class="facilities-page">
    <div class="page-header">
        <h2>Royal Facilities</h2>
        <p>Manage amenities available in royal chambers</p>
    </div>

    <!-- Search and Actions -->
    <div class="page-actions">
        <form method="GET" class="search-form">
            <input type="hidden" name="c" value="facility">
            <input type="hidden" name="a" value="index">
            <div class="search-group">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search facilities..." class="search-input">
                <button type="submit" class="btn btn-royal">🔍 Search</button>
            </div>
        </form>
        
        <div class="action-buttons">
            <a href="?c=facility&a=create" class="btn btn-royal">⚙️ Add Facility</a>
        </div>
    </div>

    <?php if (empty($facilities)): ?>
        <div class="empty-state">
            <div class="empty-icon">⚙️</div>
            <p>No facilities found</p>
            <a href="?c=facility&a=create" class="btn btn-royal">Add First Facility</a>
        </div>
    <?php else: ?>
        <!-- Facilities Grid -->
        <div class="facilities-grid">
            <?php foreach ($facilities as $facility): ?>
            <div class="facility-card royal-card">
                <div class="facility-header">
                    <div class="facility-icon-large"><?= $facility['icon'] ?></div>
                    <h3><?= htmlspecialchars($facility['name']) ?></h3>
                </div>
                
                <div class="facility-details">
                    <p class="facility-description"><?= htmlspecialchars($facility['description']) ?></p>
                </div>

                <div class="facility-actions">
                    <a href="?c=facility&a=edit&id=<?= $facility['id'] ?>" class="btn btn-small btn-secondary">Edit</a>
                    <form method="POST" action="?c=facility&a=delete" class="inline-form" 
                          onsubmit="return confirm('Delete this royal facility?')">
                        <input type="hidden" name="id" value="<?= $facility['id'] ?>">
                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?c=facility&a=index&page=<?= $i ?>&q=<?= urlencode($search) ?>" 
               class="pagination-link <?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>