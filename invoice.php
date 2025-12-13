<?php include 'db.php'; ?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice Generator</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #eef5ff, #f9fbff);
    }

    h2, h3, h5 {
        color: #0d6efd;
        font-weight: 700;
    }

    /* Back Button */
    .back-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        background: white;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        text-align: center;
        line-height: 48px;
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(13,110,253,0.25);
        transition: 0.3s;
    }

    .back-btn:hover {
        background: #0d6efd;
        color: white;
        transform: scale(1.1);
    }

    /* Cards */
    .card {
        border-radius: 18px;
        border: none;
        box-shadow: 0 15px 40px rgba(13,110,253,0.15);
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
    }

    /* Invoice Box */
    .invoice-box {
        background: white;
        padding: 35px;
        border-radius: 20px;
        box-shadow: 0 25px 60px rgba(13,110,253,0.2);
        border-top: 6px solid #0d6efd;
    }

    .invoice-header {
        border-bottom: 2px dashed #cfe2ff;
        margin-bottom: 25px;
        padding-bottom: 15px;
    }

    .invoice-header h2 {
        margin-bottom: 5px;
    }

    /* Buttons */
    .btn-blue {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 22px;
        border: none;
        box-shadow: 0 8px 20px rgba(13,110,253,0.35);
        transition: 0.3s;
    }

    .btn-blue:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(13,110,253,0.45);
    }

    /* Tables */
    table thead {
        background: #0d6efd;
        color: white;
    }

    table th, table td {
        vertical-align: middle;
    }

    /* Print */
    @media print {
        .no-print { display:none !important; }
        body { background: white; }
    }
</style>

<script>
function downloadPDF() {
    window.print();
}
</script>

</head>

<body class="p-4">

<div class="back-btn no-print" onclick="window.location.href='index.php'">⬅</div>

<div class="container">

    <h2 class="fw-bold mb-4 text-center no-print">🧾 Invoice Generator</h2>


    <!-- SELECT ORDERS -->
    <div class="card p-4 mb-5 no-print">
        <h5 class="mb-3">Select Orders</h5>

        <form method="GET">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = $conn->query("
                            SELECT orders.*, products.name AS product_name 
                            FROM orders
                            JOIN products ON products.id = orders.product_id
                            ORDER BY orders.order_id DESC
                        ");

                        while($o = $orders->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="order_ids[]" value="<?= $o['order_id'] ?>">
                            </td>
                            <td><?= $o['order_id'] ?></td>
                            <td><?= $o['product_name'] ?></td>
                            <td><?= $o['quantity'] ?></td>
                            <td>₹<?= $o['total_price'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <button class="btn-blue mt-3">Generate Invoice</button>
        </form>
    </div>

    <?php
    if (isset($_GET['order_ids'])) {
        $ids = implode(",", $_GET['order_ids']);

        $invoice = $conn->query("
            SELECT
                orders.order_id,
                orders.quantity,
                orders.total_price,
                products.name AS product_name,
                products.price AS unit_price
            FROM orders
            JOIN products ON products.id = orders.product_id
            WHERE orders.order_id IN ($ids)
        ");

        if ($invoice->num_rows > 0):
    ?>

    <!-- INVOICE -->
    <div class="invoice-box">

        <div class="invoice-header text-center">
            <h2>E-Catalog</h2>
            <p class="text-muted mb-0">Official Tax Invoice</p>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>Date:</strong> <?= date("d M Y") ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <p><strong>Invoice No:</strong> INV-<?= time() ?></p>
            </div>
        </div>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Unit Price (₹)</th>
                    <th>Qty</th>
                    <th>Total (₹)</th>
                </tr>
            </thead>
            <tbody>

            <?php 
                $grandTotal = 0;
                while($row = $invoice->fetch_assoc()):
                    $grandTotal += $row['total_price'];
            ?>
                <tr>
                    <td><?= $row['order_id'] ?></td>
                    <td><?= $row['product_name'] ?></td>
                    <td>₹<?= $row['unit_price'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td>₹<?= $row['total_price'] ?></td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>

        <h3 class="text-end mt-4">
            <strong>Grand Total: ₹<?= $grandTotal ?></strong>
        </h3>

        <div class="text-center mt-4 no-print">
            <button onclick="downloadPDF()" class="btn-blue px-4">
                📄 Download PDF
            </button>
        </div>
    </div>

    <?php endif; } ?>

</div>
</body>
</html>
