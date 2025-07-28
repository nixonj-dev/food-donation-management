<?php
// Check if a session is already active, and start it only if not
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection code
$connection = mysqli_connect("localhost", "root", "", "demo");
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

include '../connection.php';
include "connect.php";

// Check if user is logged in
if (empty($_SESSION['name'])) {
    header("location:deliverylogin.php");
    exit;
}

// Retrieve session data
$name = $_SESSION['name'];
$id = isset($_SESSION['Did']) ? $_SESSION['Did'] : null;

// Redirect if delivery person ID is missing
if (!$id) {
    header("location:deliverylogin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Orders</title>
    <link rel="stylesheet" href="delivery.css">
    <link rel="stylesheet" href="../home.css">
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
            <li><a href="delivery.php">Home</a></li>
            <!--<li><a href="openmap.php">Map</a></li>-->
            <li><a href="deliverymyord.php" class="active">My Orders</a></li>
        </ul>
    </nav>
</header>

<script>
    const hamburger = document.querySelector(".hamburger");
    hamburger.onclick = function () {
        const navBar = document.querySelector(".nav-bar");
        navBar.classList.toggle("active");
    }
</script>



<div class="get">
    <?php
    // Fetch assigned orders for the logged-in delivery person
    $sql = "SELECT fd.Fid, fd.name, fd.phoneno, fd.date, fd.address AS From_address, 
            ad.name AS delivery_person_name, ad.address AS To_address
            FROM food_donations fd
            LEFT JOIN admin ad ON fd.assigned_to = ad.Aid
            WHERE fd.delivery_by = '$id'"; // Filtering by the logged-in delivery person's ID

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        die("Error fetching orders: " . mysqli_error($connection));
    }

    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    ?>
    <div class="log">
        <a>Order assigned to you</a>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone No</th>
                        <th>Date/Time</th>
                        <th>Pickup Address</th>
                        <th>Delivery Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="5">No orders assigned to you.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['phoneno']); ?></td>
                                <td><?= htmlspecialchars($row['date']); ?></td>
                                <td><?= htmlspecialchars($row['From_address']); ?></td>
                                <td><?= htmlspecialchars($row['To_address']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php mysqli_close($connection); ob_end_flush(); ?>
</body>
</html>
