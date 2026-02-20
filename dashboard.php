<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../inc/db.php';
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <section>
        <h2>Your Current Rentals</h2>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM rentals WHERE user_id = :user_id AND status = 'RENTED'");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $rentals = $stmt->fetchAll();
        ?>
        <table>
            <thead>
                <tr>
                    <th>Bike Code</th>
                    <th>Start Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rentals as $rental): ?>
                <tr>
                    <td><?php echo htmlspecialchars($rental['bike_code']); ?></td>
                    <td><?php echo htmlspecialchars($rental['start_time']); ?></td>
                    <td><a href="return.php?rental_id=<?php echo urlencode($rental['rental_id']); ?>">Return Bike</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</body>
</html>