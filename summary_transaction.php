<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "mokti_login");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the date range based on the filter selected
$date_filter = '';
if (isset($_POST['filter'])) {
    $filter = $_POST['filter'];
    
    $current_date = date('Y-m-d');
    $start_of_week = date('Y-m-d', strtotime('last sunday', strtotime($current_date)));
    $end_of_week = date('Y-m-d', strtotime('next saturday', strtotime($current_date)));
    $start_of_month = date('Y-m-01');
    
    switch ($filter) {
        case 'daily':
            $date_filter = "WHERE DATE(order_date) = '$current_date'";
            break;
        case 'weekly':
            $date_filter = "WHERE order_date BETWEEN '$start_of_week' AND '$end_of_week'";
            break;
        case 'monthly':
            if (isset($_POST['month']) && isset($_POST['year'])) {
                $selected_month = $_POST['month'];
                $selected_year = $_POST['year'];
                $start_of_month = "$selected_year-$selected_month-01";
                $end_of_month = date('Y-m-t', strtotime($start_of_month)); // Get last day of selected month
                $date_filter = "WHERE order_date BETWEEN '$start_of_month' AND '$end_of_month'";
            }
            break;
        default:
            $date_filter = ''; // No filter
            break;
    }
}

// Fetch orders based on the filter
$query = "SELECT order_id, user_id, items, subtotal, order_date FROM orders $date_filter ORDER BY order_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Summary</title>
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
            color: #fff;
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

        .dashboard-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Transaction Summary</h1>

        <!-- Filter form -->
        <form method="post" class="filter-form">
            <label for="filter">Filter by: </label>
            <select name="filter" id="filter" onchange="this.form.submit()">
                <option value="daily" <?php echo isset($filter) && $filter == 'daily' ? 'selected' : ''; ?>>Daily</option>
                <option value="weekly" <?php echo isset($filter) && $filter == 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                <option value="monthly" <?php echo isset($filter) && $filter == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
            </select>
            
            <!-- Show month and year selection if "monthly" is selected -->
            <?php if (isset($filter) && $filter == 'monthly'): ?>
                <label for="month">Month: </label>
                <select name="month" id="month">
                    <option value="01" <?php echo isset($_POST['month']) && $_POST['month'] == '01' ? 'selected' : ''; ?>>January</option>
                    <option value="02" <?php echo isset($_POST['month']) && $_POST['month'] == '02' ? 'selected' : ''; ?>>February</option>
                    <option value="03" <?php echo isset($_POST['month']) && $_POST['month'] == '03' ? 'selected' : ''; ?>>March</option>
                    <option value="04" <?php echo isset($_POST['month']) && $_POST['month'] == '04' ? 'selected' : ''; ?>>April</option>
                    <option value="05" <?php echo isset($_POST['month']) && $_POST['month'] == '05' ? 'selected' : ''; ?>>May</option>
                    <option value="06" <?php echo isset($_POST['month']) && $_POST['month'] == '06' ? 'selected' : ''; ?>>June</option>
                    <option value="07" <?php echo isset($_POST['month']) && $_POST['month'] == '07' ? 'selected' : ''; ?>>July</option>
                    <option value="08" <?php echo isset($_POST['month']) && $_POST['month'] == '08' ? 'selected' : ''; ?>>August</option>
                    <option value="09" <?php echo isset($_POST['month']) && $_POST['month'] == '09' ? 'selected' : ''; ?>>September</option>
                    <option value="10" <?php echo isset($_POST['month']) && $_POST['month'] == '10' ? 'selected' : ''; ?>>October</option>
                    <option value="11" <?php echo isset($_POST['month']) && $_POST['month'] == '11' ? 'selected' : ''; ?>>November</option>
                    <option value="12" <?php echo isset($_POST['month']) && $_POST['month'] == '12' ? 'selected' : ''; ?>>December</option>
                </select>
                <label for="year">Year: </label>
                <select name="year" id="year">
                    <?php
                    $current_year = date('Y');
                    for ($i = 0; $i < 10; $i++) {
                        $year = $current_year - $i;
                        echo "<option value='$year' ".(isset($_POST['year']) && $_POST['year'] == $year ? 'selected' : '').">$year</option>";
                    }
                    ?>
                </select>
                <button type="submit">Apply Filter</button>
            <?php endif; ?>
        </form>

        <!-- Transaction table -->
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Items</th>
                    <th>Subtotal</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Print order data in table
                        echo "<tr>";
                        echo "<td>" . $row['order_id'] . "</td>";
                        echo "<td>" . $row['user_id'] . "</td>";
                        echo "<td>" . $row['items'] . "</td>";
                        echo "<td>$" . number_format($row['subtotal'], 2) . "</td>";
                        echo "<td>" . $row['order_date'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Button to download the PDF -->
        <form method="post" action="download_pdf_summary.php">
            <button type="submit" name="download_pdf">Download PDF</button>
        </form>

        <!-- Go to Admin Dashboard Button -->
        <div class="dashboard-button">
            <button onclick="location.href='admin_dashboard.html'">Go to Admin Dashboard</button>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
