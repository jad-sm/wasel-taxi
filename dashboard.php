<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $pickup = trim($_POST['pickup_location'] ?? '');
    $dropoff = trim($_POST['dropoff_location'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (!in_array($vehicle_type, ['car', 'moto', 'caravan'], true)) {
        $errors[] = 'Choose a vehicle type.';
    }
    if ($pickup === '' || $dropoff === '') {
        $errors[] = 'Enter both a pickup and a drop-off location.';
    }

    if (empty($errors)) {
        $ride_code = generate_ride_code();
        $stmt = $mysqli->prepare('INSERT INTO rides (ride_code, user_id, vehicle_type, pickup_location, dropoff_location, notes) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sissss', $ride_code, $user['id'], $vehicle_type, $pickup, $dropoff, $notes);
        if ($stmt->execute()) {
            header('Location: my-rides.php?booked=' . urlencode($ride_code));
            exit;
        } else {
            $errors[] = 'Could not create the booking. Please try again.';
        }
    }
}

// Recent rides preview
$stmt = $mysqli->prepare('SELECT ride_code, vehicle_type, pickup_location, dropoff_location, status, requested_at FROM rides WHERE user_id = ? ORDER BY requested_at DESC LIMIT 3');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$recent_rides = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = 'Book a ride';
require_once __DIR__ . '/includes/header.php';
?>
<main class="app-shell">
    <div class="wrap">
        <div class="app-head">
            <div>
                <h1>Hi <?php echo htmlspecialchars(explode(' ', $user['full_name'])[0]); ?>, where to?</h1>
                <p>Pick a vehicle and drop your pickup and drop-off locations.</p>
            </div>
        </div>

        <div class="grid-2">
            <div class="panel">
                <h2>New booking</h2>

                <?php foreach ($errors as $e): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($e); ?></div>
                <?php endforeach; ?>

                <form method="post" novalidate>
                    <div class="vehicle-choice">
                        <div>
                            <input type="radio" id="v_car" name="vehicle_type" value="car" checked>
                            <label for="v_car">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 13l1.5-5A2 2 0 0 1 6.4 6.5h11.2a2 2 0 0 1 1.9 1.5L21 13"/><rect x="3" y="13" width="18" height="6" rx="1.5"/><circle cx="7.5" cy="19" r="1.6"/><circle cx="16.5" cy="19" r="1.6"/></svg>
                                Car
                            </label>
                        </div>
                        <div>
                            <input type="radio" id="v_moto" name="vehicle_type" value="moto">
                            <label for="v_moto">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="5.5" cy="17.5" r="2.5"/><circle cx="18.5" cy="17.5" r="2.5"/><path d="M5.5 17.5 9 10h4l3 4.5h2.5M9 10 7.5 6.5h-2"/></svg>
                                Moto
                            </label>
                        </div>
                        <div>
                            <input type="radio" id="v_caravan" name="vehicle_type" value="caravan">
                            <label for="v_caravan">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2.5 16V9a2 2 0 0 1 2-2h9l5 4.5V16"/><rect x="2.5" y="16" width="16.5" height="2.5"/><circle cx="7" cy="19.3" r="1.6"/><circle cx="16" cy="19.3" r="1.6"/></svg>
                                Caravan
                            </label>
                        </div>
                    </div>

                    <div class="field">
                        <label for="pickup_location">Pickup location</label>
                        <input type="text" id="pickup_location" name="pickup_location" placeholder="e.g. Hamra Street, Beirut" value="<?php echo htmlspecialchars($_POST['pickup_location'] ?? ''); ?>" required>
                    </div>
                    <div class="field">
                        <label for="dropoff_location">Drop-off location</label>
                        <input type="text" id="dropoff_location" name="dropoff_location" placeholder="e.g. Beirut Rafic Hariri Airport" value="<?php echo htmlspecialchars($_POST['dropoff_location'] ?? ''); ?>" required>
                    </div>
                    <div class="field">
                        <label for="notes">Notes for the driver (optional)</label>
                        <textarea id="notes" name="notes" placeholder="Gate code, luggage, landmark near you..."><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">Request ride</button>
                </form>
            </div>

            <div class="panel">
                <h2>Recent bookings</h2>
                <?php if (empty($recent_rides)): ?>
                    <div class="empty-state">No rides yet — your first booking will show up here.</div>
                <?php else: ?>
                    <?php foreach ($recent_rides as $ride): ?>
                        <div class="ride-item">
                            <div>
                                <div class="ride-route">
                                    <strong><?php echo htmlspecialchars($ride['pickup_location']); ?></strong>
                                    <span class="arrow">&rarr;</span>
                                    <strong><?php echo htmlspecialchars($ride['dropoff_location']); ?></strong>
                                </div>
                                <div class="ride-meta"><?php echo strtoupper($ride['vehicle_type']); ?> &middot; <?php echo htmlspecialchars($ride['ride_code']); ?></div>
                            </div>
                            <span class="status-pill status-<?php echo $ride['status']; ?>"><?php echo $ride['status']; ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-foot"><a href="my-rides.php">View all rides &rarr;</a></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
