<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $conn->query("SELECT * FROM products WHERE id = '$product_id'")->fetch_assoc();

if (!$product) {
    header("Location: addproducts.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
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
        $stmt->bind_param("ssdssi", $name, $type, $price, $quantity, $target_file, $product_id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, type=?, price=?, quantity=? WHERE id=?");
        $stmt->bind_param("ssdii", $name, $type, $price, $quantity, $product_id);
    }
    $stmt->execute();
    header("Location: addproducts.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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
            max-width: 500px;
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
        h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
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

            input[type="text"],
            input[type="number"],
            input[type="file"],
            select {
                padding: 10px;
                font-size: 14px;
            }

            button {
                padding: 10px 15px;
                font-size: 14px;
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
        <a href="admin_index.php">HOME</a>
        <a href="orders.php">ORDERS</a>
        <a href="addproducts.php">PRODUCTS</a>
        <a href="admin_users.php">USERS</a>
        <a href="profile.php">PROFILE</a>
        <a href="logout.php">LOG-OUT</a>
    </nav>

    <div class="container">
        <h2>Edit Product</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            <div class="form-group">
                <select name="type">
                    <option value="chair" <?= $product['type'] == 'chair' ? 'selected' : '' ?>>Chair</option>
                    <option value="table" <?= $product['type'] == 'table' ? 'selected' : '' ?>>Table</option>
                    <option value="bed" <?= $product['type'] == 'bed' ? 'selected' : '' ?>>Bed</option>
                    <option value="cabinet" <?= $product['type'] == 'cabinet' ? 'selected' : '' ?>>Cabinet</option>
                    <option value="door" <?= $product['type'] == 'door' ? 'selected' : '' ?>>Door</option>
                </select>
            </div>
            <div class="form-group">
                <input type="number" name="price" value="<?= $product['price'] ?>" step="0.01" required>
            </div>
            <div class="form-group">
                <input type="number" name="quantity" value="<?= $product['quantity'] ?>" required>
            </div>
            <div class="form-group">
                <input type="file" name="image" accept="image/*">
            </div>
            <button type="submit" name="edit_product">Update Product</button>
        </form>
    </div>
</body>
</html>