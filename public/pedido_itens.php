<?php
require_once __DIR__ . '/../auth.php';
require_once '../src/Database.php';
$db = Database::pdo();

$pedido_id = (int)($_GET['pedido_id'] ?? 0);
$erro_estoque = '';
$mensagem_sucesso = '';

// Adição de produto ao pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_preco'])) {
    $codigo = trim($_POST['codigo_barras'] ?? '');
    $qtd = max(1, (int)($_POST['quantidade'] ?? 1));
    $produto = $db->prepare("SELECT id, preco, quantidade FROM produtos WHERE codigo_barras = :cb");
    $produto->execute([':cb' => $codigo]);
    $p = $produto->fetch(PDO::FETCH_ASSOC);
    if ($p) {
        $st = $db->prepare("SELECT SUM(quantidade) AS total_sep FROM itens_pedido WHERE pedido_id = :pid AND produto_id = :prodid");
        $st->execute([':pid' => $pedido_id, ':prodid' => $p['id']]);
        $jasep = (int)($st->fetchColumn() ?: 0);
        $estoque_disp = $p['quantidade'] - $jasep;

        if ($qtd > $estoque_disp) {
            $erro_estoque = "Estoque insuficiente! Já reservado neste pedido: {$jasep}. Só há {$estoque_disp} disponível para adicionar.";
        } elseif ($qtd <= 0) {
            $erro_estoque = "Quantidade inválida.";
        } else {
            $db->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario)
              VALUES (:pid,:prod,:qtd,:preco)")
              ->execute([
                  ':pid'   => $pedido_id,
                  ':prod'  => $p['id'],
                  ':qtd'   => $qtd,
                  ':preco' => $p['preco']
              ]);
            header("Location: pedido_itens.php?pedido_id=$pedido_id");
            exit;
        }
    } else {
        $erro_estoque = "Código de barras não encontrado!";
    }
}

// Atualização do preço unitário do item
if (isset($_POST['edit_preco'])) {
    $item_id = (int)$_POST['item_id'];
    $novo_preco = (float)$_POST['novo_preco'];
    if ($novo_preco <= 0) {
        $erro_estoque = "Preço inválido.";
    } else {
        $sql = "UPDATE itens_pedido SET preco_unitario = :preco WHERE id = :id AND pedido_id = :pid";
        $stm = $db->prepare($sql);
        $stm->execute([':preco' => $novo_preco, ':id' => $item_id, ':pid' => $pedido_id]);
        $mensagem_sucesso = "Preço atualizado com sucesso.";
    }
    header("Location: pedido_itens.php?pedido_id=$pedido_id");
    exit;
}

// Exclusão de item
if (isset($_GET['del_item'])) {
    $item_id = (int)$_GET['del_item'];
    $db->prepare("DELETE FROM itens_pedido WHERE id = :id AND pedido_id = :pid")
        ->execute([':id' => $item_id, ':pid' => $pedido_id]);
    header("Location: pedido_itens.php?pedido_id=$pedido_id");
    exit;
}

// Busca os itens para exibir
$itens = $db->prepare("SELECT i.id, p.nome, p.codigo_barras, p.categoria, i.quantidade, i.preco_unitario
    FROM itens_pedido i
    JOIN produtos p ON i.produto_id = p.id WHERE i.pedido_id = :pid");
$itens->execute([':pid' => $pedido_id]);
$lista = $itens->fetchAll(PDO::FETCH_ASSOC);

// Soma total da separação considerando preços alterados
$total_separacao = 0;
foreach ($lista as $item) {
    $total_separacao += $item['quantidade'] * $item['preco_unitario'];
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Separação de Pedido</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
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
        <a href="produtos_create.php">Novo produto</a>
        <a href="pedido_create.php">Novo pedido</a>
    </nav>
    <main class="main-content w-100">
        <div class="container mb-4" style="max-width:900px;">
            <h2 class="mb-4">Separação de Pedido</h2>
            <?php if ($erro_estoque): ?>
                <div class="alert alert-danger"><?=htmlspecialchars($erro_estoque)?></div>
            <?php endif; ?>
            <?php if ($mensagem_sucesso): ?>
                <div class="alert alert-success"><?=htmlspecialchars($mensagem_sucesso)?></div>
            <?php endif; ?>
            <section class="mb-4">
                <form method="post" class="row g-2 align-items-end">
                    <div class="col-md-6 col-lg-5">
                        <label class="form-label">Código de Barras</label>
                        <input type="text" name="codigo_barras" class="form-control" autofocus required>
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantidade" min="1" value="1" class="form-control" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </div>
                </form>
            </section>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário (R$)</th>
                            <th>Total Item (R$)</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista as $item): ?>
                        <tr>
                            <td><?=htmlspecialchars($item['codigo_barras'])?></td>
                            <td><?=htmlspecialchars($item['nome'])?></td>
                            <td><?=htmlspecialchars($item['categoria'])?></td>
                            <td><?=htmlspecialchars($item['quantidade'])?></td>
                            <td>R$ <?=number_format($item['preco_unitario'], 2, ',', '.')?></td>
                            <td>R$ <?=number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.')?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarPreco" data-itemid="<?= $item['id'] ?>" data-preco="<?= $item['preco_unitario'] ?>">Editar Preço</button>
                                <a href="pedido_itens.php?pedido_id=<?=$pedido_id?>&del_item=<?=$item['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Remover este item do pedido?')">Excluir</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mb-4 d-flex justify-content-end">
                <h5>Total separação: R$ <?=number_format($total_separacao, 2, ',', '.')?></h5>
            </div>
            <div class="d-flex gap-3">
                <a href="pedido_finalizar.php?pedido_id=<?=$pedido_id?>" class="btn btn-success">Finalizar Venda</a>
                <a href="pedido_cancelar.php?pedido_id=<?=$pedido_id?>" class="btn btn-secondary">Cancelar Pedido</a>
            </div>
        </div>

        <!-- Modal para edição de preço -->
        <div class="modal fade" id="modalEditarPreco" tabindex="-1" aria-labelledby="modalEditarPrecoLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form method="post" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEditarPrecoLabel">Editar Preço Unitário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="item_id" id="modalItemId" value="">
                <div class="mb-3">
                    <label for="modalNovoPreco" class="form-label">Novo preço (R$)</label>
                    <input type="number" step="0.01" min="0.01" name="novo_preco" id="modalNovoPreco" class="form-control" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" name="edit_preco" class="btn btn-primary">Salvar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              </div>
            </form>
          </div>
        </div>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var modalEditarPreco = document.getElementById('modalEditarPreco')
    modalEditarPreco.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget
      var itemId = button.getAttribute('data-itemid')
      var preco = button.getAttribute('data-preco')

      var inputItemId = modalEditarPreco.querySelector('#modalItemId')
      var inputPreco = modalEditarPreco.querySelector('#modalNovoPreco')

      inputItemId.value = itemId
      inputPreco.value = preco
    })
</script>
</body>
</html>
