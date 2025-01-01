<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "mokti_login");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'];
    $product_id = $_POST['product_id'];
    $product_name = $_POST['name'];
    $price = $_POST['price'];

    // Check if the product already exists in the user's cart
    $query = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $query->bind_param("ii", $user_id, $product_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // If product exists, update quantity
        $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $update->bind_param("ii", $user_id, $product_id);
        $update->execute();
    } else {
        // Add new product to cart
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, 1)");
        $insert->bind_param("iisd", $user_id, $product_id, $product_name, $price);
        $insert->execute();
    }

    $message = "Product added to cart!";
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Added</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color:rgb(121, 7, 7); /* Change this to customize the background color */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: 300px;
        }
        .container h1 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .container p {
            font-size: 1rem;
            margin-bottom: 20px;
        }
        .container a {
            display: inline-block;
            padding: 10px 20px;
            background-color:rgb(162, 15, 15);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
        }
        .container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo isset($message) ? $message : ""; ?></h1>
        <a href="browse_product.php">Back to Shopping</a>
    </div>
</body>
</html>
