<?php
function open_database() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Erro ao conectar ao banco: " . $e->getMessage());
    }
}

function close_database(&$conn) {
    $conn = null;
}

// === Buscar registro(s) ===
function find($table = null, $id = null) {
    $db = open_database();
    $found = null;

    try {
        if ($id) {
            $stmt = $db->prepare("SELECT * FROM {$table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $found = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $db->query("SELECT * FROM {$table}");
            $found = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erro ao buscar dados: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }

    close_database($db);
    return $found;
}

function find_all($table) {
    return find($table);
}

// === Inserir registro ===
function save($table = null, $data = null) {
    $db = open_database();

    try {
        $fields = array_keys($data);
        $columns = implode(", ", $fields);
        $placeholders = ":" . implode(", :", $fields);

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        $stmt->execute();
        $_SESSION['message'] = "Registro cadastrado com sucesso.";
        $_SESSION['type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erro ao cadastrar: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }

    close_database($db);
}

// === Atualizar registro ===
function update($table = null, $id = 0, $data = null) {
    $db = open_database();

    try {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
        }
        $fields_str = implode(", ", $fields);

        $sql = "UPDATE {$table} SET {$fields_str} WHERE id = :id";
        $stmt = $db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();
        $_SESSION['message'] = "Registro atualizado com sucesso.";
        $_SESSION['type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erro ao atualizar: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }

    close_database($db);
}

// === Remover registro ===
function remove($table = null, $id = null) {
    $db = open_database();

    try {
        $stmt = $db->prepare("DELETE FROM {$table} WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['message'] = "Registro removido com sucesso.";
        $_SESSION['type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erro ao remover: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }

    close_database($db);
}

// === Filtro (com parâmetros seguros) ===
function filter($table = null, $where = null, $params = []) {
    $db = open_database();
    $found = null;

    try {
        if ($where) {
            $sql = "SELECT * FROM {$table} WHERE {$where}";
            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->execute();
            $found = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            throw new Exception("Condição inválida para filtro.");
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Erro: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }

    close_database($db);
    return $found;
}

// criptografia 
function cri($senha){ 
    $custo = "08"; $salt = "Cf1f11ePArKlBJomM0F6aJ"; $hash = crypt($senha, "$2a$" . $custo . "$" . $salt . "$"); return $hash; }

// === Funções auxiliares ===
function telefone($dado) {
    $dado = preg_replace('/\D/', '', $dado);
    return "(" . substr($dado, 0, 2) . ") " . substr($dado, 2, 5) . "-" . substr($dado, 7, 4);
}

function formatadata($date, $formato = 'd/m/Y') {
    if (!$date) return '';
    $dt = new DateTime($date, new DateTimeZone('America/Sao_Paulo'));
    return $dt->format($formato);
}

function cep($cepdado) {
    $cepdado = preg_replace('/\D/', '', $cepdado);
    return substr($cepdado, 0, 5) . "-" . substr($cepdado, 5);
}

function cpf($cpf) {
    $cpf = preg_replace('/\D/', '', $cpf);
    return substr($cpf, 0, 3) . "." . substr($cpf, 3, 3) . "." . substr($cpf, 6, 3) . "-" . substr($cpf, 9, 2);
}

// === NOVAS FUNÇÕES CORRIGIDAS ===
function valor($v) {
    // força ser float e formata com 2 casas decimais
    return "R$ " . number_format(floatval($v), 2, ',', '');
}

function duracao($t) {
    // assume formato hh:mm:ss e retorna hh:mm
    $p = explode(':', $t);
    if(count($p) >= 2){
        $hora = str_pad($p[0], 2, '0', STR_PAD_LEFT);
        $min = str_pad($p[1], 2, '0', STR_PAD_LEFT);
        return $hora . 'h' . $min . 'm';
    }
    return $t;
}

function clear_messages() {
    $_SESSION['message'] = null;
    $_SESSION['type'] = null;
}

?>
