<?php
    require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/Database.php';

class ProdutoRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::pdo();
    }

    public function create(array $d): int {
        $sql = "INSERT INTO produtos (codigo_barras, nome, categoria, marca, preco, quantidade)
                VALUES (:cb, :nome, :cat, :marca, :preco, :qtd)";
        $st = $this->db->prepare($sql);
        $st->bindValue(':cb', trim($d['codigo_barras']), PDO::PARAM_STR);
        $st->bindValue(':nome', trim($d['nome']), PDO::PARAM_STR);
        $st->bindValue(':cat', $d['categoria'] ?? null, PDO::PARAM_STR);
        $st->bindValue(':marca', $d['marca'] ?? null, PDO::PARAM_STR);
        $st->bindValue(':preco', (float) $d['preco']);
        $st->bindValue(':qtd', (int) $d['quantidade'], PDO::PARAM_INT);
        $st->execute();

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool {
        $sql = "UPDATE produtos 
                SET codigo_barras = :cb, nome = :nome, categoria = :cat,
                    marca = :marca, preco = :preco, quantidade = :qtd
                WHERE id = :id";
        $st = $this->db->prepare($sql);
        $st->bindValue(':cb', trim($d['codigo_barras']), PDO::PARAM_STR);
        $st->bindValue(':nome', trim($d['nome']), PDO::PARAM_STR);
        $st->bindValue(':cat', $d['categoria'] ?? null, PDO::PARAM_STR);
        $st->bindValue(':marca', $d['marca'] ?? null, PDO::PARAM_STR);
        $st->bindValue(':preco', (float) $d['preco']);
        $st->bindValue(':qtd', (int) $d['quantidade'], PDO::PARAM_INT);
        $st->bindValue(':id', $id, PDO::PARAM_INT);

        return $st->execute();
    }

    public function delete(int $id): bool {
    // Apaga os itens relacionados ao produto
    $this->db->prepare("DELETE FROM itens_pedido WHERE produto_id = :id")
        ->execute([':id' => $id]);
    // Depois apaga o produto
    $st = $this->db->prepare("DELETE FROM produtos WHERE id = :id");
    $st->bindValue(':id', $id, PDO::PARAM_INT);
    return $st->execute();
}


    public function find(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM produtos WHERE id = :id");
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        $st->execute();
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function findByCodigo(string $codigo): ?array {
        $st = $this->db->prepare("SELECT * FROM produtos WHERE codigo_barras = :cb");
        $st->bindValue(':cb', trim($codigo), PDO::PARAM_STR);
        $st->execute();
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function list(string $q = '', int $page = 1, int $per = 20): array {
        $off = ($page - 1) * $per;

        if (trim($q) !== '') {
            $sql = "SELECT * FROM produtos 
                    WHERE nome LIKE :q1 OR codigo_barras LIKE :q2 
                    ORDER BY atualizado_em DESC 
                    LIMIT :per OFFSET :off";
            $st = $this->db->prepare($sql);
            $search = '%' . $q . '%';
            $st->bindValue(':q1', $search, PDO::PARAM_STR);
            $st->bindValue(':q2', $search, PDO::PARAM_STR);
        } else {
            $sql = "SELECT * FROM produtos 
                    ORDER BY atualizado_em DESC 
                    LIMIT :per OFFSET :off";
            $st = $this->db->prepare($sql);
        }

        $st->bindValue(':per', $per, PDO::PARAM_INT);
        $st->bindValue(':off', $off, PDO::PARAM_INT);
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(string $q = ''): int {
        if (trim($q) !== '') {
            $sql = "SELECT COUNT(*) AS c FROM produtos 
                    WHERE nome LIKE :q1 OR codigo_barras LIKE :q2";
            $st = $this->db->prepare($sql);
            $search = '%' . $q . '%';
            $st->bindValue(':q1', $search, PDO::PARAM_STR);
            $st->bindValue(':q2', $search, PDO::PARAM_STR);
            $st->execute();
        } else {
            $sql = "SELECT COUNT(*) AS c FROM produtos";
            $st = $this->db->prepare($sql);
            $st->execute();
        }

        return (int) $st->fetchColumn();
    }
}
