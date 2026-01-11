<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../src/ProdutoRepository.php';
require_once __DIR__ . '/../src/helpers.php';
$repo = new ProdutoRepository();

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 20;

$total = $repo->count($q);
$rows = $repo->list($q, $page, $per);
$pages = max(1, (int)ceil($total / $per));
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Produtos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body { min-height: 100vh; }
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
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Menu lateral -->
    <nav class="side-menu">
        <h5 class="ps-3 mb-4">Jaqueline Andrade</h5>
        <a href="index.php"><i class="bi bi-box"></i> Produtos</a>
        <a href="pedidos_list.php"><i class="bi bi-basket"></i> Vendas</a>
        <a href="produtos_create.php"><i class="bi bi-plus-square"></i> Novo produto</a>
        <a href="pedido_create.php"><i class="bi bi-plus-square"></i> Nova venda</a>
		<a href="/logout.php"><i class="bi bi-plus-square"></i> Sair</a>

    </nav>

    <!-- Conteúdo principal -->
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Produtos</h1>
            <a href="produtos_create.php" class="btn btn-success">+ Novo produto</a>
        </div>

        <form method="get" action="index.php" class="mb-4 row g-2">
            <div class="col-sm-4">
                <input type="text" name="q" class="form-control" placeholder="Buscar por nome ou código" value="<?=h($q)?>" autofocus>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Código de barras</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Qtd</th>
                        <th>Atualizado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?=h($r['id'])?></td>
                        <td><?=h($r['codigo_barras'])?></td>
                        <td><a href="produtos_show.php?id=<?=$r['id']?>"><?=h($r['nome'])?></a></td>
                        <td>R$ <?=number_format($r['preco'], 2, ',', '.')?></td>
                        <td><?=h($r['quantidade'])?></td>
                        <td><?=h($r['atualizado_em'])?></td>
                        <td>
                            <a class="btn btn-sm btn-info" href="produtos_edit.php?id=<?=$r['id']?>">Editar</a>
                            <a class="btn btn-sm btn-danger" href="produtos_delete.php?id=<?=$r['id']?>" onclick="return confirm('Excluir este produto?');">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>Página <?=$page?> de <?=$pages?> (<?=$total?> itens)</div>
            <nav>
                <ul class="pagination mb-0">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?q=<?=urlencode($q)?>&page=<?=$page-1?>">Anterior</a></li>
                    <?php endif; ?>
                    <?php if ($page < $pages): ?>
                        <li class="page-item"><a class="page-link" href="?q=<?=urlencode($q)?>&page=<?=$page+1?>">Próxima</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </main>
</div>
<!-- Ícones Bootstrap (opcional, se quiser usar <i> no menu, incluir): -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</body>
</html>
