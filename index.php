<?php
require_once 'inc/db.php';

// Fetch all available bikes
$query = $pdo->query("SELECT * FROM motorbikes WHERE is_active = 1");
$motorbikes = $query->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoCity - Home</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>MotoCity</h1>
            <nav>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Available Motorbikes</h2>
        <table class="bike-table">
            <thead>
                <tr>
                    <th>Bike Code</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Cost per Hour</th>
                    <th>Rent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($motorbikes as $bike): ?>
                <tr>
                    <td><?php echo htmlspecialchars($bike['bike_code']); ?></td>
                    <td><?php echo htmlspecialchars($bike['renting_location']); ?></td>
                    <td><?php echo htmlspecialchars($bike['description']); ?></td>
                    <td>$<?php echo htmlspecialchars($bike['cost_per_hour']); ?></td>
                    <td><a href="rent.php?bike_code=<?php echo urlencode($bike['bike_code']); ?>">Rent Now</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 MotoCity. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>