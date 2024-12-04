<?php
// Start session
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Library Management System</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class="centered">
    <div class="form-container">
        <h2>Login</h2>
        <!-- Login form -->
        <form method="POST" action="process_login.php">
            <label for="loginEmail">Email:</label>
            <input type="email" id="loginEmail" name="email" required><br>
            <label for="loginPassword">Password:</label>
            <input type="password" id="loginPassword" name="password" required><br>
            <button type="submit">Login</button>
        </form>
        <div id="loginMessage">
            <!-- Display login message if set in session -->
            <?php
            if (isset($_SESSION['message'])) {
                echo $_SESSION['message'];
                unset($_SESSION['message']);
            }
            ?>
        </div>
    </div>
</body>
</html>