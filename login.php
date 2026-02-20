<?php
session_start();
require_once 'inc/db.php';  // Include the database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user input
    $email = $_POST['email'];          // User email input
    $password = $_POST['password'];    // User password input

    // Fetch the user from the database using the email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();  // Fetch user data from the database

    // Ensure user is found and password is verified
    if ($user) {
        $hashed_password_from_db = $user['password_hash'];  // Get the hashed password from the database
        $password_input = $password;  // The password entered by the user

        // Debugging: Check the entered password and hash from the DB
        echo "Entered Password: " . htmlspecialchars($password_input) . "\n";
        echo "Hash from DB: " . $hashed_password_from_db . "\n";

        // Check if the password is correct
        if (password_verify($password_input, $hashed_password_from_db)) {
            // Correct password, set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['first_name'] = $user['first_name'];

            // Redirect to the dashboard or user-specific page
            header('Location: dashboard.php');
            exit;
        } else {
            // Incorrect password
            $error = "Password is incorrect!";
        }
    } else {
        // No user found with the provided email
        $error = "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header><h1>Login</h1></header>
    <section>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </section>
</body>
</html>