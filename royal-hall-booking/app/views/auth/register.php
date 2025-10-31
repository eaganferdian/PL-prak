<div class="auth-container">
    <div class="parchment-form">
        <div class="form-header">
            <h2>Join the Royal Court</h2>
            <p>Register as a noble to book our royal chambers</p>
        </div>

        <form method="POST" action="?c=auth&a=store" class="royal-form">
            <div class="form-group">
                <label for="full_name" class="form-label">👤 Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-input" 
                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="username" class="form-label">📛 Username</label>
                <input type="text" id="username" name="username" class="form-input" 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">📧 Email</label>
                <input type="email" id="email" name="email" class="form-input" 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">🗝️ Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
                <small class="form-help">Must be at least 6 characters long</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-royal">✍️ Join Court</button>
                <a href="?c=auth&a=login" class="btn btn-secondary">↩️ Back to Login</a>
            </div>
        </form>
    </div>
</div>