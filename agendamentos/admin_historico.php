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
            u.u_email  AS user_email,
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
      'email' => $r['user_email'],
      'appointments' => []
    ];
    }
    $byUser[$uid]['appointments'][] = $r;
}
?>

<style>
 @media (max-width: 768px) {
  .tabela-lunaris thead:first-of-type {
    display: block;
    background: #fff;
    border-radius: 10px;
    margin-bottom: 0.2rem;
    padding: 0.2rem 0.5rem;  /* levemente mais compacto */
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    border-bottom: 2px solid rgba(115, 33, 61, 0.15); /* borda sutil */
  }

  .tabela-lunaris thead:nth-of-type(2) {
    display: none;
  }

  .tabela-lunaris thead:first-of-type tr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
  }

  .tabela-lunaris thead:first-of-type th {
    flex: 1 1 100%;
    text-align: left;
    font-weight: 600;
    color: #73213d;
    font-size: 0.9rem;
    padding: 4px 0;
  }

  .tabela-lunaris,
  .tabela-lunaris tbody,
  .tabela-lunaris tr,
  .tabela-lunaris td {
    display: block;
    width: 100%;
  }

  .tabela-lunaris tr {
    margin-bottom: 0.3rem;
    background: #fff;
    border-radius: 10px;
    padding: 0.7rem;
  }

  .tabela-lunaris td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border: none;
    font-size: 0.95rem;
    font-weight: 500;
    color: #111111ff;
    text-align: left;
  }

  .tabela-lunaris td:not(:last-child) {
    border-bottom: 1px solid #f0f0f0;
  }

  .tabela-lunaris td::before {
    content: attr(data-label);
    font-weight: 600;
    color: #73213d;
    flex: 1;
    text-align: left;
    margin-right: 20px;
  }
}

</style>

<section class="section-light section-cor3 py-5" id="historico-admin">
  <div class="container mt-5" style="margin-top: 6rem !important;">
    <h2 class="txt1 mb-1 text-center">Histórico de Agendamentos — Admin</h2>
    <p class="txt4 text-center mb-2">Visualize todos os procedimentos agendados logo abaixo.</p>
    <div class="col-12 col-md-12 text-md-end mt-3 mt-md-0">
      <a href="<?php echo BASEURL; ?>agendamentos/admin_upcoming.php" class="buttonc" style="text-decoration:none;">Ver futuros</a>
    </div>
    <hr>
    <?php if (!empty($byUser)): ?>
      <?php foreach ($byUser as $user): ?>
        <div class="row">
          <div class="col-md-12">
            <div class="tabela-wrapper">
              <table class="tabela-lunaris">
                <thead>
                  <tr>
                    <th colspan="4">
                      <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                        <div class="mb-2 mb-md-0">
                          <h5 class="mb-0"><?php echo htmlspecialchars($user['name']); ?></h5>
                          <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                        </div>
                        <div><small class="text-muted"><?php echo count($user['appointments']); ?> agendamento(s)</small></div>
                      </div>
                    </th>
                  </tr>
                </thead>
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
                      <td data-label="Procedimento"><?php echo htmlspecialchars($a['procedimento']); ?></td>
                      <td data-label="Data"><?php echo date('d/m/Y', strtotime($a['a_dia'])); ?></td>
                      <td data-label="Hora"><?php echo duracao($a['a_hora']); ?></td>
                      <td data-label="Agendado em"><?php echo !empty($a['created_at']) ? date('d/m/Y H:i', strtotime($a['created_at'])) : '-'; ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <br>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted text-center mt-4">Nenhum agendamento encontrado.</p>
    <?php endif; ?>

  </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>