<!DOCTYPE html>
<html>
<head>
    <title>Add Book - Library Management System</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent form from submitting the traditional way

                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'process_add_book.php', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        const messageDiv = document.getElementById('message');
                        messageDiv.textContent = response.message;
                        if (response.success) {
                            form.reset(); // Clear the form if the book was added successfully
                        }
                    }
                };
                xhr.send(formData);
            });
        });
    </script>
</head>
<body class="centered">
    <div class="addbook-container">
        <h2>Add Book</h2>
        <!-- Form to add a new book -->
        <form method="POST">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br>
            <label for="author">Author:</label>
            <input type="text" id="author" name="author" required><br>
            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" required><br>
            <label for="publication_year">Publication Year:</label>
            <input type="number" id="publication_year" name="publication_year" required><br>
            <label for="available_copies">Available Copies:</label>
            <input type="number" id="available_copies" name="available_copies" required><br>
            <button type="submit">Add Book</button>
        </form>
        <div id="message"></div>
    </div>
</body>
</html>