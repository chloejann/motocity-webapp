<?php
session_start();
require_once 'inc/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$motorbike_id = $_GET['bike_code'] ?? null;

if ($motorbike_id) {
    // Fetch motorbike details
    $stmt = $pdo->prepare("SELECT * FROM motorbikes WHERE bike_code = :bike_code");
    $stmt->bindParam(':bike_code', $motorbike_id);
    $stmt->execute();
    $motorbike = $stmt->fetch();

    if ($motorbike && $motorbike['is_active'] == 1) {
        // Rent the motorbike (insert into rentals table)
        $stmt = $pdo->prepare("INSERT INTO rentals (user_id, bike_code, start_time) VALUES (:user_id, :bike_code, NOW())");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':bike_code', $motorbike_id);
        $stmt->execute();

        // Mark motorbike as unavailable
        $stmt = $pdo->prepare("UPDATE motorbikes SET is_active = 0 WHERE bike_code = :bike_code");
        $stmt->bindParam(':bike_code', $motorbike_id);
        $stmt->execute();

        // Display the rent details and notifications
        $message = "Motorbike rented successfully! Start time: " . date('Y-m-d H:i:s') . "<br>Cost per hour: $" . $motorbike['cost_per_hour'];
    } else {
        $message = "Motorbike is no longer available.";
    }
} else {
    $message = "Invalid motorbike ID.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Motorbike</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <h1>Rent Motorbike</h1>
    </header>

    <section>
        <p><?php echo $message; ?></p>
        
        <?php if ($motorbike): ?>
            <div class="motorbike-details">
                <h3><?php echo $motorbike['bike_code']; ?> - <?php echo $motorbike['description']; ?></h3>
                <p><strong>Location:</strong> <?php echo $motorbike['renting_location']; ?></p>
                <p><strong>Cost per hour:</strong> $<?php echo $motorbike['cost_per_hour']; ?></p>
            </div>
        <?php endif; ?>

        <a href="view_bikes.php" class="btn btn-primary">Back to Motorbikes</a>
    </section>
</body>
</html>