<?php
session_start();
require_once 'inc/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize variables for error and success messages
$error_message = '';
$success_message = '';

// Handle the insert/update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use the @ operator to suppress the warning for undefined index
    $bike_code = @$_POST['bike_code'];  // Suppress the warning here
    $renting_location = $_POST['renting_location'];
    $description = $_POST['description'];
    $cost_per_hour = $_POST['cost_per_hour'];
    $is_active = $_POST['is_active'];

    // If bike_code is empty, generate the next available bike_code for a new entry
    if (empty($bike_code)) {
        // Fetch the last inserted bike_code
        $query = "SELECT bike_code FROM motorbikes ORDER BY bike_code DESC LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $lastBikeCode = $stmt->fetchColumn();

        // Generate the next bike_code
        $nextBikeCode = 'MOTO' . str_pad((intval(substr($lastBikeCode, 4)) + 1), 3, '0', STR_PAD_LEFT);
        $bike_code = $nextBikeCode;  // Set the new bike_code
    } else {
        // Check if bike_code already exists (to avoid duplicates)
        $query = "SELECT COUNT(*) FROM motorbikes WHERE bike_code = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$bike_code]);
        if ($stmt->fetchColumn() > 0) {
            $error_message = "Error: Bike code already exists!";
        }
    }

    if (empty($error_message)) {
        // Insert or Update the motorbike record in the database
        try {
            if (!empty($_POST['existing_bike_code'])) {
                // Update existing motorbike (only if existing_bike_code is provided)
                $query = "UPDATE motorbikes SET renting_location = ?, description = ?, cost_per_hour = ?, is_active = ? WHERE bike_code = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$renting_location, $description, $cost_per_hour, $is_active, $_POST['existing_bike_code']]);
                $success_message = "Motorbike updated successfully!";
            } else {
                // Insert a new motorbike
                $query = "INSERT INTO motorbikes (bike_code, renting_location, description, cost_per_hour, is_active) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$bike_code, $renting_location, $description, $cost_per_hour, $is_active]);
                $success_message = "Motorbike added successfully!";
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// If bike_code is provided, edit the motorbike
$motorbike_to_edit = null;
if (isset($_GET['bike_code'])) {
    $bike_code = $_GET['bike_code'];
    $stmt = $pdo->prepare("SELECT * FROM motorbikes WHERE bike_code = ?");
    $stmt->execute([$bike_code]);
    $motorbike_to_edit = $stmt->fetch();
}

// Fetch available motorbikes from the database
$stmt = $pdo->prepare("SELECT * FROM motorbikes WHERE is_active = 1");
$stmt->execute();
$motorbikes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Motorbikes</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <header>
        <h1>Manage Motorbikes</h1>
        <a href="logout.php" class="btn btn-ghost">Logout</a>
    </header>

    <section>
        <h2>Manage Motorbikes</h2>

        <!-- Add New Motorbike Button -->
        <a href="manage_bikes.php" class="btn btn-primary">Add New Motorbike</a>

        <!-- Display Success or Error Message -->
        <?php if ($success_message): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <h3><?php echo $motorbike_to_edit ? 'Edit Motorbike' : 'Insert New Motorbike'; ?></h3>
        
        <!-- Insert/Edit Form -->
        <form action="manage_bikes.php" method="POST">
            <input type="hidden" name="existing_bike_code" value="<?= htmlspecialchars($motorbike_to_edit['bike_code'] ?? '') ?>">

            <label for="renting_location">Location:</label>
            <input type="text" name="renting_location" value="<?= htmlspecialchars($motorbike_to_edit['renting_location'] ?? '') ?>" required>

            <label for="description">Description:</label>
            <input type="text" name="description" value="<?= htmlspecialchars($motorbike_to_edit['description'] ?? '') ?>" required>

            <label for="cost_per_hour">Cost per Hour:</label>
            <input type="number" name="cost_per_hour" value="<?= htmlspecialchars($motorbike_to_edit['cost_per_hour'] ?? '') ?>" required>

            <label for="is_active">Is Active:</label>
            <select name="is_active" required>
                <option value="1" <?= (isset($motorbike_to_edit['is_active']) && $motorbike_to_edit['is_active'] == 1) ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= (isset($motorbike_to_edit['is_active']) && $motorbike_to_edit['is_active'] == 0) ? 'selected' : '' ?>>No</option>
            </select>

            <button type="submit"><?= $motorbike_to_edit ? 'Update' : 'Insert' ?></button>
        </form>

        <h3>Motorbikes List</h3>
        
        <?php if (count($motorbikes) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Bike Code</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Cost per Hour</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($motorbikes as $motorbike): ?>
                        <tr>
                            <td><?php echo $motorbike['bike_code']; ?></td>
                            <td><?php echo $motorbike['renting_location']; ?></td>
                            <td><?php echo $motorbike['description']; ?></td>
                            <td>$<?php echo $motorbike['cost_per_hour']; ?></td>
                            <td>
                                <a href="manage_bikes.php?bike_code=<?php echo $motorbike['bike_code']; ?>" class="btn btn-primary">Edit</a>
                                <a href="manage_bikes.php?bike_code=<?php echo $motorbike['bike_code']; ?>" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No motorbikes available for rent at the moment.</p>
        <?php endif; ?>
    </section>

</body>
</html>