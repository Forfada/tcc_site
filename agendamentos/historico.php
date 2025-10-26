<?php
include '../config.php';
include DBAPI;
session_start(); // garantir sessão antes do HEADER

// se for administrador, redireciona para o histórico admin (não usar o histórico de usuário)
if (function_exists('is_admin') && is_admin()) {
    header('Location: ' . BASEURL . 'agendamentos/admin_historico.php');
    exit();
}

include (INIT);
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
       ORDER BY a.a_dia ASC, a.a_hora ASC
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

<section class="section-light section-cor3 py-5" id="historico">
    <div class="container mt-5" style="margin-top: 6rem !important;">
        <h1 class="titulo-historico">Histórico de Agendamentos</h1>
        <p class="txt4 text-center mb-2">Visualize todos os seus procedimentos agendados logo abaixo.</p>
       <hr>
        <div class="alert alert-info" role="alert">
        <i class="fa-solid fa-circle-info"></i> Solicitamos que, em caso de desistência, o cancelamento (exlusão do agendamento) seja realizado até 2 dias antes da data marcada. Exclua seus procedimentos <a href="<?php echo BASEURL; ?>agendamentos/agendamento.php#tabela-agendamentos" class="alert-link">aqui</a>.
        </div>
        <div style="width:100%; max-width:1000px; margin-bottom:2.5rem;">
            <h2 class="text-start subtitulo-historico">Será realizado</h2>
            <?php if (!empty($sera_realizado)): ?>
                <div class="cards-container">
                    <?php foreach ($sera_realizado as $ag): ?>
                        <div class="card-agendamento">
                            <div class="card-header">
                                <h4><?= htmlspecialchars($ag['procedimento']) ?></h4>
                            </div>
                            <div class="card-body">
                                <p><strong><i class="fa-regular fa-calendar"></i> Data:</strong> <?= date('d/m/Y', strtotime($ag['a_dia'])) ?></p>
                                <p><strong><i class="fa-regular fa-clock"></i> Hora:</strong> <?= duracao($ag['a_hora']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="mensagem-vazia">Nenhum agendamento futuro encontrado.</p>
            <?php endif; ?>
        </div>

        <div style="width:100%; max-width:1000px; margin-bottom:2.5rem;">
            <h2 class="text-start subtitulo-historico">Realizados</h2>
            <?php if (!empty($realizados)): ?>
                <div class="cards-container">
                    <?php foreach ($realizados as $ag): ?>
                        <div class="card-agendamento">
                            <div class="card-header">
                                <h4><?= htmlspecialchars($ag['procedimento']) ?></h4>
                            </div>
                            <div class="card-body">
                                <p><strong><i class="fa-regular fa-calendar"></i> Data:</strong> <?= date('d/m/Y', strtotime($ag['a_dia'])) ?></p>
                                <p><strong><i class="fa-regular fa-clock"></i> Hora:</strong> <?= htmlspecialchars($ag['a_hora']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="mensagem-vazia">Nenhum procedimento realizado ainda.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>

<style>
/* ====== Estilo Modernizado (sem quebrar estrutura) ====== */
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.container hr {
    align-self: stretch;
}

.titulo-historico {
    font-family: 'Playfair Display', serif;
    color: var(--cor2);
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 1rem;
    position: relative;
}

.subtitulo-historico {
    color: var(--cor2);
    font-weight: 600;
    font-size: 1.5rem;
    position: relative;
    padding-left: 0.8rem;
}
.subtitulo-historico::before {
    content: '';
    position: absolute;
    left: -0.2rem;
    top: 0.45rem;
    width: 5px;
    height: 80%;
    background: var(--cor2);
    border-radius: 3px;
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
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.card-agendamento:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(0,0,0,0.12);
}

.card-header {
    padding: 1rem;
    height: 5rem;
    text-align: center;
    background: linear-gradient(135deg, var(--cor7), var(--cor4));
    color: #fff;
}
.card-passado .card-header {
    background: linear-gradient(135deg, var(--cor7), var(--cor6));
    color: var(--cor1);
}
.card-body {
    padding: 1.1rem;
    color: var(--cor5);
    text-align: start;
    font-size: 1.05rem;
    line-height: 1.5;
}

.mensagem-vazia {
    background: rgba(255,255,255,0.7);
    padding: 1rem 1.3rem;
    border-radius: 10px;
    text-align: center;
    color: var(--cor2);
    font-weight: 500;
}

/* ====== RESPONSIVIDADE ====== */
@media (min-width: 992px) {
    .titulo-historico {
        font-size: 2rem;
    }

    .subtitulo-historico {
        font-size: 1.4rem;
        padding-left: 0.6rem;
    }

    .container {
        padding: 0 1.2rem;
    }

    .card-header h4 {
        font-size: 1.1rem;
    }

    .card-body {
        font-size: 0.95rem;
    }
}

@media (max-width: 768px) {
    .titulo-historico {
        font-size: 1.8rem;
        margin-bottom: 0.8rem;
    }

    .txt4 {
        font-size: 0.95rem;
        padding: 0 0.5rem;
    }

    .cards-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .card-agendamento {
        border-radius: 0.8rem;
        box-shadow: 0 5px 16px rgba(0,0,0,0.07);
    }

    .card-header {
        height: auto;
        padding: 0.8rem;
    }

    .card-body p {
        font-size: 0.9rem;
    }

    .subtitulo-historico::before {
        top: 0.4rem;
        height: 75%;
    }
}

@media (max-width: 480px) {
    .titulo-historico {
        font-size: 1.6rem;
    }

    .subtitulo-historico {
        font-size: 1.1rem;
        padding-left: 0.5rem;
        left: 0.5rem;
    }

    .cards-container {
        width: 95%;
        padding: 10px 18px;
        align-items: center;
        gap: 1rem;
    }

    .card-agendamento {
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .card-header h4 {
        font-size: 1.1rem;
    }

    .card-body {
        padding: 0.9rem;
        font-size: 0.9rem;
    }

    .mensagem-vazia {
        font-size: 0.9rem;
        padding: 0.8rem 1rem;
    }

    hr {
        margin: 1rem 0;
    }
}
</style>
