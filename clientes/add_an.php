<?php
include('functions.php');

// só administrador pode acessar
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['anamnese'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    error_log("Processando POST em add_an.php");
    error_log("Dados POST: " . print_r($_POST['anamnese'], true));
    
    try {
        if (add_an()) {
            $_SESSION['message'] = "Anamnese cadastrada com sucesso!";
            $_SESSION['type'] = "success";
            error_log("Sucesso - Redirecionando para view.php?id=" . $client_id);
            header("Location: view.php?id=" . $client_id);
        } else {
            throw new Exception("Falha ao salvar anamnese");
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['type'] = "danger";
        error_log("Erro - Redirecionando de volta para add_an.php");
        header("Location: " . $_SERVER['PHP_SELF'] . "?client_id=" . $client_id);
    }
    exit;
}

view($client_id);

include(INIT);
include(HEADER_TEMPLATE);
?>

<style>
    :root {
        --card-bg: var(--cor1);
        --card-border: rgba(13,110,253,0.08);
        --radius: 10px;
    }

    .section-box {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 1.5rem; 
        box-shadow: 0 6px 20px rgba(29, 45, 62, 0.06);
    }

    .form-group {
        --bs-gutter-x: 1rem !important;
        width: 100%;
        margin-bottom: 1rem;
    }

    label {
        display: block;
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 0.4rem;
    }

    .radio-group {
        display: flex;
        gap: 18px;
        margin-top: 6px;
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid rgba(13,110,253,0.03);
        justify-content: center;
    }

    .form-check {
        margin: 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--cor7);
        font-weight: 500;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 0;
    }

    .form-check-input:checked {
        background-color: var(--cor7);
        border-color: var(--cor7);
    }

    .form-control {
        padding: 0.55rem 0.9rem;
        border-radius: 8px;
        border: 1px solid rgba(33,53,71,0.08);
        box-shadow: none;
        transition: border-color 0.12s ease, box-shadow 0.12s ease;
    }

    @media (max-width: 991px) {
        .section-box { padding: 16px; }
      }

    @media (max-width: 767px) {
        .radio-group { 
            gap: 10px;
            padding: 6px 8px; 
        }
        .col-md-2, .col-md-3, .col-md-11 { 
            width: 100% !important; 
            max-width: 100% !important; 
            flex: 0 0 100% !important; }
        .section-box { 
            padding: 12px; 
            width: 85%;
        }
    }
</style>
<section class="clientes section-light section-cor3 py-5" id="anamnese">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <div class="text-center mb-4">
            <h2 class="txt1 mb-2">Nova Anamnese</h2>
            <p class="txt4">Adicione uma nova Anamnese para o seu Cliente.</p>
        </div>
        <hr>
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo !empty($_SESSION['type']) ? $_SESSION['type'] : 'info'; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php $_SESSION['message'] = null; $_SESSION['type'] = null; ?>
        <?php endif; ?>

        <form action="add_an.php" method="post">
            <!-- area de campos do form -->
            <input type="hidden" name="anamnese[id_cli]" value="<?php echo $client_id; ?>">
            <div class="row justify-content-center mb-4">
                <div class="col-md-8 section-box">
                    <div class="row gx-4 gy-4 justify-content-evenly">
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Fumante:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_fumante]" id="fumante_sim" value="Sim" required>
                                    <label class="form-check-label" for="fumante_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_fumante]" id="fumante_nao" value="Não">
                                    <label class="form-check-label" for="fumante_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Queloide:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_queloide]" id="queloide_sim" value="Sim" required>
                                    <label class="form-check-label" for="queloide_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_queloide]" id="queloide_nao" value="Não">
                                    <label class="form-check-label" for="queloide_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Gravidez:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_gravidez]" id="gravidez_sim" value="Sim" required>
                                    <label class="form-check-label" for="gravidez_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_gravidez]" id="gravidez_nao" value="Não">
                                    <label class="form-check-label" for="gravidez_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Depressão:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_depressao]" id="depressao_sim" value="Sim" required>
                                    <label class="form-check-label" for="depressao_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_depressao]" id="depressao_nao" value="Não">
                                    <label class="form-check-label" for="depressao_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-4">
                <div class="col-md-8 section-box">
                    <div class="row gx-4 gy-4 justify-content-evenly">
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>HIV:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hiv]" id="hiv_sim" value="Sim" required>
                                    <label class="form-check-label" for="hiv_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hiv]" id="hiv_nao" value="Não">
                                    <label class="form-check-label" for="hiv_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Herpes:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_herpes]" id="herpes_sim" value="Sim" required>
                                    <label class="form-check-label" for="herpes_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_herpes]" id="herpes_nao" value="Não">
                                    <label class="form-check-label" for="herpes_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Câncer:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_cancer]" id="cancer_sim" value="Sim" required>
                                    <label class="form-check-label" for="cancer_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_cancer]" id="cancer_nao" value="Não">
                                    <label class="form-check-label" for="cancer_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Hepatite:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hepatite]" id="hepatite_sim" value="Sim" required>
                                    <label class="form-check-label" for="hepatite_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hepatite]" id="hepatite_nao" value="Não">
                                    <label class="form-check-label" for="hepatite_nao">Não</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-4">
                <div class="col-md-8 section-box">
                    <div class="row gx-4 gy-4 justify-content-evenly">
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Cardiopata:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_cardiopata]" id="cardiopata_sim" value="Sim" required>
                                    <label class="form-check-label" for="cardiopata_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_cardiopata]" id="cardiopata_nao" value="Não">
                                    <label class="form-check-label" for="cardiopata_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Anemia:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_anemia]" id="anemia_sim" value="Sim" required>
                                    <label class="form-check-label" for="anemia_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_anemia]" id="anemia_nao" value="Não">
                                    <label class="form-check-label" for="anemia_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Hipertensão:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hipertensao]" id="hipertensao_sim" value="Sim" required>
                                    <label class="form-check-label" for="hipertensao_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hipertensao]" id="hipertensao_nao" value="Não">
                                    <label class="form-check-label" for="hipertensao_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Diabetes:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_diabetes]" id="diabetes_sim" value="Sim" required>
                                    <label class="form-check-label" for="diabetes_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_diabetes]" id="diabetes_nao" value="Não">
                                    <label class="form-check-label" for="diabetes_nao">Não</label>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>

            <div class="row justify-content-center mb-4">
                <div class="col-md-8 section-box">
                    <div class="row gx-4 gy-4 justify-content-evenly">
                        <div class="col-md-4 mb-4">
                            <label class="mb-2"><h5>Doença de Pele:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_pele]" id="doenca_pele_sim" value="Sim" required>
                                    <label class="form-check-label" for="doenca_pele_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_pele]" id="doenca_pele_nao" value="Não">
                                    <label class="form-check-label" for="doenca_pele_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Alergia:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_alergia]" id="alergia_sim" value="Sim" required>
                                    <label class="form-check-label" for="alergia_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_alergia]" id="alergia_nao" value="Não">
                                    <label class="form-check-label" for="alergia_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Glaucoma:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_glaucoma]" id="glaucoma_sim" value="Sim" required>
                                    <label class="form-check-label" for="glaucoma_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_glaucoma]" id="glaucoma_nao" value="Não">
                                    <label class="form-check-label" for="glaucoma_nao">Não</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-4">
                <div class="col-md-8 section-box">
                    <div class="row gx-4 gy-4 justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="an_medic">Toma medicação contínua?</label>
                                <input type="text" class="form-control" id="an_medic" name="anamnese[an_medic]" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="an_acne">Toma medicação para acne?</label>
                                <input type="text" class="form-control" id="an_acne" name="anamnese[an_acne]" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="an_outro">Possui outro problema de saúde?</label>
                                <input type="text" class="form-control" id="an_outro" name="anamnese[an_outro]" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="an_data">Data de Criação</label>
                                <input type="date" class="form-control text-center" id="an_data" name="anamnese[an_data]" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div id="actions" class="col-md-6 mt-4">
                    <div class="d-flex justify-content-center gap-3">
                        <button type="submit" class="buttonc"><i class="fa-solid fa-check"></i> Salvar</button>
                        <a href="view.php?id=<?php echo $cli['id']; ?>" class="buttona"><i class="fa-solid fa-arrow-rotate-left"></i> Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>