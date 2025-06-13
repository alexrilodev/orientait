<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define("BASE_URL", "https://orientait.free.nf/");
define("UPLOAD_DIR", __DIR__ . '/uploads/');
define("UPLOAD_URL", BASE_URL . 'uploads/');

require_once __DIR__ . '/database/db.php';
?>