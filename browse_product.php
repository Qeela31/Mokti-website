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

// Handle product deletion (Admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id'])) {
    if ($_SESSION['role'] !== 'admin') {
        header("Location: unauthorized_access.php");
        exit;
    }

    $delete_product_id = $_POST['delete_product_id'];

    // Prepare the SQL query to delete the product from the database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_product_id);  // Bind the product ID
    $stmt->execute();
    $stmt->close();

    // Refresh the page to show the updated list of products
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle adding a new product (Admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['price']) && isset($_POST['category']) && !isset($_POST['update_product_id'])) {
    if ($_SESSION['role'] !== 'admin') {
        header("Location: unauthorized_access.php");
        exit;
    }

    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Prepare the SQL query to insert the new product into the database
    $stmt = $conn->prepare("INSERT INTO products (name, price, category) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $name, $price, $category);  // Bind parameters (string, double, string)
    $stmt->execute();
    $stmt->close();

    // Redirect to the same page to show the updated list of products
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle updating a product (Admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product_id'])) {
    if ($_SESSION['role'] !== 'admin') {
        header("Location: unauthorized_access.php");
        exit;
    }

    $update_product_id = $_POST['update_product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Prepare the SQL query to update the product details
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, category = ? WHERE id = ?");
    $stmt->bind_param("sdsi", $name, $price, $category, $update_product_id);  // Bind parameters
    $stmt->execute();
    $stmt->close();

    // Redirect to the same page to show the updated list of products
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
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

        .product button {
            background-color: #FFD700;
            color: #8B0000;
        }

        .product button:hover {
            background-color: #FFC107;
        }

        .add-product {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 10px;
            margin-top: 20px;
            border-radius: 10px;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
        }

        .add-product h2 {
            margin-bottom: 10px;
        }

        input[type="text"], input[type="number"], select {
            padding: 8px;
            width: 90%;
            margin-bottom: 10px;
        }

        button {
            margin-top: 10px;
            padding: 10px;
            font-size: 1em;
            background-color: #D32F2F;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #B71C1C;
        }

        .button-container {
            margin-top: 20px;
        }

        .button-container a {
            color: #FFD700;
            font-size: 1.2em;
            text-decoration: none;
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

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <!-- Edit Product Form -->
                        <form method="POST" action="">
                            <input type="hidden" name="update_product_id" value="<?php echo $product['id']; ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
                            <select name="category" required>
                                <option value="soft-serve" <?php echo $product['category'] === 'soft-serve' ? 'selected' : ''; ?>>soft-serve</option>
                                <option value="tub" <?php echo $product['category'] === 'tub' ? 'selected' : ''; ?>>tub</option>
                                <option value="ice-pop" <?php echo $product['category'] === 'ice-pop' ? 'selected' : ''; ?>>ice-pop</option>
                            </select>
                            <button type="submit">Update Product</button>
                        </form>

                        <form method="POST" action="">
                            <input type="hidden" name="delete_product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit">Delete Product</button>
                        </form>
                    <?php elseif ($_SESSION['role'] === 'user'): ?>
                        <!-- Add to Cart Form -->
                        <form method="POST" action="add_to_cart.php">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                            <button type="submit">Add to Cart</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Admin-Only Add Product Section -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="add-product">
                <h2>Add New Product</h2>
                <form method="POST" action="">
                    <label for="name">Product Name:</label>
                    <input type="text" name="name" id="name" required>
                    
                    <label for="price">Price:</label>
                    <input type="number" step="0.01" name="price" id="price" required>
                    
                    <label for="category">Category:</label>
                    <select name="category" id="category" required>
                        <option value="soft-serve">soft-serve</option>
                        <option value="tub">tub</option>
                        <option value="ice-pop">ice-pop</option>
                    </select>
                    
                    <button type="submit">Add Product</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Navigation Buttons -->
        <div class="button-container">
        <?php if ($_SESSION['role'] === 'user'): ?>
        <a href="cart.php">Go to Cart</a>
    <?php endif; ?>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.html">Go to Admin Dashboard</a>
            <?php elseif ($_SESSION['role'] === 'user'): ?>
                <a href="dashboard.html">Go to Member Dashboard</a> 
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
