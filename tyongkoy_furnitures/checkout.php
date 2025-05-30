<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
echo "Debug: User ID: $user_id<br>";

// Fetch cart items
$stmt = $conn->prepare("SELECT cart.*, products.price FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
if (!$stmt) {
    echo "Debug: Prepare failed: " . $conn->error . "<br>";
    exit();
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
            echo "Debug: Insert prepare failed: " . $conn->error . "<br>";
            continue;
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

    // Redirect to checkouts.php
    header("Location: checkouts.php");
    exit();
} else {
    echo "Your cart is empty.";
}
?>