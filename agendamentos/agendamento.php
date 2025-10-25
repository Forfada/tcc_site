<?php
    include '../config.php';
    include(DBAPI);

    // garante sessão iniciada
    if (session_status() === PHP_SESSION_NONE) session_start();

    // se não estiver logado, salva mensagem e redireciona para a página de login
    if (empty($_SESSION['id'])) {
        $_SESSION['message'] = 'Você precisa estar logado para agendar.';
        $_SESSION['type'] = 'warning';
        header('Location: ../inc/login.php');
        exit;
    }

    include(INIT);

  // ensure session is started and capture flash messages before header prints them
  if (session_status() === PHP_SESSION_NONE) session_start();
  $old = $_SESSION['old_inputs'] ?? [];
  $message = $_SESSION['message'] ?? null;
  $type = $_SESSION['type'] ?? 'info';
  // consume flash now so header doesn't also display it
  unset($_SESSION['message'], $_SESSION['type']);
  // ensure old inputs are used only once (prevents date staying after navigation/refresh)
  if (isset($_SESSION['old_inputs'])) unset($_SESSION['old_inputs']);

  include(HEADER_TEMPLATE);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<!-- locale PT-BR for flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/pt.js"></script>

<style>
/* ===== Elegant Flatpickr Theme (custom) ===== */
#agendamento input#a_dia {
  background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="%2373213d" d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 14H5V9h14v9Z"/></svg>') no-repeat 12px center;
  padding-left: 40px;
  padding-right: 14px;
  border-radius: 12px;
  border: 1px solid #e6cfd2;
  box-shadow: 0 6px 18px rgba(115,33,61,0.06);
  height: 44px;
  font-size: 1rem;
  background-color: #fffaf9;
}

/* Calendar container */
.flatpickr-calendar {
  border: none;
  border-radius: 14px;
  box-shadow: 0 18px 40px rgba(50,20,30,0.12);
  background: linear-gradient(180deg,#fff,#fff);
  font-family: "Poppins", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  color: #2d2d2d;
  padding: 10px;
  width: 320px;
  z-index: 3000;
}

/* header / month bar */
.flatpickr-months {
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:6px;
}
.flatpickr-month {
  background: linear-gradient(90deg,#f9eef0,#fffdfd);
  padding: 10px 12px;
  border-radius: 10px;
  display:flex;
  gap:8px;
  align-items:center;
  box-shadow: inset 0 -1px 0 rgba(0,0,0,0.02);
}
.flatpickr-current-month .cur-month {
  font-weight:700;
  color:#73213d;
  font-size:1rem;
}
.flatpickr-prev-month, .flatpickr-next-month {
  color:#73213d;
  background: transparent;
  border-radius: 8px;
  padding:6px;
}
.flatpickr-prev-month:hover, .flatpickr-next-month:hover {
  background: rgba(115,33,61,0.06);
}

/* weekdays */
.flatpickr-weekdays {
  margin-top:8px;
  margin-bottom:6px;
}
.flatpickr-weekday {
  color:#8a6a75;
  font-weight:600;
  font-size:0.85rem;
}

/* day cells layout */
.flatpickr-days .dayContainer {
  display:grid;
  grid-template-columns: repeat(7,1fr);
  gap:6px;
}
.flatpickr-day {
  border-radius:10px;
  height:40px;
  line-height:40px;
  width:40px;
  margin:0;
  display:inline-block;
  text-align:center;
  transition: all .16s ease;
  color: #3a3a3a;
  background: transparent;
  border: 1px solid transparent;
  box-sizing: border-box;
  font-weight:600;
}

/* hover and focus */
.flatpickr-day:hover {
  transform: translateY(-4px);
  background: linear-gradient(180deg,#fff4f6,#fff);
  box-shadow: 0 6px 18px rgba(115,33,61,0.06);
  color:#73213d;
}

/* selected day */
.flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
  background: linear-gradient(180deg,#73213d,#a05a6f) !important;
  color: #fff !important;
  box-shadow: 0 8px 22px rgba(115,33,61,0.18);
}

/* today indicator */
.flatpickr-day.today {
  box-shadow: 0 0 0 2px rgba(115,33,61,0.08);
  border: 1px dashed rgba(115,33,61,0.12);
}

/* disabled (past) */
.flatpickr-day.flatpickr-disabled {
  opacity: 0.45;
  color: #999;
  pointer-events: none;
  transform: none;
  background: transparent;
  border: 1px dashed #eee;
}

/* days with no slots */
.flatpickr-day.no-slots {
  background: linear-gradient(180deg,#fdecea,#fff1f1) !important;
  color: #7a1721 !important;
  border: 1px solid #f5c6cb !important;
  position: relative;
}
.flatpickr-day.no-slots::after {
  content: "";
  position: absolute;
  right: 6px;
  top: 6px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #c82333;
  box-shadow: 0 0 6px rgba(200,40,50,0.35);
}

/* compact / responsive */
@media (max-width:420px) {
  .flatpickr-calendar { width: 92vw; padding:8px; }
  .flatpickr-day { height:36px; width:36px; }
  #agendamento input#a_dia { height:42px; }
}

/* ======= Dropdown: correções de alinhamento e estilo ======= */
.dropdown-group {
  align-items: flex-start; /* sobrescreve align-items:center de .form-group */
  text-align: left;
  width: 100%;
  max-width: 700px;
}

.dropdown-group > label {
  align-self: flex-start;
  text-align: left;
  width: 100%;
  margin-bottom: 8px;
}

/* wrapper e botão */
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
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* painel do dropdown */
.dropdown-content {
  display: none;
  position: absolute;
  left: 0;
  top: calc(100% + 4px);
  z-index: 1200;
  background: #fff;
  border: 1px solid #eed3d7;
  border-radius: 10px;
  width: 100%;
  max-height: 300px;
  overflow-y: auto;
  box-shadow: 0 12px 32px rgba(35,20,30,0.12);
  padding: 8px;
  box-sizing: border-box;
  text-align: left;
  margin-bottom: 16px;
}

/* exibir */
.dropdown-content.show { display: block; }

/* item: checkbox à esquerda, texto à esquerda */
.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 10px;
  cursor: pointer;
  justify-content: flex-start;
  text-align: left;
  font-size: 0.95rem;
  color: #444;
  border-radius: 8px;
  width: 100%;
  box-sizing: border-box;
}
.dropdown-item:hover { background: #fff4f6; }

.dropdown-item input[type="checkbox"] {
  margin: 0;
  flex: 0 0 auto;
  width: 18px;
  height: 18px;
  cursor: pointer;
  appearance: checkbox;
  -webkit-appearance: checkbox;
  border: 2px solid #73213d;
  border-radius: 3px;
  position: relative;
  outline: none;
  accent-color: #73213d;
  visibility: visible !important;
  opacity: 1 !important;
  display: block !important;
}

/* texto ocupa o restante */
.dropdown-item span {
  display: inline-block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1 1 auto;
  text-align: left;
  color: #73213d;
}

/* preço do procedimento no dropdown - separação visual */
.dropdown-item .proc-price {
  margin-left: 12px;
  color: #6b2b3b;
  font-weight: 700;
  flex: 0 0 auto;
}

/* nome do procedimento no dropdown */
.dropdown-item .proc-name {
  flex: 1 1 auto;
  padding-right: 8px;
  text-overflow: ellipsis;
  overflow: hidden;
}

/* ===== Summary spacing ===== */
#summary-list li {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding: 8px 6px;
  border-bottom: 1px dashed rgba(0,0,0,0.04);
  align-items: center;
}
#summary-list li span:first-child { color: #73213d; text-align:left; flex:1; }
#summary-list li span:last-child { color: #333; font-weight:700; white-space:nowrap; margin-left:8px; }
#summary-total { color: #73213d; }
#summary-range { color: #666; font-size:0.95rem; }
/* ...existing code... */
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  window.unavailableDates = new Set();

  const getDurationMinutes = () =>
    typeof computeSummary === 'function' ? computeSummary().minutes : 30;

  const el = document.querySelector("#a_dia");
  if (!el) return;

  if (typeof flatpickr !== 'function') {
    console.error('flatpickr não está disponível.');
    return;
  }

  const fp = flatpickr(el, {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d/m/Y",
    locale: "pt",
    minDate: (() => {
      const t = new Date();
      return new Date(t.getFullYear(), t.getMonth(), t.getDate() + 1);
    })(),
    allowInput: false,
    clickOpens: true,
    appendTo: document.body,
    monthSelectorType: 'static',
    onDayCreate: function(dateObj, dateStr, instance, dayElem) {
      // flatpickr may pass parameters in different orders in some builds
      // or the date object may be available as dayElem.dateObj. Be defensive.
      try {
        if (!dayElem) return;
        var d = null;
        if (Object.prototype.toString.call(dateObj) === '[object Date]') {
          d = dateObj;
        } else if (dayElem && dayElem.dateObj && Object.prototype.toString.call(dayElem.dateObj) === '[object Date]') {
          d = dayElem.dateObj;
        } else {
          return; // nothing we can do
        }

        var y = d.getFullYear();
        var m = String(d.getMonth() + 1).padStart(2, '0');
        var dd = String(d.getDate()).padStart(2, '0');
        var ymd = y + '-' + m + '-' + dd;

        if (window.unavailableDates && window.unavailableDates.has(ymd)) {
          dayElem.classList.add('no-slots');
          dayElem.title = 'Sem horários disponíveis';
        } else {
          var today = new Date();
          var dayOnly = new Date(y, d.getMonth(), d.getDate());
          if (dayOnly <= new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
            dayElem.classList.add('flatpickr-disabled');
          }
        }
      } catch (ex) {
        // don't break the calendar on unexpected errors
        console && console.debug && console.debug('onDayCreate fallback caught', ex);
      }
    },
    onChange: function(selectedDates, dateStr) {
      if (selectedDates && selectedDates[0]) {
        const dt = selectedDates[0];
        const ymd = `${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}-${String(dt.getDate()).padStart(2,'0')}`;
        if (window.unavailableDates.has(ymd)) {
          el.value = '';
          alert('Data sem horários disponíveis. Escolha outra data.');
          return;
        }
      }
      if (typeof fetchHorarios === 'function') fetchHorarios();
    },
  onReady: function(selectedDates, dateStr, instance) { loadUnavailable(instance); },
  onMonthChange: function(selectedDates, dateStr, instance) { loadUnavailable(instance); },
  onYearChange: function(selectedDates, dateStr, instance) { loadUnavailable(instance); }
  });

  // Ensure clicking the <label for="a_dia"> focuses the visible flatpickr input and opens the calendar.
  try {
    const label = document.querySelector('label[for="a_dia"]');
    if (label) {
      // indicate clickable
      label.style.cursor = 'pointer';
      const openPicker = function (ev) {
        try {
          // prefer using the fp instance
          if (fp && typeof fp.open === 'function') fp.open();
          // focus the visible alt input if present
          const alt = fp && fp.altInput ? fp.altInput : document.querySelector('#a_dia');
          if (alt && typeof alt.focus === 'function') alt.focus();
        } catch (err) {
          console && console.warn && console.warn('Erro ao abrir flatpickr via label:', err);
        }
        // prevent default label behavior that might focus hidden original input
        if (ev && typeof ev.preventDefault === 'function') ev.preventDefault();
      };
      label.addEventListener('click', openPicker);
      label.addEventListener('touchstart', openPicker);
    }
  } catch (e) {
    console && console.warn && console.warn('Erro ao vincular label a flatpickr:', e);
  }

  async function loadUnavailable(fpInstance) {
    const year = fpInstance.currentYear;
    const month = fpInstance.currentMonth;
    const start = `${year}-${String(month+1).padStart(2,'0')}-01`;
    const end = `${year}-${String(month+1).padStart(2,'0')}-${String(new Date(year, month+1, 0).getDate()).padStart(2,'0')}`;
    const duration = getDurationMinutes();

    try {
      const url = '<?= BASEURL ?>agendamentos/unavailable_dates.php'
                + '?start=' + encodeURIComponent(start)
                + '&end=' + encodeURIComponent(end)
                + '&duration=' + encodeURIComponent(duration);
      const res = await fetch(url);
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const arr = await res.json();
      window.unavailableDates = new Set(Array.isArray(arr) ? arr : []);

      if (fpInstance && typeof fpInstance.set === 'function') {
        fpInstance.set('disable', [function(date) {
          const ymd = `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
          const today = new Date();
          const dayOnly = new Date(date.getFullYear(), date.getMonth(), date.getDate());
          const isPast = dayOnly <= new Date(today.getFullYear(), today.getMonth(), today.getDate());
          return isPast || window.unavailableDates.has(ymd);
        }]);
      }
      fpInstance.redraw && fpInstance.redraw();
    } catch (err) {
      console.error('Erro ao carregar datas indisponíveis:', err);
    }
  }

  const procContainers = document.querySelectorAll('#procedimentos-list, #dropdownContent');
  procContainers.forEach(c => c && c.addEventListener('change', function() {
    loadUnavailable(fp);
    if (typeof computeSummary === 'function') computeSummary();
  }));
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const dataInput = document.getElementById('a_dia');
  const procContainer = document.getElementById('procedimentos-list') || document.getElementById('dropdownContent');
  const horaSelect = document.getElementById('a_hora');
  const summaryList = document.getElementById('summary-list');
  const summaryTotal = document.getElementById('summary-total');

  function parseDurToMinutes(dur) {
    if (!dur) return 30;
    const [hh, mm] = dur.split(':').map(Number);
    return hh * 60 + mm || 30;
  }

  function computeSummary() {
    if (typeof renderSummary === 'function') return renderSummary();
    const checks = Array.from(procContainer.querySelectorAll('input[type="checkbox"]:checked'));
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
      .then(res => res.text())
      .then(text => {
        let list = JSON.parse(text);
        horaSelect.innerHTML = '';
        if (!Array.isArray(list) || list.length === 0) {
          horaSelect.innerHTML = '<option>Nenhum horário disponível</option>';
        } else {
          list.forEach(h => {
            if (/^[0-2]\d:[0-5]\d$/.test(h.trim())) {
              const opt = document.createElement('option');
              opt.value = h;
              opt.textContent = h;
              horaSelect.appendChild(opt);
            }
          });
        }
        horaSelect.disabled = false;
      })
      .catch(err => {
        console.error('Erro ao buscar horários:', err);
        horaSelect.innerHTML = '<option>Erro ao buscar horários</option>';
        horaSelect.disabled = false;
      });
  }

  // === 🚀 NOVAS REGRAS CORRIGIDAS ===
  function limitProcedimentos(e) {
    const selected = procContainer.querySelectorAll('input[type="checkbox"]:checked');
    if (selected.length > 3) {
      e.target.checked = false;
      alert('Você só pode selecionar até 3 procedimentos.');
      return true;
    }
    return false;
  }

  function enforceTratamentoAvaliacao(e) {
    const all = procContainer.querySelectorAll('input[type="checkbox"]');
    let chkTrat = null, chkAval = null;

    all.forEach(ch => {
      const txt = ch.parentNode.textContent.toLowerCase();
      if (txt.includes('tratamento') && txt.includes('fio')) chkTrat = ch;
      if ((txt.includes('avaliaç') || txt.includes('avaliacao')) && txt.includes('fio')) chkAval = ch;
    });

    if (!chkTrat || !chkAval) return;

    const alvoTxt = e.target.parentNode.textContent.toLowerCase();

    // Se marcar Tratamento → marca Avaliação
    if (alvoTxt.includes('tratamento') && alvoTxt.includes('fio') && e.target.checked) {
      chkAval.checked = true;
    }

    // Se desmarcar Tratamento → desmarca os dois
    if (alvoTxt.includes('tratamento') && alvoTxt.includes('fio') && !e.target.checked) {
      chkAval.checked = false;
      chkTrat.checked = false;
    }

    // Se marcar Avaliação → não faz nada
  }

  if (procContainer) {
    procContainer.addEventListener('change', function(e) {
      if (e.target.matches('input[type="checkbox"]')) {
        if (limitProcedimentos(e)) return;
        enforceTratamentoAvaliacao(e);
        computeSummary();
        fetchHorarios();
      }
    });
  }

  if (dataInput) dataInput.addEventListener('change', fetchHorarios);

  <?php if (isset($_SESSION['old_inputs']['a_dia']) || isset($_SESSION['old_inputs']['id_p'])): ?>
    computeSummary();
    fetchHorarios();
  <?php unset($_SESSION['old_inputs']); endif; ?>
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form.form-agendamento');
  if (form) {
    form.addEventListener('submit', function(e) {
      // Check procedures
      const checked = document.querySelectorAll('#dropdownContent input[name="procedimentos[]"]:checked').length;
      if (checked === 0) {
        e.preventDefault();
        alert('Selecione ao menos 1 procedimento para agendar.');
        return;
      }

      // Check date
      const date = document.querySelector('input[name="data"]').value;
      if (!date) {
        e.preventDefault();
        alert('Selecione uma data para o agendamento.');
        return;
      }

      // Check time
      const time = document.querySelector('select[name="horario"]').value;
      if (!time) {
        e.preventDefault();
        alert('Selecione um horário para o agendamento.');
        return;
      }

      // If we get here, form is valid
      return true;
    });
  }
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

        // ---------- Server-side business rules validation ----------
        // normalize names to lowercase for checks
        $names = array_map(function($p){ return mb_strtolower($p['p_nome'] ?? '', 'UTF-8'); }, $procs);
        $has = function($keywords) use ($names) {
          foreach ($names as $n) {
            foreach ((array)$keywords as $kw) {
              if (mb_strpos($n, mb_strtolower($kw,'UTF-8')) !== false) return true;
            }
          }
          return false;
        };

        $has_micro = $has(['micropig','micropigment']);
        $has_desp  = $has(['despig','despigment']);
        // tratamento dos fios: require both 'tratamento' and 'fio' present in name OR explicit 'tratamento dos fios'
        $has_trat_fios = false;
        foreach ($names as $n) {
          if ((mb_strpos($n,'tratamento') !== false && (mb_strpos($n,'fio') !== false || mb_strpos($n,'fios') !== false)) || mb_strpos($n,'tratamento dos fios') !== false) {
            $has_trat_fios = true; break;
          }
        }
        $has_avaliacao = $has(['avaliacao','avaliaç','avalia']);

        // rule: micropig + despig not together
        if ($has_micro && $has_desp) {
          $message = 'Micropigmentação e Despigmentação não podem ser agendadas no mesmo dia.';
          $type = 'warning';
          close_database($db);
        }
        // rule: tratamento dos fios cannot be with micropig OR despig
        elseif ($has_trat_fios && ($has_micro || $has_desp)) {
          $message = 'Tratamento dos fios não pode ser agendado junto com Micropigmentação ou Despigmentação.';
          $type = 'warning';
          close_database($db);
        }
        // rule: avaliação de crescimento only with tratamento dos fios
        elseif ($has_avaliacao && !$has_trat_fios) {
          $message = 'Avaliação de crescimento só pode ser agendada em conjunto com Tratamento dos fios.';
          $type = 'warning';
          close_database($db);
        }
        // if any rule set $message, abort further processing for this request
        if (!empty($message) && isset($type) && $type === 'warning') {
          // do not proceed with insertion
          // close db already called above in each branch
          // fall through to render page with $message
        } else {
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
                  // mensagem curta e padronizada
                  $message = 'agendamento realizado com sucesso';
                   $type = 'success';
                }
              } catch (Exception $e) {
                $db->rollBack();
                throw $e;
              }
              close_database($db);
            }
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
  } catch (PDOException $e) {
    $message = "Erro ao carregar procedimentos: " . $e->getMessage();
    $type = "danger";
  }

    /* ============= BUSCA DOS AGENDAMENTOS DO USUÁRIO ============= */
    $agendamentos = [];
    try {
        $db = open_database();
        // include duration and value so the form and JS can use them
        $stmt = $db->query("SELECT id, p_nome, p_duracao, p_valor FROM procedimentos ORDER BY p_nome ASC");
        $procedimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // buscar apenas agendamentos futuros do usuário (data+hora posterior ao momento atual)
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
              AND CONCAT(a.a_dia, ' ', a.a_hora) > NOW()
            ORDER BY a.a_dia ASC, a.a_hora ASC
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
// removed duplicate inline flash; page uses inc/alert.php (included at the end) as standard
?>
<section id="agendamento" class="section-cor3"> 
  <div class="form-agendamento">
    <h2>Agendar Procedimento</h2>
    <form action="agendar.php" method="POST" class="form-agendamento">

      <!-- Dropdown de Procedimentos -->
      <div class="form-group dropdown-group">
        <label for="dropdownButton">Procedimentos</label>
        <div class="dropdown-wrapper" id="procPanel">
          <button type="button" id="dropdownButton" class="dropdown-btn">Selecionar procedimentos ▼</button>
          <div id="dropdownContent" class="dropdown-content" aria-hidden="true">
            <?php foreach ($procedimentos as $p):
              $p_id = (int) $p['id'];
              $p_nome = htmlspecialchars($p['p_nome'], ENT_QUOTES);
              $p_valor = number_format(floatval($p['p_valor']), 2, ',', '.');
              $p_dur = $p['p_duracao'] ?? '00:30';
            ?>
              <label class="dropdown-item">
                <input type="checkbox" name="procedimentos[]" value="<?= $p_id ?>" data-dur="<?= $p_dur ?>" data-valor="<?= $p_valor ?>" data-name="<?= $p_nome ?>">
                <span class="proc-name"><?= $p_nome ?></span>
                <span class="proc-price">R$ <?= $p_valor ?></span>
              </label>
            <?php endforeach; ?>
          </div>

      <!-- Data e Hora -->
      <div class="form-row">
        <div class="form-group">
          <label for="a_dia">Data:</label>
          <!-- readonly removido para garantir que clique/focus funcionem; flatpickr controla input -->
          <input type="text" name="data" id="a_dia" required value="<?= $old['data'] ?? '' ?>" autocomplete="off">
        </div>
        <div class="form-group">
          <label for="a_hora">Hora:</label>
          <select class="form-control" id="a_hora" name="horario" required>
            <option value="">Selecione a data e procedimentos primeiro</option>
          </select>
        </div>
      </div>

      <!-- Resumo -->
      <div class="summary-box" id="summary">
        <h3 style="margin:0 0 8px 0; color:var(--cor2); font-size:1rem;">Resumo</h3>
        <ul id="summary-list" style="margin:0; padding:0; list-style:none;"></ul>
        <div id="summary-total" style="margin-top:10px; font-weight:700;"></div>
        <div id="summary-range" style="color:#666; font-size:0.95rem; margin-top:6px;"></div>
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
               <td><?= htmlspecialchars($ag['procedimento']) ?></td>
               <td><?= formatadata($ag['a_dia'], 'd/m/Y') ?></td>
               <td><?= htmlspecialchars($ag['a_hora']) ?></td>
              <td><?= formatadata($ag['created_at'], 'd/m/Y') ?></td>
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
  padding-top: 110px; /* aumento para não ficar colado no header fixo */
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

/* ======= Campos ======= */
.form-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  gap: 8px;
}

/* ======= Painel de Procedimentos ======= */
.proc-panel {
  display: block;
  background: #fff9f8;
  border: 1px solid #f1d7db;
  border-radius: 14px;
  padding: 20px;
  width: 100%;
  max-width: 700px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
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

/* ======= Tabela (novo visual elegante) ======= */
.tabela-wrapper {
  width: 100%;
  max-width: 920px;
  background: linear-gradient(180deg, #fff, #fffaf9);
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 8px 28px rgba(35,20,30,0.08);
  overflow-x: auto;
  border: 1px solid rgba(115,33,61,0.06);
}

.tabela-lunaris {
  width: 100%;
  border-collapse: collapse;
  color: #2d2d2d;
  font-family: "Inter", "Poppins", system-ui, -apple-system, "Segoe UI", Roboto, Arial;
  background: transparent;
}

/* CENTRALIZA os nomes/células da tabela (header e corpo) */
.tabela-lunaris thead th,
.tabela-lunaris tbody td {
  text-align: center;
}

.tabela-lunaris thead th {
  background: linear-gradient(90deg, #fff8f7, #fff);
  color: #73213d;
  padding: 12px 14px;
  font-weight: 700;
  font-size: 0.95rem;
  border-bottom: 2px solid rgba(115,33,61,0.06);
}

.tabela-lunaris tbody td {
  padding: 12px 14px;
  border-bottom: 1px solid rgba(115,33,61,0.04);
  vertical-align: middle;
  color: #444;
  font-size: 0.95rem;
}

.tabela-lunaris tbody tr {
  background: transparent;
  transition: background 0.18s ease, transform 0.18s ease;
}

.tabela-lunaris tbody tr:hover {
  background: linear-gradient(90deg, rgba(115,33,61,0.03), rgba(160,90,111,0.02));
  transform: translateY(-2px);
}

.tabela-lunaris tbody tr:nth-child(even) {
  background: rgba(115,33,61,0.02);
}

.tabela-lunaris button {
  background: linear-gradient(180deg, #a05a6f, #73213d);
  border: none;
  color: #fff;
  border-radius: 8px;
  padding: 8px 12px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
}

/* ====== Remover exibição do tempo dentro do dropdown ======
   - oculta elementos que contenham atributo data-dur dentro do painel dropdown
   - oculta elementos com classe .proc-duration dentro do dropdown
   (se necessário, ajuste a classe conforme template de saída dos procedimentos)
*/
#dropdownContent [data-dur],
#dropdownContent .proc-duration,
.dropdown-item [data-dur] {
  display: none !important;
}

/* fallback: pequenos spans/elem de duração dentro do dropdown */
#dropdownContent .proc-meta small,
#dropdownContent .proc-meta .duracao {
  display: none !important;
}

/* ensure header cells don't look cramped on small screens */
@media (max-width: 768px) {
  .tabela-lunaris thead th, .tabela-lunaris tbody td { padding: 10px 8px; font-size: 0.9rem; }
  .tabela-wrapper { padding: 12px; }
}
/* ...existing code... */
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

    // collect normalized names of selected procedures
    const names = getCheckedInputs().map(cb => (cb.dataset.name || (cb.parentNode && cb.parentNode.textContent) || '').toLowerCase());

    // helper to check if any name contains a keyword
    const has = kw => names.some(n => n.indexOf(kw) !== -1);
    const hasMicro = has('micropig');      // micropigmentação
    const hasDespig = has('despig');       // despigmentação
    const hasTratFios = names.some(n => (n.indexOf('tratamento') !== -1 && (n.indexOf('fio') !== -1 || n.indexOf('fios') !== -1)) || n.indexOf('tratamento dos fios') !== -1);
    const hasAvaliacao = has('avalia') || has('avaliacao'); // avaliação de crescimento

    // RULE: micropigmentação e despigmentação não podem juntas
    if (hasMicro && hasDespig) {
      // undo the last action
      e.target.checked = false;
      showToast('Micropigmentação e Despigmentação não podem ser agendadas no mesmo dia.');
      // update visuals then exit
      if (procGrid) {
        procGrid.querySelectorAll('.proc-card').forEach(function(card) {
          const cb = card.querySelector('input[type="checkbox"]');
          if (cb && cb.checked) card.classList.add('selected'); else card.classList.remove('selected');
        });
      }
      updateCounter();
      if (document.getElementById('a_dia') && document.getElementById('a_dia').value && typeof fetchHorarios === 'function') fetchHorarios();
      if (typeof renderSummary === 'function') renderSummary();
      return;
    }

    // RULE: tratamento dos fios NÃO pode ser agendado com micropig OR despig
    if (hasTratFios && (hasMicro || hasDespig)) {
      e.target.checked = false;
      showToast('Tratamento dos fios não pode ser agendado junto com Micropigmentação ou Despigmentação.');
      if (procGrid) {
        procGrid.querySelectorAll('.proc-card').forEach(function(card) {
          const cb = card.querySelector('input[type="checkbox"]');
          if (cb && cb.checked) card.classList.add('selected'); else card.classList.remove('selected');
        });
      }
      updateCounter();
      if (document.getElementById('a_dia') && document.getElementById('a_dia').value && typeof fetchHorarios === 'function') fetchHorarios();
      if (typeof renderSummary === 'function') renderSummary();
      return;
    }

    // RULE: avaliação de crescimento deve sempre acompanhar Tratamento dos Fios
    // se avaliação foi selecionada sem tratamento, auto-seleciona o tratamento
    if (hasAvaliacao && !hasTratFios) {
      // Find treatment-of-hair checkboxes by name (avoid hard-coded IDs)
      const allCbs = Array.from(document.querySelectorAll('#dropdownContent input[type="checkbox"], #procedimentos-list input[type="checkbox"]'));
      const tratCbs = allCbs.filter(cb => {
        const n = (cb.dataset.name || cb.parentNode.textContent || '').toLowerCase();
        return ((n.indexOf('tratamento') !== -1 && (n.indexOf('fio') !== -1 || n.indexOf('fios') !== -1)) || n.indexOf('tratamento dos fios') !== -1);
      });

      const alreadyChecked = getCheckedInputs().length;
      const needToAdd = tratCbs.filter(cb => !cb.checked).length;

      if (alreadyChecked + needToAdd > maxSelect) {
        e.target.checked = false;
        showToast('Não é possível selecionar Avaliação pois excederia o número máximo de procedimentos.');
      } else {
        tratCbs.forEach(cb => {
          if (!cb.checked) {
            cb.checked = true;
            cb.dataset.auto = '1';
            // Dispara evento de change para atualizar o UI
            cb.dispatchEvent(new Event('change', { bubbles: true }));
          }
        });
      }
    }
    
    // Se desmarcou Avaliação, desmarca também Tratamento se foi auto-selecionado
    if (!hasAvaliacao) {
      const ID_TRAT_FIOS = '7';
      const tratCbs = Array.from(document.querySelectorAll('input[type="checkbox"][value="' + ID_TRAT_FIOS + '"][data-auto="1"]'));
      tratCbs.forEach(cb => {
        cb.checked = false;
        delete cb.dataset.auto;
        cb.dispatchEvent(new Event('change', { bubbles: true }));
      });
    }

    // Auto-select rule: when 'Tratamento dos Fios' is selected, automatically select 'Avaliação de crescimento de fios'
    // and disable unchecking it while the treatment is selected. When treatment is removed, restore the evaluation state.
    try {
      // Identify treatment and evaluation checkboxes by name (avoid brittle numeric IDs)
      const allCbs = Array.from(document.querySelectorAll('#dropdownContent input[type="checkbox"], #procedimentos-list input[type="checkbox"]'));
      const findByName = (pred) => allCbs.filter(cb => {
        const n = (cb.dataset.name || cb.parentNode.textContent || '').toLowerCase();
        return pred(n, cb);
      });

      const isTratName = (n) => ((n.indexOf('tratamento') !== -1 && (n.indexOf('fio') !== -1 || n.indexOf('fios') !== -1)) || n.indexOf('tratamento dos fios') !== -1);
      const isAvalName = (n) => (n.indexOf('avalia') !== -1 || n.indexOf('avaliacao') !== -1 || n.indexOf('avaliaç') !== -1);

      const targetName = (e.target && (e.target.dataset.name || e.target.parentNode.textContent || '')).toLowerCase();
      const targetIsTrat = isTratName(targetName);
      const targetIsAval = isAvalName(targetName);

      // If treatment was just checked, ensure evaluation is checked and marked as auto
      if (targetIsTrat && e.target.checked) {
        const evalCbs = findByName((n)=>isAvalName(n));
        evalCbs.forEach(cb => {
          if (!cb.checked) {
            cb.checked = true;
            cb.dataset.auto = '1';
            cb.dispatchEvent(new Event('change', { bubbles: true }));
          }
          cb.disabled = true; // prevent unchecking while treatment active
        });
      }

      // If treatment was just unchecked, remove auto-check and re-enable evaluation (if it was auto-checked)
      if (targetIsTrat && !e.target.checked) {
        const evalCbs = findByName((n)=>isAvalName(n));
        evalCbs.forEach(cb => {
          if (cb.dataset.auto === '1') {
            cb.checked = false;
            delete cb.dataset.auto;
            cb.dispatchEvent(new Event('change', { bubbles: true }));
          }
          cb.disabled = false;
        });
      }

      // If evaluation was just checked, ensure treatment is checked and mark treatment as auto (mirror behavior)
      if (targetIsAval && e.target.checked) {
        const tratCbs = findByName((n)=>isTratName(n));
        tratCbs.forEach(cb => {
          if (!cb.checked) {
            cb.checked = true;
            cb.dataset.auto = '1';
            cb.dispatchEvent(new Event('change', { bubbles: true }));
          }
          cb.disabled = true;
        });
      }

      // If evaluation was just unchecked, remove auto-check and re-enable treatment (if it was auto-checked)
      if (targetIsAval && !e.target.checked) {
        const tratCbs = findByName((n)=>isTratName(n));
        tratCbs.forEach(cb => {
          if (cb.dataset.auto === '1') {
            cb.checked = false;
            delete cb.dataset.auto;
            cb.dispatchEvent(new Event('change', { bubbles: true }));
          }
          cb.disabled = false;
        });
      }
    } catch (exAuto) {
      console.warn('Auto-select tratamento/avaliacao failed', exAuto);
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

<?php include_once __DIR__ . '/../inc/alert.php'; ?>
<?php include(FOOTER_TEMPLATE); ?>



