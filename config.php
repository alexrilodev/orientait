<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define("BASE_URL", "/");
define("UPLOAD_DIR", __DIR__ . 'uploads/');
require_once __DIR__ . '/database/db.php';
?>