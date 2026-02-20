<?php
session_start();
require_once 'inc/db.php';

$motorbike = null;  // Initialize the variable here

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the rental_id from the URL (rental_id should be a numeric ID, not the bike_code)
$bike_code = $_GET['bike_code'] ?? null;
$rental_id = $_GET['rental_id'] ?? null;

if ($rental_id) {
    // Fetch rental details using rental_id (and user_id) to ensure it's the correct rental
    $stmt = $pdo->prepare("SELECT * FROM rentals WHERE rental_id = :rental_id AND user_id = :user_id AND end_time IS NULL");
    $stmt->bindParam(':rental_id', $rental_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $rental = $stmt->fetch();

    // If the rental exists
    if ($rental) {
        // Get the bike_code from the rental and fetch the motorbike details
        $stmt = $pdo->prepare("SELECT * FROM motorbikes WHERE bike_code = :bike_code");
        $stmt->bindParam(':bike_code', $rental['bike_code']);  // Use bike_code from rental
        $stmt->execute();
        $motorbike = $stmt->fetch();  // Fetch the motorbike details

        // If the motorbike exists
        if ($motorbike) {
            // Mark the motorbike as returned by setting the end_time
            date_default_timezone_set('Asia/Singapore');
            $end_time = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("UPDATE rentals SET end_time = :end_time WHERE rental_id = :rental_id AND user_id = :user_id AND end_time IS NULL");
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':rental_id', $rental_id);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();

            // Calculate the total cost
            $start_time = strtotime($rental['start_time']);
            $end_time = strtotime($rental['end_time']);

            // Calculate the duration in hours
            $duration = ($end_time - $start_time) / 3600;

            if ($duration < 0.01) {  // 5 seconds is less than 0.01 hours
            $duration = 1;  // Minimum duration of 1 hour
            }

            // Calculate the total cost
            $total_cost = $duration * $motorbike['cost_per_hour'];

            // Mark motorbike as available again
            $stmt = $pdo->prepare("UPDATE motorbikes SET is_active = 1 WHERE bike_code = :bike_code");
            $stmt->bindParam(':bike_code', $rental['bike_code']);
            $stmt->execute();

            // Display the return details and cost
            $message = "Motorbike returned successfully! Total cost: $" . number_format($total_cost, 2);
        } else {
            $message = "Motorbike not found.";
        }
    } else {
        $message = "No active rental found for this motorbike.";
    }
} else {
    $message = "Invalid rental ID.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Motorbike</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <h1>Return Motorbike</h1>
    </header>

    <section>
        <p><?php echo $message; ?></p>
        
        <?php if (isset($motorbike)): ?>
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