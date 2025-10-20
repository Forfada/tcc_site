<?php
    include 'functions.php';
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
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="txt1"><?php echo $cli['cli_nome']; ?></h2>
                <div class="d-flex gap-2">
                    <a href="index.php" class="btn btn-dark btn-sm">&larr; Voltar</a>
                    <a href="edit.php?id=<?php echo $cli['id']; ?>" class="btn btn-secondary btn-sm">Editar</a>
                </div>
            </div>
        </div>

        <div class="row align-items-center mb-4">
            <div class="col-md-3 text-center mb-3 mb-md-0">
                <!-- Avatar estilizado -->
                <div class="avatar-placeholder mx-auto mb-1"><?php echo strtoupper(substr($cli['cli_nome'],0,1)); ?></div>
                <h3 class="txt4"><?php echo htmlspecialchars($cli['cli_nome']); ?></h3>
            </div>

            <div class="col-md-9">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    
                </div>

                <div class="info-grid">
                    <div>
                        <span class="info-label">Idade</span><div class="info-value"><?php echo htmlspecialchars($cli['cli_idade']); ?></div>
                        <span class="info-label">CPF</span><div class="info-value"><?php echo cpf($cli['cli_cpf']); ?></div>
                    </div>
                    <div>
                        <span class="info-label">Telefone</span><div class="info-value"><?php echo telefone($cli['cli_num']); ?></div>
                        <span class="info-label">Nascimento</span><div class="info-value"><?php echo formatadata($cli['cli_nasc'], 'd/m/Y'); ?></div>
                    </div>
                    <div>
                        <span class="info-label">Observação</span><div class="info-value"><?php echo $cli['cli_obs']; ?></div>
                    </div>
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
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
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
                                            <a href="edit_an.php?anid=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                            <a href="delete_an.php?anid=<?php echo $a['id']; ?>&client_id=<?php echo $cli['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Confirma remoção desta anamnese?');">Excluir</a>
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
</section>

<style>
    .clientes table th {
        background-color: #ffffffff;
        font-weight: 600;
        white-space: nowrap;
    }

    .clientes table td, 
    .clientes table th {
        vertical-align: middle;
    }

    .clientes .table {
        border-color: #dee2e6;
        box-shadow: 0 6px 18px rgba(38,57,77,0.04);
    }

    .clientes .border {
        border-color: #e3e6ea !important;
    }

    .clientes .shadow-sm {
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05) !important;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .clientes .shadow-sm:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08) !important;
    }

    @media (max-width: 768px) {
        .clientes .p-4 {
            padding: 1.25rem !important;
        }
        .clientes table th,
        .clientes table td {
            font-size: 0.9rem;
        }
    }

    .avatar-placeholder {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #910dfdff 0%, #350715ff 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 2rem;
        margin: 0 auto;
        box-shadow: 0 6px 20px rgba(13,110,253,0.12);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem 1.5rem;
        margin-top: .5rem;
        justify-items: start;
        justify-content: start;
    }

    .info-label {
        display: block;
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: .25rem;
    }

    .info-value {
        font-size: 1rem;
        color: #212529;
        font-weight: 600;
    }

    .anamnese-panel {
        background-color: transparent;
        padding: 1rem;
        
    }
</style>

<?php include(FOOTER_TEMPLATE); ?>
