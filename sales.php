<?php
include "db.php";
$products = $conn->query("SELECT id, name, price FROM products");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Order</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #e6f0ff, #f8fbff);
        min-height: 100vh;
    }

    h2 {
        color: #0d6efd;
        font-weight: 700;
    }

    /* Glass Card */
    .glass-card {
        background: rgba(255,255,255,0.75);
        padding: 30px;
        border-radius: 20px;
        backdrop-filter: blur(12px);
        box-shadow: 0 15px 40px rgba(13,110,253,0.15);
        border: 1px solid rgba(13,110,253,0.2);
    }

    label {
        font-weight: 600;
        color: #084298;
    }

    input, select {
        background: white !important;
        color: #000 !important;
        border-radius: 12px !important;
        height: 48px !important;
        border: 1px solid #cfe2ff !important;
    }

    input:focus, select:focus {
        box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25) !important;
        border-color: #0d6efd !important;
    }

    select option {
        background: white;
        color: black;
    }

    /* Buttons */
    .btn-modern {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        border: none;
        font-weight: 600;
        padding: 12px;
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(13,110,253,0.35);
        transition: 0.3s;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(13,110,253,0.5);
    }

    /* Back button */
    .back-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        background: white;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        text-align: center;
        line-height: 48px;
        font-size: 20px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: 0.3s;
    }

    .back-btn:hover {
        background: #0d6efd;
        color: white;
        transform: scale(1.1);
    }

    /* Modal */
    .modal-content {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 50px rgba(13,110,253,0.35);
    }

    .modal-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }

    .table thead {
        background: #0d6efd;
        color: white;
    }

    .table td, .table th {
        vertical-align: middle;
    }
    </style>
</head>

<body>

<div class="back-btn" onclick="window.location.href='index.php'">⬅</div>

<div class="container mt-5">

    <h2 class="text-center mb-3">🛒 Create a New Sale Order</h2>

    <!-- VIEW ORDERS BUTTON -->
    <div class="text-end mb-3">
        <button class="btn-modern" data-bs-toggle="modal" data-bs-target="#ordersModal">
            📦 View Orders
        </button>
    </div>

    <div class="glass-card mx-auto" style="max-width: 600px;">
        <form id="orderForm">

            <label>Select Product</label>
            <select id="productSelect" class="form-control mb-4" required>
                <option value="">-- Choose Product --</option>
                <?php while($p = $products->fetch_assoc()) { ?>
                    <option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>">
                        <?= $p['name'] ?>
                    </option>
                <?php } ?>
            </select>

            <label>Price (₹)</label>
            <input type="number" id="price" class="form-control mb-4" readonly>

            <label>Quantity</label>
            <input type="number" id="quantity" class="form-control mb-4" required>

            <label>Total Price (₹)</label>
            <input type="number" id="total" class="form-control mb-4" readonly>

            <button type="submit" class="btn-modern w-100">✔ Confirm Order</button>
        </form>
    </div>
</div>

<!-- ORDERS MODAL -->
<div class="modal fade" id="ordersModal">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">📦 Orders</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="text" id="orderSearch" class="form-control mb-3"
                       placeholder="🔍 Search by product or order ID">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Total (₹)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">
                        <tr>
                            <td colspan="5" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let allOrders = [];

productSelect.addEventListener('change', () => {
    price.value = productSelect.options[productSelect.selectedIndex].dataset.price;
    calculateTotal();
});

quantity.addEventListener('input', calculateTotal);

function calculateTotal() {
    total.value = price.value && quantity.value ? price.value * quantity.value : '';
}

orderForm.addEventListener('submit', e => {
    e.preventDefault();

    fetch('api.php?action=add_order', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            product_id: productSelect.value,
            quantity: quantity.value,
            total_price: total.value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert('Order placed! Order ID: ' + data.order_id);
        orderForm.reset();
        price.value = total.value = '';
    });
});

ordersModal.addEventListener('show.bs.modal', loadOrders);

function loadOrders() {
    fetch('api.php?action=get_orders')
        .then(res => res.json())
        .then(data => {
            allOrders = data;
            renderOrders(data);
        });
}

function renderOrders(data) {
    ordersTable.innerHTML = data.length
        ? data.map(o => `
            <tr>
                <td>#${o.order_id}</td>
                <td>${o.product_name}</td>
                <td>${o.quantity}</td>
                <td>₹${o.total_price}</td>
                <td>
                    <button class="btn btn-danger btn-sm"
                            onclick="cancelOrder(${o.order_id})">❌</button>
                </td>
            </tr>`).join('')
        : `<tr><td colspan="5" class="text-center">No orders</td></tr>`;
}

orderSearch.addEventListener('input', () => {
    const val = orderSearch.value.toLowerCase();
    renderOrders(allOrders.filter(o =>
        o.product_name.toLowerCase().includes(val) ||
        o.order_id.toString().includes(val)
    ));
});

function cancelOrder(id) {
    if (!confirm("Cancel this order?")) return;

    fetch('api.php?action=cancel_order', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ order_id: id })
    }).then(loadOrders);
}
</script>

</body>
</html>
