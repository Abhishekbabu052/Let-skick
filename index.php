<?php
$conn = new mysqli("localhost", "root", "", "Book");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// INSERT
if (isset($_POST['insert'])) {
    $stmt = $conn->prepare("INSERT INTO books (image, name, author, publisher, genre, binding, year_of_publish, number_of_pages, language, description, price)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiissd", $_POST['image'], $_POST['name'], $_POST['author'], $_POST['publisher'], $_POST['genre'],
                      $_POST['binding'], $_POST['year_of_publish'], $_POST['number_of_pages'], $_POST['language'],
                      $_POST['description'], $_POST['price']);
    $stmt->execute();
    echo "<p style='color:green;'>Book inserted successfully!</p>";
    $stmt->close();
}

// DELETE
if (isset($_POST['delete'])) {
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $_POST['delete_id']);
    $stmt->execute();
    echo "<p style='color:green;'>Book deleted successfully!</p>";
    $stmt->close();
}

// UPDATE
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE books SET image=?, name=?, author=?, publisher=?, genre=?, binding=?, year_of_publish=?, number_of_pages=?, language=?, description=?, price=? WHERE id=?");
    $stmt->bind_param("ssssssiissdi", $_POST['image'], $_POST['name'], $_POST['author'], $_POST['publisher'], $_POST['genre'],
                      $_POST['binding'], $_POST['year_of_publish'], $_POST['number_of_pages'], $_POST['language'],
                      $_POST['description'], $_POST['price'], $_POST['update_id']);
    $stmt->execute();
    echo "<p style='color:green;'>Book updated successfully!</p>";
    $stmt->close();
}

// FETCH ALL BOOKS
$result = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Dashboard</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        input[type="text"], input[type="number"], textarea { width: 100%; }
        .form-section { margin-bottom: 40px; }
        .delete-btn { background-color: red; color: white; border: none; padding: 5px 10px; }
        .update-btn { background-color: #007BFF; color: white; border: none; padding: 5px 10px; }
    </style>
</head>
<body>
    <h1>📚 Book Dashboard</h1>

    <!-- Insert Form -->
    <div class="form-section">
        <h2>Insert New Book</h2>
        <form method="POST">
            <?php foreach (["image", "name", "author", "publisher", "genre", "binding", "year_of_publish", "number_of_pages", "language", "description", "price"] as $field): ?>
                <label><?= ucfirst(str_replace("_", " ", $field)) ?>:</label><br>
                <input type="<?= $field == "description" ? "textarea" : ($field == "price" || $field == "year_of_publish" || $field == "number_of_pages" ? "number" : "text") ?>"
                       name="<?= $field ?>" required><br><br>
            <?php endforeach; ?>
            <input type="submit" name="insert" value="Insert Book">
        </form>
    </div>

    <!-- Book Table -->
    <h2>All Books</h2>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Author</th><th>Publisher</th><th>Genre</th><th>Price</th><th>Actions</th>
        </tr>
        <?php while ($book = $result->fetch_assoc()): ?>
        <tr>
            <form method="POST">
                <td><?= $book['id'] ?><input type="hidden" name="update_id" value="<?= $book['id'] ?>"></td>
                <td><input type="text" name="name" value="<?= htmlspecialchars($book['name']) ?>"></td>
                <td><input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>"></td>
                <td><input type="text" name="publisher" value="<?= htmlspecialchars($book['publisher']) ?>"></td>
                <td><input type="text" name="genre" value="<?= htmlspecialchars($book['genre']) ?>"></td>
                <td><input type="number" step="0.01" name="price" value="<?= $book['price'] ?>"></td>
                <td>
                    <input type="hidden" name="image" value="<?= htmlspecialchars($book['image']) ?>">
                    <input type="hidden" name="binding" value="<?= htmlspecialchars($book['binding']) ?>">
                    <input type="hidden" name="year_of_publish" value="<?= $book['year_of_publish'] ?>">
                    <input type="hidden" name="number_of_pages" value="<?= $book['number_of_pages'] ?>">
                    <input type="hidden" name="language" value="<?= htmlspecialchars($book['language']) ?>">
                    <input type="hidden" name="description" value="<?= htmlspecialchars($book['description']) ?>">
                    <input type="submit" name="update" value="Update" class="update-btn">
                </td>
            </form>
            <form method="POST">
                <td colspan="7">
                    <input type="hidden" name="delete_id" value="<?= $book['id'] ?>">
                    <input type="submit" name="delete" value="Delete" class="delete-btn">
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
  
