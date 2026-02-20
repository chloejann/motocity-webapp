<?php
session_start();
require_once 'inc/db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ADMIN') {
    header('Location: login.php');
    exit;
}

// Get the search keyword from the query string or form
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the SQL query with a LIKE condition for the search term
$query = "SELECT * FROM users";

if (!empty($searchTerm)) {
    // Add search conditions for name, email, and phone
    $query .= " WHERE (first_name LIKE :searchTerm OR last_name LIKE :searchTerm OR email LIKE :searchTerm OR phone LIKE :searchTerm)";
}

$stmt = $pdo->prepare($query);

if (!empty($searchTerm)) {
    $searchParam = "%$searchTerm%";  // % for partial matching
    $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);
}

$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="assets/style.css">
    <script>
        // Function to dynamically filter the table without reloading the page
        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');

            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const phone = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const userType = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

                if (name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm) || userType.includes(searchTerm)) {
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
        <h1>Manage Users</h1>
        <a href="logout.php" class="btn btn-ghost">Logout</a>
    </header>

    <section>
        <!-- Search Input -->
        <div class="search-container">
            <input type="text" id="searchInput" oninput="filterTable()" placeholder="Search by Name, Email, Phone, or User Type" />
        </div>

        <?php if (count($users) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>User Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td><?php echo $user['user_type']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </section>

</body>
</html>