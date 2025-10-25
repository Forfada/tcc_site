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
        header('Location: index.php');
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

<style>
.view-cli-btns {
    display: flex;
    margin-top: auto;
    justify-content: center;
    width: 90%;
    flex-wrap: nowrap;
    gap: 4px !important;
    margin: 0 !important;
    padding: 0 !important;
}

.anamnese-panel { 
    background-color: transparent; 
    padding: 1rem; 
}

.anamnese-panel .view-cli-btns {
    flex-direction: row !important;
}

.anamnese-panel .view-cli-btns a.buttonc,
.anamnese-panel .tabela-lunaris a.buttonc {
    white-space: nowrap !important;
}

.anamnese-panel .tabela-lunaris thead th,
.anamnese-panel .tabela-lunaris tbody td {
    padding: 8px 8px;
    font-size: 1rem;
}

.view-cli-btns a.buttonc,
.anamnese-panel .tabela-lunaris a.buttonc {
    margin: 0 !important;
    padding: 8px 8px !important;
    font-size: 1rem !important;
    line-height: 1.1rem !important;
}

@media (max-width: 768px) {
    .view-cli-btns {
        flex-wrap: wrap;
        gap: 4px !important;
        justify-content: flex-end;
        width: auto;
    }
}

@media (max-width: 480px) {
    .anamnese-panel .tabela-wrapper { overflow-x: auto; }
}

.modal-card-custom {
    border-radius: 12px;
    padding: 20px 22px;
    background: #fff;
    box-shadow: 0 12px 30px rgba(0,0,0,0.25);
    color: #222;
}

.modal-title-custom {
    margin-top: 0;
    color: #73213d;
}

.modal-text {
    margin-top: 10px;
    color: #444;
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 14px;
}

.modal-actions .buttonc {
    padding: 10px 14px;
    border-radius: 8px;
}

.modal-actions .buttonc.cancel {
    background: #ccc;
    color: #333;
}
</style>

<section class="clientes section-light section-cor3 py-5" id="clientes">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h2 class="txt1 mb-1 text-center">Visualização de Cliente</h2>
        <p class="txt4 text-center mb-2">Acompanhe os dados cadastrados completos deste cliente.</p>
        <div class="col-12 col-md-12 text-md-end mt-3 mt-md-0">  
            <a href="index.php" class="buttona"><i class="fa-solid fa-arrow-rotate-left"></i> Voltar</a>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="tabela-wrapper">
                    <table class="tabela-lunaris">
                        <thead>
                            <tr>
                                <th scope="col" colspan="6" class="mb-0 text-start"><h5>Cliente: <?php echo $cli['cli_nome']; ?></h5></th>
                                <th scope="col" colspan="1" class="mb-0">
                                    <div class="justify-content-end view-cli-btns">
                                        <a href="edit.php?id=<?php echo $cli['id']; ?>" class="buttonc"><i class="fa-regular fa-pen-to-square"></i> Editar</a>
                                        <a href="#" class="buttonc" data-bs-toggle="modal" data-bs-target="#delete-cli-modal" data-clientes="<?php echo $cli['id'];?>">
                                            <i class="fa fa-trash  me-2"></i> Excluir
                                        </a>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th width="5%">Idade</th>
                                <th width="17%">Cidade</th>
                                <th width="8%">Sexo</th>
                                <th width="14%">CPF</th>
                                <th width="15%">Telefone</th>
                                <th width="10%">Nascimento</th>
                                <th width="31%">Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>  
                                <td><?php echo htmlspecialchars($cli['cli_idade']); ?></td>
                                <td><?php echo htmlspecialchars($cli['cli_cidade']); ?></td>
                                <td><?php echo htmlspecialchars($cli['cli_sexo']); ?></td>
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

        <div class="row my-5">
            <div class="col-md-12">
                <div class="anamnese-panel p-3">
                    <h4 class="mb-0 text-md-start">Anamnese</h4>
                    <div class="col-12 col-md-12 text-md-end mt-2 mt-md-0">
                        <a href="add_an.php?client_id=<?php echo $cli['id']; ?>" class="buttonc" style="text-decoration: none"><i class="fa fa-plus"></i> Adicionar Anamnese</a>
                    </div>
                    <hr>
                    <?php if (!empty($anamnese) && is_array($anamnese)): ?>
                        <?php foreach ($anamnese as $a): ?>
                        <div class="tabela-wrapper">
                            <table class="tabela-lunaris">
                                <thead>
                                    <tr>
                                        <th>Queloide</th>
                                        <th>Hepatite</th>
                                        <th>Cardiopata</th>
                                        <th>Anemia</th>
                                        <th>Depressão</th>
                                        <th>Glaucoma</th>
                                        <th>HIV</th>
                                        <th>Doença Pele</th>
                                        <th>Outro</th>
                                        <th>Registrado em</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo htmlspecialchars($a['an_queloide']); ?></td>
                                        <td><?php echo htmlspecialchars($a['an_hepatite']); ?></td>
                                        <td><?php echo htmlspecialchars($a['an_cardiopata']); ?></td>
                                        <td><?php echo htmlspecialchars($a['an_anemia']); ?></td>
                                        <td><?php echo htmlspecialchars($a['an_depressao']); ?></td>
                                        <td><?php echo htmlspecialchars($a['an_glaucoma']); ?></td>
                                        <td><?php echo htmlspecialchars($a['an_hiv']); ?></td>
                                        <td style="word-wrap: break-word; word-break: break-word;"><?php echo htmlspecialchars($a['an_pele']); ?></td>
                                        <td style="word-wrap: break-word; word-break: break-word;"><?php echo htmlspecialchars($a['an_outro']); ?></td>
                                        <td><?php echo formatadata($a['an_data']); ?></td>
                                    </tr>
                                </tbody>
                                <thead>
                                    <tr>
                                        <th>Hipertensão</th>
                                        <th>Diabetes</th>
                                        <th>Câncer</th>
                                        <th>Fumante</th>
                                        <th>Alergia</th>
                                        <th>Gravidez</th>
                                        <th>Herpes</th>
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
                                        <td style="word-wrap: break-word; word-break: break-word;"><?php echo htmlspecialchars($a['an_acne']); ?></td>
                                        <td style="word-wrap: break-word; word-break: break-word;"><?php echo nl2br(htmlspecialchars($a['an_medic'])); ?></td>
                                        <td>
                                            <div class="view-cli-btns">
                                                <a href="edit_an.php?anid=<?php echo $a['id']; ?>" class="buttonc"><i class="fa-regular fa-pen-to-square"></i> Editar</a>
                                                <a href="#" class="buttonc btn-excluir-anamnese" 
                                                    data-id="<?php echo $a['id']; ?>" 
                                                    data-client="<?php echo $cli['id']; ?>" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteAnamneseModal">
                                                    <i class="fa fa-trash"></i> Excluir
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Não há anamnese cadastrada para este cliente.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal: Excluir Anamnese -->
<div class="modal fade" id="deleteAnamneseModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-card-custom">
            <div class="modal-body text-center">
                <h3 class="modal-title-custom">Excluir Anamnese</h3>
                <p class="modal-text">Deseja realmente excluir esta anamnese? Esta ação não pode ser desfeita.</p>

                <div class="modal-actions d-flex justify-content-center gap-3">
                    <a type="button" class="buttonc gap" id="confirmDeleteAnamnese" href="#">
                        <i class="fa-solid fa-check"></i> Sim, excluir
                    </a>
                    <button type="button" class="buttonc cancel" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const confirmBtn = document.getElementById('confirmDeleteAnamnese');
document.querySelectorAll('.btn-excluir-anamnese').forEach(btn => {
    btn.addEventListener('click', function() {
        const anamneseId = this.dataset.id;
        const clientId = this.dataset.client;
        confirmBtn.href = `delete_an.php?anid=${anamneseId}&client_id=${clientId}`;
    });
});
</script>

<?php include("modal.php"); ?>
<?php include(FOOTER_TEMPLATE); ?>
