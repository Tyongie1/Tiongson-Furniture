<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_image'])) {
    $image_name = $_FILES['profile_image']['name'];
    $image_tmp = $_FILES['profile_image']['tmp_name'];
    $image_path = "uploads/" . time() . "_" . $image_name;

    if (move_uploaded_file($image_tmp, $image_path)) {
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->bind_param("si", $image_path, $user_id);
        $stmt->execute();
    }
}

// Handle bio update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_bio'])) {
    $bio = trim($_POST['bio']);
    $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt->bind_param("si", $bio, $user_id);
    $stmt->execute();
}

// Fetch user data
$stmt = $conn->prepare("SELECT username, profile_image, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
            background: linear-gradient(90deg, rgba(0, 0, 0, 0.9), rgba(50, 50, 50, 0.8));
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

        /* Form Container with glassmorphism */
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            margin: 50px auto;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .container h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 20px;
            margin: 20px 0 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Profile Picture Display */
        .profile-pic {
            margin: 20px 0;
        }

        .profile-pic img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f5a623;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .profile-pic p {
            font-size: 16px;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Bio Display */
        .bio-display {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 14px;
            text-align: left;
        }

        .bio-display p {
            margin: 0;
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
        input[type="email"],
        input[type="password"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 12px;
            margin: 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        textarea {
            resize: none;
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
                margin: 20px;
                padding: 20px;
            }

            .navbar a {
                margin: 5px;
                display: inline-block;
            }

            .profile-pic img {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="orders.php">HOME</a>
            <a href="orders.php">ORDERS</a>
            <a href="addproducts.php">PRODUCTS</a>
        <?php else: ?>
            <a href="user_index.php">HOME</a>
            <a href="cart.php">CART</a>
            <a href="checkouts.php">CHECK-OUTS</a>
        <?php endif; ?>
        <a href="profile.php">PROFILE</a>
        <a href="logout.php">LOG-OUT</a>
    </nav>

    <div class="container">
        <h2>Profile</h2>

        <!-- Profile Picture Display Section -->
        <div class="profile-pic">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image">
            <?php else: ?>
                <p>No profile picture uploaded.</p>
            <?php endif; ?>
        </div>

        <h3>Profile Picture</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="profile_image" accept="image/*" required>
            <button type="submit" name="upload_image">Upload</button>
        </form>

        <h3>User Info</h3>
        <p><strong>ID:</strong> <?= $user_id ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>

        <!-- Bio Display Section -->
        <h3>Bio</h3>
        <div class="bio-display">
            <?php if (!empty($user['bio'])): ?>
                <p><?= htmlspecialchars($user['bio']) ?></p>
            <?php else: ?>
                <p>No bio set.</p>
            <?php endif; ?>
        </div>

        <h3>Update Bio</h3>
        <form method="post">
            <textarea name="bio" rows="4" cols="30"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea><br>
            <button type="submit" name="update_bio">Update Bio</button>
        </form>
    </div>
</body>
</html>