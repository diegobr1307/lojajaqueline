<?php
// Topo do index.php
session_start();
if (!isset($_SESSION['logado'])) {
    header('Location: login.php');
    exit;
}
?>
