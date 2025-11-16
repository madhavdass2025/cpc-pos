<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Customer.php';

if (isset($_GET['id'])) {
    $customer = new Customer($conn);
    $customer->RegID = $_GET['id'];

    if ($customer->delete()) {
        header('Location: customers.php');
        exit;
    } else {
        echo 'Failed to delete customer.';
    }
} else {
    echo 'No customer ID specified.';
}
