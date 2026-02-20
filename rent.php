<?php
session_start();
require_once '../inc/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['bike_code'])) {
    $bike_code = $_GET['bike_code'];
    $user_id = $_SESSION['user_id'];
    $start_time = date('Y-m-d H:i:s');

    // Insert rental into DB
    $stmt = $pdo->prepare("INSERT INTO rentals (user_id, bike_code, start_time, status) VALUES (:user_id, :bike_code, :start_time, 'RENTED')");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':bike_code', $bike_code);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->execute();

    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Bike</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header><h1>Rent Bike</h1></header>
    <section>
        <p>Renting bike: <?php echo htmlspecialchars($_GET['bike_code']); ?></p>
        <a href="rent.php?bike_code=<?php echo htmlspecialchars($_GET['bike_code']); ?>">Confirm Rental</a>
    </section>
</body>
</html>