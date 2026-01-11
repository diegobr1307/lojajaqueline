<?php
require_once __DIR__ . '/../auth.php';
require_once '../src/Database.php';
$db = Database::pdo();
$pedido_id = (int)($_GET['pedido_id'] ?? 0);

$erro_finalizacao = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Busca itens para validar estoque
    $sql = "SELECT p.nome, p.categoria, p.quantidade AS estoque, i.quantidade, i.preco_unitario, i.produto_id
            FROM itens_pedido i
            JOIN produtos p ON i.produto_id = p.id
            WHERE i.pedido_id = :pid";
    $itens = $db->prepare($sql);
    $itens->execute([':pid' => $pedido_id]);
    $total = 0;
    $resumo = [];
    foreach ($itens->fetchAll(PDO::FETCH_ASSOC) as $item) {
        if ($item['quantidade'] > $item['estoque']) {
            $erro_finalizacao .= "Estoque insuficiente para {$item['nome']} ({$item['categoria']}). Só há {$item['estoque']} unidade(s).<br>";
        }
    }
    if ($erro_finalizacao) {
        // Mensagem amigável
        echo '<!doctype html>
        <html lang="pt-br"><head><meta charset="utf-8"><title>Finalizar Pedido</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"></head><body>
        <div class="container" style="max-width: 600px; margin-top: 60px;">
            <div class=\"alert alert-danger\">' . $erro_finalizacao . '</div>
            <a href="pedido_itens.php?pedido_id=' . $pedido_id . '" class="btn btn-secondary">Voltar</a>
        </div></body></html>';
        exit;
    }
    // Agora desconta e finaliza pedido
    $itens->execute([':pid' => $pedido_id]);
    foreach ($itens->fetchAll(PDO::FETCH_ASSOC) as $item) {
        $db->prepare("UPDATE produtos SET quantidade = quantidade - :qtd WHERE id = :pid")
            ->execute([':qtd' => $item['quantidade'], ':pid' => $item['produto_id']]);
        $total += $item['quantidade'] * $item['preco_unitario'];
        $resumo[] = $item['quantidade'] . "x [" . $item['categoria'] . "] " . $item['nome'];
    }
    $resumo_str = implode(', ', $resumo);

    $db->prepare("UPDATE pedidos SET status = 'finalizado', preco_total = :total, resumo_itens = :resumo WHERE id = :id")
        ->execute([':id' => $pedido_id, ':total' => $total, ':resumo' => $resumo_str]);
    header("Location: pedidos_list.php");
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Finalizar Pedido</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
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
        <a href="index.php"><i class="bi bi-box"></i> Produtos</a>
        <a href="pedidos_list.php"><i class="bi bi-basket"></i> Vendas</a>
        <a href="produtos_create.php"><i class="bi bi-plus-square"></i> Novo produto</a>
        <a href="pedido_create.php"><i class="bi bi-plus-square"></i> Nova venda</a>
		<a href="logout.php"><i class="bi bi-plus-square"></i> Sair</a>
    </nav>
    <main class="main-content w-100">
        <div class="container" style="max-width:500px;">
            <div class="text-center">
                <h2 class="mb-4">Finalizar Venda</h2>
            </div>
            <form method="post" class="card shadow p-4 bg-light">
                <p class="mb-3">Clique no botão abaixo para confirmar e descontar o estoque. Esta ação não pode ser desfeita!</p>
                <button type="submit" class="btn btn-success btn-lg w-100">Confirmar e Descontar Estoque</button>
            </form>
            <div class="mt-3 text-end">
                <a href="pedido_itens.php?pedido_id=<?=$pedido_id?>" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
