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

lojajaqueline/                    <br>
│   ├── login.php                 <br>
│   ├── logout.php                <br>
│   ├── backup.php                <br>
│   ├── index.html                <br>
├── README.md                     <br>
├── .gitignore                    <br>
├── config/                       <br>
    │   └── config.php        <br>
├── lib/                          <br>
    │   └── GoogleAuthenticator.php   <br>
├── public/                       <br>
    │   ├── index.php                 <br>
    │   ├── pedido_cancelar.php       <br>
    │   ├── pedido_finalizar.php      <br>
    │   ├── pedido_list.php           <br>
    │   ├── itens_pedido.php          <br>
    │   └── produtos_*.php            <br>
└── src/                          <br>
    ├── helpers.php               <br>
    ├── produto_repository.php    <br>
    └── auth_produ_repository.php <br>


## 🖥️ Tecnologias
 PHP 8+ | MySQL | PDO | Bootstrap 5<br>
 password_hash() | Google Authenticator (2FA)<br>

PHP 8+ | MySQL | PDO | Bootstrap 5<br>
password_hash() | Google Authenticator (2FA)<br>
