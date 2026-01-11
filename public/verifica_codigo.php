<?php
require_once __DIR__ . '/../auth.php';
require_once '../src/Database.php';
$db = Database::pdo();
$codigo = trim($_GET['codigo'] ?? '');

if ($codigo !== '') {
    $st = $db->prepare("SELECT 1 FROM produtos WHERE codigo_barras = :cb LIMIT 1");
    $st->execute([':cb' => $codigo]);
    echo $st->fetch() ? 'EXISTE' : 'LIVRE';
} else {
    echo 'VAZIO';
}
