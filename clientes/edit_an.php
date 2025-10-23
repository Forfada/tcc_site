<?php
include 'functions.php';

// só administrador pode acessar
if (!function_exists('is_admin') || !is_admin()) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['message'] = "Você não pode acessar essa funcionalidade.";
    $_SESSION['type'] = "danger";
    header("Location: " . BASEURL . "index.php");
    exit;
}

// se for GET e tiver 'anid', carrega os dados da anamnese para edição
if (isset($_GET['anid']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $anid = intval($_GET['anid']);
    edit_an(); // deve preencher $anamnese global

    global $anamnese;
    if (empty($anamnese)) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['message'] = "Anamnese não encontrada.";
        $_SESSION['type'] = "danger";
        header('Location: index.php');
        exit;
    }

    $client_id = $anamnese['id_cli'];
    include(INIT);
    include(HEADER_TEMPLATE);
}

// se for POST, processa a atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['anamnese']) && isset($_GET['anid'])) {
    $success = edit_an(); // função deve retornar true/false

    if (session_status() === PHP_SESSION_NONE) session_start();
    if ($success) {
        $_SESSION['message'] = "Erro ao atualizar anamnese.";
        $_SESSION['type'] = "danger";
    } else {
        $_SESSION['message'] = "Anamnese atualizada com sucesso!";
        $_SESSION['type'] = "success";
    }

    // redireciona para a página do cliente
    header('Location: view.php?id=' . intval($_POST['anamnese']['id_cli']));
    exit;
}
?>



<style>
    .form-group{
        --bs-gutter-x: 1rem !important;
        width: 100%;
    }

    label {
        font-size: 1rem;
        }

    @media (max-width: 767px) {
        .form {
            flex-direction: column;
            display: flex;
            gap: 0;
        }
        .form-group {
            width: 100%;
            margin-left: 0;
            margin-top: 0.5rem;
        }
        .d-flex {
            flex-wrap: wrap;
        }
        .col-md-5.d-flex {
            flex-direction: row;
            flex-wrap: nowrap;
            width: 100%;
            gap: 0.5rem;
        }
    }
</style>
<section class="clientes section-light section-cor3 py-5" id="clientes">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h2  class="txt1 mb-1 text-center">Editação de Anamnese</h2>
        <p class="txt4 text-center mb-2"> Edite as informações de uma Anamnese e a mantenha atualizada.</p>
        <hr>

         <form method="post" action="edit_an.php?anid=<?php echo intval($_GET['anid']); ?>">
            <!-- area de campos do form -->
            <div class="row justify-content-center">
                <div class="col-md-6 d-flex justify-content-center gap-2 form">
                    <input type="hidden" name="anamnese[id_cli]" value="<?php echo $client_id; ?>">
                    <div class="form-group mb-3">
                        <label for="an_hipertensao">Possui histórico de Hipertensão?</label>
                        <input type="text" class="form-control text-center" id="an_hipertensao" name="anamnese[an_hipertensao]" value="<?php echo htmlspecialchars($anamnese['an_hipertensao']); ?>">
                    </div>
                    <div class="form-group mb-3">
                        <label for="an_diabetes">Possui histórico de Diabetes?</label>
                        <input type="text" class="form-control text-center" id="an_diabetes" name="anamnese[an_diabetes]" value="<?php echo htmlspecialchars($anamnese['an_diabetes']); ?>">
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-6 d-flex justify-content-center gap-2">
                    <div class="form-group mb-3 mt-4">
                        <label for="an_cancer">Possui histórico de Câncer?</label>
                        <input type="text" class="form-control text-center" id="an_cancer" name="anamnese[an_cancer]" value="<?php echo htmlspecialchars($anamnese['an_cancer']); ?>">
                    </div>
                    <div class="form-group mb-3">
                        <label for="an_medic">Está fazendo uso de alguma Medicação? Se sim, qual?</label>
                        <input type="text" class="form-control text-center" id="an_medic" name="anamnese[an_medic]" value="<?php echo htmlspecialchars($anamnese['an_medic']); ?>">
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-3 d-flex justify-content-center gap-2">
                    
                <div class="form-group mb-3">
                    <label class="form-label">Data / Hora</label>
                    <input type="date" name="anamnese[an_data]" class="form-control" value="<?php echo date('Y-m-d', strtotime($anamnese['an_data'])); ?>">
                </div>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div id="actions" class="col-md-6 mt-3">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" class="buttonc"><i class="fa-solid fa-check"></i> Salvar</button>
                        <a href="index.php" class="buttona"><i class="fa-solid fa-arrow-rotate-left"></i> Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>

