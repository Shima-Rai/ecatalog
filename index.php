<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Catalog | Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Sidebar */
        .sidebar {
            width: 230px;
            height: 100vh;
            background: #1e88e5;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .sidebar a {
            display: block;
            padding: 14px 25px;
            color: white;
            font-size: 15px;
            text-decoration: none;
            margin-bottom: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #1565c0;
        }

        .sidebar i {
            margin-right: 10px;
        }

        /* Topbar */
        .topbar {
            margin-left: 230px;
            background: white;
            padding: 18px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            font-size: 18px;
            font-weight: 600;
        }

        /* Main Content */
        .content {
            margin-left: 230px;
            padding: 30px;
        }

        /* Cards */
        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card i {
            font-size: 35px;
            color: #1e88e5;
        }

        .card h3 {
            margin: 15px 0 5px;
            font-size: 22px;
        }

        .card p {
            color: #666;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>E-Catalog</h2>
        <a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
        <a href="products.php"><i class="fa fa-box"></i> Products</a>
        <a href="sales.php"><i class="fa fa-cart-plus"></i> Add Sale</a>
         <a href="sellers.php"><i class="fa fa-cart-plus"></i>Top Sellers</a>
        <a href="invoice.php"><i class="fa fa-chart-line"></i> Invoice</a>
       
    </div>

    <!-- Top bar -->
    <div class="topbar">
        📊 Dashboard Overview
    </div>

    <!-- Main Content -->
    <div class="content">

        <!-- Dashboard Cards -->
        <div class="cards">

            <!-- Card 1 -->
            <div class="card">
                <i class="fa fa-box"></i>
                <h3>Total Products</h3>

                <?php
                $prod = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc();
                ?>
                <p><b><?php echo $prod['total']; ?></b> products available</p>
            </div>

            <!-- Card 2 -->
            <div class="card">
                <i class="fa fa-cart-plus"></i>
                <h3>Total Sales</h3>

                <?php
                $orders = $conn->query("SELECT SUM(quantity) AS total FROM orders")->fetch_assoc();
                ?>
                <p><b><?php echo $orders['total'] ?? 0; ?></b> items sold</p>
            </div>

            <!-- Card 3 -->
            <div class="card">
                <i class="fa fa-coins"></i>
                <h3>Total Revenue</h3>

                <?php
                $rev = $conn->query("SELECT SUM(total_price) AS total FROM orders")->fetch_assoc();
                ?>
                <p><b>₹<?php echo $rev['total'] ?? 0; ?></b> earned</p>
            </div>

            <!-- Card : Total Orders -->
<div class="card">
    <i class="fa fa-receipt"></i>
    <h3>Total Orders</h3>

    <?php
    $totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc();
    ?>
    <p><b><?php echo $totalOrders['total']; ?></b> orders placed</p>
</div>
<div class="card">
    <i class="fa fa-star"></i>
    <h3>Top Product</h3>

    <?php
    $top = $conn->query("
        SELECT products.name, SUM(orders.quantity) AS total
        FROM orders
        JOIN products ON products.id = orders.product_id
        GROUP BY products.id
        ORDER BY total DESC
        LIMIT 1
    ")->fetch_assoc();
    ?>
    <p><b><?php echo $top['name'] ?? 'N/A'; ?></b></p>
</div>


        </div>

        <br><br>

       

    </div>

</body>
</html>
