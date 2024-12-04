<?php

// Include the necessary files
require_once '../config/auth.php';
require_once '../config/db.php';

// Check if a session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to the login page if the user is not authenticated
redirectIfNotAuthenticated('login.php');

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Prepare the SQL statement to fetch the user's name and email
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");

// Bind the user ID to the SQL statement
$stmt->bind_param("i", $user_id);

// Execute the SQL statement
$stmt->execute();

// Bind the result to variables
$stmt->bind_result($name, $email);

// Fetch the result
$stmt->fetch();

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Account Details</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class="centered">
    <div class="form-container">
        <h2>User Account Details</h2>
        <p>Name: <?php echo htmlspecialchars($name); ?></p>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <a class="button" href="index.php">Back to Home</a>
    </div>
</body>
</html>