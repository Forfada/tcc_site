<?php
include('functions.php');

// somente administrador pode acessar a área de clientes
if (!function_exists('is_admin') || !is_admin()) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['message'] = "Você não pode acessar essa funcionalidade.";
    $_SESSION['type'] = "danger";
    header("Location: " . BASEURL . "index.php");
    exit;
}

// iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) session_start();

// valida client_id vindo via GET ou POST
$client_id = $_GET['client_id'] ?? $_POST['anamnese']['id_cli'] ?? null;
$client_id = $client_id ? intval($client_id) : null;

if (!$client_id) {
    $_SESSION['message'] = "Cliente não definido.";
    $_SESSION['type'] = "danger";
    header('Location: index.php');
    exit;
}

// processa submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['anamnese'])) {
    $success = add_an(); // assume que add_an() retorna true/false
    if ($success) {
        $_SESSION['message'] = "Erro ao cadastrar anamnese.";
        $_SESSION['type'] = "danger";
    } else {
        $_SESSION['message'] = "Anamnese cadastrada com sucesso!";
        $_SESSION['type'] = "success";
    }
    header("Location: view.php?id=" . $client_id);
    exit;
}

// carrega dados do cliente para o formulário
view($client_id);

include(INIT);
include(HEADER_TEMPLATE);
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
                    <div class="col-md-6 d-flex justify-content-center gap-2 form">
                        <input type="hidden" name="anamnese[id_cli]" value="<?php echo $client_id; ?>">
                        <div class="form-group mb-3">
                            <label for="an_hipertensao">Possui histórico de Hipertensão?</label>
                            <input type="text" class="form-control text-center" id="an_hipertensao" name="anamnese[an_hipertensao]" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="an_diabetes">Possui histórico de Diabetes?</label>
                            <input type="text" class="form-control text-center" id="an_diabetes" name="anamnese[an_diabetes]" required>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6 d-flex justify-content-center gap-2">
                        <div class="form-group mb-3 mt-4">
                            <label for="an_cancer">Possui histórico de Câncer?</label>
                            <input type="text" class="form-control text-center" id="an_cancer" name="anamnese[an_cancer]" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="an_medic">Está fazendo uso de alguma Medicação? Se sim, qual?</label>
                            <input type="text" class="form-control text-center" id="an_medic" name="anamnese[an_medic]" required>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-3 d-flex justify-content-center gap-2">
                        <div class="form-group mb-3">
                            <label for="an_data">Data de Criação</label>
                            <input type="date" class="form-control text-center" id="an_data" name="anamnese[an_data]" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row justify-content-center">
                    <div id="actions" class="col-md-6 mt-3">
                        <div class="d-flex justify-content-center gap-2">
                            <button type="submit" class="buttonc"><i class="fa-solid fa-check"></i> Salvar</button>
                            <a href="view.php?id=<?php echo $cli['id']; ?>" class="buttona"><i class="fa-solid fa-arrow-rotate-left"></i> Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>