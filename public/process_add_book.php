<?php
require_once '../config/db.php';
require_once '../config/auth.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not authenticated
redirectIfNotAuthenticated('login.php');

$response = ['success' => false, 'message' => ''];
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $publication_year = $_POST['publication_year'];
    $available_copies = $_POST['available_copies'];

    // Prepare SQL statement to insert book data
    $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, publication_year, available_copies) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $title, $author, $isbn, $publication_year, $available_copies);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Book added successfully!';
    } else {
        $response['message'] = 'Error: ' . $stmt->error;
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>