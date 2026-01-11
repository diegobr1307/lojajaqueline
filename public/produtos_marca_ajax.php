<?php
require_once __DIR__ . '/../auth.php';
require_once '../src/Database.php';
$db = Database::pdo();
$term = $_GET['term'] ?? '';
$results = [];

if (strlen($term) >= 2) {
    $st = $db->prepare("SELECT DISTINCT marca FROM produtos WHERE marca LIKE :marca AND marca IS NOT NULL AND marca != '' LIMIT 10");
    $st->execute([':marca' => '%' . $term . '%']);
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $results[] = $row['marca'];
    }
}
echo json_encode($results);
?>
