<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f0f4ff;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Sidebar */
        .sidebar {
            width: 230px;
            height: 100vh;
            background: linear-gradient(180deg, #0d6efd, #084298);
            color: white;
            padding: 25px 15px;
            position: fixed;
            box-shadow: 4px 0 15px rgba(0,0,0,0.15);
        }

        .sidebar h4 {
            font-weight: bold;
            margin-bottom: 30px;
        }

        .sidebar button {
            width: 100%;
            margin-bottom: 12px;
            border-radius: 10px;
            font-weight: 600;
        }

        /* Content */
        .content {
            margin-left: 260px;
            padding: 30px;
        }

        h2 {
            font-weight: 700;
            color: #0d6efd;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        label {
            font-weight: 600;
        }

        input {
            border-radius: 10px !important;
        }

        /* Table */
        table {
            border-radius: 14px;
            overflow: hidden;
        }

        thead {
            background: #0d6efd;
            color: white;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
        }

        .btn-warning {
            color: white;
        }

        /* Modal */
        .modal-content {
            border-radius: 16px;
            border: none;
        }

        .modal-header {
            background: #0d6efd;
            color: white;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center">📊 Dashboard</h4>

    <button class="btn btn-light text-primary"
            onclick="showSection('addSection')">➕ Add Product</button>

    <button class="btn btn-outline-light"
            onclick="showSection('viewSection'); loadProducts()">📦 View Products</button>

    <button class="btn btn-secondary mt-3"
            onclick="window.location.href='index.php'">⬅ Back</button>
</div>

<!-- Main Content -->
<div class="content">

    <!-- ADD PRODUCT SECTION -->
    <div id="addSection">
        <h2 class="mb-4">Add New Product</h2>

        <div class="card p-4 col-md-6">
            <form id="addProductForm">
                <div class="mb-3">
                    <label>Product Name</label>
                    <input type="text" id="name" class="form-control" placeholder="Enter product name" required>
                </div>

                <div class="mb-3">
                    <label>Price (₹)</label>
                    <input type="number" id="price" class="form-control" placeholder="Enter price" required>
                </div>

                <button class="btn btn-primary w-100">Add Product</button>
            </form>
        </div>
    </div>

    <!-- VIEW PRODUCTS SECTION -->
    <div id="viewSection" class="hidden">
        <h2 class="mb-4">Products List</h2>

        <div class="card p-3">
            <table class="table table-bordered mb-0" id="productTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price (₹)</th>
                        <th width="160">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <form id="editProductForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="editId">

                <label>Product Name</label>
                <input type="text" class="form-control mb-3" id="editName" required>

                <label>Price (₹)</label>
                <input type="number" class="form-control" id="editPrice" required>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary w-100">Update Product</button>
            </div>
        </form>
    </div>
</div>
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const apiUrl = "api.php";

// Switch sections
function showSection(id) {
    document.getElementById("addSection").classList.add("hidden");
    document.getElementById("viewSection").classList.add("hidden");
    document.getElementById(id).classList.remove("hidden");
}

// Add Product
document.getElementById("addProductForm").addEventListener("submit", (e) => {
    e.preventDefault();

    fetch(apiUrl, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            name: document.getElementById("name").value,
            price: document.getElementById("price").value
        })
    })
    .then(res => res.json())
    .then(() => {
        alert("Product added!");
        document.getElementById("addProductForm").reset();
    });
});

// Load Products
function loadProducts() {
    fetch(apiUrl)
    .then(res => res.json())
    .then(products => {
        const tbody = document.querySelector("#productTable tbody");
        tbody.innerHTML = "";

        products.forEach(p => {
            tbody.innerHTML += `
                <tr>
                    <td>${p.id}</td>
                    <td>${p.name}</td>
                    <td>${p.price}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="openEdit(${p.id}, '${p.name}', ${p.price})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(${p.id})">Delete</button>
                    </td>
                </tr>
            `;
        });
    });
}

// Open Edit Modal
function openEdit(id, name, price) {
    document.getElementById("editId").value = id;
    document.getElementById("editName").value = name;
    document.getElementById("editPrice").value = price;

    new bootstrap.Modal(document.getElementById("editModal")).show();
}

// Update Product
document.getElementById("editProductForm").addEventListener("submit", (e) => {
    e.preventDefault();

    const id = document.getElementById("editId").value;

    fetch(apiUrl + "?id=" + id, {
        method: "PUT",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            name: document.getElementById("editName").value,
            price: document.getElementById("editPrice").value
        })
    })
    .then(res => res.json())
    .then(() => {
        loadProducts();
        bootstrap.Modal.getInstance(document.getElementById("editModal")).hide();
    });
});

// Delete Product
function deleteProduct(id) {
    if (!confirm("Delete this product?")) return;

    fetch(apiUrl + "?id=" + id, { method: "DELETE" })
    .then(res => res.json())
    .then(() => loadProducts());
}

</script>
</body>
</html>
