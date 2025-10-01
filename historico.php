<?php
include 'config.php';
include(DBAPI);
include(HEADER_TEMPLATE);
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['nome'])) {
    echo '<div class="container py-5"><div class="alert alert-danger text-center">Você precisa estar logado para ver seu histórico. <a href="inc/login.php" class="btn btn-sm btn-danger ms-2">Entrar</a></div></div>';
    include(FOOTER_TEMPLATE);
    exit;
}


$id_usuario = $_SESSION['id'];
$agendamentos = filter('agendamento', "id_usuario = '" . $id_usuario . "' ORDER BY a_dia DESC");

?>
<div class="container py-5">
    <h2 class="text-center mb-4" style="color:var(--cor2)">Histórico de Agendamentos</h2>
    <?php if (!$agendamentos || count($agendamentos) == 0): ?>
        <div class="alert alert-info text-center">Nenhum agendamento encontrado.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Procedimento</th>
                    <th>Data</th>
                    <th>Horário</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agendamentos as $ag): ?>
                    <?php $proc = find('procedimentos', $ag['id_procedimento']); ?>
                    <tr>
                        <td><?php echo $proc ? $proc['p_nome'] : 'Procedimento'; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($ag['a_dia'])); ?></td>
                        <td><?php echo date('H:i', strtotime($ag['a_hora'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php include(FOOTER_TEMPLATE); ?>
