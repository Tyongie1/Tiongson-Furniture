<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    $target_dir = "uploads/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name;
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    $stmt = $conn->prepare("INSERT INTO products (name, type, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $name, $type, $price, $quantity, $target_file);
    $stmt->execute();
    header("Location: admin_products.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . time() . "_" . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

        $stmt = $conn->prepare("UPDATE products SET name=?, type=?, price=?, quantity=?, image=? WHERE id=?");
        $stmt->bind_param("ssdssi", $name, $type, $price, $quantity, $target_file, $id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, type=?, price=?, quantity=? WHERE id=?");
        $stmt->bind_param("ssdii", $name, $type, $price, $quantity, $id);
    }
    $stmt->execute();
    header("Location: admin_products.php");
    exit();
}

if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header("Location: admin_products.php");
    exit();
}

$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="admin_index.php">HOME</a>
        <a href="orders.php">ORDERS</a>
        <a href="admin_products.php">PRODUCTS</a>
        <a href="admin_users.php">USERS</a>
        <a href="profile.php">PROFILE</a>
        <a href="logout.php">LOG-OUT</a>
    </nav>

    <div class="container">
        <h2>Product Management</h2>
        <h3>Add Product</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <select name="type">
                <option value="chair">Chair</option>
                <option value="table">Table</option>
                <option value="bed">Bed</option>
                <option value="cabinet">Cabinet</option>
                <option value="door">Door</option>
            </select>
            <input type="number" name="price" placeholder="Price" step="0.01" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit" name="add_product">Add Product</button>
        </form>

        <h3>Available Products</h3>
        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Type</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $products->fetch_assoc()): ?>
            <tr>
                <td><img src="<?= $row['image'] ?>" width="80" height="80" class="product-img"></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['type']) ?></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>
                    <form method="post" enctype="multipart/form-data" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                        <select name="type">
                            <option value="chair" <?= $row['type'] == 'chair' ? 'selected' : '' ?>>Chair</option>
                            <option value="table" <?= $row['type'] == 'table' ? 'selected' : '' ?>>Table</option>
                            <option value="bed" <?= $row['type'] == 'bed' ? 'selected' : '' ?>>Bed</option>
                            <option value="cabinet" <?= $row['type'] == 'cabinet' ? 'selected' : '' ?>>Cabinet</option>
                            <option value="door" <?= $row['type'] == 'door' ? 'selected' : '' ?>>Door</option>
                        </select>
                        <input type="number" name="price" value="<?= $row['price'] ?>" step="0.01" required>
                        <input type="number" name="quantity" value="<?= $row['quantity'] ?>" required>
                        <input type="file" name="image" accept="image/*">
                        <button type="submit" name="edit_product">Update</button>
                    </form>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>