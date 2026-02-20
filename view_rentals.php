<?php
session_start();
require_once 'inc/db.php';

// Fetch all rental records
$query = "SELECT rentals.*, motorbikes.bike_code, motorbikes.renting_location, users.first_name AS user_first_name, users.last_name AS user_last_name
          FROM rentals
          INNER JOIN motorbikes ON rentals.bike_code = motorbikes.bike_code
          INNER JOIN users ON rentals.user_id = users.user_id
          ORDER BY rentals.start_time DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$rentals = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rentals</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <header>
        <h1>View All Rentals</h1>
        <a href="logout.php" class="btn btn-ghost">Logout</a>
    </header>

    <section>
        <h2>Rental Records</h2>

        <?php if (count($rentals) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Rental ID</th>
                        <th>User</th>
                        <th>Bike Code</th>
                        <th>Location</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rentals as $rental): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rental['rental_id']); ?></td>
                            <td><?php echo htmlspecialchars($rental['user_first_name'] . ' ' . $rental['user_last_name']); ?></td>
                            <td><?php echo htmlspecialchars($rental['bike_code']); ?></td>
                            <td><?php echo htmlspecialchars($rental['renting_location']); ?></td>
                            <td><?php echo htmlspecialchars($rental['start_time']); ?></td>
                            <td><?php echo htmlspecialchars($rental['end_time']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No rental records found.</p>
        <?php endif; ?>
    </section>

</body>
</html>