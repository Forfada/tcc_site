<?php
include 'functions.php';

// Se receber GET com anid, carrega a anamnese para edição
if (isset($_GET['anid']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $id = intval($_GET['anid']);
    // função edit_an quando chamada sem POST preenche a global $anamnese
    edit_an();
    // $anamnese global deve conter os dados
    global $anamnese;
    if (empty($anamnese)) {
        header('Location: index.php'); exit;
    }
    $client_id = $anamnese['id_cli'];
    include(INIT);
    include(HEADER_TEMPLATE);
}

// Se for POST, processa e salva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['anamnese']) && isset($_GET['anid'])) {
    edit_an(); // função atualiza e redireciona
    exit;
}

?>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-3">Editar Anamnese</h2>
            <form method="post" action="edit_an.php?anid=<?php echo intval($_GET['anid']); ?>">
                <input type="hidden" name="anamnese[id_cli]" value="<?php echo htmlspecialchars($anamnese['id_cli']); ?>">

                <div class="mb-3">
                    <label class="form-label">Hipertensão</label>
                    <input name="anamnese[an_hipertensao]" class="form-control" value="<?php echo htmlspecialchars($anamnese['an_hipertensao']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Diabetes</label>
                    <input name="anamnese[an_diabetes]" class="form-control" value="<?php echo htmlspecialchars($anamnese['an_diabetes']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Medicações</label>
                    <textarea name="anamnese[an_medic]" class="form-control" rows="4"><?php echo htmlspecialchars($anamnese['an_medic']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data / Hora</label>
                    <input type="datetime-local" name="anamnese[an_data]" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($anamnese['an_data'])); ?>">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="view.php?id=<?php echo $client_id; ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>
