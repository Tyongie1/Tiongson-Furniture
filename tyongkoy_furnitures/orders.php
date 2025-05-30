<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

function sendEmail($to, $subject, $message) {
    $headers = "From: tyongkoy@furnitures.com\r\n";
    $headers .= "Reply-To: tyongkoy@furnitures.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    mail($to, $subject, $message, $headers);
}

if (isset($_POST['update_order'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['update_order'];

    $stmt = $conn->prepare("
        SELECT checkouts.*, users.email, users.username, products.name 
        FROM checkouts 
        JOIN users ON checkouts.user_id = users.id
        JOIN products ON checkouts.product_id = products.id
        WHERE checkouts.id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if ($order) {
        $stmt = $conn->prepare("UPDATE checkouts SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();

        $to = $order['email'];
        $subject = "Your Order Status - Tyongkoy Furnitures";
        $message = "
            <h3>Hello, " . htmlspecialchars($order['username']) . "!</h3>
            <p>Your order for <strong>" . htmlspecialchars($order['name']) . "</strong> has been <strong>$new_status</strong>.</p>
            <p>Thank you for shopping with us!</p>
            <p>- Tyongkoy Furnitures Team</p>
        ";
        sendEmail($to, $subject, $message);
    }
}

$orders = $conn->query("
    SELECT checkouts.*, users.username, users.email, products.name, products.image, products.price 
    FROM checkouts 
    JOIN users ON checkouts.user_id = users.id
    JOIN products ON checkouts.product_id = products.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
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
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            border: 2px solid #f5a623;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Action Buttons */
        .action-btn {
            padding: 8px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .approve-btn {
            background: linear-gradient(45deg, #28a745, #218838);
            color: #fff;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.5);
        }

        .approve-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.7);
        }

        .disapprove-btn {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: #fff;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.5);
        }

        .disapprove-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.7);
        }

        /* Status Text */
        .status-approved {
            color: #28a745;
            font-weight: 600;
        }

        .status-disapproved {
            color: #dc3545;
            font-weight: 600;
        }

        .status-pending {
            color: #f5a623;
            font-weight: 600;
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

            .product-img {
                width: 80px;
                height: 80px;
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
        <h2>Admin Dashboard - Orders</h2>
        <h3>Pending Orders</h3>
        <table>
            <tr>
                <th>User</th>
                <th>Product</th>
                <th>Image</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $orders->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><img src="<?= htmlspecialchars($row['image']) ?>" alt="Product Image" class="product-img"></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td class="status-<?= strtolower($row['status']) ?>">
                    <?= htmlspecialchars($row['status']) ?>
                </td>
                <td>
                    <?php if ($row['status'] == "Pending"): ?>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="update_order" value="Approved" class="action-btn approve-btn">Approve</button>
                            <button type="submit" name="update_order" value="Disapproved" class="action-btn disapprove-btn">Disapprove</button>
                        </form>
                    <?php else: ?>
                        <span class="status-<?= strtolower($row['status']) ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>