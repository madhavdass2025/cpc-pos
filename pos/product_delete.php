<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Product.php';

if (isset($_GET['id'])) {
    $product = new Product($conn);
    $product->id = $_GET['id'];

    if ($product->delete()) {
        header('Location: products.php');
        exit;
    } else {
        echo 'Failed to delete product.';
    }
} else {
    echo 'No product ID specified.';
}
