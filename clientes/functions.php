<?php

    include("../config.php");
	include(DBAPI);

	$cli = null;
	$clientes = null;

    // Listagem de clientes
    function index() {
            global $clientes;
            if (!empty($_POST['cli'])) {
                $clientes = filter("clientes","cli_cpf like '%" . $_POST['cli'] . "%';");
            }
            else {
                $clientes = find_all ("clientes");
            }
        }

    //  Visualização de um cliente
	function view($id = null) {
    global $cli;
    	$cli = find("clientes", $id);
}

    //  Cadastro de clientes
    function add() {
		if (!empty($_POST['cli'])) {
			try{
				$cli = $_POST['cli'];
				
				save('clientes', $cli);
				return header('location: index.php');
			} catch (Exception $e) {
				$_SESSION['message'] = "Aconteceu um erro: " . $e->getMessage();
				$_SESSION['type'] = "danger";
			}
		}
	}

     //Atualizacao/Edicao de clientes
	function edit() {
		try {
			if (isset($_GET['id'])) {
 
				$id = $_GET['id'];
				
				if (isset($_POST['cli'])) {
					$cli = $_POST["cli"];
 
					update("clientes", $id, $cli);
                    header("Location: index.php");
				} else {
					global $cli;
					$cli = find("clientes", $id);
				} 
			} else {
				header('Location: index.php');
			}
		} catch (Exception $e) {
			$_SESSION['message'] = "Aconteceu um erro: " . $e->getMessage();
			$_SESSION['type'] = "danger";
		}
	}

    // Exclusão de um cliente
	function delete($id = null) {
		global $clientes;
		$clientes = remove("clientes", $id);

		header("location: index.php");
	}

// =====================
// Funções CRUD - Anamnese
// =====================

// Busca a anamnese do cliente (retorna a primeira encontrada ou null)
function index_an($id_cli = null) {
	global $anamnese;
	$anamnese = [];
	if ($id_cli) {
		$rows = filter("anamnese", "id_cli = " . intval($id_cli) . ";");
		if ($rows && count($rows) > 0) {
			// retorna todas as anamneses associadas ao cliente
			$anamnese = $rows;
		}
	}
	return $anamnese;
}

function add_an() {
    if (empty($_POST['anamnese'])) return false;

    $an = $_POST['anamnese'];
    if (empty($an['id_cli'])) {
        $_SESSION['message'] = "ID do cliente ausente.";
        $_SESSION['type'] = "danger";
        return false;
    }

    // Ajusta formato da data
    if (!empty($an['an_data'])) $an['an_data'] .= ':00';

    // Usa função genérica save() do sistema
    save('anamnese', $an);

    return true;
}

// Atualiza uma anamnese (pega id por GET 'anid' e dados em $_POST['anamnese'])
function edit_an() {
    try {
        if (!isset($_GET['anid'])) {
            return false;
        }

        $id = intval($_GET['anid']);
        
        if (isset($_POST['anamnese'])) {
            return update('anamnese', $id, $_POST['anamnese']);
        } else {
            global $anamnese;
            $anamnese = find('anamnese', $id);
            return !empty($anamnese);
        }
    } catch (Exception $e) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['message'] = "Erro: " . $e->getMessage();
        $_SESSION['type'] = "danger";
        return false;
	}
}

// Remove uma anamnese (por id) e redireciona para index de clientes
function anamnese_delete($id = null) {
	if ($id) {
		remove('anamnese', $id);
	}
	header('Location: view.php');
}
?>