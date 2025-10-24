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
    edit_an();

    global $anamnese;
    if (empty($anamnese)) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['message'] = "Anamnese não encontrada.";
        $_SESSION['type'] = "danger";
        header('Location: index.php');
        exit;
    }

    $client_id = $anamnese['id_cli'];
    
    global $cli;
    $cli = find('clientes', $client_id);
    
    include(INIT);
    include(HEADER_TEMPLATE);
}

// se for POST, processa a atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['anamnese']) && isset($_GET['anid'])) {
    // Define valores padrão 'Não' para campos não preenchidos
    foreach (['an_fumante', 'an_queloide', 'an_gravidez', 'an_depressao', 'an_hiv', 'an_herpes', 'an_cancer', 
              'an_hepatite', 'an_cardiopata', 'an_anemia', 'an_hipertensao', 'an_diabetes', 'an_pele', 
              'an_alergia', 'an_glaucoma'] as $field) {
        if (!isset($_POST['anamnese'][$field])) $_POST['anamnese'][$field] = 'Não';
    }
    
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (!($success = edit_an())) {
        $_SESSION['message'] = "Anamnese atualizada com sucesso!";
        $_SESSION['type'] = "success";
        header('Location: view.php?id=' . intval($_POST['anamnese']['id_cli']));
    } else {
        $_SESSION['message'] = "Erro ao atualizar anamnese.";
        $_SESSION['type'] = "danger";
        header('Location: edit_an.php?anid=' . intval($_GET['anid']));
    }
    exit;
}
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
<section class="clientes section-light section-cor3 py-5" id="clientes">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h2  class="txt1 mb-1 text-center">Editação de Anamnese</h2>
        <p class="txt4 text-center mb-2"> Edite as informações de uma Anamnese e a mantenha atualizada.</p>
        <hr>

         <form method="post" action="edit_an.php?anid=<?php echo intval($_GET['anid']); ?>">
             <!-- area de campos do form -->
            <input type="hidden" name="anamnese[id_cli]" value="<?php echo $client_id; ?>">
            <div class="row justify-content-center mb-4">
                <div class="col-md-8 section-box">
                    <div class="row gx-4 gy-4 justify-content-evenly">
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Fumante:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_fumante]" id="fumante_sim" value="sim" <?php echo ($anamnese['an_fumante'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="fumante_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_fumante]" id="fumante_nao" value="não" <?php echo ($anamnese['an_fumante'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="fumante_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Queloide:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_queloide]" id="queloide_sim" value="sim" <?php echo ($anamnese['an_queloide'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="queloide_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_queloide]" id="queloide_nao" value="não" <?php echo ($anamnese['an_queloide'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="queloide_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Gravidez:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_gravidez]" id="gravidez_sim" value="sim" <?php echo ($anamnese['an_gravidez'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="gravidez_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_gravidez]" id="gravidez_nao" value="não" <?php echo ($anamnese['an_gravidez'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="gravidez_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Depressão:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_depressao]" id="depressao_sim" value="sim" <?php echo ($anamnese['an_depressao'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="depressao_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_depressao]" id="depressao_nao" value="não" <?php echo ($anamnese['an_depressao'] == 'Não') ? 'checked' : ''; ?>>
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
                                    <input class="form-check-input" type="radio" name="anamnese[an_hiv]" id="hiv_sim" value="sim" <?php echo ($anamnese['an_hiv'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="hiv_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hiv]" id="hiv_nao" value="não" <?php echo ($anamnese['an_hiv'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="hiv_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Herpes:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_herpes]" id="herpes_sim" value="sim" <?php echo ($anamnese['an_herpes'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="herpes_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_herpes]" id="herpes_nao" value="não" <?php echo ($anamnese['an_herpes'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="herpes_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Câncer:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_cancer]" id="cancer_sim" value="sim" <?php echo ($anamnese['an_cancer'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cancer_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_cancer]" id="cancer_nao" value="não" <?php echo ($anamnese['an_cancer'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cancer_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Hepatite:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hepatite]" id="hepatite_sim" value="sim" <?php echo ($anamnese['an_hepatite'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="hepatite_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hepatite]" id="hepatite_nao" value="não" <?php echo ($anamnese['an_hepatite'] == 'Não') ? 'checked' : ''; ?>>
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
                                    <input class="form-check-input" type="radio" name="anamnese[an_cardiopata]" id="cardiopata_sim" value="sim" <?php echo ($anamnese['an_cardiopata'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cardiopata_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_cardiopata]" id="cardiopata_nao" value="não" <?php echo ($anamnese['an_cardiopata'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cardiopata_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Anemia:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_anemia]" id="anemia_sim" value="sim" <?php echo ($anamnese['an_anemia'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="anemia_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_anemia]" id="anemia_nao" value="não" <?php echo ($anamnese['an_anemia'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="anemia_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Hipertensão:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hipertensao]" id="hipertensao_sim" value="sim" <?php echo ($anamnese['an_hipertensao'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="hipertensao_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_hipertensao]" id="hipertensao_nao" value="não" <?php echo ($anamnese['an_hipertensao'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="hipertensao_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Diabetes:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_diabetes]" id="diabetes_sim" value="sim" <?php echo ($anamnese['an_diabetes'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="diabetes_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_diabetes]" id="diabetes_nao" value="não" <?php echo ($anamnese['an_diabetes'] == 'Não') ? 'checked' : ''; ?>>
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
                                    <input class="form-check-input" type="radio" name="anamnese[an_pele]" id="doenca_pele_sim" value="sim" <?php echo ($anamnese['an_pele'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="doenca_pele_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_pele]" id="doenca_pele_nao" value="não" <?php echo ($anamnese['an_pele'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="doenca_pele_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Alergia:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_alergia]" id="alergia_sim" value="sim" <?php echo ($anamnese['an_alergia'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="alergia_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_alergia]" id="alergia_nao" value="não" <?php echo ($anamnese['an_alergia'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="alergia_nao">Não</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="mb-2"><h5>Glaucoma:</h5></label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_glaucoma]" id="glaucoma_sim" value="sim" <?php echo ($anamnese['an_glaucoma'] == 'Sim') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="alergia_sim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="anamnese[an_glaucoma]" id="glaucoma_nao" value="não" <?php echo ($anamnese['an_glaucoma'] == 'Não') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="alergia_nao">Não</label>
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
                                <label for="an_medicacao_continua">Toma medicação contínua?</label>
                                <input type="text" class="form-control" id="an_medicacao_continua" name="anamnese[an_medic]" value="<?php echo htmlspecialchars($anamnese['an_medic']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="an_medicacao_acne">Toma medicação para acne?</label>
                                <input type="text" class="form-control" id="an_medicacao_acne" name="anamnese[an_acne]" value="<?php echo htmlspecialchars($anamnese['an_acne']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="an_outro_problema">Possui outro problema de saúde?</label>
                            <input type="text" class="form-control" id="an_outro_problema" name="anamnese[an_outro]" value="<?php echo htmlspecialchars($anamnese['an_outro']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="an_data">Data de Criação</label>
                                <input type="date" class="form-control text-center" id="an_data" name="anamnese[an_data]" value="<?php echo date('Y-m-d', strtotime($anamnese['an_data'])); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div id="actions" class="col-md-6 mt-4">
                    <div class="d-flex justify-content-center gap-3">
                        <button type="submit" class="buttonc"><i class="fa-solid fa-check"></i> Salvar</button>
                        <a href="view.php?id=<?php echo intval($client_id); ?>" class="buttona"><i class="fa-solid fa-arrow-rotate-left"></i> Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<?php include(FOOTER_TEMPLATE); ?>
