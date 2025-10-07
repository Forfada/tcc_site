<?php
include '../config.php';
include DBAPI;
include (HEADER_TEMPLATE);
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$agendamentos = [];

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
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    close_database($db);
} catch (PDOException $e) {
    $_SESSION['message'] = "Erro ao carregar histórico: " . $e->getMessage();
    $_SESSION['type'] = "danger";
}
?>


<main class="container-historico">
    <h1 class="titulo-historico">🕓 Histórico de Agendamentos</h1>

    <?php if (!empty($agendamentos)): ?>
        <div class="cards-container">
            <?php foreach ($agendamentos as $ag): ?>
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
        <p class="mensagem-vazia">Nenhum agendamento encontrado 😢</p>
    <?php endif; ?>
</main>

<?php include(FOOTER_TEMPLATE); ?>

<style>
/* ====== Estilo Histórico (Lunaris) ====== */
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
    margin-bottom: 3rem;
    text-align: center;
}

.cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    width: 100%;
    max-width: 1000px;
}

.card-agendamento {
    background: #fff;
    border-radius: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card-agendamento:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: var(--cor2);
    color: #fff;
    padding: 1rem;
    text-align: center;
}

.card-header h2 {
    font-size: 1.4rem;
    margin: 0;
}

.card-body {
    padding: 1.2rem 1.5rem;
    color: var(--cor1);
    font-family: 'Open Sans', sans-serif;
}

.card-body p {
    margin: 0.4rem 0;
    font-size: 1rem;
}

.mensagem-vazia {
    background: rgba(255, 255, 255, 0.2);
    padding: 1.5rem 2rem;
    border-radius: 1rem;
    color: var(--cor2);
    font-size: 1.2rem;
    text-align: center;
}
</style>
