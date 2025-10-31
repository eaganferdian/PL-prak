<div class="auth-container">
    <div class="parchment-form">
        <div class="form-header">
            <h2>Enter the Royal Castle</h2>
            <p>Present your credentials to gain access</p>
        </div>

        <form method="POST" action="?c=auth&a=authenticate" class="royal-form">
            <div class="form-group">
                <label for="username" class="form-label">📜 Username or Email</label>
                <input type="text" id="username" name="username" class="form-input" 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">🗝️ Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-royal">🚪 Enter Castle</button>
            </div>
        </form>

        <div class="auth-links">
            <p>New to the kingdom? <a href="?c=auth&a=register" class="royal-link">🛡️ Register as Noble</a></p>
        </div>
    </div>
</div>