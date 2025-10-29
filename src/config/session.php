<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
// Si no hay usuario logueado, redirige al login
if (!isset($_SESSION['usuario_nombre']) && !isset($_SESSION['user_name'])) {
    header('Location: /sistema/public/login');
    exit();
}
ob_end_flush();