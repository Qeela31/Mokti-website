<?php
session_start(); // Start the session

// Mock role assignment for demonstration
$_SESSION['role'] = $_SESSION['role'] ?? 'guest'; // Default to 'guest'

// Mock User ID (In a real system, retrieve this from the login session)
$user_id = 1;

// Database connection
$host = 'localhost';   // Your database host (usually localhost)
$db = 'mokti_login';   // Your database name
$user = 'root';        // Your MySQL username (usually 'root' for local development)
$pass = '';            // Your MySQL password (blank if not set for local dev)

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch all products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Retrieve search query and category filter from the URL
$search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Filter products based on search and category
$filtered_products = array_filter($products, function ($product) use ($search, $category) {
    $matches_search = empty($search) || strpos(strtolower($product['name']), $search) !== false;
    $matches_category = empty($category) || $product['category'] === $category;
    return $matches_search && $matches_category;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mokti Shopping Page</title>
    <style>
       /* Dark Red Theme */
        body {
            background: linear-gradient(145deg, #8B0000, #A52A2A);
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
            color: #fff;
        }

        .product {
            background-color: #4B0000;
            padding: 20px;
            margin: 10px 0;
            border-radius: 10px;
        }

        .product h3 {
            color: #FFD700;
        }

        button {
    margin-top: 10px;
    padding: 10px 20px;
    font-size: 1em;
    font-weight: bold;
    background-color: #FFD700; /* Bright yellow background */
    color: #8B0000; /* Dark red text */
    border: none;
    border-radius: 15px; /* Rounded corners */
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3); /* Adds a shadow for a lifted effect */
    transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button:hover {
    background-color: #FFC107; /* Slightly brighter yellow on hover */
    transform: scale(1.05); /* Enlarges the button slightly */
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.4); /* Darker shadow for hover effect */
}

        .button-container {
            margin-top: 20px;
        }

        .button-container a {
            color: #FFD700;
            font-size: 1.2em;
            text-decoration: none;
            border-radius: 20px;
            padding: 5px 15px;
            margin: 0 10px;
        }

        .button-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mokti Shopping Page</h1>

        <!-- Filter Form -->
        <form method="GET" action="">
            <label for="search">Search: </label>
            <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>">

            <label for="category">Category: </label>
            <select name="category" id="category">
                <option value="">All Categories</option>
                <option value="soft-serve" <?php echo $category === 'soft-serve' ? 'selected' : ''; ?>>soft-serve</option>
                <option value="tub" <?php echo $category === 'tub' ? 'selected' : ''; ?>>tub</option>
                <option value="ice-pop" <?php echo $category === 'ice-pop' ? 'selected' : ''; ?>>ice-pop</option>
            </select>

            <button type="submit">Filter</button>
        </form>

        <!-- Display Products -->
        <?php if (empty($filtered_products)): ?>
            <p>No products found!</p>
        <?php else: ?>
            <?php foreach ($filtered_products as $product): ?>
                <div class="product">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                    <p>Category: <?php echo htmlspecialchars($product['category']); ?></p>

                    <!-- Add to Cart Form -->
                    <form method="POST" action="add_to_cart_user.php">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Navigation Buttons -->
        <div class="button-container">
            <a href="cart_user.php">Go to Cart</a>
            <a href="verymainpage.html">Back to Menu</a>
        </div>
    </div>
</body>
</html>
