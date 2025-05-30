<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
echo "Debug: User ID: $user_id<br>";

// Process cart items if the checkout form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    echo "Debug: Checkout form submitted.<br>";
    // Fetch cart items
    $stmt = $conn->prepare("SELECT cart.*, products.price FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
    if (!$stmt) {
        die("Debug: Prepare failed for cart fetch: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo "Debug: Number of cart items found: " . $result->num_rows . "<br>";

    if ($result->num_rows > 0) {
        // Insert cart items into checkouts
        while ($item = $result->fetch_assoc()) {
            echo "Debug: Processing cart item - Product ID: " . $item['product_id'] . ", Quantity: " . $item['quantity'] . "<br>";
            $stmt = $conn->prepare("INSERT INTO checkouts (user_id, product_id, quantity, status) VALUES (?, ?, ?, 'Pending')");
            if (!$stmt) {
                die("Debug: Insert prepare failed: " . $conn->error);
            }
            $stmt->bind_param("iii", $user_id, $item['product_id'], $item['quantity']);
            if ($stmt->execute()) {
                echo "Debug: Successfully inserted checkout for Product ID: " . $item['product_id'] . "<br>";
            } else {
                echo "Debug: Insert failed: " . $stmt->error . "<br>";
            }
        }

        // Clear the cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo "Debug: Cart cleared successfully.<br>";
        } else {
            echo "Debug: Cart clear failed: " . $stmt->error . "<br>";
        }

        // Redirect to avoid re-processing on refresh
        header("Location: checkouts.php");
        exit();
    } else {
        echo "Debug: No cart items found to process.<br>";
    }
}

// Fetch user's orders from checkouts table
$stmt = $conn->prepare("SELECT checkouts.*, products.name, products.price FROM checkouts JOIN products ON checkouts.product_id = products.id WHERE checkouts.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
echo "Debug: Number of orders found: " . $orders->num_rows . "<br>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Summary</title>
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
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
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
            margin-bottom: 10px;
        }

        h3 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        /* No Orders Message */
        .no-orders {
            font-size: 16px;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Table */
        table {
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            border-collapse: collapse;
            color: #333;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background: #f5a623;
            color: #fff;
            text-transform: uppercase;
            font-weight: 600;
        }

        table td {
            font-size: 14px;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            width: 100%;
            text-align: center;
        }

        .footer p {
            color: #fff;
            font-size: 14px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                background-attachment: scroll;
            }

            .container {
                width: 90%;
                margin: 10px auto;
                padding: 10px;
            }

            table th, table td {
                padding: 8px;
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
        <a href="user_index.php">HOME</a>
        <a href="cart.php">CART</a>
        <a href="checkouts.php">CHECK-OUTS</a>
        <a href="profile.php">PROFILE</a>
        <a href="logout.php">LOG-OUT</a>
    </nav>

    <div class="container">
        <h2>Order Status</h2>
        <h3>Your Orders</h3>
        <?php if ($orders->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
                <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($order['name']) ?></td>
                    <td><?= $order['quantity'] ?></td>
                    <td>₱<?= number_format($order['price'], 2) ?></td>
                    <td>₱<?= number_format($order['price'] * $order['quantity'], 2) ?></td>
                    <td><?= ucfirst(htmlspecialchars($order['status'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-orders">No orders found.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>© 2023 Tyongkoy Furnitures. All rights reserved.</p>
    </div>
</body>
</html>