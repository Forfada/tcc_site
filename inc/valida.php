<?php
    include("../config.php");

    session_start();
    
    require_once(DBAPI);

    $bd = open_database();

    try {
        $bd->exec("USE " . DB_NAME);

        $usuario = $_POST['login'];
        $senha = $_POST['senha'];

        if (!empty($usuario) && !empty($senha)) {
            $senha = cri($_POST['senha']);

            // Agora seleciona também o campo 'foto'
            $sql = "SELECT id_u, u_user, u_num, u_senha, foto FROM usuarios WHERE (u_num = :usuario) AND (u_senha = :senha)";
            $stmt = $bd->prepare($sql);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':senha', $senha);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $dados = $stmt->fetch(PDO::FETCH_ASSOC);
                $id = $dados["id_u"];
                $nome = $dados["u_user"];
                $user = $dados["u_num"];
                $password = $dados["u_senha"];
                $foto = $dados["foto"];

                if (!empty($user)) {
                    if (!isset($_SESSION)) session_start();
                    $_SESSION['message'] = "Bem vindo " . $nome . "!";
                    $_SESSION['type'] = "info";
                    $_SESSION['id_u'] = $id;
                    $_SESSION['nome'] = $nome;
                    $_SESSION['user'] = $user;
                    $_SESSION['foto'] = $foto;
                } else {
                    throw new Exception("Não foi possível se conectar!<br>Verifique seu usuário e senha.");
                }

                header("Location:" . BASEURL . "index.php");
            } else {
                throw new Exception("Não foi possível se conectar!<br>Verifique seu usuário e senha.");
            }
        } else {
            throw new Exception("Não foi possível se conectar!<br>Verifique seu usuário e senha.");
        }

    } catch (Exception $e) {
        include(INIT);
        $_SESSION['message'] = "Ocorreu um erro: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }
?>
<?php if (!empty($_SESSION['message'])) : ?>
    <div class="alert alert-<?php echo $_SESSION['type']; ?> alert-dismissible" role="alert" id="actions">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php clear_messages(); ?>
<?php endif; ?>

<header>
    <a href="<?php echo BASEURL ?>inc/login.php" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i> Voltar</a>
</header>
