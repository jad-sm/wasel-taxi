<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$booked_code = $_GET['booked'] ?? null;

$stmt = $mysqli->prepare('SELECT ride_code, vehicle_type, pickup_location, dropoff_location, notes, status, requested_at FROM rides WHERE user_id = ? ORDER BY requested_at DESC');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$rides = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = 'My rides';
require_once __DIR__ . '/includes/header.php';
?>
<main class="app-shell">
    <div class="wrap">
        <div class="app-head">
            <div>
                <h1>My rides</h1>
                <p>Every ride you've booked with Wasel, newest first.</p>
            </div>
            <a href="dashboard.php" class="btn btn-primary">Book another ride</a>
        </div>

        <?php if ($booked_code): ?>
            <div class="alert alert-success">Ride requested — reference <span class="mono"><?php echo htmlspecialchars($booked_code); ?></span>. We'll match you with a driver shortly.</div>
        <?php endif; ?>

        <div class="panel">
            <?php if (empty($rides)): ?>
                <div class="empty-state">No rides yet. <a href="dashboard.php">Book your first ride &rarr;</a></div>
            <?php else: ?>
                <?php foreach ($rides as $ride): ?>
                    <div class="ride-item">
                        <div>
                            <div class="ride-route">
                                <strong><?php echo htmlspecialchars($ride['pickup_location']); ?></strong>
                                <span class="arrow">&rarr;</span>
                                <strong><?php echo htmlspecialchars($ride['dropoff_location']); ?></strong>
                            </div>
                            <div class="ride-meta">
                                <?php echo strtoupper($ride['vehicle_type']); ?> &middot;
                                <?php echo htmlspecialchars($ride['ride_code']); ?> &middot;
                                <?php echo date('M j, Y g:ia', strtotime($ride['requested_at'])); ?>
                                <?php if (!empty($ride['notes'])): ?> &middot; "<?php echo htmlspecialchars($ride['notes']); ?>"<?php endif; ?>
                            </div>
                        </div>
                        <span class="status-pill status-<?php echo $ride['status']; ?>"><?php echo $ride['status']; ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
