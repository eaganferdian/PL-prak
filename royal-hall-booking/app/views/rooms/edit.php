<div class="form-page">
    <div class="page-header">
        <h2>Edit Royal Chamber</h2>
        <p>Modify <?= htmlspecialchars($room['name']) ?></p>
    </div>

    <div class="parchment-form large">
        <form method="POST" action="?c=room&a=update" class="royal-form">
            <input type="hidden" name="id" value="<?= $room['id'] ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="name" class="form-label">🏛️ Chamber Name</label>
                    <input type="text" id="name" name="name" class="form-input" 
                           value="<?= htmlspecialchars($_POST['name'] ?? $room['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="location" class="form-label">📍 Location</label>
                    <input type="text" id="location" name="location" class="form-input" 
                           value="<?= htmlspecialchars($_POST['location'] ?? $room['location']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="capacity" class="form-label">👥 Capacity</label>
                    <input type="number" id="capacity" name="capacity" class="form-input" 
                           value="<?= htmlspecialchars($_POST['capacity'] ?? $room['capacity']) ?>" min="1" required>
                </div>

                <div class="form-group">
                    <label for="hourly_rate" class="form-label">💰 Hourly Rate (Gold)</label>
                    <input type="number" id="hourly_rate" name="hourly_rate" class="form-input" 
                           value="<?= htmlspecialchars($_POST['hourly_rate'] ?? $room['hourly_rate']) ?>" step="0.01" min="0" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">📜 Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="4" required><?= htmlspecialchars($_POST['description'] ?? $room['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">🏰 Royal Amenities</label>
                <div class="facilities-checkbox">
                    <?php 
                    $selectedFacilities = explode(',', $room['facility_ids'] ?? '');
                    foreach ($facilities as $facility): 
                    ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="facilities[]" value="<?= $facility['id'] ?>"
                               <?= in_array($facility['id'], $selectedFacilities) ? 'checked' : '' ?>>
                        <span class="checkmark"></span>
                        <?= htmlspecialchars($facility['name']) ?> 
                        <span class="facility-icon"><?= $facility['icon'] ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-royal">💾 Update Chamber</button>
                <a href="?c=room&a=show&id=<?= $room['id'] ?>" class="btn btn-secondary">↩️ Cancel</a>
            </div>
        </form>
    </div>
</div>