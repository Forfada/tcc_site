<?php
    include '../config.php';
    include DBAPI;
    include INIT;
    include HEADER_TEMPLATE;
?>

<?php
    session_start();

    // Verifica login
    $logado = isset($_SESSION['id']);
    if (!$logado) {
        header("Location: login.php");
        exit();
    }

    $idUsuario = $_SESSION['id'];
    $message = '';
    $type = '';

    /* ============= CADASTRO DE AGENDAMENTO ============= */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = $_POST['a_dia'] ?? '';
        $hora = $_POST['a_hora'] ?? '';
        $id_p = $_POST['id_p'] ?? '';

        if ($data && $hora && $id_p) {
            try {
                $db = open_database();
                $sql = "INSERT INTO agendamento (a_dia, a_hora, id_u, id_p, created_at)
                        VALUES (:a_dia, :a_hora, :id_u, :id_p, NOW())";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':a_dia', $data);
                $stmt->bindParam(':a_hora', $hora);
                $stmt->bindParam(':id_u', $idUsuario);
                $stmt->bindParam(':id_p', $id_p);
                $stmt->execute();
                close_database($db);

                $message = "Agendamento realizado com sucesso!";
                $type = "success";
            } catch (PDOException $e) {
                $message = "Erro ao agendar: " . $e->getMessage();
                $type = "danger";
            }
        } else {
            $message = "Preencha todos os campos.";
            $type = "warning";
        }
    }

    /* ============= BUSCA DE PROCEDIMENTOS ============= */
    $procedimentos = [];
    try {
        $db = open_database();
        $stmt = $db->query("SELECT id, p_nome FROM procedimentos ORDER BY p_nome ASC");
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
        $stmt->bindParam(':id_u', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        close_database($db);
    } catch (PDOException $e) {
        $message = "Erro ao carregar agendamentos: " . $e->getMessage();
        $type = "danger";
    }
?>


<section class="container-agendamento" id="agendamento">
    <div class="tabela-section">
        <div class="titulo-area">
            <h2>Agendamentos</h2>
        </div>

        <!-- Mensagem de feedback -->
        <?php if ($message): ?>
            <p class="alert alert-<?= $type ?>"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <!-- Formulário de Novo Agendamento -->
        <form method="POST" class="form-agendamento">
            <div class="form-group">
                <label for="id_p">Procedimento:</label>
                <select name="id_p" id="id_p" required>
                    <option value="">Selecione um procedimento</option>
                    <?php foreach ($procedimentos as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['p_nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="a_dia">Data:</label>
                <input type="date" name="a_dia" id="a_dia" required>
            </div>

            <div class="form-group">
                <label for="a_hora">Hora:</label>
                <input type="time" name="a_hora" id="a_hora" required>
            </div>

            <button type="submit" class="btn-agendar">Agendar</button>
        </form>

        <!-- Tabela de Agendamentos -->
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="sem-registro">Você ainda não possui agendamentos.</p>
        <?php endif; ?>
    </div>
</section>

<?php include(FOOTER_TEMPLATE); ?>

<style>
/* ====== Estilo Lunaris ====== */
.container-agendamento {
    padding: 60px 5%;
    background-color: var(--cor3);
    min-height: 90vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.tabela-section {
    width: 100%;
    max-width: 1000px;
    background-color: #fff;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
}

.titulo-area h2 {
    color: var(--cor1);
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    margin-bottom: 25px;
}

/* Mensagem de feedback */
.alert {
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}
.alert-success { background-color: #d4edda; color: #155724; }
.alert-danger { background-color: #f8d7da; color: #721c24; }
.alert-warning { background-color: #fff3cd; color: #856404; }

/* Formulário */
.form-agendamento {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    color: var(--cor1);
    margin-bottom: 6px;
}

.form-group input,
.form-group select {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    outline: none;
    transition: 0.3s;
}

.form-group input:focus,
.form-group select:focus {
    border-color: var(--cor1);
}

.btn-agendar {
    background-color: var(--cor1);
    color: #fff;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: 0.3s;
    grid-column: span 3;
}

.btn-agendar:hover {
    background-color: var(--cor2);
}

.tabela-wrapper {
    overflow-x: auto;
}

.tabela-lunaris {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
}

.tabela-lunaris th, .tabela-lunaris td {
    padding: 12px 10px;
    border-bottom: 1px solid #ddd;
}

.tabela-lunaris th {
    background-color: var(--cor3);
    color: var(--cor1);
    font-weight: 600;
}

.tabela-lunaris tr:hover {
    background-color: #f6f6f6;
}

.sem-registro {
    text-align: center;
    color: #777;
    font-style: italic;
    margin-top: 20px;
}

/* Responsivo */
@media (max-width: 768px) {
    .form-agendamento {
        grid-template-columns: 1fr;
    }

    .btn-agendar {
        grid-column: span 1;
        width: 100%;
    }
}
</style>
