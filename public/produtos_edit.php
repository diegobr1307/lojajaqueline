<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../src/ProdutoRepository.php';
require_once __DIR__ . '/../src/helpers.php';
$repo = new ProdutoRepository();

$categorias = ['Acessórios', 'Brinquedos', 'Calçados', 'Consignado', 'Garrafas/Copos', 'Maquiagem', 'Meias', 'Papelaria', 'Utilidades'];

$id = (int)($_GET['id'] ?? 0);
$produto = $repo->find($id);
if (!$produto) {
    http_response_code(404);
    exit('Produto não encontrado');
}

$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $dados = [
        'codigo_barras' => $_POST['codigo_barras'] ?? '',
        'nome' => $_POST['nome'] ?? '',
        'categoria' => $_POST['categoria'] ?? '',
        'marca' => $_POST['marca'] ?? '',
        'preco' => $_POST['preco'] ?? '0',
        'quantidade' => $_POST['quantidade'] ?? '0',
    ];
    if ($dados['codigo_barras'] === '') $erros[] = 'Informe o código de barras';
    if ($dados['nome'] === '') $erros[] = 'Informe o nome do produto';
    if (!$dados['categoria'] || !in_array($dados['categoria'], $categorias)) $erros[] = 'Selecione uma categoria';
    if (!is_numeric($dados['preco'])) $erros[] = 'Preço inválido';
    if (!ctype_digit((string)$dados['quantidade'])) $erros[] = 'Quantidade inválida';

    // Verifica se o código já está cadastrado em outro produto
    $existe = $repo->findByCodigo($dados['codigo_barras']);
    if ($existe && (int)$existe['id'] !== $id) {
        $erros[] = 'Já existe um produto cadastrado com este código de barras.';
    }

    if (!$erros) {
        $repo->update($id, $dados);
        header("Location: produtos_show.php?id=" . $id);
        exit;
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Editar Produto <?=htmlspecialchars($produto['nome'])?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery & jQuery UI for autocomplete -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

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
    </nav>
    <main class="main-content w-100">
        <div class="container d-flex justify-content-center align-items-center" style="min-height: 85vh;">
            <div class="w-100" style="max-width:700px;">
                <h2 class="mb-4">Editar Produto</h2>
                <?php if ($erros): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($erros as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <form method="post" class="card shadow p-4 bg-light">
                    <input type="hidden" name="csrf" value="<?=htmlspecialchars(csrf_token())?>">
                    <div class="mb-3">
                        <label class="form-label">Código de barras</label>
                        <input type="text" name="codigo_barras" id="codigo_barras" class="form-control" required autofocus value="<?=htmlspecialchars($_POST['codigo_barras'] ?? $produto['codigo_barras'])?>">
                        <div id="codigo-msg" class="form-text text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" id="nome" class="form-control" required value="<?=htmlspecialchars($_POST['nome'] ?? $produto['nome'])?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select name="categoria" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?=htmlspecialchars($cat)?>" <?= (($_POST['categoria'] ?? $produto['categoria']) === $cat) ? 'selected' : '' ?>>
                                <?=htmlspecialchars($cat)?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Marca</label>
                        <input type="text" name="marca" id="marca" class="form-control" value="<?=htmlspecialchars($_POST['marca'] ?? $produto['marca'])?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preço (R$)</label>
                        <input type="number" name="preco" class="form-control" step="0.01" min="0" required value="<?=htmlspecialchars($_POST['preco'] ?? $produto['preco'])?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantidade" class="form-control" min="0" required value="<?=htmlspecialchars($_POST['quantidade'] ?? $produto['quantidade'])?>">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="produtos_show.php?id=<?=$produto['id']?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
$(function() {
    $('#codigo_barras').on('input blur', function() {
        var codigo = $(this).val().trim();
        if (codigo.length === 0) {
            $('#codigo-msg').text('');
            return;
        }
        $.get('verifica_codigo.php', {codigo: codigo}, function(resp) {
            // Ignorar se o código é o mesmo do produto que está editando
            if (resp === 'EXISTE' && codigo !== "<?=htmlspecialchars($produto['codigo_barras'])?>") {
                $('#codigo-msg').text('Já existe um produto com este código de barras!');
            } else {
                $('#codigo-msg').text('');
            }
        });
    });

    $("#nome").autocomplete({
        source: "produtos_nome_ajax.php",
        minLength: 2
    });
    $("#marca").autocomplete({
        source: "produtos_marca_ajax.php",
        minLength: 2
    });
});
</script>
</body>
</html>
