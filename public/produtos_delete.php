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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $repo->delete($id);
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Excluir produto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
<h1>Excluir produto</h1>
<p>Tem certeza que deseja excluir o produto "<?=h($produto['nome'])?>" (código <?=h($produto['codigo_barras'])?>)?</p>
<form method="post">
    <input type="hidden" name="csrf" value="<?=h(csrf_token())?>">
    <button type="submit" style="background:#b00020;color:#fff">Excluir</button>
    <a href="produtos_show.php?id=<?=$produto['id']?>">Cancelar</a>
</form>
</body>
</html>
