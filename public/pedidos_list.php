<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../auth.php';
require_once '../src/Database.php';
$db = Database::pdo();

// Buscar os pedidos finalizados agrupados por dia, ordenado decrescentemente pela data
$sql = "SELECT DATE(criado_em) AS data, SUM(preco_total) AS total
        FROM pedidos
        WHERE status = 'finalizado'
        GROUP BY DATE(criado_em)
        ORDER BY DATE(criado_em) DESC";
$st = $db->prepare($sql);
$st->execute();
$vendas_por_dia = $st->fetchAll(PDO::FETCH_ASSOC);

// Para cada dia pegar os pedidos do dia
$pedidos_por_dia = [];
foreach ($vendas_por_dia as $dia) {
    $data = $dia['data'];
    $st2 = $db->prepare("SELECT * FROM pedidos WHERE status = 'finalizado' AND DATE(criado_em) = :data ORDER BY criado_em DESC");
    $st2->execute([':data' => $data]);
    $pedidos_por_dia[$data] = $st2->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Vendas por dia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body { min-height: 100vh;}
    .side-menu {
        min-width: 200px;
        max-width: 220px;
        background: #212529;
        color: #fff;
        min-height: 100vh;
        position: fixed;
        left: 0; top: 0; bottom: 0;
        padding-top: 20px;
    }
    .side-menu a {
        color: #fff;
        text-decoration: none;
        display: block;
        padding: .6rem 1.2rem;
        border-radius: .3rem;
    }
    .side-menu a:hover { background: #343a40; }
    .main-content {
        margin-left: 220px;
        padding: 2rem 2rem 2rem 1.5rem;
    }
    @media (max-width: 800px) {
        .main-content { margin-left: 0; padding: 1rem; }
        .side-menu { position: static; min-width:0; max-width:100vw; min-height:auto;}
    }
    </style>
</head>
<body>
<div class="d-flex">
    <nav class="side-menu">
        <h5 class="ps-3 mb-4">Jaqueline Andrade</h5>
        <a href="index.php">Produtos</a>
        <a href="pedidos_list.php">Pedidos</a>
        <a href="produtos_create.php">Novo Produto</a>
        <a href="pedido_create.php">Nova Venda</a>
    </nav>
    <main class="main-content w-100">
        <div class="container-fluid px-0">
            <h1>Vendas por dia</h1>
            <?php foreach ($vendas_por_dia as $dia): ?>
            <section class="mb-5">
                <h3 class="mb-3"><?=date('d/m/Y', strtotime($dia['data']))?> - Total vendido: R$ <?=number_format($dia['total'], 2, ',', '.')?></h3>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Preço Total (R$)</th>
                            <th>Descrição dos itens</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos_por_dia[$dia['data']] as $pedido): ?>
                        <tr>
                            <td><?=htmlspecialchars($pedido['id'])?></td>
                            <td><?=htmlspecialchars($pedido['criado_em'])?></td>
                            <td><?=number_format($pedido['preco_total'], 2, ',', '.')?></td>
                            <td><?=htmlspecialchars($pedido['resumo_itens'])?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
            <?php endforeach; ?>
        </div>
    </main>
</div>
</body>
</html>
