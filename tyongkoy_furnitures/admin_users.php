<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role='user'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: admin_users.php");
    exit();
}

$users = $conn->query("SELECT id, username FROM users WHERE role='user'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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

        /* Delete Button */
        .delete-btn {
            display: inline-block;
            padding: 8px 15px;
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
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
                padding: 10px;
            }

            table th, table td {
                padding: 8px;
                font-size: 12px;
            }

            .delete-btn {
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
        <a href="admin_products.php">PRODUCTS</a>
        <a href="admin_users.php">USERS</a>
        <a href="profile.php">PROFILE</a>
        <a href="logout.php">LOG-OUT</a>
    </nav>

    <div class="container">
        <h2>User Management</h2>
        <h3>Registered Users</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')" class="delete-btn">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>