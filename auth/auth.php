<?php
if(session_status()=== PHP_SESSION_NONE){
    session_start();
  }

function checkAccess() {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header('Location: http://localhost/skillhub/auth/login.php');
        exit();
    }
}

function checkRole($allowedRoles) {
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowedRoles)) {
        header('Location: http://localhost/skillhub/no_access.php');
        exit();
    }
}
?>