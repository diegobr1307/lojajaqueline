<?php
// Seus dados (do config.php)
$host = '';  // ← SEU HOST MySQL
$dbname = '';   // ← NOME DO SEU BANCO
$user = '';             // ← SEU USUÁRIO
$pass = '';           // ← SUA SENHA

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $date = date('Y-m-d_H-i');
    $filename = "backup_loja_$date.sql";
    
    $tables = [];
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch()) {
        $tables[] = $row[0];
    }
    
    $sql = "-- Backup Loja Jaqueline - $date\nSET FOREIGN_KEY_CHECKS=0;\n";
    
    foreach ($tables as $table) {
        $sql .= "\nDROP TABLE IF EXISTS `$table`;\n";
        
        $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        $sql .= $create[1] . ";\n\n";
        
        $rows = $pdo->query("SELECT * FROM `$table`");
        while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
            $sql .= "INSERT INTO `$table` VALUES (";
            foreach (array_values($row) as $value) {
                $sql .= $pdo->quote($value) . ",";
            }
            $sql = rtrim($sql, ',') . ");\n";
        }
        $sql .= "\n";
    }
    
    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($sql));
    echo $sql;
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>

