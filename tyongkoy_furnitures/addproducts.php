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
    header("Location: addproducts.php");
    exit();
}

if (isset($_GET['delete_product'])) {
    $product_id = intval($_GET['delete_product']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header("Location: addproducts.php");
    exit();
}

$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Open+Sans:wght@300;400&display=swap" rel="stylesheet">
    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Open Sans', Arial, sans-serif;
        }

        /* Body with wooden background and gradient overlay */
        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('bg1.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Navigation Bar */
        .navbar {
            width: 100%;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar a {
            position: relative;
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            text-transform: uppercase;
            font-weight: 400;
            transition: color 0.3s ease;
        }

        .navbar a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: #f5a623;
            left: 0;
            bottom: -5px;
            transition: width 0.3s ease;
        }

        .navbar a:hover {
            color: #f5a623;
        }

        .navbar a:hover::after {
            width: 100%;
        }

        /* Container for Content */
        .container {
            width: 80%;
            margin: 20px auto;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
            text-align: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Headings */
        h2, h3 {
            font-family: 'Poppins', sans-serif;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        h3 {
            font-size: 20px;
            margin: 20px 0 10px;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .form-group label {
            font-size: 16px;
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 12px;
            margin: 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: rgba(255, 255, 255, 0.9) url('data:image/svg+xml;utf8,<svg fill="black" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center;
            background-size: 16px;
        }

        button {
            padding: 12px 20px;
            background: linear-gradient(45deg, #f5a623, #e69500);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(245, 166, 35, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(245, 166, 35, 0.7);
        }

        /* Table */
        table {
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            border-collapse: collapse;
            color: #333;
            border-radius: 10px;
            overflow: hidden;
        }

        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background: linear-gradient(45deg, #f5a623, #e69500);
            color: #fff;
            text-transform: uppercase;
            font-weight: 600;
        }

        table td {
            font-size: 14px;
        }

        /* Product Image Styling */
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            border: 2px solid #f5a623;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Action Buttons */
        .action-btn {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .edit-btn {
            background: linear-gradient(45deg, #f5a623, #e69500);
            color: #fff;
            box-shadow: 0 4px 15px rgba(245, 166, 35, 0.5);
        }

        .edit-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(245, 166, 35, 0.7);
        }

        .delete-btn {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: #fff;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.5);
        }

        .delete-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.7);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                background-attachment: scroll;
            }

            .container {
                width: 90%;
                margin: 10px auto;
                padding: 20px;
            }

            table th, table td {
                padding: 8px;
                font-size: 12px;
            }

            .product-img {
                width: 60px;
                height: 60px;
            }

            .action-btn {
                padding: 6px 10px;
                font-size: 12px;
            }

            .navbar a {
                margin: 5px;
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="orders.php">HOME</a>
        <a href="orders.php">ORDERS</a>
        <a href="addproducts.php">PRODUCTS</a>
        <a href="admin_users.php">USERS</a>
        <a href="profile.php">PROFILE</a>
        <a href="logout.php">LOG-OUT</a>
    </nav>

    <div class="container">
        <h2>Manage Products</h2>
        <h3>Add Product</h3>
        <form action="addproducts.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="name" placeholder="Product Name" required>
            </div>
            <div class="form-group">
                <select name="type">
                    <option value="chair">Chair</option>
                    <option value="table">Table</option>
                    <option value="bed">Bed</option>
                    <option value="cabinet">Cabinet</option>
                    <option value="door">Door</option>
                </select>
            </div>
            <div class="form-group">
                <input type="number" name="price" placeholder="Price" step="0.01" required>
            </div>
            <div class="form-group">
                <input type="number" name="quantity" placeholder="Available Quantity" required>
            </div>
            <div class="form-group">
                <input type="file" name="image" accept="image/*" required>
            </div>
            <button type="submit" name="add_product">Add Product</button>
        </form>

        <h3>Available Products</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Type</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $products->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><img src="<?= htmlspecialchars($row['image']) ?>" alt="Product Image" class="product-img"></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['type']) ?></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $row['id'] ?>" class="action-btn edit-btn">Edit</a>
                    <a href="?delete_product=<?= $row['id'] ?>" onclick="return confirm('Are you sure?');" class="action-btn delete-btn">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>