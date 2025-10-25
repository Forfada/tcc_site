<?php
include '../config.php';
include(DBAPI);
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['nome'])) {
    $_SESSION['message'] = 'Você precisa estar logado para agendar.';
    $_SESSION['type'] = 'danger';
    header('Location: inc/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_u = $_SESSION['id'];
    $procedimentos = isset($_POST['procedimentos']) ? (array)$_POST['procedimentos'] : [];
    $data = $_POST['data'] ?? null;
    $hora = $_POST['horario'] ?? null;
    // debug log (do not leave enabled in production long-term)
    $debugLog = __DIR__ . DIRECTORY_SEPARATOR . 'agendamento_debug.log';
    try {
        $log = date('Y-m-d H:i:s') . " | POST recibo: procedimentos=" . json_encode($procedimentos) . ", data=" . var_export($data, true) . ", hora=" . var_export($hora, true) . "\n";
        file_put_contents($debugLog, $log, FILE_APPEND | LOCK_EX);
    } catch (Exception $ex) {
        // ignore logging errors
    }

    if (!empty($procedimentos) && !empty($data) && !empty($hora)) {
        $a_dia = date('Y-m-d', strtotime($data));
        $a_hora = date('H:i:s', strtotime($hora));

        // Server-side validation: 'Avaliação de crescimento de fios' must be scheduled together with 'Tratamento dos fios'
        try {
            $dbv = open_database();
            $placeholders_v = implode(',', array_fill(0, count($procedimentos), '?'));
            $stmtv = $dbv->prepare("SELECT p_nome FROM procedimentos WHERE id IN ($placeholders_v)");
            foreach ($procedimentos as $k => $pid) $stmtv->bindValue($k+1, $pid, PDO::PARAM_INT);
            $stmtv->execute();
            $names = $stmtv->fetchAll(PDO::FETCH_COLUMN);
            close_database($dbv);
            $norm = array_map(function($n){ return mb_strtolower($n,'UTF-8'); }, $names);
            $has_avali = false; $has_trat_fios = false;
            foreach ($norm as $n) {
                if (mb_strpos($n,'avalia') !== false || mb_strpos($n,'avaliacao') !== false || mb_strpos($n,'avaliaç') !== false) $has_avali = true;
                if ((mb_strpos($n,'tratamento') !== false && (mb_strpos($n,'fio') !== false || mb_strpos($n,'fios') !== false)) || mb_strpos($n,'tratamento dos fios') !== false) $has_trat_fios = true;
            }
            if ($has_avali && !$has_trat_fios) {
                $_SESSION['old_inputs'] = ['procedimentos' => $procedimentos, 'data' => $data, 'horario' => $hora];
                $_SESSION['message'] = 'Avaliação de crescimento de fios deve ser agendada junto com Tratamento dos fios.';
                $_SESSION['type'] = 'warning';
                header('Location: agendamento.php');
                exit;
            }
        } catch (Exception $e) {
            // ignore validation error and continue; main try/catch will handle DB errors
        }

        // proteger no servidor: não permitir agendar para hoje nem datas passadas
        if ($a_dia <= date('Y-m-d')) {
            $_SESSION['old_inputs'] = [
                'procedimentos' => $procedimentos,
                'data' => $data,
                'horario' => $hora
            ];
            $_SESSION['message'] = 'Não é possível agendar para hoje ou datas passadas. Escolha uma data futura.';
            $_SESSION['type'] = 'warning';
            header('Location: agendamento.php');
            exit;
        }

        // server-side overlap check using procedure durations
        try {
            $db = open_database();

            // get total duration for all selected procedures
            $duration = 0;
            foreach ($procedimentos as $proc_id) {
                $stmt = $db->prepare('SELECT p_duracao FROM procedimentos WHERE id = :id');
                $stmt->bindParam(':id', $proc_id, PDO::PARAM_INT);
                $stmt->execute();
                $proc = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($proc && !empty($proc['p_duracao'])) {
                    list($hh, $mm) = array_pad(explode(':', $proc['p_duracao']), 2, '00');
                    $duration += intval($hh) * 60 + intval($mm);
                } else {
                    $duration += 30; // default duration if not specified
                }
            }
            if ($duration <= 0) $duration = 30;

            // check if user already has any of these procedures on this date
            $placeholders = str_repeat('?,', count($procedimentos) - 1) . '?';
            $sql = "SELECT COUNT(*) FROM agendamento WHERE id_u = ? AND a_dia = ? AND id_p IN ($placeholders)";
            $params = array_merge([$id_u, $a_dia], $procedimentos);
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $same = (int) $stmt->fetchColumn();
            if ($same > 0) {
                // preserve inputs so the form can be repopulated
                $_SESSION['old_inputs'] = [
                    'procedimentos' => $procedimentos,
                    'data' => $data,
                    'horario' => $hora
                ];
                $_SESSION['message'] = 'Você já possui um agendamento para este procedimento nesta data.';
                $_SESSION['type'] = 'warning';
                close_database($db);
                header('Location: agendamento.php');
                exit;
            }

            $start_ts = strtotime($a_dia . ' ' . $a_hora);
            $end_ts = $start_ts + $duration * 60;

            $sql = 'SELECT a.a_hora, p.p_duracao FROM agendamento a JOIN procedimentos p ON a.id_p = p.id WHERE a.a_dia = :a_dia';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':a_dia', $a_dia);
            $stmt->execute();
            $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $conflict = false;
            foreach ($existing as $e) {
                $s_ts = strtotime($a_dia . ' ' . $e['a_hora']);
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
                $_SESSION['old_inputs'] = [
                    'procedimentos' => $procedimentos,
                    'data' => $data,
                    'horario' => $hora
                ];
                $_SESSION['message'] = 'Este horário conflita com outro agendamento.';
                $_SESSION['type'] = 'danger';
            } else {
                // inserir todos os procedimentos selecionados
                $success = true;
                $db->beginTransaction();
                try {
                    foreach ($procedimentos as $proc_id) {
                        $dados = [
                            'a_dia' => $a_dia,
                            'a_hora' => $a_hora,
                            'id_u' => $id_u,
                            'id_p' => $proc_id,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        $sql = "INSERT INTO agendamento (a_dia, a_hora, id_u, id_p, created_at) VALUES (:a_dia, :a_hora, :id_u, :id_p, :created_at)";
                        $stmt = $db->prepare($sql);
                        if (!$stmt->execute($dados)) {
                            throw new Exception("Falha ao inserir agendamento");
                        }
                    }
                    $db->commit();
                    $_SESSION['message'] = 'Agendamento realizado com sucesso';
                    $_SESSION['type'] = 'success';
                } catch (Exception $e) {
                    $db->rollBack();
                    $_SESSION['message'] = "Erro ao salvar agendamento: " . $e->getMessage();
                    $_SESSION['type'] = 'danger';
                    $success = false;
                }
            }

            close_database($db);
        } catch (Exception $e) {
            // write full exception to debug log and set a short message for the user
            try {
                file_put_contents($debugLog, date('Y-m-d H:i:s') . " | Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n", FILE_APPEND | LOCK_EX);
            } catch (Exception $__) {}
            $_SESSION['message'] = 'Erro ao processar agendamento. Detalhes no log.';
            $_SESSION['type'] = 'danger';
        }

        header('Location: historico.php');
        exit;
    } else {
        // preserve what the user filled (if any)
        $_SESSION['old_inputs'] = [
            'procedimentos' => $procedimentos,
            'data' => $data,
            'horario' => $hora
        ];
        $_SESSION['message'] = 'Preencha todos os campos corretamente.';
        $_SESSION['type'] = 'danger';
        header('Location: agendamento.php');
        exit;
    }
} else {
    header('Location: agendamento.php');
    exit;
}
