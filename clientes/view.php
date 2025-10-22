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
                                            <th>Queloide</th>
                                            <th>Hepatite</th>
                                            <th>Cardiopata</th>
                                            <th style="width: 5%">Anemia</th>
                                            <th>Depressão</th>
                                            <th>Glaucoma</th>
                                            <th style="width: 2%">HIV</th>
                                            <th>Doença Pele</th>
                                            <th>Outro</th>
                                            <th>Registrado em</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($anamnese as $a): ?>
                                        <!-- linha superior: campos compactos + medicações + ações -->
                                        <tr>
                                            <td><?php echo htmlspecialchars($a['an_queloide']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_hepatite']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_cardiopata']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_anemia']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_depressao']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_glaucoma']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_hiv']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_pele']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_outro']); ?></td>
                                            <td><?php echo formatadata($a['an_data']); ?></td>
                                        </tr>
                                    </tbody>
                                    <thead>
                                        <tr>
                                            <th>Hipertensão</th>
                                            <th>Diabetes</th>
                                            <th style="width: 5%">Câncer</th>
                                            <th>Fumante</th>
                                            <th style="width: 5%">Alergia</th>
                                            <th>Gravidez</th>
                                            <th style="width: 5%">Herpes</th>
                                            <th>Remédio Acne</th>
                                            <th>Medicações</th>
                                            <th>Ações</th>
                                        </tr>
                                        
                                    </thead>
                                     <tbody>
                                        <tr>
                                            <td><?php echo htmlspecialchars($a['an_hipertensao']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_diabetes']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_cancer']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_fumante']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_alergia']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_gravidez']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_herpes']); ?></td>
                                            <td><?php echo htmlspecialchars($a['an_acne']); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($a['an_medic'])); ?></td>
                                            <td>
                                                <div class="view-cli-btns">
                                                    <a href="edit_an.php?anid=<?php echo $a['id']; ?>" class="buttonc"><i class="fa-regular fa-pen-to-square"></i> Editar</a>
                                                    <a href="delete_an.php?anid=<?php echo $a['id']; ?>&client_id=<?php echo $cli['id']; ?>" class="buttonc" onclick="return confirm('Confirma remoção desta anamnese?');"><i class="fa fa-trash"></i> Excluir</a>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- linha inferior: campos restantes + data -->
                                        
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
    margin-top: auto;
    justify-content: center;
    width: 90%;
    flex-wrap: nowrap;
    gap: 4px !important;            /* menor espaçamento entre botões */
    margin: 0 !important;
    padding: 0 !important;
}
.anamnese-panel { 
    background-color: transparent; 
    padding: 1rem; 
}

/* container de botões da anamnese: sempre em linha, sem wrap */
.anamnese-panel .view-cli-btns {
    flex-direction: row !important;
}

/* botões dentro da anamnese: compactos e inline */
.anamnese-panel .view-cli-btns a.buttonc,
.anamnese-panel .tabela-lunaris a.buttonc {
    white-space: nowrap !important;
}

/* manter células compactas na anamnese (reduz padding/font) */
.anamnese-panel .tabela-lunaris thead th,
.anamnese-panel .tabela-lunaris tbody td {
    padding: 6px 8px;
    font-size: 0.85rem;
}

/* garantir que os links/botões fiquem compactos */
.view-cli-btns a.buttonc,
.view-cli-btns a.buttona,
.anamnese-panel .tabela-lunaris a.buttonc {
    margin: 0 !important;           /* remove margens extras */
    padding: 8px 8px !important;    /* botões mais compactos */
    font-size: 1rem !important;
    line-height: 1.1rem !important;
    display: inline-flex !important;
    gap: 6px !important;            /* espaço interno entre ícone/texto */
    white-space: nowrap !important;
    min-width: 0 !important;
}

/* manter células compactas na anamnese (reduz padding/font) */
.anamnese-panel .tabela-lunaris thead th,
.anamnese-panel .tabela-lunaris tbody td {
    padding: 6px 8px;
    font-size: 0.85rem;
}

/* evitar quebra inesperada dos botões na célula de ações */
.anamnese-panel .tabela-lunaris tbody td:last-child,
.tabela-lunaris thead th:last-child {
    white-space: nowrap !important;
}

/* pequena proteção em telas muito pequenas: permitir scroll horizontal se necessário */
@media (max-width: 480px) {
    .view-cli-btns { gap: 6px !important; }
    .view-cli-btns a.buttonc,
    .view-cli-btns a.buttona { padding: 6px 8px !important; font-size: 0.9rem !important; }
    .anamnese-panel .tabela-wrapper { overflow-x: auto; }
}
</style>
</style>

<?php include(FOOTER_TEMPLATE); ?>
