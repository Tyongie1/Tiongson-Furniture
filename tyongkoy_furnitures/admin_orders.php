<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['approve'])) {
    $order_id = intval($_GET['approve']);
    $stmt = $conn->prepare("UPDATE checkouts SET status='Approved' WHERE id=?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    header("Location: admin_orders.php");
    exit();
} elseif (isset($_GET['disapprove'])) {
    $order_id = intval($_GET['disapprove']);
    $stmt = $conn->prepare("UPDATE checkouts SET status='Disapproved' WHERE id=?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    header("Location: admin_orders.php");
    exit();
}

$orders = $conn->query("SELECT checkouts.id, users.username, products.name, checkouts.quantity, checkouts.status 
                        FROM checkouts
                        JOIN users ON checkouts.user_id = users.id
                        JOIN products ON checkouts.product_id = products.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    <nav>
        <a href="admin_index.php">HOME</a>
        <a href="admin_orders.php">ORDERS</a>
        <a href="addproducts.php">PRODUCTS</a>
        <a href="admin_users.php">USERS</a>
        <a href="profile.php">PROFILE</a>
        <a href="logout.php">LOG-OUT</a>
    </nav>

    <div class="container">
        <h2>Order Management</h2>
        <h3>Pending Orders</h3>
        <table>
            <tr>
                <th>User</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $orders->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <?php if ($row['status'] == 'Pending'): ?>
                        <a href="?approve=<?= $row['id'] ?>">Approve</a> | 
                        <a href="?disapprove=<?= $row['id'] ?>">Disapprove</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>