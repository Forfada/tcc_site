<?php
include 'config.php';
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
    $id_p = $_POST['procedimento'] ?? null;
    $data = $_POST['data'] ?? null;
    $hora = $_POST['horario'] ?? null;

    if ($id_p && $data && $hora) {
        $a_dia = date('Y-m-d', strtotime($data));
        $a_hora = date('H:i:s', strtotime($hora));

        // proteger no servidor: não permitir agendar para hoje nem datas passadas
        if ($a_dia <= date('Y-m-d')) {
            $_SESSION['old_inputs'] = [
                'procedimento' => $id_p,
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

            // get duration for selected procedure
            $stmt = $db->prepare('SELECT p_duracao FROM procedimentos WHERE id = :id');
            $stmt->bindParam(':id', $id_p, PDO::PARAM_INT);
            $stmt->execute();
            $proc = $stmt->fetch(PDO::FETCH_ASSOC);
            $duration = 30;
            if ($proc && !empty($proc['p_duracao'])) {
                list($hh, $mm) = array_pad(explode(':', $proc['p_duracao']), 2, '00');
                $duration = intval($hh) * 60 + intval($mm);
                if ($duration <= 0) $duration = 30;
            }

            // check if user already has same procedure on this date
            $stmt = $db->prepare('SELECT COUNT(*) FROM agendamento WHERE id_u = :id_u AND a_dia = :a_dia AND id_p = :id_p');
            $stmt->bindParam(':id_u', $id_u, PDO::PARAM_INT);
            $stmt->bindParam(':a_dia', $a_dia);
            $stmt->bindParam(':id_p', $id_p, PDO::PARAM_INT);
            $stmt->execute();
            $same = (int) $stmt->fetchColumn();
            if ($same > 0) {
                // preserve inputs so the form can be repopulated
                $_SESSION['old_inputs'] = [
                    'procedimento' => $id_p,
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
                // preserve inputs so user doesn't need to reselect
                $_SESSION['old_inputs'] = [
                    'procedimento' => $id_p,
                    'data' => $data,
                    'horario' => $hora
                ];
                $_SESSION['message'] = 'Este horário conflita com outro agendamento.';
                $_SESSION['type'] = 'danger';
            } else {
                $dados = [
                    'a_dia' => $a_dia,
                    'a_hora' => $a_hora,
                    'id_u' => $id_u,
                    'id_p' => $id_p
                ];
                save('agendamento', $dados);
                // mensagem padronizada curta
                $_SESSION['message'] = 'agendamento realizado com sucesso';
                $_SESSION['type'] = 'success';
            }

            close_database($db);
        } catch (Exception $e) {
            $_SESSION['message'] = 'Erro ao processar agendamento: ' . $e->getMessage();
            $_SESSION['type'] = 'danger';
        }

        header('Location: historico.php');
        exit;
    } else {
        // preserve what the user filled (if any)
        $_SESSION['old_inputs'] = [
            'procedimento' => $id_p,
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
