<?php

// Include database and authentication configuration files
require_once '../config/db.php';
require_once '../config/auth.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to select user by email
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        // Bind result variables
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Start session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            // Store user ID in session
            $_SESSION['user_id'] = $id;
            $_SESSION['message'] = "Login successful!";
            // Redirect to add_book.php page
            header("Location: add_book.php");
            exit();
        } else {
            // Set error message for invalid password
            $_SESSION['message'] = "Invalid password!";
            // Redirect to login.php page
            header("Location: login.php");
            exit();
        }
    } else {
        // Set error message for no user found
        $_SESSION['message'] = "No user found with that email address!";
        // Redirect to login.php page
        header("Location: login.php");
        exit();
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();
}
?>