<?php
    include('functions.php');

    // valida client_id vindo via GET ou POST
    $client_id = null;
    if (!empty($_GET['client_id'])) $client_id = intval($_GET['client_id']);
    if (!empty($_POST['anamnese']['id_cli'])) $client_id = intval($_POST['anamnese']['id_cli']);

    // se for POST, processa a submissão
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['anamnese'])) {
        add_an();
        // não exit aqui: se ocorrer erro, add_an() define $_SESSION['message'] e
        // queremos renderizar o formulário novamente com a mensagem.
    }

    if (!$client_id) {
        header('Location: index.php');
        exit;
    }

    view($client_id);

    include(INIT);
    include(HEADER_TEMPLATE);
?>

<section class="clientes section-light section-cor3 py-5" id="anamnese">
     <div class="container mt-5" style="margin-top: 6rem !important;">
            <h2  class="txt1 mb-1 text-center">Nova Anamnese</h2>
            <p class="txt4 text-center mb-2">Adicione uma nova Anamnese para o seu Cliente.</p>
            <hr>

            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo !empty($_SESSION['type']) ? $_SESSION['type'] : 'info'; ?>">
                    <?php echo $_SESSION['message']; ?>
                </div>
                <?php $_SESSION['message'] = null; $_SESSION['type'] = null; ?>
            <?php endif; ?>

            <form action="add_an.php" method="post">
                <!-- area de campos do form -->
                <div class="row justify-content-center">
                    <div class="col-md-10 d-flex gap-2 form">
                        <input type="hidden" name="anamnese[id_cli]" value="<?php echo $client_id; ?>">
                        <div class="form-group mb-3">
                            <label for="an_hipertensao">Tem histórico de Hipertensão?</label>
                            <input type="text" class="form-control" id="an_hipertensao" name="anamnese[an_hipertensao]" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="an_diabetes">Tem histórico de Diabetes</label>
                            <input type="text" class="form-control" id="an_diabetes" name="anamnese[an_diabetes]" required>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-10 d-flex gap-2">
                        <div class="col-md-5 d-flex gap-2">
                            <div class="form-group mb-3">
                                <label for="an_medic">Está fazendo uso de alguma Medicação?</label>
                                <input type="text" class="form-control text-center" id="an_medic" name="anamnese[an_medic]" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="an_data">Data / Hora</label>
                                <input type="datetime-local" class="form-control text-center" id="an_data" name="anamnese[an_data]" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row justify-content-center">
                    <div id="actions" class="col-md-6 mt-3">
                        <div class="d-flex justify-content-center gap-2">
                            <button type="submit" class="buttonc"><i class="fa-solid fa-check"></i> Salvar</button>
                            <a href="index.php" class="buttona" style="text-decoration: none;"><i class="fa-solid fa-arrow-rotate-left"></i> Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>