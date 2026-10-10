<?php
/**
 * Web-Verse shared helpers
 * - session handling
 * - user storage (data/users.json, passwords are hashed)
 * - form validation using regular expressions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const USERS_FILE = __DIR__ . '/../data/users.json';

// Static demo login required by the earlier activity
const STATIC_USER = ['username' => 'admin', 'password' => '12345', 'name' => 'admin'];

/* ---------- Regular expressions used for validation ---------- */

// Full name: starts with a letter; letters, spaces, hyphens, apostrophes, dots; 2-60 chars
const RX_NAME = "/^[A-Za-z\\x{00C0}-\\x{024F}][A-Za-z\\x{00C0}-\\x{024F}\\s'.-]{1,59}$/u";

// Email: name@domain.tld
const RX_EMAIL = '/^[A-Za-z0-9._%+-]+@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*\.[A-Za-z]{2,}$/';

// Password: at least 6 characters, with at least one letter and one number
const RX_PASSWORD = '/^(?=.*[A-Za-z])(?=.*\d).{6,}$/';

// Login username or email: 3+ characters, no spaces
const RX_LOGIN_ID = '/^[A-Za-z0-9._@%+-]{3,254}$/';

/* ---------- Output + redirect helpers ---------- */

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* ---------- Session helpers ---------- */

function is_logged_in(): bool
{
    return !empty($_SESSION['webverse_user']);
}

function current_user(): string
{
    return $_SESSION['webverse_user'] ?? '';
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function redirect_if_logged_in(): void
{
    if (is_logged_in()) {
        redirect('index.php');
    }
}

/* ---------- CSRF token ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(?string $token): bool
{
    return is_string($token) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

/* ---------- User storage ---------- */

function get_users(): array
{
    if (!is_readable(USERS_FILE)) {
        return [];
    }
    $data = json_decode((string) file_get_contents(USERS_FILE), true);
    return is_array($data) ? $data : [];
}

function save_users(array $users): bool
{
    return file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT), LOCK_EX) !== false;
}

function find_user_by_email(string $email): ?array
{
    $email = strtolower(trim($email));
    foreach (get_users() as $user) {
        if ($user['email'] === $email) {
            return $user;
        }
    }
    return null;
}

/* ---------- Validation ---------- */

/** Returns an array of field => error message (empty array = valid). */
function validate_login(array $in): array
{
    $errors = [];
    $username = trim($in['username'] ?? '');
    $password = $in['password'] ?? '';

    if ($username === '') {
        $errors['username'] = 'Please enter your username or email.';
    } elseif (!preg_match(RX_LOGIN_ID, $username)) {
        $errors['username'] = 'Username must be at least 3 characters with no spaces.';
    }

    if ($password === '') {
        $errors['password'] = 'Please enter your password.';
    }

    return $errors;
}

function validate_register(array $in): array
{
    $errors = [];
    $name     = trim($in['fullname'] ?? '');
    $email    = trim($in['email'] ?? '');
    $password = $in['password'] ?? '';
    $confirm  = $in['confirm-password'] ?? '';

    if ($name === '') {
        $errors['fullname'] = 'Please enter your full name.';
    } elseif (!preg_match(RX_NAME, $name)) {
        $errors['fullname'] = 'Names must be 2-60 characters: letters, spaces, hyphens, apostrophes and dots only.';
    }

    if ($email === '') {
        $errors['email'] = 'Please enter your email address.';
    } elseif (!preg_match(RX_EMAIL, $email)) {
        $errors['email'] = "That doesn't look like a valid email (try name@example.com).";
    } elseif (find_user_by_email($email) !== null) {
        $errors['email'] = 'That email is already registered. Try logging in.';
    }

    if ($password === '') {
        $errors['password'] = 'Please create a password.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    } elseif (!preg_match(RX_PASSWORD, $password)) {
        $errors['password'] = 'Use at least one letter and one number.';
    }

    if ($confirm === '') {
        $errors['confirm-password'] = 'Please confirm your password.';
    } elseif ($confirm !== $password) {
        $errors['confirm-password'] = "Passwords don't match. Please type the same password again.";
    }

    return $errors;
}

/** Prints an error message span + helper for the input class. */
function field_error(array $errors, string $field): void
{
    if (isset($errors[$field])) {
        echo '<span class="error-msg">' . e($errors[$field]) . '</span>';
    }
}

function field_class(array $errors, string $field, bool $submitted): string
{
    if (isset($errors[$field])) {
        return ' class="input-error"';
    }
    return $submitted ? ' class="input-ok"' : '';
}
