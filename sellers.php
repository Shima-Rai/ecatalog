<?php
include "db.php";

// Query top 5 best-selling products
$sql = "
    SELECT 
        p.name AS product_name,
        SUM(o.quantity) AS total_sold
    FROM orders o
    JOIN products p ON o.product_id = p.id
    GROUP BY o.product_id
    ORDER BY total_sold DESC
    LIMIT 5
";
$res = $conn->query($sql);
$topSellers = [];
while($row = $res->fetch_assoc()) {
    $topSellers[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Top Sellers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #eef5ff, #f8fbff);
    color: #1f2937;
    min-height: 100vh;
}

        .glass-card {
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0px 20px 50px rgba(13,110,253,0.2);
    margin-top: 50px;
    border-top: 6px solid #0d6efd;
}
h2 {
    color: #0d6efd;
    font-weight: 700;
}

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: white;
    color: #0d6efd;
    box-shadow: 0 6px 18px rgba(13,110,253,0.25);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            text-align: center;
            padding-top: 11px;
            cursor: pointer;
            transition: 0.3s;
        }
        .back-btn:hover {
            background: #0d6efd;
    color: white;
            transform: scale(1.1);
        }
        table thead {
    background: #0d6efd;
    color: white;
}

table tbody tr:hover {
    background: #eef5ff;
}

    </style>
</head>
<body>

<div class="back-btn" onclick="window.location.href='index.php'">⬅</div>

<div class="container">
    <h2 class="text-center">🏆 Top 5 Best-Selling Products</h2>

    <div class="glass-card mx-auto" style="max-width: 700px;">
        <table class="table table-bordered table-hover text-center">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Total Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($topSellers as $i => $product) { ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= $product['product_name'] ?></td>
                        <td><?= $product['total_sold'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <canvas id="topSellersChart" height="200"></canvas>
    </div>
</div>

<script>
const labels = <?= json_encode(array_column($topSellers, 'product_name')) ?>;
const data = <?= json_encode(array_column($topSellers, 'total_sold')) ?>;

new Chart(document.getElementById('topSellersChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Units Sold',
            data: data,
            backgroundColor: 'rgba(13,110,253,0.7)',
borderColor: '#0d6efd',

            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

</body>
</html>
