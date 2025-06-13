<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define("BASE_URL", "/");
require_once __DIR__ . '/database/db.php';
?>