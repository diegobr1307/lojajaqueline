# 🛒 Loja Jaqueline Andrade

Sistema PHP/MySQL completo com autenticação 2FA.

## ✨ Funcionalidades
- ✅ Login + **2FA Google Authenticator**
- ✅ CRUD **Produtos** (criar/editar/listar)
- ✅ **Gerenciar Pedidos/Vendas**
- ✅ Dashboard **responsivo Bootstrap**
- ✅ Backup automático banco

## 🚀 Deploy em 3 passos

### 1. Configurar banco

## 🗄️ Banco de teste incluído

## **public/banco_example.sql**:

### 2. Configurar credenciais

## **Edite** `login.php`,`config/config.php` e `backup.php`:

## $host = 'HOST'; 
## $dbname = 'NOME_DO_BANCO';
## $user = 'SEU_USUARIO';
## $pass = 'SUA_SENHA_AQUI';

## Em login.php apontar servidor web
## Document Root → /caminho/para/public/
## https://seudominio.com/login.php

## 🛠️ Configurar 2FA (Login)
## Primeiro login (senha normal)

## Vá Configurações → escaneie QR Code

## Backup codes salvos em papel

## Próximos logins pedem código 6 dígitos

## Backup Automático

https://seudominio.com/backup.php → baixa SQL atualizado

📁 Estrutura do Projeto
text
lojajaqueline/
├── public/           # Raiz do servidor web
│   ├── index.php
│   ├── login.php
│   ├── backup.php
│   └── banco_example.sql
├── config/
│   └── config.example.php  # COPIE para config.php
├── src/              # Backend
└── README.md

🖥️ Tecnologias
text
PHP 8+ | MySQL | PDO | Bootstrap 5
password_hash() | Google Authenticator (2FA)
