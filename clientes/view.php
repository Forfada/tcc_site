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

    // valida id recebido via GET (retorna int ou false)
    $cid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($cid === false || $cid === null || $cid <= 0) {
        // id inválido: redirecionar ou mostrar erro amigável
        header('Location: index.php'); // ou mostrar mensagem
        exit;
    }
    view($cid);
    $anamnese = index_an($cid);

    if (empty($cli)) {
        echo '<p>Cliente não encontrado.</p>';
        exit;
    };
    include(INIT);
    include(HEADER_TEMPLATE);
?>

<section class="clientes section-light section-cor3 py-5" id="clientes">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h2 class="txt1 mb-1 text-center">Visualização de Cliente</h2>
        <p class="txt4 text-center mb-2">Acompanhe os dados cadastrados completos deste cliente.</p>

        <div class="container my-5">
            <div class="row my-5">
                <div class="col-12">
                    <div class="tabela-wrapper">
                        <table class="tabela-lunaris">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="4" class="mb-0 text-start"><h5>Cliente: <?php echo $cli['cli_nome']; ?></h5></th>
                                    <th scope="col" colspan="1" class="mb-0">
                                        <div class="justify-content-end view-cli-btns">
                                            <a href="index.php" class="buttona"><i class="fa-solid fa-arrow-rotate-left"></i> Voltar</a>
                                            <a href="edit.php?id=<?php echo $cli['id']; ?>" class="buttonc"><i class="fa-regular fa-pen-to-square"></i> Editar</a>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                <th style="width:7%">Idade</th>
                                <th style="width:17%">CPF</th>
                                <th style="width:17%">Telefone</th>
                                <th style="width:14%">Nascimento</th>
                                <th style="width:45%">Observação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>  
                                    <td><?php echo htmlspecialchars($cli['cli_idade']); ?></td>
                                    <td><?php echo cpf($cli['cli_cpf']); ?></td>
                                    <td><?php echo telefone($cli['cli_num']); ?></td>
                                    <td><?php echo formatadata($cli['cli_nasc']); ?></td>
                                    <td style="word-wrap: break-word; word-break: break-word;"><?php echo htmlspecialchars($cli['cli_obs']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>	
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="anamnese-panel p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Anamnese</h4>
                            <a href="add_an.php?client_id=<?php echo $cli['id']; ?>" class="buttonc" style="text-decoration: none"><i class="fa fa-plus"></i> Adicionar Anamnese</a>
                        </div>

                        <?php if (!empty($anamnese) && is_array($anamnese)): ?>
                            <div class="tabela-wrapper">
                                <table class="tabela-lunaris">
                                    <thead>
                                        <tr>
                                            <th>Hipertensão</th>
                                            <th>Diabetes</th>
                                            <th>Câncer</th>
                                            <th>Medicações</th>
                                            <th>Registrado em</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($anamnese as $a): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($a['an_hipertensao']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_diabetes']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_cancer']); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($a['an_medic'])); ?></td>
                                            <td><?php echo formatadata($a['an_data']); ?></td>
                                            <td>
                                                <div class="view-cli-btns">
                                                    <a href="edit_an.php?anid=<?php echo $a['id']; ?>" class="buttonc"><i class="fa-regular fa-pen-to-square"></i> Editar</a>
                                                    <a href="delete_an.php?anid=<?php echo $a['id']; ?>&client_id=<?php echo $cli['id']; ?>" class="buttonc" onclick="return confirm('Confirma remoção desta anamnese?');"><i class="fa fa-trash"></i> Excluir</a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Não há anamnese cadastrada para este cliente.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.view-cli-btns {
    display: flex;
    gap: 0;
    margin-top: auto;
    justify-content: center;
    width: 100%;
    flex-wrap: wrap;
}
.anamnese-panel {
    background-color: transparent;
    padding: 1rem;
}

@media (max-width: 480px) {
    .view-cli-btns {
        flex-direction: column;
        gap: 0.5rem;
    }
    .buttonc {
        width: 100%;
        justify-content: center;

    }
    .view-cli-btns{
        padding-left: 5%;
        padding-right: 5%;
        justify-content: center;
    }
}
</style>

<?php include(FOOTER_TEMPLATE); ?>
