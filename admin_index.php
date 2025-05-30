<?php
session_start();
include 'db_connect.php';

// Fetch featured products (e.g., Modern Chair and Dining Table)
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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