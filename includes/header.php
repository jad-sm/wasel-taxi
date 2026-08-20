<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$is_logged_in = isset($_SESSION['user_id']);
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' — Wasel' : 'Wasel — Book a ride, any ride'; ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
<header class="site-header">
    <div class="wrap header-inner">
        <a href="index.html" class="logo">Wasel<span class="logo-dot">.</span></a>
        <nav class="main-nav">
            <?php if ($is_logged_in): ?>
                <a href="dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">Book a ride</a>
                <a href="my-rides.php" class="<?php echo $current_page === 'my-rides.php' ? 'active' : ''; ?>">My rides</a>
                <a href="messages.php" class="<?php echo $current_page === 'messages.php' ? 'active' : ''; ?>">Messages</a>
                <a href="logout.php" class="btn btn-ghost">Log out</a>
            <?php else: ?>
                <a href="index.html#vehicles">Vehicles</a>
                <a href="index.html#how">How it works</a>
                <a href="login.php" class="<?php echo $current_page === 'login.php' ? 'active' : ''; ?>">Log in</a>
                <a href="register.php" class="btn btn-primary">Sign up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
