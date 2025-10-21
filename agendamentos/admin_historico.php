<?php
include '../config.php';
include(DBAPI);
if (session_status() === PHP_SESSION_NONE) session_start();

// somente administrador pode acessar
if (!function_exists('is_admin') || !is_admin()) {
    $_SESSION['message'] = "Você não pode acessar essa funcionalidade.";
    $_SESSION['type'] = "danger";
    header("Location: " . BASEURL . "index.php");
    exit;
}

include(INIT);
include(HEADER_TEMPLATE);

$rows = [];
try {
    $db = open_database();
    $sql = "
        SELECT 
            u.id AS user_id,
            u.u_user AS user_name,
            u.u_num  AS user_phone,
            a.id AS ag_id,
            a.a_dia,
            a.a_hora,
            p.p_nome AS procedimento,
            a.created_at
        FROM agendamento a
        JOIN usuarios u ON a.id_u = u.id
        JOIN procedimentos p ON a.id_p = p.id
        ORDER BY u.u_user ASC, a.a_dia DESC, a.a_hora DESC
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    close_database($db);
} catch (Exception $e) {
    $_SESSION['message'] = "Erro ao carregar histórico: " . $e->getMessage();
    $_SESSION['type'] = "danger";
}

// agrupar por usuário
$byUser = [];
foreach ($rows as $r) {
    $uid = $r['user_id'];
    if (!isset($byUser[$uid])) {
        $byUser[$uid] = [
            'name' => $r['user_name'],
            'phone' => $r['user_phone'],
            'appointments' => []
        ];
    }
    $byUser[$uid]['appointments'][] = $r;
}
?>

<section class="section-light section-cor3 py-5" style="padding-top:6.5rem;">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="txt1">Histórico de Agendamentos — Admin</h2>
      <a href="<?php echo BASEURL; ?>agendamentos/admin_upcoming.php" class="buttonc" style="text-decoration:none;">Ver futuros</a>
    </div>

    <?php if (!empty($byUser)): ?>
      <?php foreach ($byUser as $user): ?>
        <div class="card mb-4 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                <h5 class="mb-0"><?php echo htmlspecialchars($user['name']); ?></h5>
                <small class="text-muted"><?php echo htmlspecialchars($user['phone']); ?></small>
              </div>
              <div><small class="text-muted"><?php echo count($user['appointments']); ?> agendamentos</small></div>
            </div>

            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead>
                  <tr>
                    <th>Procedimento</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Agendado em</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($user['appointments'] as $a): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($a['procedimento']); ?></td>
                      <td><?php echo date('d/m/Y', strtotime($a['a_dia'])); ?></td>
                      <td><?php echo htmlspecialchars(substr($a['a_hora'],0,5)); ?></td>
                      <td><?php echo !empty($a['created_at']) ? date('d/m/Y H:i', strtotime($a['created_at'])) : '-'; ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">Nenhum agendamento encontrado.</p>
    <?php endif; ?>

  </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>