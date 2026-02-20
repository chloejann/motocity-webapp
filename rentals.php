<?php
session_start();
require_once 'inc/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch current rentals (not yet returned motorbikes)
$stmt = $pdo->prepare("SELECT * FROM rentals JOIN motorbikes ON rentals.bike_code = motorbikes.bike_code WHERE rentals.user_id = :user_id AND rentals.end_time IS NULL");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$current_rentals = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Rentals</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <h1>Current Rentals</h1>
        <a href="logout.php" class="btn btn-ghost">Logout</a>
    </header>

    <section>
        <?php if (count($current_rentals) > 0): ?>
            <ul>
                <?php foreach ($current_rentals as $rental): ?>
                    <li>
                        <h3><?php echo $rental['description']; ?></h3>
                        <p>Rented on: <?php echo $rental['start_time']; ?></p>
                        <p>Cost per hour: $<?php echo $rental['cost_per_hour']; ?></p>
                        <a href="return_bike.php?rental_id=<?php echo $rental['rental_id']; ?>" class="btn btn-danger">Return Motorbike</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No current rentals.</p>
        <?php endif; ?>
    </section>
</body>
</html>