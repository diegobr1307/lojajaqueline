<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../src/ProdutoRepository.php';
require_once __DIR__ . '/../src/helpers.php';
$repo = new ProdutoRepository();

$id = (int)($_GET['id'] ?? 0);
$produto = $repo->find($id);
if (!$produto) {
    http_response_code(404);
    exit('Produto não encontrado');
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Produto <?=h($produto['nome'])?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >
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
        <h4 class="ps-3 mb-4">Minha Loja</h4>
        <a href="index.php">Produtos</a>
        <a href="pedidos_list.php">Pedidos</a>
        <a href="produtos_create.php">Novo produto</a>
		<a href="logout.php"><i class="bi bi-plus-square"></i> Sair</a>
    </nav>
    <main class="main-content w-100">
        <div class="container" style="max-width:600px;">
            <h1><?=h($produto['nome'])?></h1>
            <ul class="list-group mb-4">
                <li class="list-group-item"><strong>ID:</strong> <?=h($produto['id'])?></li>
                <li class="list-group-item"><strong>Código de barras:</strong> <?=h($produto['codigo_barras'])?></li>
                <li class="list-group-item"><strong>Categoria:</strong> <?=h($produto['categoria'])?></li>
                <li class="list-group-item"><strong>Marca:</strong> <?=h($produto['marca'])?></li>
                <li class="list-group-item"><strong>Preço:</strong> R$ <?=number_format($produto['preco'], 2, ',', '.')?></li>
                <li class="list-group-item"><strong>Quantidade:</strong> <?=h($produto['quantidade'])?></li>
                <li class="list-group-item"><strong>Atualizado:</strong> <?=h($produto['atualizado_em'])?></li>
            </ul>
            <div>
                <a href="produtos_edit.php?id=<?=$produto['id']?>" class="btn btn-primary me-2">Editar</a>
                <a href="produtos_delete.php?id=<?=$produto['id']?>" class="btn btn-danger" onclick="return confirm('Deseja realmente excluir este produto?')">Excluir</a>
                <a href="index.php" class="btn btn-secondary ms-2">Voltar</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
