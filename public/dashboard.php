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

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$user_stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_stmt->bind_result($name, $email);
$user_stmt->fetch();
$user_stmt->close();

// Fetch borrowed books for the authenticated user
$borrowed_books_stmt = $conn->prepare("SELECT bb.id, b.title, b.author, bb.borrowed_at, bb.due_date, bb.returned_at FROM borrowed_books bb JOIN books b ON bb.book_id = b.id WHERE bb.user_id = ?");
$borrowed_books_stmt->bind_param("i", $user_id);
$borrowed_books_stmt->execute();
$borrowed_books_stmt->bind_result($borrow_id, $book_title, $book_author, $borrowed_at, $due_date, $returned_at);
$borrowed_books = [];

// Store borrowed books in an array
while ($borrowed_books_stmt->fetch()) {
    $borrowed_books[] = [
        'id' => $borrow_id,
        'title' => $book_title,
        'author' => $book_author,
        'borrowed_at' => $borrowed_at,
        'due_date' => $due_date,
        'returned_at' => $returned_at
    ];
}
$borrowed_books_stmt->close();

// Fetch all books from the database
$books_stmt = $conn->query("SELECT id, title, author, publication_year, available_copies FROM books");
$books = [];
while ($row = $books_stmt->fetch_assoc()) {
    $books[] = $row;
}
$books_stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Library Management System</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class="centered">
    <div class="dashboard-container" style="width: 80%;">
        <h2>User Account Details</h2>
        <!-- Display user details -->
        <p>Name: <?php echo htmlspecialchars($name ?? ''); ?></p>
        <p>Email: <?php echo htmlspecialchars($email ?? ''); ?></p>

        <h2>Borrowed Books</h2>
        <!-- Display borrowed books if any -->
        <?php if (!empty($borrowed_books)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Borrowed At</th>
                        <th>Due Date</th>
                        <th>Returned At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Display borrowed books -->
                    <?php foreach ($borrowed_books as $borrowed_book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($borrowed_book['title']); ?></td>
                            <td><?php echo htmlspecialchars($borrowed_book['author']); ?></td>
                            <td><?php echo htmlspecialchars($borrowed_book['borrowed_at']); ?></td>
                            <td><?php echo htmlspecialchars($borrowed_book['due_date']); ?></td>
                            <td><?php echo htmlspecialchars($borrowed_book['returned_at'] ?? ''); ?></td>
                            <td>
                                <?php if (is_null($borrowed_book['returned_at'])): ?>
                                    <!-- Form to return borrowed book -->
                                    <form method="POST" action="return_book.php">
                                        <input type="hidden" name="borrow_id" value="<?php echo $borrowed_book['id']; ?>">
                                        <button type="submit" class="button">Return</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No borrowed books.</p>
        <?php endif; ?>

        <h2>Book Details</h2>
        <!-- Display all books -->
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Publication Year</th>
                    <th>Available Copies</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['publication_year']); ?></td>
                        <td><?php echo htmlspecialchars($book['available_copies']); ?></td>
                        <td>
                            <?php if ($book['available_copies'] > 0): ?>
                                <!-- Form to borrow a book -->
                                <form method="POST" action="borrow_book.php">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="button">Borrow</button>
                                </form>
                            <?php else: ?>
                                <span>Not Available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Button to go back to home page -->
        <button class="button" onclick="window.location.href='index.php'">Back to Home</button>
    </div>
</body>
</html>