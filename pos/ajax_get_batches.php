<?php
require_once 'includes/db_connect.php';
require_once 'includes/classes/Stock.php';

if (isset($_GET['product_id'])) {
    $stock = new Stock($conn);
    $stmt = $stock->get_batches_for_product($_GET['product_id']);

    $batches = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $batches[] = $row;
    }

    echo json_encode($batches);
}
