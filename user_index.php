<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - Home</title>
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

        /* Container for Table */
        .container {
            width: 80%;
            margin: 20px auto;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Heading */
        h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            color: #fff;
            margin: 20px 0;
            text-align: center;
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

        .product-img {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .product-img:hover {
            transform: scale(1.1);
        }

        /* Buttons */
        button {
            padding: 8px 15px;
            background: linear-gradient(45deg, #f5a623, #e69500);
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 14px;
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

        button:disabled {
            background: #666;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            margin: 10% auto;
            padding: 20px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            color: #fff;
            animation: fadeIn 0.5s ease-in-out;
        }

        .modal-content h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        .modal-content p {
            font-size: 16px;
            margin: 10px 0;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .modal-content img {
            border-radius: 10px;
            margin: 10px 0;
        }

        .close {
            color: #ff4d4d;
            font-size: 24px;
            float: right;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #ff6666;
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

            .modal-content {
                width: 90%;
                margin: 20% auto;
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
        <h2>Available Products</h2>
        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Type</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $products->fetch_assoc()): ?>
            <tr>
                <td>
                    <img src="<?= $row['image'] ?>" width="100" height="100" class="product-img" onclick="openModal('<?= $row['id'] ?>', '<?= $row['name'] ?>', '<?= $row['type'] ?>', '<?= $row['price'] ?>', '<?= $row['quantity'] ?>', '<?= $row['image'] ?>')">
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['type']) ?></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>
                    <?php if ($row['quantity'] > 0): ?>
                        <form method="post" action="cart.php">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="add_to_cart">Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <button disabled>Out of Stock</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <h2 id="modalName"></h2>
            <img id="modalImage" width="200" height="200">
            <p><strong>Type:</strong> <span id="modalType"></span></p>
            <p><strong>Price:</strong> ₱<span id="modalPrice"></span></p>
            <p><strong>Quantity:</strong> <span id="modalQuantity"></span></p>
            <form method="post" action="cart.php">
                <input type="hidden" id="modalProductId" name="product_id">
                <button type="submit" id="modalAddToCart" name="add_to_cart">Add to Cart</button>
            </form>
            <br>
            <form method="post" action="checkout.php">
                <input type="hidden" id="modalCheckoutProductId" name="product_id">
                <button type="submit" id="modalBuyNow" name="buy_now">Buy Now</button>
            </form>
        </div>
    </div>

    <div class="footer">
        <p>© 2023 Tyongkoy Furnitures. All rights reserved.</p>
    </div>

    <script>
        function openModal(id, name, type, price, quantity, image) {
            document.getElementById("modalName").innerText = name;
            document.getElementById("modalType").innerText = type;
            document.getElementById("modalPrice").innerText = price;
            document.getElementById("modalQuantity").innerText = quantity;
            document.getElementById("modalImage").src = image;
            document.getElementById("modalProductId").value = id;
            document.getElementById("modalCheckoutProductId").value = id;

            let addToCartButton = document.getElementById("modalAddToCart");
            let buyNowButton = document.getElementById("modalBuyNow");

            if (quantity == 0) {
                addToCartButton.disabled = true;
                buyNowButton.disabled = true;
                addToCartButton.innerText = "Out of Stock";
                buyNowButton.innerText = "Out of Stock";
            } else {
                addToCartButton.disabled = false;
                buyNowButton.disabled = false;
                addToCartButton.innerText = "Add to Cart";
                buyNowButton.innerText = "Buy Now";
            }

            document.getElementById("productModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("productModal").style.display = "none";
        }
    </script>
</body>
</html>