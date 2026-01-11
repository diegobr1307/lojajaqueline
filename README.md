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

 🗄️ Banco de teste incluído

 **public/banco_example.sql**:

## 2. Configurar credenciais

 **Edite** `login.php`,`config/config.php` e `backup.php`:

 $host = 'HOST'; <br>
 $dbname = 'NOME_DO_BANCO';<br>
 $user = 'SEU_USUARIO';<br>
 $pass = 'SUA_SENHA_AQUI';<br>

 Em login.php apontar servidor web<br>
 Document Root → /caminho/para/public/<br>
 https://seudominio.com/login.php<br>

# 🛠️ Configurar 2FA (Login)
 Primeiro login (senha normal)<br>

 Vá Configurações → escaneie QR Code<br>

 Backup codes salvos em papel<br>

 Próximos logins pedem código 6 dígitos<br>

## Backup Automático

 https://seudominio.com/backup.php → baixa SQL atualizado

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
 PHP 8+ | MySQL | PDO | Bootstrap 5<br>
 password_hash() | Google Authenticator (2FA)<br>

PHP 8+ | MySQL | PDO | Bootstrap 5<br>
password_hash() | Google Authenticator (2FA)<br>
