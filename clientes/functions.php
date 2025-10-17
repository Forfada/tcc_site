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

// Adiciona uma nova anamnese (espera dados em $_POST['anamnese'])
function add_an() {
	if (!empty($_POST['anamnese'])) {
		try {
			$an = $_POST['anamnese'];
			// garante que exista id_cli
			if (empty($an['id_cli'])) {
				throw new Exception('ID do cliente ausente.');
			}

			// Normaliza a data enviada por datetime-local (ex: 2025-10-17T14:30)
			if (!empty($an['an_data'])) {
				$an['an_data'] = str_replace('T', ' ', $an['an_data']);
				// se não contiver segundos, adiciona :00
				if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $an['an_data'])) {
					$an['an_data'] .= ':00';
				}
			}

			// Não definimos o campo 'id' aqui: deixamos o banco gerar o id (AUTO_INCREMENT)
			// Assumimos que a tabela `anamnese` terá `id` AUTO_INCREMENT e `id_cli` será FK para clientes.id
			// Inserção com prepared statement para maior controle de erros
			$db = open_database();
			try {
				$sql = "INSERT INTO anamnese (an_hipertensao, an_diabetes, an_medic, an_data, id_cli) VALUES (:hip, :dia, :med, :dat, :idcli)";
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':hip', isset($an['an_hipertensao']) ? $an['an_hipertensao'] : null);
				$stmt->bindValue(':dia', isset($an['an_diabetes']) ? $an['an_diabetes'] : null);
				$stmt->bindValue(':med', isset($an['an_medic']) ? $an['an_medic'] : null);
				$stmt->bindValue(':dat', isset($an['an_data']) ? $an['an_data'] : null);
				$stmt->bindValue(':idcli', intval($an['id_cli']), PDO::PARAM_INT);
				$stmt->execute();
				close_database($db);
				header('Location: view.php?id=' . intval($an['id_cli']));
				return;
			} catch (PDOException $e) {
				close_database($db);
				$_SESSION['message'] = "Erro ao inserir anamnese: " . $e->getMessage();
				$_SESSION['type'] = 'danger';
				// Não redireciona para permitir exibição da mensagem na mesma página
				return;
			}
		} catch (Exception $e) {
			$_SESSION['message'] = "Aconteceu um erro: " . $e->getMessage();
			$_SESSION['type'] = "danger";
		}
	}
}

// Atualiza uma anamnese (pega id por GET 'anid' e dados em $_POST['anamnese'])
function edit_an() {
	try {
		if (isset($_GET['anid'])) {
			$id = intval($_GET['anid']);
			if (isset($_POST['anamnese'])) {
				$an = $_POST['anamnese'];
				update('anamnese', $id, $an);
				// tenta redirecionar para a view do cliente se id_cli foi enviado
				$cliId = isset($an['id_cli']) ? intval($an['id_cli']) : null;
				if ($cliId) header('Location: view.php?id=' . $cliId);
			} else {
				global $anamnese;
				$anamnese = find('anamnese', $id);
			}
		}
	} catch (Exception $e) {
		$_SESSION['message'] = "Aconteceu um erro: " . $e->getMessage();
		$_SESSION['type'] = "danger";
	}
}

// Remove uma anamnese (por id) e redireciona para index de clientes
function anamnese_delete($id = null) {
	if ($id) {
		remove('anamnese', $id);
	}
	header('Location: index.php');
}

?>