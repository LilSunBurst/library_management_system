<?php
// Start session
session_start();

// Include database configuration file
require_once '../config/db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Management System</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to the Library Management System</h1>
        <img src="images/library.png" alt="Library" class="banner">
        <p>
            <!-- Check if user is logged in -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Links for logged-in users -->
                <a class="button" href="add_book.php">Add Book</a>
                <a href="dashboard.php" class="button">Dashboard</a>
                <a class="button" href="logout.php">Logout</a>
            <?php else: ?>
                <!-- Links for guests -->
                <a class="button" href="register.php">Register</a>
                <a class="button" href="login.php">Login</a>
            <?php endif; ?>
        </p>
        
        <!-- Book Search Form -->
        <div class="search-container">
            <h2>Book Search</h2>
            <form method="GET" action="index.php">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" required>
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Display search results if any -->
        <?php if (isset($_GET['search'])): ?>
            <?php
            // Get search term from form
            $searchTerm = $_GET['search'];
            
            // Prepare SQL statement to search books by title or author
            $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ?");
            $searchTermWildcard = "%" . $searchTerm . "%";
            $stmt->bind_param("ss", $searchTermWildcard, $searchTermWildcard);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <div class="search-results">
                <h2>Search Results</h2>
                <!-- Check if any results were found -->
                <?php if ($result->num_rows > 0): ?>
                    <ul>
                        <!-- Display each search result -->
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li><a href="book_details.php?id=<?php echo $row['id']; ?>"><?php echo "Title: " . htmlspecialchars($row['title']) . " | Author: " . htmlspecialchars($row['author']); ?></a></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No books found.</p>
                <?php endif; ?>
                <?php $stmt->close(); ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>