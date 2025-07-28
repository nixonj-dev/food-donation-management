<?php
include("login.php");

if ($_SESSION['name'] == '') {
    header("location: signup.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Profile | Food Donate</title>
</head>

<body>
    <header>
        <div class="logo">Food <b style="color:rgb(0, 0, 0);">Donate</b></div>
        <div class="hamburger">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
        <nav class="nav-bar">
            <ul>
                <li><a href="home.html">Home</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
            </ul>
        </nav>
    </header>

    <script>
        document.querySelector(".hamburger").onclick = function () {
            document.querySelector(".nav-bar").classList.toggle("active");
        };
    </script>

    <div class="profile">
        <div class="profilebox">
            <p class="headingline">
                <img src="" alt="" class="profile-icon"> Profile
            </p>
            <div class="info">
                <p>Name: <?php echo $_SESSION['name']; ?></p>
                <p>Email: <?php echo $_SESSION['email']; ?></p>
                <p>Gender: <?php echo $_SESSION['gender']; ?></p>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <hr>
            <p class="heading">Your Donations</p>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Food</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Date/Time</th>
                            <th>Delivered By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $email = $_SESSION['email'];
                        $query = "SELECT * FROM food_donations WHERE email='$email'";
                        $result = mysqli_query($connection, $query);

                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td>{$row['food']}</td>
                                        <td>{$row['type']}</td>
                                        <td>{$row['category']}</td>
                                        <td>{$row['date']}</td>
                                        <td>{$row['delivery_by']}</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <style>body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(to bottom right, #e0f7e9, #f4f4f4);
    color: #333;
}

/* Header Styles */
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background: linear-gradient(to right, #06C167, #04a156);
    color: #fff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-radius: 0 0 15px 15px;
}

.logo {
    font-size: 26px;
    font-weight: bold;
    text-transform: uppercase;
}

.nav-bar ul {
    list-style: none;
    display: flex;
    gap: 20px;
    margin: 0;
    padding: 0;
}

.nav-bar ul li a {
    text-decoration: none;
    color: #fff;
    padding: 10px 15px;
    font-size: 16px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.nav-bar ul li a.active, .nav-bar ul li a:hover {
    background: #fff;
    color: #06C167;
    transform: scale(1.1);
}

/* Hamburger Menu */
.hamburger {
    display: none;
    flex-direction: column;
    cursor: pointer;
    gap: 6px;
}

.hamburger .line {
    width: 28px;
    height: 3px;
    background: #fff;
    border-radius: 2px;
}

.nav-bar.active {
    display: block;
    position: absolute;
    background: #06C167;
    top: 60px;
    right: 0;
    width: 100%;
    padding: 15px 0;
}

@media (max-width: 768px) {
    .hamburger {
        display: flex;
    }

    .nav-bar {
        display: none;
        flex-direction: column;
        align-items: center;
    }

    .nav-bar ul {
        flex-direction: column;
        gap: 10px;
    }
}

/* Profile Section */
.profile {
    padding: 20px;
    max-width: 900px;
    margin: 40px auto;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    animation: fadeIn 1s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.profilebox {
    text-align: center;
    padding: 30px;
    background: linear-gradient(to top right, #e0ffe5, #f9f9f9);
    border-radius: 15px;
    box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.05);
}

.headingline {
    font-size: 30px;
    color: #06C167;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 30px;
}

.headingline .profile-icon {
    width: 40px;
    height: auto;
    animation: bounce 1.5s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

.info {
    text-align: left;
    margin: 20px 0;
    font-size: 18px;
    line-height: 1.8;
    color: #444;
}

.logout-btn {
    display: inline-block;
    background: #06C167;
    color: #fff;
    text-decoration: none;
    padding: 10px 25px;
    border-radius: 25px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.logout-btn:hover {
    background: #04a156;
    transform: scale(1.1);
}

/* Table Styles */
.table-wrapper {
    margin: 20px auto;
    overflow-x: auto;
    background: #f9f9f9;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    border: 1px solid #ddd;
    padding: 20px;
}

.table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 16px;
}

.table thead {
    background: linear-gradient(to right, #06C167, #04a156);
    color: #fff;
    font-weight: bold;
    text-transform: uppercase;
}

.table thead th {
    padding: 12px;
    text-align: center;
}

.table tbody tr {
    border-bottom: 1px solid #ddd;
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background: #eafbea;
    transform: scale(1.01);
}

.table tbody td {
    padding: 10px;
    text-align: center;
}

.table tbody tr:nth-child(even) {
    background: #f5f5f5;
}

/* Responsive Table */
@media (max-width: 768px) {
    .table-wrapper {
        padding: 0;
    }

    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
    }

    .table tbody td {
        display: flex;
        justify-content: space-between;
        padding: 10px;
    }

    .table tbody td:before {
        content: attr(data-label);
        font-weight: bold;
        color: #06C167;
        margin-right: 10px;
    }
}
</style>
</body>

</html>
