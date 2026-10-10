<?php
require_once __DIR__ . '/includes/functions.php';
redirect_if_logged_in();

$errors    = [];
$alert     = null;
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';
$fullname  = '';
$email     = '';

if ($submitted) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $alert = ['type' => 'error', 'text' => 'Your session expired. Please try again.'];
    } else {
        $errors = validate_register($_POST);

        if (!$errors) {
            $users   = get_users();
            $users[] = [
                'name'     => $fullname,
                'email'    => strtolower($email),
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            ];

            if (save_users($users)) {
                redirect('login.php?registered=1');
            }
            $alert = ['type' => 'error', 'text' => 'Could not save your account. Check that the data folder is writable.'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Spider Web</title>
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
                <p class="subtitle">Create an account and get your powers.</p>
            </header>

            <div id="formAlert" class="form-alert <?= $alert ? e($alert['type']) : 'd-none' ?>" role="alert" aria-live="polite"><?= $alert ? e($alert['text']) : '' ?></div>

            <form id="registerForm" action="register.php" method="POST" class="auth-form" novalidate>
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input
                        type="text"
                        id="fullname"
                        name="fullname"
                        placeholder="Enter your full name" autocomplete="name"
                        value="<?= e($fullname) ?>"<?= field_class($errors, 'fullname', $submitted) ?>
                    >
                    <?php field_error($errors, 'fullname'); ?>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email" autocomplete="email"
                        value="<?= e($email) ?>"<?= field_class($errors, 'email', $submitted) ?>
                    >
                    <?php field_error($errors, 'email'); ?>
                </div>

                <div class="form-group">
                    <label for="reg-password">Password</label>
                    <input
                        type="password"
                        id="reg-password"
                        name="password"
                        placeholder="Create a password" autocomplete="new-password"
                        <?= field_class($errors, 'password', $submitted) ?>
                    >
                    <?php field_error($errors, 'password'); ?>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input
                        type="password"
                        id="confirm-password"
                        name="confirm-password"
                        placeholder="Confirm your password" autocomplete="new-password"
                        <?= field_class($errors, 'confirm-password', $submitted) ?>
                    >
                    <?php field_error($errors, 'confirm-password'); ?>
                </div>

                <button type="submit" class="submit-btn">Get Powers!</button>
            </form>

            <footer class="card-footer">
                <p>Already a hero? <a href="login.php">Login</a></p>
            </footer>
        </div>
    </main>
</body>
</html>
