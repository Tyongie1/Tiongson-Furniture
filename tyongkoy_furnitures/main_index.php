<?php
session_start();
include 'db_connect.php';

// Fetch featured products (e.g., Square Table and Solo Chair)
$stmt = $conn->prepare("SELECT * FROM products LIMIT 2");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tyongkoy Furnitures</title>
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

        /* Header (Logo and Navigation) */
        header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            background: linear-gradient(90deg, rgba(0, 0, 0, 0.9), rgba(50, 50, 50, 0.8));
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 600;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        nav {
            display: flex;
            gap: 20px;
        }

        nav a {
            position: relative;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            text-transform: uppercase;
            font-weight: 400;
            transition: color 0.3s ease;
        }

        nav a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: #f5a623;
            left: 0;
            bottom: -5px;
            transition: width 0.3s ease;
        }

        nav a:hover {
            color: #f5a623;
        }

        nav a:hover::after {
            width: 100%;
        }

        /* Welcome Section with glassmorphism and animation */
        .welcome {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            margin: 50px 0;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .welcome h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 40px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .welcome p {
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Featured Furniture Section */
        .featured {
            text-align: center;
            margin-bottom: 50px;
        }

        .featured h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .furniture-grid {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .furniture-item {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            width: 220px;
            text-align: center;
            color: #333;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 2px solid transparent;
            background-clip: padding-box;
            position: relative;
        }

        .furniture-item::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #f5a623, #e69500);
            z-index: -1;
            border-radius: 15px;
        }

        .furniture-item:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .furniture-item img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .furniture-item p {
            padding: 15px;
            font-size: 16px;
            color: #6a0dad;
            font-weight: 600;
        }

        /* Sign Up Button with gradient and glow */
        .signup-btn {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(45deg, #f5a623, #e69500);
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(245, 166, 35, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .signup-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(245, 166, 35, 0.7);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                background-attachment: scroll;
            }

            .furniture-grid {
                flex-direction: column;
                align-items: center;
            }

            .furniture-item {
                width: 80%;
            }

            .welcome {
                margin: 20px;
                padding: 20px;
            }

            header {
                flex-direction: column;
                padding: 10px;
            }

            nav a {
                margin: 5px;
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">TYONG FURNITURES</div>
        <nav>
            <a href="main_index.php">HOME</a>
            <a href="about.php">ABOUT</a>
            <a href="login.php">LOG IN</a>
            <a href="register.php">SIGN UP</a>
            <a href="admin_login.php">ADMIN</a>
        </nav>
    </header>

    <section class="welcome">
        <h1>Welcome to TYONG FURNITURES</h1>
        <p>Your one-stop shop for high-quality and stylish furniture. We provide modern, elegant, and durable furniture pieces to suit all your home and office needs.</p>
        <p>Discover our collection today and transform your space with TYONG FURNITURES!</p>
    </section>

    <section class="featured">
        <h2>Our Featured Furniture</h2>
        <div class="furniture-grid">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="furniture-item">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <p><?php echo $product['name']; ?></p>
                </div>
            <?php endwhile; ?>
        </div>
        <a href="register.php" class="signup-btn">SIGN UP TO ACCESS SHOP</a>
    </section>
</body>
</html>