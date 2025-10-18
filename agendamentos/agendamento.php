<?php
    include '../config.php';
    include(DBAPI);
  include(INIT);

  // ensure session is started and capture flash messages before header prints them
  if (session_status() === PHP_SESSION_NONE) session_start();
  $old = $_SESSION['old_inputs'] ?? [];
  $message = $_SESSION['message'] ?? null;
  $type = $_SESSION['type'] ?? 'info';
  // consume flash now so header doesn't also display it
  unset($_SESSION['message'], $_SESSION['type']);

  include(HEADER_TEMPLATE);
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const dataInput = document.getElementById('a_dia');
  // procContainer may be a grid or the dropdown content depending on template
  const procContainer = document.getElementById('procedimentos-list') || document.getElementById('dropdownContent');
        const horaSelect = document.getElementById('a_hora');
        const summaryList = document.getElementById('summary-list');
        const summaryTotal = document.getElementById('summary-total');

        function parseDurToMinutes(dur) {
            if (!dur) return 30;
            const parts = dur.split(':');
            const hh = parseInt(parts[0]) || 0;
            const mm = parseInt(parts[1]) || 0;
            const m = hh*60 + mm;
            return m > 0 ? m : 30;
        }

        function computeSummary() {
            // prefer the prettier renderer if available
            if (typeof renderSummary === 'function') return renderSummary();
            const checks = Array.from((procContainer ? procContainer.querySelectorAll('input[type="checkbox"]:checked') : document.querySelectorAll('#dropdownContent input[type="checkbox"]:checked')));
            if (checks.length === 0) {
                summaryList.textContent = 'Nenhum procedimento selecionado.';
                summaryTotal.textContent = '';
                return {minutes: 0, price: 0};
            }
            let minutes = 0;
            let price = 0;
            summaryList.innerHTML = '';
            checks.forEach(ch => {
                const label = ch.parentNode.textContent.trim();
                const li = document.createElement('div');
                li.textContent = label;
                summaryList.appendChild(li);
                minutes += parseDurToMinutes(ch.dataset.dur);
                const pv = parseFloat(String(ch.dataset.valor).replace(',', '.')) || 0;
                price += pv;
            });
            summaryTotal.textContent = 'Duração total: ' + minutes + ' minutos — Valor total: R$ ' + price.toFixed(2).replace('.', ',');
            return {minutes, price};
        }

        function fetchHorarios() {
            const date = dataInput.value;
      horaSelect.innerHTML = '<option>Carregando...</option>';
      horaSelect.disabled = true;
            const minutes = computeSummary().minutes;
            if (!date || minutes === 0) {
        horaSelect.innerHTML = '<option>Selecione data e procedimentos</option>';
        horaSelect.disabled = false;
                return;
            }
      fetch('<?= BASEURL ?>agendamentos/agendamento_horarios.php?data=' + encodeURIComponent(date) + '&duration=' + encodeURIComponent(minutes))
        .then(res => {
          if (!res.ok) throw new Error('HTTP ' + res.status);
          return res.text();
        })
        .then(text => {
          let list;
          try {
            list = JSON.parse(text);
          } catch (e) {
            // show body to help debugging
            throw new Error('Resposta inválida do servidor: ' + text);
          }
          horaSelect.innerHTML = '';
          if (!Array.isArray(list) || list.length === 0) {
            horaSelect.innerHTML = '<option>Nenhum horário disponível</option>';
          } else {
            list.forEach(h => {
              if (typeof h !== 'string') return;
              const v = String(h).trim();
              // only add well-formed HH:MM values
              if (!/^[0-2]\d:[0-5]\d$/.test(v)) return;
              const opt = document.createElement('option');
              opt.value = v;
              opt.textContent = v;
              horaSelect.appendChild(opt);
            });
            if (horaSelect.children.length === 0) {
              horaSelect.innerHTML = '<option>Nenhum horário disponível</option>';
            }
          }
          horaSelect.disabled = false;
        }).catch(err => {
          console.error('Erro ao buscar horários:', err);
          const body = String(err.message || err);
          horaSelect.innerHTML = '<option>Erro ao buscar horários</option>';
          // small debug option to see server response
          const dbg = document.createElement('option');
          dbg.value = '';
          dbg.textContent = body.length > 120 ? body.slice(0,120)+'...' : body;
          horaSelect.appendChild(dbg);
          horaSelect.disabled = false;
        });
        }

    if (dataInput) dataInput.addEventListener('change', fetchHorarios);
  // delegate clicks on the procedure checkboxes
  if (procContainer) {
    procContainer.addEventListener('change', function(e) { if (e.target && e.target.matches('input[type="checkbox"]')) { computeSummary(); fetchHorarios(); } });
  }
        // if old inputs exist, trigger fetch
        <?php if (isset($_SESSION['old_inputs']['a_dia']) || isset($_SESSION['old_inputs']['id_p'])): ?>
            computeSummary();
            fetchHorarios();
        <?php unset($_SESSION['old_inputs']); endif; ?>
    });
</script>

<script>
// require at least one procedure on submit and update pretty summary
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form.form-agendamento');
  if (form) {
  form.addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('#procedimentos-list input[type="checkbox"]:checked, #dropdownContent input[type="checkbox"]:checked').length;
    if (checked === 0) {
            e.preventDefault();
            // show toast message
            const toast = document.getElementById('toast');
            if (toast) {
                toast.textContent = 'Selecione ao menos 1 procedimento para agendar.';
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 2600);
            } else alert('Selecione ao menos 1 procedimento para agendar.');
            // open panel to help user
            const panel = document.getElementById('procPanel');
            if (panel) panel.style.display = 'block';
        }
    });
  }
  // initial render summary
  if (typeof renderSummary === 'function') renderSummary();
});
</script>

<?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = $_POST['a_dia'] ?? '';
        $hora = $_POST['a_hora'] ?? '';
        $ids = $_POST['id_p'] ?? [];
        $ids = array_map('intval', (array) $ids);

        if ($data && $hora && !empty($ids)) {
            try {
                if (!isset($_SESSION)) session_start();
                // ensure user is logged in
                $idUsuario = $_SESSION['id'] ?? null;
                if (!$idUsuario) {
                    $_SESSION['message'] = 'Você precisa estar logado para agendar.';
                    $_SESSION['type'] = 'warning';
                    header('Location: ../inc/login.php');
                    exit;
                }

                $db = open_database();

                // build placeholders for IN(...) and fetch selected procedures
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $db->prepare("SELECT id, p_duracao, p_valor, p_nome FROM procedimentos WHERE id IN ($placeholders)");
                foreach ($ids as $k => $v) $stmt->bindValue($k+1, $v, PDO::PARAM_INT);
                $stmt->execute();
                $procs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $total_minutes = 0;
                $total_price = 0.0;
                foreach ($procs as $p) {
                    if (!empty($p['p_duracao'])) {
                        list($hh, $mm) = array_pad(explode(':', $p['p_duracao']), 2, '00');
                        $m = intval($hh)*60 + intval($mm);
                        if ($m <= 0) $m = 30;
                    } else {
                        $m = 30;
                    }
                    $total_minutes += $m;
                    $pp = str_replace(',', '.', preg_replace('/[^0-9,\.]/','', $p['p_valor']));
                    $total_price += (float)$pp;
                }

                // check user doesn't already have any of these procedures on the date
                $stmt = $db->prepare("SELECT COUNT(*) FROM agendamento WHERE id_u = ? AND a_dia = ? AND id_p IN ($placeholders)");
                $params = array_merge([$idUsuario, $data], $ids);
                $stmt->execute($params);
                $same = (int)$stmt->fetchColumn();
                if ($same > 0) {
                    $message = 'Você já possui um dos procedimentos selecionados nessa data.';
                    $type = 'warning';
                    close_database($db);
                } else {
                    // check availability for entire block
                    $start_ts = strtotime($data . ' ' . $hora);
                    $end_ts = $start_ts + $total_minutes*60;

                    $sql = "SELECT a.a_hora, p.p_duracao FROM agendamento a JOIN procedimentos p ON a.id_p = p.id WHERE a.a_dia = :a_dia";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':a_dia', $data);
                    $stmt->execute();
                    $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $conflict = false;
                    foreach ($existing as $e) {
                        $s_ts = strtotime($data . ' ' . $e['a_hora']);
                        $d_minutes = 30;
                        if (!empty($e['p_duracao'])) {
                            list($eh, $em) = array_pad(explode(':', $e['p_duracao']), 2, '00');
                            $d_minutes = intval($eh) * 60 + intval($em);
                        }
                        $e_end = $s_ts + $d_minutes * 60;
                        if ($s_ts < $end_ts && $e_end > $start_ts) {
                            $conflict = true;
                            break;
                        }
                    }

                    if ($conflict) {
                        $message = 'O bloco de horários selecionado conflita com outros agendamentos.';
                        $type = 'danger';
                        close_database($db);
                    } else {
                        // insert sequential appointments in a transaction
                        $db->beginTransaction();
                        try {
                            // build map of durations keyed by id
                            $dur_map = [];
                            foreach ($procs as $p) {
                                list($hh, $mm) = array_pad(explode(':', $p['p_duracao'] ?? ''), 2, '00');
                                $m = (!empty($p['p_duracao'])) ? intval($hh)*60 + intval($mm) : 30;
                                if ($m <= 0) $m = 30;
                                $dur_map[$p['id']] = $m;
                            }

                            $cursor = $start_ts;
                            $inserted = 0;
                            $sql = "INSERT INTO agendamento (a_dia, a_hora, id_u, id_p, created_at) VALUES (:a_dia, :a_hora, :id_u, :id_p, NOW())";
                            $ins = $db->prepare($sql);
                            foreach ($ids as $pid) {
                                $hora_ins = date('H:i:s', $cursor);
                                $ins->bindParam(':a_dia', $data);
                                $ins->bindParam(':a_hora', $hora_ins);
                                $ins->bindParam(':id_u', $idUsuario);
                                $ins->bindParam(':id_p', $pid);
                                $ins->execute();
                                $inserted++;
                                $cursor += ($dur_map[$pid] ?? 30) * 60;
                            }
                            $db->commit();
                            if ($inserted > 0) {
                                $message = 'Agendamento(s) realizado(s) com sucesso! Total: ' . number_format($total_price, 2, ',', '.') . ' — De ' . date('H:i', $start_ts) . ' até ' . date('H:i', $end_ts);
                                $type = 'success';
                            }
                        } catch (Exception $e) {
                            $db->rollBack();
                            throw $e;
                        }
                        close_database($db);
                    }
                }
            } catch (PDOException $e) {
                $message = 'Erro ao agendar: ' . $e->getMessage();
                $type = 'danger';
            }
        } else {
            $message = 'Preencha todos os campos.';
            $type = 'warning';
        }
    }

    /* ============= BUSCA DE PROCEDIMENTOS ============= */
    $procedimentos = [];
    try {
        $db = open_database();
        // include duration and value so the form and JS can use them
        $stmt = $db->query("SELECT id, p_nome, p_duracao, p_valor FROM procedimentos ORDER BY p_nome ASC");
        $procedimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        close_database($db);
    } catch (PDOException $e) {
        $message = "Erro ao carregar procedimentos: " . $e->getMessage();
        $type = "danger";
    }

    /* ============= BUSCA DOS AGENDAMENTOS DO USUÁRIO ============= */
    $agendamentos = [];
    try {
        $db = open_database();
        $sql = "
            SELECT
                a.id,
                a.a_dia,
                a.a_hora,
                p.p_nome AS procedimento,
                a.created_at
            FROM agendamento a
            JOIN procedimentos p ON a.id_p = p.id
            WHERE a.id_u = :id_u
            ORDER BY a.a_dia DESC, a.a_hora DESC
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_u', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->execute();
        $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        close_database($db);
    } catch (PDOException $e) {
        $message = "Erro ao carregar agendamentos: " . $e->getMessage();
        $type = "danger";
    }

?>
<?php
// robust flash handling: prefer local $message (set during this request), fall back to session flash
$flash = $message ?? ($_SESSION['message'] ?? null);
$flash_type = $type ?? ($_SESSION['type'] ?? 'info');
if (!empty($flash)):
    // clear session flash if present
    unset($_SESSION['message'], $_SESSION['type'], $_SESSION['old_inputs']);
?>
  <div class="container">
    <div class="alert alert-<?php echo htmlspecialchars($flash_type); ?> alert-dismissible fade show mt-3" role="alert">
      <?php echo htmlspecialchars($flash); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>
<section id="agendamento" class="section-cor3"> 
  <div class="form-agendamento">
    <h2>Agendar Procedimento</h2>
    <form method="POST" class="form-agendamento">

      <!-- Dropdown de Procedimentos -->
      <div class="form-group dropdown-group">
        <label for="procedimentosDropdown">Procedimentos (máx. 3):</label>
        <div class="dropdown-wrapper">
          <button type="button" id="dropdownButton" class="dropdown-btn">Selecionar procedimentos ▼</button>
          <div id="dropdownContent" class="dropdown-content">
            <?php foreach ($procedimentos as $p): 
              $d = $p['p_duracao'] ?? '00:30';
              $val = $p['p_valor'] ?? '0';
              $checked = (isset($old['id_p']) && in_array($p['id'], (array)$old['id_p'])) ? 'checked' : '';
            ?>
            <label class="dropdown-item">
              <input type="checkbox" name="id_p[]" value="<?= $p['id'] ?>" data-dur="<?= $d ?>" data-valor="<?= $p['p_valor'] ?>" <?= $checked ?>>
              <?= htmlspecialchars($p['p_nome']) ?> — R$ <?= htmlspecialchars($p['p_valor']) ?> (<?= $d ?>)
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Data e Hora -->
      <div class="form-row">
        <div class="form-group">
          <label for="a_dia">Data:</label>
          <input type="date" name="a_dia" id="a_dia" required value="<?= $old['data'] ?? '' ?>">
        </div>
        <div class="form-group">
          <label for="a_hora">Hora:</label>
          <select class="form-control" id="a_hora" name="a_hora">
            <option value="">Selecione a data e procedimentos primeiro</option>
          </select>
        </div>
      </div>

      <!-- Resumo -->
      <div class="summary-box" id="summary">
        <h4>Resumo</h4>
        <ul class="summary-list" id="summary-list"><li>Nenhum procedimento selecionado.</li></ul>
        <div class="summary-total" id="summary-total"></div>
        <div class="summary-range" id="summary-range"></div>
      </div>

      <button type="submit" class="btn-agendar">Agendar</button>
    </form>
  </div>

  <!-- Lista de agendamentos -->
  <?php if (!empty($agendamentos)): ?>
    <div class="tabela-wrapper">
      <table class="tabela-lunaris">
        <thead>
          <tr>
            <th>ID</th>
            <th>Procedimento</th>
            <th>Data</th>
            <th>Hora</th>
            <th>Agendado em</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($agendamentos as $ag): ?>
            <tr>
              <td><?= htmlspecialchars($ag['id']) ?></td>
              <td><?= htmlspecialchars($ag['procedimento']) ?></td>
              <td><?= formatadata($ag['a_dia'], 'd/m/Y') ?></td>
              <td><?= htmlspecialchars($ag['a_hora']) ?></td>
              <td><?= formatadata($ag['created_at'], 'd/m/Y H:i') ?></td>
              <td><button class="buttonc btn-delete-ag open-delete-modal" data-id="<?= htmlspecialchars($ag['id']) ?>">Excluir</button></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="sem-registro">Você ainda não possui agendamentos.</p>
  <?php endif; ?>


<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay">
    <div class="modal-card">
        <h3>Confirmar exclusão</h3>
        <p>Tem certeza que deseja excluir este agendamento? Esta ação não pode ser desfeita.</p>
        <div class="modal-actions">
            <form id="deleteForm" method="POST" action="delete.php">
                <input type="hidden" name="id" id="delete-id" value="">
                <button type="submit" class="buttonc">Sim, excluir</button>
            </form>
            <button id="cancelDelete" class="buttonc" style="background:#ccc;color:#333">Cancelar</button>
        </div>
    </div>
</div>
</section>
<style>
/* ====== Estilo Lunaris ====== */
/* ======= Container Principal ======= */
#agendamento {
  background: #faefe7;
  padding: 50px 5%;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 40px;
  font-family: "Poppins", sans-serif;
}

/* ======= Formulário ======= */
.form-agendamento {
  background: #fff;
  border-radius: 18px;
  padding: 40px 30px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 700px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 25px;
  text-align: center;
}

.form-agendamento h2 {
  font-family: "Playfair Display", serif;
  font-size: 2rem;
  color: #73213d;
  margin-bottom: 10px;
}

/* ======= Campos ======= */
.form-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  color: #73213d;
}

.form-group input,
.form-group select {
  padding: 12px 16px;
  border-radius: 10px;
  border: 1px solid #d1b2b7;
  font-size: 1rem;
  width: 100%;
  max-width: 400px;
  background-color: #fffaf9;
  transition: all 0.2s ease;
}

.form-group input:focus,
.form-group select:focus {
  border-color: #a05a6f;
  box-shadow: 0 0 5px rgba(160, 90, 111, 0.3);
  outline: none;
}

/* ======= Painel de Procedimentos ======= */
.proc-panel {
  display: block; /* Corrige o erro de não aparecer */
  background: #fff9f8;
  border: 1px solid #f1d7db;
  border-radius: 14px;
  padding: 20px;
  width: 100%;
  max-width: 700px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.proc-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 14px;
  margin-top: 10px;
}

.proc-card {
  background: #fff;
  border: 1px solid #eed3d7;
  border-radius: 12px;
  padding: 12px;
  text-align: left;
  cursor: pointer;
  transition: all 0.25s ease;
  display: flex;
  align-items: center;
  gap: 10px;
}

.proc-card:hover {
  background: #fff2f4;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  transform: translateY(-2px);
}

.proc-card.selected {
  border-color: #c76d8b;
  background: #ffe9ef;
}

.proc-card label {
  flex: 1;
  display: flex;
  flex-direction: column;
  cursor: pointer;
}

.proc-card input[type="checkbox"] {
  accent-color: #c76d8b;
  transform: scale(1.2);
}

.proc-name {
  font-weight: 600;
  color: #73213d;
}

.proc-meta {
  font-size: 0.85rem;
  color: #666;
}

/* ======= Resumo ======= */
.summary-box {
  border: 1px solid #f0d0d3;
  border-radius: 12px;
  padding: 15px;
  background: #fff;
  text-align: left;
  width: 100%;
  max-width: 700px;
}

.summary-box h4 {
  color: #73213d;
  font-size: 1.1rem;
  margin-bottom: 10px;
  text-align: center;
}

.summary-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.summary-list li {
  display: flex;
  justify-content: space-between;
  padding: 6px 0;
  border-bottom: 1px dashed #eee;
  color: #444;
}

.summary-total {
  font-weight: 600;
  margin-top: 8px;
  color: #73213d;
  text-align: right;
}

.summary-range {
  font-size: 0.85rem;
  color: #777;
}

/* ======= Botão ======= */
.btn-agendar {
  background: #73213d;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 14px 24px;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-agendar:hover {
  background: #a05a6f;
  transform: translateY(-2px);
}

/* ======= Tabela ======= */
.tabela-wrapper {
  width: 100%;
  max-width: 900px;
  background: #73213d;
  border-radius: 20px;
  padding: 25px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  overflow-x: auto;
}

.tabela-lunaris {
  width: 100%;
  border-collapse: collapse;
  color: #fff;
}

.tabela-lunaris th {
  background: #f8d9c4;
  color: #73213d;
  padding: 10px;
  text-align: left;
  font-weight: 600;
  border-radius: 6px 6px 0 0;
}

.tabela-lunaris td {
  padding: 10px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.tabela-lunaris tr:hover {
  background: rgba(255, 255, 255, 0.05);
}

.tabela-lunaris button {
  background: #a05a6f;
  border: none;
  color: #fff;
  border-radius: 8px;
  padding: 6px 10px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.tabela-lunaris button:hover {
  background: #d17d96;
}

/* ======= Responsividade ======= */
@media (max-width: 768px) {
  #agendamento {
    padding: 30px 4%;
  }

  .form-agendamento,
  .proc-panel,
  .summary-box {
    padding: 25px 20px;
  }

  .btn-agendar {
    width: 100%;
  }

  .tabela-lunaris th,
  .tabela-lunaris td {
    font-size: 0.9rem;
  }
}
/* ======= Dropdown customizado ======= */
.dropdown-group {
  width: 100%;
  max-width: 700px;
  text-align: left;
}

.dropdown-wrapper {
  position: relative;
  width: 100%;
}

.dropdown-btn {
  width: 100%;
  padding: 12px 16px;
  background: #fffaf9;
  border: 1px solid #d1b2b7;
  border-radius: 10px;
  font-size: 1rem;
  color: #73213d;
  cursor: pointer;
  text-align: left;
}

.dropdown-content {
  display: none;
  position: absolute;
  z-index: 100;
  background: #fff;
  border: 1px solid #d1b2b7;
  border-radius: 10px;
  margin-top: 4px;
  width: 100%;
  max-height: 250px;
  overflow-y: auto;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  text-align: left; /* garante alinhamento à esquerda */
  padding: 6px 0;
}

.dropdown-content.show {
  display: block;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  cursor: pointer;
  justify-content: flex-start; /* conteúdo à esquerda */
  text-align: left;
  font-size: 0.95rem;
  color: #73213d;
}

.dropdown-item:hover {
  background: #fff4f6;
}


.form-row {
  display: flex;
  gap: 20px;
  justify-content: center;
  flex-wrap: wrap;
}


</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // mark the agendamento/nav link active (handles different base paths)
        var links = document.querySelectorAll('#mainNavbar .nav-link');
        links.forEach(function(a){
            try {
                var href = a.getAttribute('href') || '';
                if (href.indexOf('agendamento') !== -1 && window.location.href.indexOf('agendamento') !== -1) {
                    a.classList.add('active');
                }
            } catch(e){}
        });
    } catch(e){}
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const maxSelect = 3;
  const dropdownBtn = document.getElementById('dropdownButton');
  const dropdownContent = document.getElementById('dropdownContent');

  // abrir/fechar dropdown
  dropdownBtn.addEventListener('click', function() {
    dropdownContent.classList.toggle('show');
  });

  // limitar 3 seleções (global: conta checados do grid+dropdown)
  dropdownContent.addEventListener('change', function(e) {
    const totalChecked = document.querySelectorAll('#procedimentos-list input[type="checkbox"]:checked, #dropdownContent input[type="checkbox"]:checked').length;
    if (totalChecked > maxSelect) {
      e.target.checked = false;
      alert('Você pode selecionar no máximo ' + maxSelect + ' procedimentos.');
      return;
    }

    // atualizar texto do botão somente com os checados do dropdown
    const checked = dropdownContent.querySelectorAll('input[type="checkbox"]:checked');
    if (checked.length > 0) {
      dropdownBtn.textContent = `${checked.length} selecionado${checked.length > 1 ? 's' : ''}`;
    } else {
      dropdownBtn.textContent = 'Selecionar procedimentos ▼';
    }

    // atualizar resumo e horários
    if (typeof renderSummary === 'function') renderSummary();
    const data = document.getElementById('a_dia');
    if (data && typeof fetchHorarios === 'function') fetchHorarios();
  });

  // fechar dropdown ao clicar fora
  window.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown-wrapper')) {
      dropdownContent.classList.remove('show');
    }
  });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const maxSelect = 3;
  const procGrid = document.getElementById('procedimentos-list');
  const dropdownContent = document.getElementById('dropdownContent');
  const counterText = document.getElementById('proc-counter-text');
  const procPanel = document.getElementById('procPanel');
  const procBoxToggle = document.getElementById('procBoxToggle');
  const toast = document.getElementById('toast');

  // helper: return array of checked checkbox inputs from both containers
  function getCheckedInputs() {
    return Array.from(document.querySelectorAll('#procedimentos-list input[type="checkbox"]:checked, #dropdownContent input[type="checkbox"]:checked'));
  }

  function showToast(msg, timeout = 2400) {
    if (!toast) return alert(msg);
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), timeout);
  }

  function updateCounter() {
    const checked = getCheckedInputs().length;
    if (counterText) counterText.textContent = `Selecionados ${checked}/${maxSelect}`;
  }

  // unified change handler to enforce global maxSelect and update UI
  function onProcedureChange(e) {
    if (!e.target || !e.target.matches('input[type="checkbox"]')) return;
    const totalChecked = getCheckedInputs().length;
    if (totalChecked > maxSelect) {
      // undo the action
      e.target.checked = false;
      showToast('Você pode selecionar no máximo ' + maxSelect + ' procedimentos.');
      return;
    }
    // update selected class for grid cards if present
    if (procGrid) {
      procGrid.querySelectorAll('.proc-card').forEach(function(card) {
        const cb = card.querySelector('input[type="checkbox"]');
        if (cb && cb.checked) card.classList.add('selected'); else card.classList.remove('selected');
      });
    }
    updateCounter();
    const dateIn = document.getElementById('a_dia');
    if (dateIn && dateIn.value && typeof fetchHorarios === 'function') fetchHorarios();
    if (typeof renderSummary === 'function') renderSummary();
  }

  // attach listeners to both containers if they exist
  if (procGrid) {
    procGrid.addEventListener('change', onProcedureChange);
    procGrid.addEventListener('click', function(e) {
      const card = e.target.closest('.proc-card');
      if (!card) return;
      const cb = card.querySelector('input[type="checkbox"]');
      if (!cb) return;
      cb.checked = !cb.checked;
      cb.dispatchEvent(new Event('change', { bubbles: true }));
    });
  }
  if (dropdownContent) {
    dropdownContent.addEventListener('change', onProcedureChange);
  }

  if (procBoxToggle) {
    procBoxToggle.addEventListener('click', function() {
      if (procPanel && procPanel.style.display === 'block') {
        procPanel.style.display = 'none';
        this.querySelector('small').textContent = 'Clique para abrir';
      } else {
        if (procPanel) procPanel.style.display = 'block';
        this.querySelector('small').textContent = 'Clique para fechar';
      }
    });
  }

    updateCounter();

  // modal delete logic (guard existence)
  const deleteModal = document.getElementById('deleteModal');
  const deleteIdInput = document.getElementById('delete-id');
  const deleteButtons = document.querySelectorAll('.open-delete-modal');
  if (deleteModal && deleteIdInput && deleteButtons.length) {
    // move modal to document.body to avoid stacking/context issues
    try {
      if (deleteModal.parentNode !== document.body) document.body.appendChild(deleteModal);
    } catch (ex) { /* fallback: if append fails, continue using existing placement */ }
    deleteButtons.forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        console.debug('open-delete-modal clicked, id=', this.dataset.id);
        const id = this.dataset.id;
        deleteIdInput.value = id;
        deleteModal.style.display = 'flex';
      });
    });
    const cancel = document.getElementById('cancelDelete');
    if (cancel) {
      cancel.addEventListener('click', function() {
        deleteModal.style.display = 'none';
        deleteIdInput.value = '';
      });
    }
    deleteModal.addEventListener('click', function(e) {
      if (e.target === deleteModal) {
        deleteModal.style.display = 'none';
        deleteIdInput.value = '';
      }
    });
  }
});

// pretty summary renderer used by earlier computeSummary
function renderSummary() {
  const grid = document.getElementById('procedimentos-list');
  const dropdown = document.getElementById('dropdownContent');
  const summaryList = document.getElementById('summary-list');
  const summaryTotal = document.getElementById('summary-total');
  const summaryRange = document.getElementById('summary-range');
  summaryList.innerHTML = '';

  // collect checked checkboxes from either source
  let checks = [];
  if (grid) checks = Array.from(grid.querySelectorAll('input[type="checkbox"]:checked'));
  else if (dropdown) checks = Array.from(dropdown.querySelectorAll('input[type="checkbox"]:checked'));

  if (checks.length === 0) {
    summaryList.innerHTML = '<li>Nenhum procedimento selecionado.</li>';
    summaryTotal.textContent = '';
    summaryRange.textContent = '';
    return {minutes:0, price:0};
  }

  let minutes = 0, price = 0;
  checks.forEach(ch => {
    // name may be present in data-name or fall back to label text
    let name = ch.dataset.name || '';
    if (!name) {
      const lbl = ch.parentNode && ch.parentNode.textContent ? ch.parentNode.textContent.trim() : '';
      // label like: "Name — R$ 120,00 (01:30)" -> take before '—'
      if (lbl.indexOf('—') !== -1) name = lbl.split('—')[0].trim(); else name = lbl;
    }
    const dur = ch.dataset.dur || '00:30';
    const val = parseFloat(String(ch.dataset.valor || '0').replace(',', '.')) || 0;
    const li = document.createElement('li');
    li.innerHTML = '<span>'+escapeHtml(name)+'</span><span>R$ '+val.toFixed(2).replace('.',',')+'</span>';
    summaryList.appendChild(li);
    const parts = dur.split(':');
    const hh = parseInt(parts[0]) || 0; const mm = parseInt(parts[1]) || 0;
    minutes += hh*60 + mm;
    price += val;
  });
  summaryTotal.textContent = 'Total: R$ ' + price.toFixed(2).replace('.',',');
  // if horario selected show range
  const dateEl = document.getElementById('a_dia');
  const horaEl = document.getElementById('a_hora');
  const date = dateEl ? dateEl.value : '';
  const hora = horaEl ? horaEl.value : '';
  if (date && hora) {
    // normalize date: accept YYYY-MM-DD or DD/MM/YYYY
    let dstr = date;
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(date)) {
      const parts = date.split('/');
      dstr = parts[2] + '-' + parts[1] + '-' + parts[0];
    }
    const time = (hora.length===5?hora+':00':hora);
    const start = new Date(dstr + 'T' + time);
    if (isNaN(start.getTime())) {
      summaryRange.textContent = '';
    } else {
      const end = new Date(start.getTime() + minutes*60000);
      const pad = n=>('0'+n).slice(-2);
      summaryRange.textContent = 'De ' + pad(start.getHours())+':'+pad(start.getMinutes()) + ' até ' + pad(end.getHours())+':'+pad(end.getMinutes());
    }
  } else summaryRange.textContent = '';
  return {minutes, price};
}

// small helper to avoid html injection into created nodes
function escapeHtml(str) {
  return String(str).replace(/&/g, '&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<style>
/* Ensure modal overlay sits above footer and page content */
.modal-overlay {
  position: fixed !important;
  top: 0; left: 0; right: 0; bottom: 0;
  display: none;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.5);
  z-index: 2000;
}
.modal-overlay .modal-card {
  z-index: 2001;
  position: relative;
  background: #fff;
  color: #222;
  padding: 20px 22px;
  width: 100%;
  max-width: 520px;
  border-radius: 12px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.25);
  pointer-events: auto;
}
.modal-overlay .modal-card h3 { margin-top: 0; color: #73213d; }
.modal-overlay .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 14px; }
.modal-overlay .modal-actions .buttonc { padding: 10px 14px; border-radius: 8px; }
.modal-overlay .modal-actions form { margin: 0; }
</style>


 
<?php include(FOOTER_TEMPLATE); ?>



