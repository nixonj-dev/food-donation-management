<?php
session_start();
ob_start();

// Database connection using environment variables
$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, 'demo');
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Ensure the user is logged in
if (empty($_SESSION['name'])) {
    header("Location: deliverylogin.php");
    exit;
}

$name = $_SESSION['name'];
$city = $_SESSION['city'] ?? null;
$id = isset($_SESSION['Did']) ? $_SESSION['Did'] : null;


// Fetch city using an external API if not set in the session
if (!$city) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://ip-api.com/json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($result);
    $city = $result->city ?? 'Unknown';
    $_SESSION['city'] = $city;
}

// Handle form submission to assign an order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food'])) {
    $order_id = intval($_POST['order_id']);
    $delivery_person_id = intval($_POST['delivery_person_id']);

    // Check if the order is already assigned
    $stmt = $connection->prepare("SELECT * FROM food_donations WHERE Fid = ? AND delivery_by IS NULL");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Assign the order
        $update_stmt = $connection->prepare("UPDATE food_donations SET delivery_by = ? WHERE Fid = ?");
        $update_stmt->bind_param("ii", $delivery_person_id, $order_id);
        $update_stmt->execute();
        $update_stmt->close();
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        echo "<script>alert('Sorry, this order has already been assigned.');</script>";
    }

    $stmt->close();
}

// Fetch unassigned orders in the user's city
$sql = "SELECT fd.Fid, fd.name, fd.phoneno, fd.date, fd.delivery_by, fd.address AS From_address, 
        ad.name AS delivery_person_name, ad.address AS To_address
        FROM food_donations fd
        LEFT JOIN admin ad ON fd.assigned_to = ad.Aid
        WHERE fd.assigned_to IS NOT NULL AND fd.delivery_by IS NULL AND fd.location = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $city);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard</title>
    <link rel="stylesheet" href="../home.css">
    <link rel="stylesheet" href="delivery.css">
    <style>
        .itm {
            background-color: white;
            display: grid;
        }

        .itm img {
            width: 400px;
            height: 400px;
            margin: auto;
        }

        .table-container {
            margin: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Food <b style="color: #06C167;">Donate</b></div>
        <div class="hamburger">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
        <nav class="nav-bar">
            <ul>
                <li><a href="#home" class="active">Home</a></li>
               <!-- <li><a href="openmap.php">Map</a></li>-->
                <li><a href="deliverymyord.php">My Orders</a></li>
            </ul>
        </nav>
    </header>
    <script>
        document.querySelector(".hamburger").onclick = function () {
            document.querySelector(".nav-bar").classList.toggle("active");
        };
    </script>

    <h2 style="text-align: center;">Welcome, <?php echo htmlspecialchars($name); ?></h2>
    <h2 style="text-align: center;">ID NO: <?php echo htmlspecialchars($id); ?></h2>

    <div class="itm">
        <img src="../img/delivery.gif" alt="Delivery" width="400" height="400">
    </div>

    <div class="table-container">
        <h3 style="text-align: center;">Available Orders in <?php echo htmlspecialchars($city); ?></h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone No</th>
                    <th>Date/Time</th>
                    <th>Pickup Address</th>
                    <th>Delivery Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)) {
                    foreach ($data as $row) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['phoneno']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['From_address']); ?></td>
                            <td><?php echo htmlspecialchars($row['To_address']); ?></td>
                            <td>
                                <?php if (is_null($row['delivery_by'])) { ?>
                                    <form method="post" style="display: inline-block;">
                                        <input type="hidden" name="order_id" value="<?php echo $row['Fid']; ?>">
                                        <input type="hidden" name="delivery_person_id" value="<?php echo $id; ?>">
                                        <button type="submit" name="food">Take Order</button>
                                    </form>
                                <?php } else if ($row['delivery_by'] == $id) { ?>
                                    Order assigned to you
                                <?php } else { ?>
                                    Order assigned to another delivery person
                                <?php } ?>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="6">No available orders</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
mysqli_close($connection);
ob_end_flush();
?>
