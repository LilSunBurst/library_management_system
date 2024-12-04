<?php

// Include authentication and database configuration files
require_once '../config/auth.php';
require_once '../config/db.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not authenticated
redirectIfNotAuthenticated('login.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get borrow ID from form data
    $borrow_id = $_POST['borrow_id'];
    
    // Fetch the book ID associated with this borrow ID
    $stmt = $conn->prepare("SELECT book_id FROM borrowed_books WHERE id = ?");
    $stmt->bind_param("i", $borrow_id);
    $stmt->execute();
    $stmt->bind_result($book_id);
    $stmt->fetch();
    $stmt->close();

    // Update the returned_at field in the borrowed_books table
    $stmt = $conn->prepare("UPDATE borrowed_books SET returned_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $borrow_id);
    if ($stmt->execute()) {
        // Increment the available copies in the books table
        $stmt = $conn->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        if ($stmt->execute()) {
            // Set success message for book return
            $_SESSION['message'] = "Book returned successfully!";
        } else {
            // Set error message for updating available copies
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
    } else {
        // Set error message for updating return date
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();
    
    // Redirect to dashboard page to show message
    header("Location: dashboard.php");
    exit();
}
?>