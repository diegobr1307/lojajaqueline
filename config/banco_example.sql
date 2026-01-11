-- Loja Jaqueline - banco_example.sql
-- Estrutura COMPLETA + 3 produtos/pedidos de TESTE

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

-- Tabela users (usuário teste incluso)
CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(191) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `totp_secret` varchar(64) DEFAULT NULL,
  `twofa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuário TESTE: email: teste@teste.com | senha: "123456"
INSERT INTO `users` (`id`, `email`, `password_hash`, `totp_secret`, `twofa_enabled`) VALUES
(1, 'teste@teste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 0);

-- --------------------------------------------------------

-- Tabela produtos (3 exemplos genéricos)
CREATE TABLE `produtos` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo_barras` varchar(64) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `produtos` (`id`, `codigo_barras`, `nome`, `categoria`, `marca`, `preco`, `quantidade`) VALUES
(1, '1234567890123', 'Produto Exemplo 1', 'Eletrônicos', 'Marca Teste', 29.90, 50),
(2, '1234567890124', 'Produto Exemplo 2', 'Acessórios', 'Marca Teste', 15.99, 25),
(3, '1234567890125', 'Produto Exemplo 3', 'Brinquedos', 'Marca Teste', 45.00, 10);

-- --------------------------------------------------------

-- Tabela pedidos (1 pedido teste)
CREATE TABLE `pedidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  `status` enum('separacao','finalizado','cancelado') DEFAULT 'separacao',
  `preco_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `resumo_itens` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pedidos` (`id`, `status`, `preco_total`, `resumo_itens`) VALUES
(1, 'finalizado', 45.89, '1x Produto Exemplo 1, 1x Produto Exemplo 2');

-- --------------------------------------------------------

-- Tabela itens_pedido
CREATE TABLE `itens_pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `produto_id` int(10) UNSIGNED NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `preco_unitario` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `itens_pedido` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 1, 1, 29.90),
(2, 1, 2, 1, 15.99);

-- --------------------------------------------------------

-- Índices e chaves estrangeiras
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`);

ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

-- AUTO_INCREMENT
ALTER TABLE `users` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `produtos` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `pedidos` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `itens_pedido` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

COMMIT;
