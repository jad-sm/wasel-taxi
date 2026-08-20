<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = trim($_POST['body'] ?? '');
    if ($body !== '') {
        $stmt = $mysqli->prepare('INSERT INTO messages (user_id, sender, body) VALUES (?, "client", ?)');
        $stmt->bind_param('is', $user['id'], $body);
        $stmt->execute();
    }
    header('Location: messages.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT sender, body, created_at FROM messages WHERE user_id = ? ORDER BY created_at ASC');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$thread = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = 'Messages';
require_once __DIR__ . '/includes/header.php';
?>
<main class="app-shell">
    <div class="wrap">
        <div class="app-head">
            <div>
                <h1>Messages</h1>
                <p>Reach Wasel support about a ride, a fare, or anything else.</p>
            </div>
        </div>

        <div class="panel">
            <h2>Support thread</h2>
            <div class="thread">
                <?php if (empty($thread)): ?>
                    <div class="empty-state">No messages yet — say hello below.</div>
                <?php else: ?>
                    <?php foreach ($thread as $m): ?>
                        <div class="msg msg-<?php echo $m['sender']; ?>">
                            <?php echo nl2br(htmlspecialchars($m['body'])); ?>
                            <span class="msg-time"><?php echo date('M j, g:ia', strtotime($m['created_at'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form method="post" class="message-form">
                <textarea name="body" placeholder="Type a message to support..." required></textarea>
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
