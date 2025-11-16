<?php
require_once 'includes/db_connect.php';

if (isset($_GET['term'])) {
    $term = $_GET['term'] . '%';
    $stmt = $conn->prepare("SELECT id, product_name, mrp FROM products WHERE product_name LIKE :term");
    $stmt->bindParam(':term', $term);
    $stmt->execute();

    $products = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Also check for available stock for this product
        $stock_stmt = $conn->prepare("SELECT SUM(current_qty) as total_stock FROM stock_batches WHERE product_id = :product_id");
        $stock_stmt->bindParam(':product_id', $row['id']);
        $stock_stmt->execute();
        $stock_row = $stock_stmt->fetch(PDO::FETCH_ASSOC);

        if ($stock_row['total_stock'] > 0) {
            $row['stock'] = $stock_row['total_stock'];
            $products[] = $row;
        }
    }

    echo json_encode($products);
}
