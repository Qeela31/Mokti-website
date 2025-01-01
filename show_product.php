<?php
// Database connection
$host = 'localhost'; // Change if needed
$db = 'mokti_login'; // Replace with your database name
$user = 'root';      // Replace with your database username
$pass = '';          // Replace with your database password

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product count by category
$sql = "SELECT category, COUNT(*) as count FROM products GROUP BY category";
$result = $conn->query($sql);

$chartData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $chartData[] = $row;
    }
}

// Prepare data for Chart.js
$categories = array_column($chartData, 'category');
$counts = array_column($chartData, 'count');

// Fetch all products by category
$productsByCategory = [];
foreach ($categories as $category) {
    $sql = "SELECT name FROM products WHERE category = '$category'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productsByCategory[$category][] = $row['name'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mokti's Products Inventory Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(46, 48, 51);
            color: white;
        }
        h1 {
            font-size: 2.5rem;
            text-align: center;
            margin-top: 40px;
            color: white;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
        }
        .chart-container {
            width: 85%;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            background: linear-gradient(to right, rgb(103, 18, 18), rgb(29, 31, 34));
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }
        canvas {
            width: 100% !important;
            height: 400px !important;
            border-radius: 8px;
        }
        .table-container {
            width: 85%;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(to right, rgb(103, 18, 18), rgb(29, 31, 34));
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: rgb(0, 0, 0);
        }
        tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 1.2rem;
            color: #bdc3c7;
        }
        .dashboard-button {
            display: block;
            width: 200px;
            margin: 40px auto 0 auto;
            padding: 10px 20px;
            background-color: #8f0202;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            text-decoration: none;
        }
        .dashboard-button:hover {
            background-color: #d42c2c;
        }
    </style>
</head>
<body>
    <h1>Mokti's Products Inventory Statistics</h1>
    <div class="chart-container">
        <canvas id="productChart"></canvas>
    </div>

    <div class="table-container">
        <h2>Products by Category</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Product Names</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productsByCategory as $category => $products): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category); ?></td>
                        <td>
                            <ul>
                                <?php foreach ($products as $product): ?>
                                    <li><?php echo htmlspecialchars($product); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Admin Dashboard Button Above Footer -->
    <a href="admin_dashboard.html" class="dashboard-button">Go to Admin Dashboard</a>

    <div class="footer">
        <p>&copy; 2024 Mokti Ice Cream. All Rights Reserved.</p>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const categories = <?php echo json_encode($categories); ?>;
        const counts = <?php echo json_encode($counts); ?>;

        // Initialize Chart.js
        const ctx = document.getElementById('productChart').getContext('2d');
        const productChart = new Chart(ctx, {
            type: 'bar', // Bar chart for product categories
            data: {
                labels: categories, // Categories as labels
                datasets: [{
                    label: 'Number of Products',
                    data: counts, // Counts as data
                    backgroundColor: [
                        'rgba(143, 2, 2, 0.9)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(231, 76, 60, 0.6)'
                    ],
                    borderColor: [
                        'rgb(216, 66, 20)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(231, 76, 60, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                family: 'Arial',
                                size: 14,
                                weight: 'bold',
                                color: 'white'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Arial',
                                size: 14,
                                weight: 'bold',
                                color: 'white'
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleColor: 'white',
                        bodyColor: 'white'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            fontColor: 'white',
                            font: {
                                family: 'Arial',
                                size: 16,
                                weight: 'bold'
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuad'
                }
            }
        });
    </script>
</body>
</html>
