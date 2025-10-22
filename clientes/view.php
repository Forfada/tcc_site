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
                                            <th style="width: 5%">Câncer</th>
                                            <th>Fumante</th>
                                            <th style="width: 5%">Alergia</th>
                                            <th>Gravidez</th>
                                            <th style="width: 5%">Herpes</th>
                                            <th>Queloide</th>
                                            <th>Hepatite</th>
                                            <th>Cardiopata</th>
                                            <th style="width: 5%">Anemia</th>
                                            <th>Depressão</th>
                                            <th>Glaucoma</th>
                                            <th style="width: 2%">HIV</th>
                                            <th>Doença Pele</th>
                                            <th>Remédio Acne</th>
                                            <th>Outro</th>
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
                                            <td style="width: 5%"><?php echo htmlspecialchars($a['an_cancer']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_fumante']); ?></td>
                                            <td style="width: 5%"><?php echo htmlspecialchars($a['an_alergia']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_gravidez']); ?></td>
                                            <td style="width: 5%"><?php echo htmlspecialchars($a['an_herpes']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_queloide']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_hepatite']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_cardiopata']); ?></td>
                                            <td style="width: 2%"><?php echo htmlspecialchars($a['an_anemia']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_depressao']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_glaucoma']); ?></td>
                                            <td style="width: 5%"><?php echo htmlspecialchars($a['an_hiv']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_pele']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_acne']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_outro']); ?></td>
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

/* ===== Compact table buttons and tighter anamnese columns ===== */
/* reduce cell padding / font-size only for the anamnese table */
.anamnese-panel .tabela-lunaris thead th,
.anamnese-panel .tabela-lunaris tbody td {
    padding: 6px 8px;
    font-size: 0.86rem;
    vertical-align: middle;
}

/* compact buttons/links inside the anamnese table */
.anamnese-panel .tabela-lunaris button,
.anamnese-panel .tabela-lunaris a.buttonc,
.anamnese-panel .view-cli-btns a.buttonc {
    padding: 6px 8px;
    font-size: 1rem;
    border-radius: 6px;
    line-height: 0.5rem;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    height: auto;
}

/* force narrower widths for the many short boolean columns (override inline styles if present) */
.anamnese-panel .tabela-lunaris thead th:nth-child(-n+14),
.anamnese-panel .tabela-lunaris tbody td:nth-child(-n+14) {
    width: 5% !important;
    max-width: 5% !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* slightly wider for descriptive small columns */
.anamnese-panel .tabela-lunaris thead th:nth-child(15),
.anamnese-panel .tabela-lunaris tbody td:nth-child(15) {
    width: 9% !important;
    max-width: 9% !important;
}
.anamnese-panel .tabela-lunaris thead th:nth-child(16),
.anamnese-panel .tabela-lunaris tbody td:nth-child(16) {
    width: 9% !important;
    max-width: 9% !important;
}
.anamnese-panel .tabela-lunaris thead th:nth-child(17),
.anamnese-panel .tabela-lunaris tbody td:nth-child(17) {
    width: 9% !important;
    max-width: 9% !important;
}

/* medicações, data e ações */
.anamnese-panel .tabela-lunaris thead th:nth-child(18),
.anamnese-panel .tabela-lunaris tbody td:nth-child(18) {
    width: 12% !important;
    max-width: 12% !important;
}
.anamnese-panel .tabela-lunaris thead th:nth-child(19),
.anamnese-panel .tabela-lunaris tbody td:nth-child(19) {
    width: 8% !important;
    max-width: 8% !important;
    white-space: nowrap;
}
.anamnese-panel .tabela-lunaris thead th:nth-child(20),
.anamnese-panel .tabela-lunaris tbody td:nth-child(20) {
    width: 10% !important;
    max-width: 10% !important;
}

/* responsive adjustments so table remains usable on very small screens */
@media (max-width: 768px) {
    .anamnese-panel .tabela-lunaris thead th,
    .anamnese-panel .tabela-lunaris tbody td {
        padding: 6px 6px;
        font-size: 0.84rem;
    }
    .anamnese-panel .tabela-lunaris thead th:nth-child(-n+14),
    .anamnese-panel .tabela-lunaris tbody td:nth-child(-n+14) {
        width: 6% !important;
        max-width: 6% !important;
    }
    .anamnese-panel .tabela-lunaris thead th:nth-child(18),
    .anamnese-panel .tabela-lunaris tbody td:nth-child(18),
    .anamnese-panel .tabela-lunaris thead th:nth-child(20),
    .anamnese-panel .tabela-lunaris tbody td:nth-child(20) {
        width: 12% !important;
    }
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
    /* allow table to scroll naturally on very small screens */
    .anamnese-panel .tabela-wrapper { overflow-x: auto; }
    .anamnese-panel .tabela-lunaris thead th,
    .anamnese-panel .tabela-lunaris tbody td {
        white-space: nowrap;
    }
}
</style>

<?php include(FOOTER_TEMPLATE); ?>
