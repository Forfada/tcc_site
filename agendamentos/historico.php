<?php
include '../config.php';
include DBAPI;
session_start(); // garantir sessão antes do HEADER

// se for administrador, redireciona para o histórico admin (não usar o histórico de usuário)
if (function_exists('is_admin') && is_admin()) {
    header('Location: ' . BASEURL . 'agendamentos/admin_historico.php');
    exit();
}

include (HEADER_TEMPLATE);

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$rows = [];
$realizados = [];
$sera_realizado = [];

try {
    $db = open_database();

    $sql = "
        SELECT 
            a.id,
            a.a_dia,
            a.a_hora,
            p.p_nome AS procedimento
        FROM agendamento a
        JOIN procedimentos p ON a.id_p = p.id
        WHERE a.id_u = :id_u
        ORDER BY a.a_dia DESC, a.a_hora DESC
    ";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_u', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    close_database($db);

    // separar realizados (passado) e será realizado (futuro)
    $now = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    $now_ts = $now->getTimestamp();
    foreach ($rows as $r) {
        $ts = strtotime($r['a_dia'] . ' ' . $r['a_hora']);
        if ($ts <= $now_ts) {
            $realizados[] = $r;
        } else {
            $sera_realizado[] = $r;
        }
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erro ao carregar histórico: " . $e->getMessage();
    $_SESSION['type'] = "danger";
}
?>

<main class="container-historico">
    <h1 class="titulo-historico">🕓 Histórico de Agendamentos</h1>

    <section style="width:100%; max-width:1000px; margin-bottom:2.5rem;">
        <h2 style="color:var(--cor2); margin-bottom:1rem;">Será realizado</h2>
        <?php if (!empty($sera_realizado)): ?>
            <div class="cards-container">
                <?php foreach ($sera_realizado as $ag): ?>
                    <div class="card-agendamento">
                        <div class="card-header">
                            <h2><?= htmlspecialchars($ag['procedimento']) ?></h2>
                        </div>
                        <div class="card-body">
                            <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($ag['a_dia'])) ?></p>
                            <p><strong>Hora:</strong> <?= htmlspecialchars($ag['a_hora']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mensagem-vazia">Nenhum agendamento futuro encontrado.</p>
        <?php endif; ?>
    </section>

    <section style="width:100%; max-width:1000px;">
        <h2 style="color:var(--cor2); margin-bottom:1rem;">Realizados</h2>
        <?php if (!empty($realizados)): ?>
            <div class="cards-container">
                <?php foreach ($realizados as $ag): ?>
                    <div class="card-agendamento">
                        <div class="card-header" style="background: #e9ecef; color: var(--cor1);">
                            <h2><?= htmlspecialchars($ag['procedimento']) ?></h2>
                        </div>
                        <div class="card-body">
                            <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($ag['a_dia'])) ?></p>
                            <p><strong>Hora:</strong> <?= htmlspecialchars($ag['a_hora']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mensagem-vazia">Nenhum agendamento realizado ainda.</p>
        <?php endif; ?>
    </section>
</main>

<?php include(FOOTER_TEMPLATE); ?>

<style>
/* ====== Estilo Histórico (mantido) ====== */
.container-historico {
    padding: 4rem 2rem;
    background: var(--cor3);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.titulo-historico {
    font-family: 'Playfair Display', serif;
    color: var(--cor2);
    font-size: 2.4rem;
    margin-bottom: 2rem;
    text-align: center;
}
.cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    width: 100%;
}
.card-agendamento {
    background: #fff;
    border-radius: 1rem;
    overflow: hidden;
    transition: transform 0.2s ease;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}
.card-header { padding: 0.9rem; text-align:center; background: var(--cor2); color:#fff; }
.card-body { padding: 1rem; color: var(--cor1); }
.mensagem-vazia { background: rgba(255,255,255,0.15); padding: 1rem 1.2rem; border-radius: 10px; text-align:center; color:var(--cor2); }
</style>
