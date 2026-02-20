<?php
// Start the session to check if the user is logged in
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'inc/db.php';  

$user_id = $_SESSION['user_id'];

// Modify query to join the rentals and motorbikes table based on bike_code
$query = "
    SELECT rentals.*, motorbikes.description, motorbikes.renting_location 
    FROM rentals 
    LEFT JOIN motorbikes ON rentals.bike_code = motorbikes.bike_code
    WHERE rentals.user_id = :user_id AND rentals.status = 'RETURNED' 
    ORDER BY rentals.end_time DESC
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

// Fetch all the rental records
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Rental History</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <header>
        <h1>MotoCity</h1>
        <nav>
            <a href="dashboard.php" class="nav-active">Dashboard</a>
            <a href="logout.php" class="btn-nav">Logout</a>
        </nav>
    </header>

    <section>
        <h2>Your Rental History</h2>

        <!-- Display message if no rentals found -->
        <?php if (empty($rentals)): ?>
            <div class="empty-state">
                <div class="empty-icon">🚲</div>
                <p>You have no rental history yet.</p>
            </div>
        <?php else: ?>
            <!-- Rental History Table -->
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Motorbike</th>
                            <th>Location</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rentals as $index => $rental): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($rental['bike_code']); ?> - <?php echo htmlspecialchars($rental['description']); ?></td>
                                <td><?php echo htmlspecialchars($rental['renting_location']); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($rental['start_time'])); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($rental['end_time'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </section>

    <footer>
        <p>&copy; 2026 MotoCity. All Rights Reserved.</p>
    </footer>

</body>
</html>