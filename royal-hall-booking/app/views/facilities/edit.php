<div class="form-page">
    <div class="page-header">
        <h2>Edit Royal Facility</h2>
        <p>Modify <?= htmlspecialchars($facility['name']) ?></p>
    </div>

    <div class="parchment-form">
        <form method="POST" action="?c=facility&a=update" class="royal-form">
            <input type="hidden" name="id" value="<?= $facility['id'] ?>">
            
            <div class="form-group">
                <label for="name" class="form-label">⚙️ Facility Name</label>
                <input type="text" id="name" name="name" class="form-input" 
                       value="<?= htmlspecialchars($_POST['name'] ?? $facility['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="icon" class="form-label">🎨 Icon</label>
                <input type="text" id="icon" name="icon" class="form-input" 
                       value="<?= htmlspecialchars($_POST['icon'] ?? $facility['icon']) ?>" required>
                <small class="form-help">Enter a single emoji character</small>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">📜 Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="3" required><?= htmlspecialchars($_POST['description'] ?? $facility['description']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-royal">💾 Update Facility</button>
                <a href="?c=facility&a=index" class="btn btn-secondary">↩️ Cancel</a>
            </div>
        </form>
    </div>
</div>