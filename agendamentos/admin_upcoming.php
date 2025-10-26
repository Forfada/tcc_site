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

<style>
.tabela-lunaris tbody td {
    font-size: 1rem;
}

@media (max-width: 768px) {
  .tabela-lunaris thead {
    display: none;
  }

  .tabela-lunaris,
  .tabela-lunaris tbody,
  .tabela-lunaris tr,
  .tabela-lunaris td {
    display: block;
    width: 100%;
  }

  .tabela-lunaris tr {
    margin-bottom: 0.5rem;
    background: #fff;
    border-radius: 10px;
    padding: 0.8rem;
  }

  .tabela-lunaris td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border: none;
    font-size: 0.95rem;
    font-weight: 500;
    color: #000000ff;
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
      <h2 class="txt1 mb-1 text-center">Agendamentos — Não realizado</h2>
      <p class="txt4 text-center mb-2">Visualize todos os procedimentos agendados que ainda não foram realizados logo abaixo.</p>
      <div class="col-12 col-md-12 text-md-end mt-3 mt-md-0">
        <a href="<?php echo BASEURL; ?>agendamentos/admin_historico.php" class="buttonc" style="text-decoration:none;">Ver Histórico</a>
      </div>
      <hr>

    <?php if (!empty($rows)): ?>
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <div class="tabela-wrapper">
            <table class="tabela-lunaris">
              <thead>
                <tr>
                  <th>Usuário</th>
                  <th>Procedimento</th>
                  <th>Data</th>
                  <th>Hora</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($rows as $r): ?>
                  <tr>
                    <td data-label="Usuário"><?php echo htmlspecialchars($r['user_name']); ?></td>
                    <td data-label="Procedimento"><?php echo htmlspecialchars($r['procedimento']); ?></td>
                    <td data-label="Data"><?php echo date('d/m/Y', strtotime($r['a_dia'])); ?></td>
                    <td data-label="Hora"><?php echo duracao($r['a_hora']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php else: ?>
      <p class="text-muted">Nenhum agendamento futuro encontrado.</p>
    <?php endif; ?>
  </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>