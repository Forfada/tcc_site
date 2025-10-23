<?php
include 'functions.php';

// somente administrador pode acessar a área de clientes
if (!function_exists('is_admin') || !is_admin()) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['message'] = "Você não pode acessar essa funcionalidade.";
    $_SESSION['type'] = "danger";
    header("Location: " . BASEURL . "index.php");
    exit;
}

include(INIT);
include(HEADER_TEMPLATE);
include '../inc/alert.php';

index();
?>  

<section class="clientes section-light section-cor3 py-5" id="clientes">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h2 class="txt1 mb-1 text-center">Clientes Cadastrados</h2>
        <p class="txt4 text-center mb-2">Todos os Clientes cadastrados estão listados abaixo.</p>
        <form name="filtro" action="index.php" method="post">
            <div class="row align-items-center"> 
                <div class="form-group col-12 col-md-5 mb-2 mb-md-0">
                    <div class="input-group">
                        <input type="text" class="form-control" maxlength="50" name="cli" placeholder="Procurar 000.000.000-XX...">
                        <button type="submit" class="btn text-white" style="background-color: var(--cor7)"><i class="fa-solid fa-magnifying-glass"></i> Consultar</button>
                    </div>
                </div>
                <div class="col-12 col-md-7 text-md-end mt-2 mt-md-0">
                    <a class="buttonc" href="add.php" style="text-decoration: none"><i class="fa fa-plus"></i> Cadastrar Cliente </a>
                </div> 
            </div>
		</form>

		<hr>

        <div class="colmd-12">
            <div class="tabela-wrapper">
                <table class="tabela-lunaris">
                    <thead>
                        <tr>
                            <th width="30%">Nome</th>
                            <th>Sexo</th>
                            <th>Cidade</th>
                            <th width="15%">Telefone</th>
                            <th>Nascimento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($clientes) : ?>
                            <?php foreach ($clientes as $cli) : ?>
                                <tr data-href="view.php?id=<?php echo $cli['id']; ?>" style="cursor: pointer;">
                                    <td><?php echo $cli["cli_nome"]; ?></td>
                                    <td><?php echo $cli["cli_sexo"]; ?></td>
                                    <td><?php echo $cli["cli_cidade"]; ?></td>
                                    <td><?php echo telefone($cli["cli_num"]); ?></td>
                                    <td><?php echo formatadata($cli["cli_nasc"]); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6">Nenhum registro encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>

<script>
    // Make table rows with `data-href` clickable and keyboard-accessible
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('tr[data-href]');
        rows.forEach(row => {
            // make it focusable for accessibility
            row.setAttribute('tabindex', '0');
            row.addEventListener('click', function () {
                const url = this.getAttribute('data-href');
                if (url) window.location.href = url;
            });
            row.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const url = this.getAttribute('data-href');
                    if (url) window.location.href = url;
                }
            });
        });
    });
</script>
