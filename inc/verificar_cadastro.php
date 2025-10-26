<?php
include("../config.php");
require_once(DBAPI);
session_start();

// Verifica se temos dados de cadastro em sessão
if (empty($_SESSION['cadastro'])) {
    $_SESSION['message'] = "Nenhum processo de cadastro em andamento.";
    $_SESSION['type'] = 'warning';
    header('Location: cadastro.php');
    exit;
}

$dados = $_SESSION['cadastro'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');

    if ($codigo == $dados['codigo']) {
        // inserir usuário no banco
        try {
            $bd = open_database();
            $bd->exec("USE " . DB_NAME);

            $senhaCripto = cri($dados['senha']);
            $avatares = ['avatar1.png','avatar2.png','avatar3.png','avatar4.png','avatar5.png'];
            $foto = $avatares[array_rand($avatares)];

            $stmt = $bd->prepare("INSERT INTO usuarios (u_email, u_user, u_senha, foto) VALUES (:email, :nome, :senha, :foto)");
            $stmt->bindParam(':email', $dados['email']);
            $stmt->bindParam(':nome', $dados['nome']);
            $stmt->bindParam(':senha', $senhaCripto);
            $stmt->bindParam(':foto', $foto);

            if ($stmt->execute()) {
                unset($_SESSION['cadastro']);
                $_SESSION['message'] = "Cadastro realizado com sucesso! Agora faça login.";
                $_SESSION['type'] = 'success';
                header('Location: login.php');
                exit;
            } else {
                $_SESSION['message'] = "Erro ao cadastrar usuário.";
                $_SESSION['type'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Erro no banco: " . $e->getMessage();
            $_SESSION['type'] = 'danger';
        }

    } else {
        $_SESSION['message'] = "Código incorreto. Tente novamente.";
        $_SESSION['type'] = 'danger';
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar cadastro</title>
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/style.css">
    <style>
        .verification-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #73213d, #9c2952);
            padding: 20px;
        }
        .verification-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 480px;
        }
        .verification-title {
            color: #73213d;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            text-align: center;
        }
        .verification-input {
            letter-spacing: 0.5em;
            text-align: center;
            font-size: 1.5rem;
        }
        .btn-verificar {
            background: #73213d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }
        .btn-verificar:hover {
            background: #9c2952;
            transform: translateY(-2px);
        }
        .back-link {
            color: #73213d;
            text-decoration: none;
            margin-top: 1rem;
            display: inline-block;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: #9c2952;
        }
    </style>
</head>
<body>
<div class="verification-container">
    <div class="verification-card">
        <h3 class="verification-title">Verificação de Email</h3>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['type']; ?>"><?php echo $_SESSION['message']; ?></div>
        <?php endif; ?>

    <p>Enviamos um código para <strong><?php echo htmlspecialchars($dados['email']); ?></strong>. Digite-o abaixo para concluir o cadastro. Caso não tenha recebido, verifique sua caixa de spam.</p>

    <form method="post">
        <div class="mb-3">
            <input type="text" name="codigo" class="form-control" placeholder="Código" required>
        </div>
        <div class="d-grid">
            <button class="btn btn-primary">Verificar</button>
        </div>
    </form>

    <p class="mt-3"><a href="cadastro.php">Voltar</a></p>
</div>
</body>
</html>
