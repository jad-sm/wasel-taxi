<?php
require_once __DIR__ . '/includes/auth.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($full_name === '' || $email === '' || $phone === '' || $password === '') {
        $errors[] = 'Please fill in every field.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $errors[] = 'An account with this email already exists.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare('INSERT INTO users (full_name, email, phone, password_hash) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $full_name, $email, $phone, $hash);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}

$page_title = 'Sign up';
require_once __DIR__ . '/includes/header.php';
?>
<main class="auth-wrap">
    <div class="auth-card">
        <h1>Create your account</h1>
        <p class="lead">Book a car, moto, or caravan in under a minute.</p>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>

        <form method="post" novalidate>
            <div class="field">
                <label for="full_name">Full name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="field">
                <label for="phone">Phone number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="+961 xx xxx xxx" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="field">
                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Create account</button>
        </form>

        <div class="form-foot">Already have an account? <a href="login.php">Log in</a></div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
