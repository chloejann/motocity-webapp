<?php
session_start();

// Assuming user type is stored in session after login
if (!isset($_SESSION['user_type'])) {
    header('Location: login.php');
    exit;
}

$user_type = $_SESSION['user_type'];  // Retrieve the user type (ADMIN or USER)

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <h1>Dashboard</h1>
        <a href="logout.php" class="btn btn-ghost">Logout</a>
    </header>
    
    <section class="dashboard-container">
        <?php if ($user_type == 'ADMIN'): ?>
            <div class="card">
                <h2>Welcome, Admin</h2>
                <p>As an admin, you can manage motorbikes and users.</p>
                <div class="card-actions">
                    <a href="manage_bikes.php" class="btn btn-primary">Manage Motorbikes</a>
                    <a href="manage_users.php" class="btn btn-primary">Manage Users</a>
                    <a href="view_rentals.php" class="btn btn-primary">View All Rentals</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <h2>Welcome, User</h2>
                <p>As a user, you can rent and return motorbikes.</p>
                <div class="card-actions">
                    <a href="view_bikes.php" class="btn btn-primary">View Available Motorbikes</a>
                    <a href="rental_history.php" class="btn btn-primary">View Rental History</a>
                    <a href="rentals.php" class="btn btn-primary">Manage Your Rentals</a>
                </div>
            </div>
        <?php endif; ?>
    </section>

</body>
</html>