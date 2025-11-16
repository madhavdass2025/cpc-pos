<?php
require_once 'includes/session.php';
require_login();
require_role(1); // 1 = Admin

require_once 'includes/db_connect.php';
require_once 'includes/classes/User.php';

if (isset($_GET['id'])) {
    $user = new User($conn);
    $user->id = $_GET['id'];

    if ($user->delete()) {
        header('Location: users.php');
        exit;
    } else {
        echo 'Failed to delete user.';
    }
} else {
    echo 'No user ID specified.';
}
