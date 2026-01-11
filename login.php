<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

/* =========================
   BASE PATH (funciona no localhost e no InfinityFree)
========================= */
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($basePath === '/') {
    $basePath = '';
}

/* =========================
   CONFIG BANCO (PREENCHA COM SEUS DADOS)
========================= */
$host = '';  // ← SEU HOST MySQL
$dbname = '';   // ← NOME DO SEU BANCO
$user = '';             // ← SEU USUÁRIO
$pass = '';           // ← SUA SENHA

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

/* Para onde vai depois do login + 2FA OK */
$redirectAfterLogin = $basePath . '/public/index.php';

/* Nome do app no Authenticator */
$issuerName = 'LojaJaqueline';

/* =========================
   PDO
========================= */
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die('Erro ao conectar no banco: ' . $e->getMessage());
}

/* =========================
   LIB 2FA
========================= */
require_once __DIR__ . '/lib/GoogleAuthenticator.php';
$ga = new GoogleAuthenticator();

/* =========================
   HELPERS
========================= */
function getUserByEmail(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1');
    $stmt->execute([trim($email)]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function getUserById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function redirectTo(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* =========================
   SE JÁ ESTIVER LOGADO
========================= */
if (!empty($_SESSION['user_id']) && !empty($_SESSION['twofa_ok'])) {
    redirectTo($redirectAfterLogin);
}

/* =========================
   ESTADO / STEP
========================= */
$step = $_SESSION['step'] ?? 'login';
$errors = [];

/* =========================
   AÇÕES
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    /* LOGOUT (se você quiser usar botão dentro do login) */
    if ($action === 'logout') {
        session_unset();
        session_destroy();
        redirectTo('login.php');
    }

    /* 1) LOGIN (EMAIL + SENHA) */
    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $errors[] = 'Informe e-mail e senha.';
            $step = 'login';
        } else {
            $user = getUserByEmail($pdo, $email);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errors[] = 'E-mail ou senha inválidos.';
                $step = 'login';
            } else {
                session_regenerate_id(true);

                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['twofa_ok'] = false;

                if ((int)$user['twofa_enabled'] === 1 && !empty($user['totp_secret'])) {
                    $step = 'verify_2fa';
                } else {
                    if (empty($_SESSION['temp_totp_secret'])) {
                        $_SESSION['temp_totp_secret'] = $ga->createSecret();
                    }
                    $step = 'setup_2fa';
                }

                $_SESSION['step'] = $step;
            }
        }
    }

    /* 2) SETUP DO 2FA (QR + PRIMEIRO CÓDIGO) */
    if ($action === 'setup_2fa') {
        if (empty($_SESSION['user_id'])) {
            $step = 'login';
        } else {
            $code = preg_replace('/\D+/', '', (string)($_POST['code'] ?? ''));
            $secret = (string)($_SESSION['temp_totp_secret'] ?? '');

            if ($secret === '' || $code === '') {
                $errors[] = 'Digite o código do Authenticator.';
                $step = 'setup_2fa';
            } else {
                if ($ga->verifyCode($secret, $code, 2)) {
                    $stmt = $pdo->prepare('UPDATE users SET totp_secret = ?, twofa_enabled = 1 WHERE id = ?');
                    $stmt->execute([$secret, (int)$_SESSION['user_id']]);

                    unset($_SESSION['temp_totp_secret']);
                    $_SESSION['twofa_ok'] = true;
                    $_SESSION['step'] = 'login';

                    redirectTo($redirectAfterLogin);
                } else {
                    $errors[] = 'Código inválido (expirado ou digitado errado).';
                    $step = 'setup_2fa';
                }
            }
        }
        $_SESSION['step'] = $step;
    }

    /* 3) VERIFICAÇÃO DO 2FA (LOGIN NORMAL) */
    if ($action === 'verify_2fa') {
        if (empty($_SESSION['user_id'])) {
            $step = 'login';
        } else {
            $code = preg_replace('/\D+/', '', (string)($_POST['code'] ?? ''));
            $user = getUserById($pdo, (int)$_SESSION['user_id']);

            if (!$user || empty($user['totp_secret'])) {
                session_unset();
                session_destroy();
                $step = 'login';
                $errors[] = '2FA não configurado. Faça login novamente.';
            } else {
                if ($code === '') {
                    $errors[] = 'Digite o código do Authenticator.';
                    $step = 'verify_2fa';
                } else {
                    if ($ga->verifyCode((string)$user['totp_secret'], $code, 2)) {
                        $_SESSION['twofa_ok'] = true;
                        $_SESSION['step'] = 'login';
                        redirectTo($redirectAfterLogin);
                    } else {
                        $errors[] = 'Código inválido.';
                        $step = 'verify_2fa';
                    }
                }
            }
        }
        $_SESSION['step'] = $step;
    }
}

/* Se não estiver logado, nunca fica em verify/setup */
if (empty($_SESSION['user_id'])) {
    $step = 'login';
    $_SESSION['step'] = 'login';
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Login - Loja Jaqueline</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; background:#f6f7f9; margin:0; padding:30px; }
    .box { max-width: 460px; margin: 0 auto; background:#fff; border:1px solid #e5e7eb; padding: 22px; border-radius: 10px; }
    h2 { margin: 0 0 12px; }
    .error { background:#fff2f2; border:1px solid #ffd0d0; color:#b00020; padding:10px; border-radius:8px; margin-bottom: 12px; }
    label { display:block; margin-top:12px; font-weight:600; }
    input { width:100%; padding:10px; margin-top:6px; border:1px solid #d1d5db; border-radius:8px; box-sizing:border-box; }
    button { width:100%; margin-top:14px; padding:10px; border:0; border-radius:8px; background:#2563eb; color:#fff; font-size:16px; cursor:pointer; }
    button:hover { background:#1d4ed8; }
    .qr { text-align:center; margin: 16px 0; }
    .qr img { width: 220px; height:220px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; }
    .secret { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; background:#f3f4f6; padding:10px; border-radius:8px; word-break:break-all; }
    .muted { color:#6b7280; font-size: 14px; }
  </style>
</head>
<body>

<div class="box">
  <?php if (!empty($errors)): ?>
    <div class="error">
      <?php foreach ($errors as $err): ?>
        <div><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($step === 'login'): ?>

    <h2>Login</h2>
    <form method="post" autocomplete="on">
      <input type="hidden" name="action" value="login">

      <label for="email">E-mail</label>
      <input id="email" type="email" name="email" required autocomplete="username">

      <label for="password">Senha</label>
      <input id="password" type="password" name="password" required autocomplete="current-password">

      <button type="submit">Entrar</button>
    </form>

  <?php elseif ($step === 'setup_2fa'): ?>

    <?php
      if (empty($_SESSION['temp_totp_secret'])) {
          $_SESSION['temp_totp_secret'] = $ga->createSecret();
      }
      $secret = (string)$_SESSION['temp_totp_secret'];

      $user = getUserById($pdo, (int)$_SESSION['user_id']);
      $emailLabel = $user['email'] ?? 'usuario';

      // Compatível com PHPGangsta: getQRCodeGoogleUrl($name, $secret, $title)
      $qrUrl = $ga->getQRCodeGoogleUrl($emailLabel, $secret, $issuerName);
    ?>

    <h2>Configurar Authenticator</h2>
    <p class="muted">2FA é obrigatório. Escaneie o QR no Google/Microsoft Authenticator e digite o código abaixo.</p>

    <div class="qr">
      <img src="<?php echo htmlspecialchars($qrUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="QR Code 2FA">
    </div>

    <div class="muted">Se preferir, cadastre manualmente com este secret:</div>
    <div class="secret"><?php echo htmlspecialchars($secret, ENT_QUOTES, 'UTF-8'); ?></div>

    <form method="post">
      <input type="hidden" name="action" value="setup_2fa">

      <label for="code">Código (6 dígitos)</label>
      <input id="code" name="code" maxlength="6" inputmode="numeric" pattern="\d{6}" required>

      <button type="submit">Ativar 2FA e entrar</button>
    </form>

  <?php elseif ($step === 'verify_2fa'): ?>

    <h2>Verificação 2FA</h2>
    <p class="muted">Digite o código de 6 dígitos do seu app Authenticator.</p>

    <form method="post">
      <input type="hidden" name="action" value="verify_2fa">

      <label for="code2">Código (6 dígitos)</label>
      <input id="code2" name="code" maxlength="6" inputmode="numeric" pattern="\d{6}" required>

      <button type="submit">Confirmar</button>
    </form>

  <?php else: ?>
    <?php
      $_SESSION['step'] = 'login';
      redirectTo($basePath . '/login.php');
    ?>
  <?php endif; ?>
</div>

</body>
</html>
