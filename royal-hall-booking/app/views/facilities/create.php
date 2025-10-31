<div class="form-page">
    <div class="page-header">
        <h2>Add Royal Facility</h2>
        <p>Create a new amenity for royal chambers</p>
    </div>

    <div class="parchment-form">
        <form method="POST" action="?c=facility&a=store" class="royal-form">
            <div class="form-group">
                <label for="name" class="form-label">⚙️ Facility Name</label>
                <input type="text" id="name" name="name" class="form-input" 
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="icon" class="form-label">🎨 Icon</label>
                <input type="text" id="icon" name="icon" class="form-input" 
                       value="<?= htmlspecialchars($_POST['icon'] ?? '') ?>" 
                       placeholder="e.g., 📽️, 🔊, 📶" required>
                <small class="form-help">Enter a single emoji character</small>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">📜 Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="3" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-royal">⚙️ Create Facility</button>
                <a href="?c=facility&a=index" class="btn btn-secondary">↩️ Cancel</a>
            </div>
        </form>
    </div>
</div>