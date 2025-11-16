<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Sale.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale = new Sale($conn);

    $sale->invoice_number = 'INV-' . date('YmdHis'); // Generate a unique invoice number
    $sale->invoice_date = date('Y-m-d');
    $sale->customer_id = $_POST['customer_id'];
    $sale->net_amount = $_POST['net_amount'];
    $sale->user_id = $_SESSION['user_id'];
    $sale->payment_method = $_POST['payment_method'];
    $sale->amount_paid = $_POST['net_amount']; // Assuming the full amount is paid
    $sale->payment_status = ($sale->payment_method === 'Credit') ? 'Credit' : 'Paid';
    $sale->products = $_POST['products'];

    if ($sale->create()) {
        header('Location: sales.php');
        exit;
    } else {
        // Redirect back to the POS page with an error message
        header('Location: pos.php?error=1');
        exit;
    }
}
