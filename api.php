<?php
include 'db.php';
header("Content-Type: application/json");

// Get HTTP method, optional ID, and action
$method = $_SERVER['REQUEST_METHOD'];
$id     = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

// Helper function for JSON responses
function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// -----------------------------
// PRODUCTS CRUD
// -----------------------------
if (!$action) {
    switch($method) {
        case 'GET':
            if ($id) {
                $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                if ($result) respond($result);
                else respond(["error"=>"Product not found"],404);
            } else {
                $res = $conn->query("SELECT * FROM products");
                respond($res->fetch_all(MYSQLI_ASSOC));
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['name'], $data['price']))
                respond(["error"=>"Name and price required"],400);

            $name = safe($conn,$data['name']);
            $price = floatval($data['price']);

            $stmt = $conn->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
            $stmt->bind_param("sd",$name,$price);
            $stmt->execute() ? respond(["message"=>"Product created","id"=>$stmt->insert_id],201)
                             : respond(["error"=>"Failed to create product"],500);
            break;

        case 'PUT':
            if (!$id) respond(["error"=>"ID required"],400);
            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['name'], $data['price']))
                respond(["error"=>"Name & price required"],400);

            $name = safe($conn,$data['name']);
            $price = floatval($data['price']);

            $stmt = $conn->prepare("UPDATE products SET name=?, price=? WHERE id=?");
            $stmt->bind_param("sdi",$name,$price,$id);
            $stmt->execute() ? respond(["message"=>"Product updated"]) 
                             : respond(["error"=>"Product not found or unchanged"],404);
            break;

        case 'DELETE':
            if (!$id) respond(["error"=>"ID required"],400);
            $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
            $stmt->bind_param("i",$id);
            $stmt->execute() ? respond(["message"=>"Product deleted"])
                             : respond(["error"=>"Product not found"],404);
            break;

        default:
            respond(["error"=>"Invalid request method"],405);
    }
}

// -----------------------------
// ORDERS / SALES
// -----------------------------
if ($action == "get_products") {
    $res = $conn->query("SELECT id,name,price FROM products");
    respond($res->fetch_all(MYSQLI_ASSOC));
}

if ($action == "get_sales") {
    $sql = "SELECT o.order_id, o.product_id, o.quantity, o.total_price, o.created_at, p.name AS product_name
            FROM orders o
            JOIN products p ON o.product_id=p.id
            ORDER BY o.order_id DESC";
    $res = $conn->query($sql);
    $data = [];
    while($row = $res->fetch_assoc()) $data[] = $row;
    respond($data);
}

if ($action == "add_order") {
    if ($method != "POST") respond(["error"=>"Invalid method"],405);

    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['product_id'], $data['quantity'], $data['total_price']))
        respond(["error"=>"product_id, quantity, and total_price are required"],400);

    $product_id = intval($data['product_id']);
    $quantity   = intval($data['quantity']);
    $total      = floatval($data['total_price']);

    $stmt = $conn->prepare("INSERT INTO orders (product_id, quantity, total_price) VALUES (?,?,?)");
    $stmt->bind_param("iid",$product_id,$quantity,$total);

    $stmt->execute() ? respond(["status"=>"success","order_id"=>$stmt->insert_id],201)
                     : respond(["status"=>"error","message"=>$conn->error],500);
}
// Get orders
if ($_GET['action'] === 'get_orders') {
    $q = $conn->query("
        SELECT orders.order_id, orders.quantity, orders.total_price,
               products.name AS product_name
        FROM orders
        JOIN products ON products.id = orders.product_id
        ORDER BY orders.order_id DESC
        LIMIT 10
    ");
    $data = [];
    while ($r = $q->fetch_assoc()) $data[] = $r;
    echo json_encode($data);
    exit;
}

// Cancel order
if ($_GET['action'] === 'cancel_order') {
    $d = json_decode(file_get_contents("php://input"), true);
    $conn->query("DELETE FROM orders WHERE order_id=".(int)$d['order_id']);
    echo json_encode(['status'=>'success']);
    exit;
}

?>
