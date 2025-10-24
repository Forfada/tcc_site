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
            a.id,
            a.a_dia,
            a.a_hora,
            a.created_at,
            u.id AS user_id,
            u.u_user AS user_name,
            u.u_email  AS user_email,
            p.p_nome AS procedimento
        FROM agendamento a
        JOIN usuarios u ON a.id_u = u.id
        JOIN procedimentos p ON a.id_p = p.id
        WHERE CONCAT(a.a_dia, ' ', a.a_hora) > NOW()
        ORDER BY a.a_dia ASC, a.a_hora ASC
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    close_database($db);
} catch (Exception $e) {
    $_SESSION['message'] = "Erro ao carregar agendamentos: " . $e->getMessage();
    $_SESSION['type'] = "danger";
}
?>

<section class="section-light section-cor3 py-5" style="padding-top:6.5rem;">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="txt1">Agendamentos — Será realizado</h2>
      <a href="<?php echo BASEURL; ?>agendamentos/admin_historico.php" class="buttonc" style="text-decoration:none;">Ver histórico (admin)</a>
    </div>

    <?php if (!empty($rows)): ?>
      <div class="tabela-wrapper">
        <table class="tabela-lunaris">
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuário</th>
              <th>Email</th>
              <th>Procedimento</th>
              <th>Data</th>
              <th>Hora</th>
              <th>Agendado em</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
            <tr>
              <td><?php echo intval($r['id']); ?></td>
              <td style="text-align:left;"><?php echo htmlspecialchars($r['user_name']); ?></td>
              <td><?php echo htmlspecialchars($r['user_email']); ?></td>
              <td style="text-align:left;"><?php echo htmlspecialchars($r['procedimento']); ?></td>
              <td><?php echo date('d/m/Y', strtotime($r['a_dia'])); ?></td>
              <td><?php echo htmlspecialchars(substr($r['a_hora'],0,5)); ?></td>
              <td><?php echo !empty($r['created_at']) ? date('d/m/Y H:i', strtotime($r['created_at'])) : '-'; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-muted">Nenhum agendamento futuro encontrado.</p>
    <?php endif; ?>
  </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>