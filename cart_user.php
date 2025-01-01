<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "mokti_login");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = 1; // Mock User ID

// Handle product removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_product_id'])) {
    $remove_product_id = $_POST['remove_product_id'];
    $query = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $query->bind_param("ii", $user_id, $remove_product_id);
    $query->execute();
}

// Fetch cart items for the user
$query = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

$subtotal = 0;
$items = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
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
            max-width: 800px;
            text-align: center;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #fff; /* White heading for contrast */
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #6B0000;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            font-size: 1.1em;
        }

        th {
            background-color: #5C1A1A;
            color: #fff;
        }

        td {
            background-color: #4B0000;
            color: #fff;
        }

        button {
            padding: 10px 20px;
            font-size: 1.2em;
            background-color: #D32F2F; /* Darker red for buttons */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #B71C1C; /* Slightly darker red on hover */
        }

        a {
            color: #FFD700;
            font-size: 1.2em;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }

        a:hover {
            text-decoration: underline;
        }

        .subtotal {
            font-size: 1.5em;
            margin-top: 20px;
            color: #FFD700; /* Gold color for subtotal */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Cart</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>$<?php echo number_format($row['price'] * $row['quantity'], 2); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="remove_product_id" value="<?php echo $row['product_id']; ?>">
                                <button type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                    $subtotal += $row['price'] * $row['quantity'];
                    $items[] = $row['product_name'] . " (x" . $row['quantity'] . ")";
                    ?>
                <?php endwhile; ?>
            </table>
            <div class="subtotal">
                <h2>Subtotal: $<?php echo number_format($subtotal, 2); ?></h2>
            </div>
            <form action="place_order_user.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="items" value="<?php echo implode(", ", $items); ?>">
                <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                <button type="submit">Place Order</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty!</p>
        <?php endif; ?>
        <a href="browse_product_user.php">Back to Shopping</a>
    </div>
</body>
</html>
<?php $conn->close(); ?>
