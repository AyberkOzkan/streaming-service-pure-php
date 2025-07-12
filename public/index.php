<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    require_once __DIR__ . '/../app/config/bootstrap.php';
    require_once __DIR__ . '/../app/router.php';
?>
