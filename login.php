<?php
require_once __DIR__ . '/includes/functions.php';
redirect_if_logged_in();

$errors    = [];
$alert     = null;   // ['type' => 'success|error', 'text' => '...']
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';
$username  = '';

// Message after registering
if (isset($_GET['registered'])) {
    $alert = ['type' => 'success', 'text' => 'Account created! You can log in now.'];
}

if ($submitted) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $alert = ['type' => 'error', 'text' => 'Your session expired. Please try again.'];
    } else {
        $errors = validate_login($_POST);

        if (!$errors) {
            $displayName = null;

            // 1) static credentials
            if (strtolower($username) === STATIC_USER['username'] && $password === STATIC_USER['password']) {
                $displayName = STATIC_USER['name'];
            } else {
                // 2) accounts created on the registration page
                foreach (get_users() as $u) {
                    $idMatch = $u['email'] === strtolower($username)
                            || strtolower($u['name']) === strtolower($username);
                    if ($idMatch && password_verify($password, $u['password'])) {
                        $displayName = $u['name'];
                        break;
                    }
                }
            }

            if ($displayName !== null) {
                session_regenerate_id(true);
                $_SESSION['webverse_user'] = $displayName;
                redirect('index.php');
            }

            $alert = ['type' => 'error', 'text' => 'Invalid username or password. Please try again.'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Spider Web</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/extras.css">
</head>
<body>
    <main class="page-container">
        <div class="card">
            <header class="card-header">
                <h1 class="brand-title">Spider Web</h1>
                <p class="subtitle">Welcome back, hero! Enter your details.</p>
            </header>

            <div id="formAlert" class="form-alert <?= $alert ? e($alert['type']) : 'd-none' ?>" role="alert" aria-live="polite"><?= $alert ? e($alert['text']) : '' ?></div>

            <form id="loginForm" action="login.php" method="POST" class="auth-form" novalidate>
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter your username" autocomplete="username"
                        value="<?= e($username) ?>"<?= field_class($errors, 'username', $submitted) ?>
                    >
                    <?php field_error($errors, 'username'); ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password" autocomplete="current-password"
                        <?= field_class($errors, 'password', false) ?>
                    >
                    <?php field_error($errors, 'password'); ?>
                </div>

                <div class="form-actions">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="submit-btn">Swing In!</button>
            </form>

            <p class="demo-hint">Demo login: <code>admin</code> / <code>12345</code></p>

            <footer class="card-footer">
                <p>New around here? <a href="register.php">Join the team</a></p>
            </footer>
        </div>
    </main>
</body>
</html>
