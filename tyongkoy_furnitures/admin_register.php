<?php
session_start();
include 'db_connect.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $role = trim($_POST['role']); // Allow role selection

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username or email already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);
        if ($stmt->execute()) {
            header("Location: admin_users.php");
            exit();
        } else {
            $error = "Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New User (Admin)</title>
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

        .container p {
            font-size: 16px;
            margin-bottom: 15px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .container a {
            color: #f5a623;
            text-decoration: none;
            font-weight: 600;
        }

        .container a:hover {
            text-decoration: underline;
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
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
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

        /* Error Message */
        .error-message {
            color: #ff4d4d;
            font-size: 14px;
            margin-top: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
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
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register New User (Admin)</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form action="admin_register.php" method="post" autocomplete="off">
            <input type="text" name="username" required placeholder="Username" autocomplete="off"><br>
            <input type="email" name="email" required placeholder="Email" autocomplete="off"><br>
            <input type="password" name="password" required placeholder="Password" autocomplete="off"><br>
            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select><br>
            <button type="submit">Register</button>
        </form>
        <p><a href="admin_users.php">Back to User Management</a></p>
    </div>
</body>
</html>