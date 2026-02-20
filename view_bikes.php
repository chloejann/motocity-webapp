<?php
session_start();
require_once 'inc/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the search keyword from the query string or form
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the SQL query with a LIKE condition for the search term
$query = "SELECT * FROM motorbikes WHERE is_active = 1";

if (!empty($searchTerm)) {
    // Add search conditions for bike_code, description, and renting_location
    $query .= " AND (bike_code LIKE :searchTerm OR description LIKE :searchTerm OR renting_location LIKE :searchTerm)";
}

$stmt = $pdo->prepare($query);

if (!empty($searchTerm)) {
    $searchParam = "%$searchTerm%";  // % for partial matching
    $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);
}

$stmt->execute();
$motorbikes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoCity - Available Motorbikes</title>
    <link rel="stylesheet" href="assets/style.css">
    <script>
        // Function to dynamically filter the table without reloading the page
        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');

            rows.forEach(row => {
                const bikeCode = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const description = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const location = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

                if (bikeCode.includes(searchTerm) || description.includes(searchTerm) || location.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>
    <header>
        <h1>Welcome to MotoCity</h1>
        <nav>
            <a href="dashboard.php" class="btn btn-ghost">Dashboard</a>
            <a href="logout.php" class="btn btn-ghost">Logout</a>
        </nav>
    </header>

    <section>
        <h2>Available Motorbikes</h2>

        <!-- Search Input -->
    <div class="search-container">
        <input type="text" id="searchInput" oninput="filterTable()" placeholder="Search by Bike Code, Description, or Location" />
    </div>

        <?php if (count($motorbikes) > 0): ?>
            <table>
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
                            <td><a href="rent_bike.php?bike_code=<?php echo urlencode($bike['bike_code']); ?>" class="btn btn-primary">Rent Now</a></td>
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