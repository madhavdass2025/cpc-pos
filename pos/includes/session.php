<?php
session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function has_permission($role_id) {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == $role_id;
}

function require_role($role_id) {
    require_login();
    if (!has_permission($role_id)) {
        // Redirect to a 'not authorized' page or the dashboard
        header('Location: index.php');
        exit;
    }
}
