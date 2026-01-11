<?php
// auth.php - proteção de páginas (sessão + 2FA obrigatório)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Caminho atual da página
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';

// Não bloqueia login/logout
if (str_ends_with($path, '/login.php') || str_ends_with($path, '/logout.php')) {
    return;
}

// Se não passou pelo login+2FA, manda pro login na raiz
if (empty($_SESSION['user_id']) || empty($_SESSION['twofa_ok'])) {
    header('Location: /login.php');
    exit;
}
