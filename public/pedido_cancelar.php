<?php
require_once __DIR__ . '/../auth.php';
require_once '../src/Database.php';
$db = Database::pdo();
$pedido_id = (int)($_GET['pedido_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare("UPDATE pedidos SET status = 'cancelado' WHERE id = :id")->execute([':id' => $pedido_id]);
    header("Location: pedidos_list.php");
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Cancelar Pedido</title>
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
        <div class="container" style="max-width:400px;">
            <h2 class="mb-4">Cancelar Pedido</h2>
            <form method="post" class="card shadow p-4 bg-light text-center">
                <p class="mb-4 text-danger fs-5">Tem certeza que deseja cancelar a separação deste pedido?</p>
                <button type="submit" class="btn btn-danger btn-lg w-100">Cancelar Separação</button>
            </form>
            <div class="mt-3 text-end">
                <a href="pedido_itens.php?pedido_id=<?=$pedido_id?>" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
