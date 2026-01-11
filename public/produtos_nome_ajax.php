<?php
require_once __DIR__ . '/../auth.php';
require_once '../src/Database.php';
$db = Database::pdo();
$term = $_GET['term'] ?? '';
$results = [];

if (strlen($term) >= 2) {
    $st = $db->prepare("SELECT nome FROM produtos WHERE nome LIKE :nome LIMIT 10");
    $st->execute([':nome' => '%' . $term . '%']);
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $results[] = $row['nome'];
    }
}
echo json_encode($results);
?>
