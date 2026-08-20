<?php
require_once __DIR__ . '/includes/auth.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $mysqli->prepare('SELECT id, password_hash FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $errors[] = 'Incorrect email or password.';
    }
}

$page_title = 'Log in';
require_once __DIR__ . '/includes/header.php';
?>
<main class="auth-wrap">
    <div class="auth-card">
        <h1>Welcome back</h1>
        <p class="lead">Log in to book your next ride.</p>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>

        <form method="post" novalidate>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Log in</button>
        </form>

        <div class="form-foot">New to Wasel? <a href="register.php">Create an account</a></div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
