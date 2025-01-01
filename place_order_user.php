<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "mokti_login");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'];
    $items = $_POST['items'];
    $subtotal = $_POST['subtotal'];

    // Insert order into the orders table
    $query = $conn->prepare("INSERT INTO orders (user_id, items, subtotal) VALUES (?, ?, ?)");
    $query->bind_param("isd", $user_id, $items, $subtotal);
    $query->execute();

    // Clear the cart
    $clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear->bind_param("i", $user_id);
    $clear->execute();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        /* Dark Red Theme */
        body {
            background: linear-gradient(145deg, #8B0000, #A52A2A); /* Dark red gradient background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
            color: #fff;
        }

        .container {
            background-color: rgba(0, 0, 0, 0.7); 
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 85%;
            max-width: 600px;
            text-align: center;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #fff;
        }

        p {
            font-size: 1.5em;
            margin: 20px 0;
            color: #FFD700; /* Gold color for success message */
        }

        button {
            padding: 10px 20px;
            font-size: 1.2em;
            background-color: #D32F2F; /* Darker red for the button */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #B71C1C; /* Slightly darker red on hover */
        }

        .back-to-shop {
            font-size: 1.2em;
            color: #FFD700;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }

        .back-to-shop:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Confirmation</h1>
        <p>Thank you for your order! Your items will be processed shortly.</p>
        <p>Your total is: $<?php echo number_format($subtotal, 2); ?></p>
        <form action="signup.html" method="get">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
    <button type="submit">Make Payment</button>
</form>
        <a href="browse_product_user.php" class="back-to-shop">Back to Shopping</a>
       
    </div>
</body>
</html>
