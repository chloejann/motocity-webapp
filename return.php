<?php
session_start();
require_once '../inc/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['rental_id'])) {
    $rental_id = $_GET['rental_id'];
    $end_time = date('Y-m-d H:i:s');

    // Update rental status to 'RETURNED' and set the end time
    $stmt = $pdo->prepare("UPDATE rentals SET end_time = :end_time, status = 'RETURNED' WHERE rental_id = :rental_id");
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':rental_id', $rental_id);
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
    <title>Return Bike</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header><h1>Return Bike</h1></header>
    <section>
        <p>Returning bike...</p>
    </section>
</body>
</html>