<?php
require_once __DIR__ . '/../auth.php';
require_once '../src/Database.php';
$db = Database::pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO pedidos (status) VALUES ('separacao')";
    $db->prepare($sql)->execute();
    $id = $db->lastInsertId();
    header("Location: pedido_itens.php?pedido_id=$id");
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Novo Pedido</title>
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
        <div class="container d-flex justify-content-center align-items-center" style="min-height: 75vh;">
            <div class="w-100" style="max-width:500px;">
                <div class="card shadow p-4 bg-light">
                    <h2 class="mb-4 text-center">Novo Pedido</h2>
                    <form method="post">
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Iniciar Separação</button>
                    </form>
                    <div class="mt-3 text-end">
                        <a href="pedidos_list.php" class="btn btn-link">Voltar à lista</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
