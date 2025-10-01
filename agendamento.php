<?php
include 'config.php';
include(DBAPI);
include(HEADER_TEMPLATE);

if (!isset($_SESSION)) session_start();
$logado = isset($_SESSION['id']) && isset($_SESSION['nome']);

// Buscar procedimentos do banco
define('TABLE_PROC', 'procedimentos');
$procedimentos = find_all(TABLE_PROC);

// Buscar agendamentos existentes
function horarios_ocupados($data) {
    $agendamentos = filter('agendamento', "DATE(a_dia) = '" . $data . "'");
    $horarios = [];
    if ($agendamentos) {
        foreach ($agendamentos as $ag) {
            $horarios[] = date('H:i', strtotime($ag['a_hora']));
        }
    }
    return $horarios;
}

?>

<div class="container py-5" id="agendamento">
    <h2 class="text-center mb-4" style="color:var(--cor2)">Agende seu procedimento</h2>
    <?php if (!$logado): ?>
        <div class="alert alert-danger text-center">Você precisa estar logado para agendar. <a href="inc/login.php" class="btn btn-sm btn-danger ms-2">Entrar</a></div>
    <?php else: ?>
    <form method="post" action="agendar.php" class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="procedimento" class="form-label">Procedimento</label>
                <select class="form-select" id="procedimento" name="procedimento" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($procedimentos as $proc): ?>
                        <option value="<?php echo $proc['id']; ?>">
                            <?php echo $proc['p_nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="data" class="form-label">Dia</label>
                <input type="text" class="form-control" id="data" name="data" required readonly placeholder="Escolha no calendário">
            </div>
            <div class="mb-3">
                <label for="horario" class="form-label">Horário</label>
                <select class="form-select" id="horario" name="horario" required>
                    <option value="">Selecione o dia primeiro</option>
                </select>
            </div>
            <button type="submit" class="btn custom-login-btn w-100">Agendar</button>
        </div>
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <!-- Calendário visual -->
            <div id="calendar-wrapper" class="p-3 rounded shadow" style="background:var(--cor3); min-width:320px;">
                <div id="calendar"></div>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn btn-sm btn-outline-dark" id="prevMonth">&#8592; Mês anterior</button>
                    <button type="button" class="btn btn-sm btn-outline-dark" id="nextMonth">Próximo mês &#8594;</button>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>


<script>
// Calendário com navegação de meses e bloqueio de dias
let currentMonth = (new Date()).getMonth();
let currentYear = (new Date()).getFullYear();

function renderCalendar(month = currentMonth, year = currentYear) {
    const calendar = document.getElementById('calendar');
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    let html = `<h5 style='color:var(--cor2);text-align:center;'>${firstDay.toLocaleString('default', { month: 'long' })} ${year}</h5>`;
    html += '<table class="table table-bordered text-center"><thead><tr>';
    ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'].forEach(d => html += `<th>${d}</th>`);
    html += '</tr></thead><tbody><tr>';
    for(let i=0; i<firstDay.getDay(); i++) html += '<td></td>';
    for(let d=1; d<=lastDay.getDate(); d++) {
        const dateObj = new Date(year, month, d);
        const weekDay = dateObj.getDay();
        const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        let style = 'cursor:pointer;';
        let disabled = false;
        if (weekDay === 0 || weekDay === 1) { // domingo ou segunda
            style += 'background:#e74c3c;color:white;cursor:not-allowed;';
            disabled = true;
        }
        html += `<td class='calendar-day' data-date='${dateStr}' data-disabled='${disabled}' style='${style}'>${d}</td>`;
        if((firstDay.getDay()+d)%7===0) html += '</tr><tr>';
    }
    html += '</tr></tbody></table>';
    calendar.innerHTML = html;
    document.querySelectorAll('.calendar-day').forEach(cell => {
        if (cell.dataset.disabled === 'true') return;
        cell.addEventListener('click', function() {
            document.getElementById('data').value = this.dataset.date;
            document.querySelectorAll('.calendar-day').forEach(c => c.style.background='');
            this.style.background = 'var(--cor2)';
            this.style.color = 'var(--cor1)';
            loadHorarios(this.dataset.date);
        });
    });
}

function loadHorarios(data) {
    // Horários disponíveis: 09:00 às 18:00, de hora em hora
    let horarios = [];
    for (let h = 9; h <= 18; h++) {
        horarios.push((h < 10 ? '0' : '') + h + ':00');
    }
    // AJAX para buscar horários ocupados
    fetch(`agendamento_horarios.php?data=${data}`)
        .then(res => res.json())
        .then(ocupados => {
            let select = document.getElementById('horario');
            select.innerHTML = '';
            horarios.forEach(h => {
                let disabled = ocupados.includes(h);
                let opt = document.createElement('option');
                opt.value = h;
                opt.textContent = h + (disabled ? ' (Indisponível)' : '');
                if (disabled) opt.disabled = true;
                select.appendChild(opt);
            });
        });
}

document.addEventListener('DOMContentLoaded', function() {
    renderCalendar();
    document.getElementById('prevMonth').addEventListener('click', function() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(currentMonth, currentYear);
    });
    document.getElementById('nextMonth').addEventListener('click', function() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentMonth, currentYear);
    });
});
</script>

<?php include(FOOTER_TEMPLATE); ?>
