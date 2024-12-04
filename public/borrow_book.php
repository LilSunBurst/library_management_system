<?php

// Include authentication configuration file
require_once '../config/auth.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not authenticated
redirectIfNotAuthenticated('login.php');

// Include database configuration file
require_once '../config/db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and set due date to 2 weeks from now
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];
    $due_date = date('Y-m-d', strtotime('+2 weeks'));

    // Prepare SQL statement to check if the book is available
    $check_stmt = $conn->prepare("SELECT available_copies FROM books WHERE id = ?");
    $check_stmt->bind_param("i", $book_id);
    $check_stmt->execute();

    // Bind result variable
    $check_stmt->bind_result($available_copies);
    $check_stmt->fetch();
    $check_stmt->close();

    // Check if there are available copies
    if ($available_copies > 0) {
        // Update the available copies
        $update_stmt = $conn->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ?");
        $update_stmt->bind_param("i", $book_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Insert into borrowed_books table
        $borrow_stmt = $conn->prepare("INSERT INTO borrowed_books (user_id, book_id, due_date) VALUES (?, ?, ?)");
        $borrow_stmt->bind_param("iis", $user_id, $book_id, $due_date);

        try {
            $borrow_stmt->execute();
            // Set success message
            $_SESSION['message'] = "Book borrowed successfully!";
        } catch (mysqli_sql_exception $e) {
            // Set error message
            $_SESSION['message'] = "Error: " . $e->getMessage();
        }

        $borrow_stmt->close();
    } else {
        // Set message if book is not available
        $_SESSION['message'] = "Sorry, this book is not available.";
    }

    // Redirect to the dashboard page to show message
    header("Location: dashboard.php");
    exit();
}
?>