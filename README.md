# 🛒 Loja Jaqueline Andrade

Sistema PHP/MySQL completo com autenticação 2FA.

## ✨ Funcionalidades
- ✅ Login + **2FA Google Authenticator**
- ✅ CRUD **Produtos** (criar/editar/listar)
- ✅ **Gerenciar Pedidos/Vendas**
- ✅ Dashboard **responsivo Bootstrap**
- ✅ Backup automático banco

## 🚀 Deploy em 3 passos

## 1. Configurar banco

# 🗄️ Banco de teste incluído

# **public/banco_example.sql**:

## 2. Configurar credenciais

# **Edite** `login.php`,`config/config.php` e `backup.php`:

# $host = 'HOST'; 
# $dbname = 'NOME_DO_BANCO';
# $user = 'SEU_USUARIO';
# $pass = 'SUA_SENHA_AQUI';

# Em login.php apontar servidor web
# Document Root → /caminho/para/public/
# https://seudominio.com/login.php

# 🛠️ Configurar 2FA (Login)
# Primeiro login (senha normal)

# Vá Configurações → escaneie QR Code

# Backup codes salvos em papel

# Próximos logins pedem código 6 dígitos

## Backup Automático

# https://seudominio.com/backup.php → baixa SQL atualizado

## 📁 Estrutura do Projeto

-lojajaqueline/<br>
-├── public/           # Raiz do servidor web <br>
-│   ├── index.php<br>
-│   ├── login.php<br>
-│   ├── backup.php<br>
-│   └── banco_example.sql<br>
-├── config/<br>
-│   └── config.example.php  # COPIE para config.php<br>
-├── src/              # Backend<br>
-└── README.md<br>

## 🖥️ Tecnologias
# PHP 8+ | MySQL | PDO | Bootstrap 5
# password_hash() | Google Authenticator (2FA)

PHP 8+ | MySQL | PDO | Bootstrap 5
password_hash() | Google Authenticator (2FA)
