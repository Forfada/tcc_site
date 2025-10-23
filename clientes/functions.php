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
            
            echo "Dados a serem inseridos:<br>";
            var_dump($an);

            $db = open_database();
            try {
                $sql = "INSERT INTO anamnese (
                    id_cli,
                    an_fumante,
                    an_queloide,
                    an_gravidez,
                    an_depressao,
                    an_hiv,
                    an_herpes,
                    an_cancer,
                    an_hepatite,
                    an_cardiopata,
                    an_anemia,
                    an_hipertensao,
                    an_diabetes,
                    an_pele,
                    an_alergia,
                    an_medic,
                    an_acne,
                    an_outro,
                    an_data,
                    an_glaucoma
                ) VALUES (
                    :id_cli,
                    :fumante,
                    :queloide,
                    :gravidez,
                    :depressao,
                    :hiv,
                    :herpes,
                    :cancer,
                    :hepatite,
                    :cardiopata,
                    :anemia,
                    :hipertensao,
                    :diabetes,
                    :pele,
                    :alergia,
                    :medic,
                    :acne,
                    :outro,
                    :data,
                    'não'
                )";

                $stmt = $db->prepare($sql);
                
                // Bind todos os valores com os nomes corretos das colunas
                $stmt->bindValue(':id_cli', intval($an['id_cli']), PDO::PARAM_INT);
                $stmt->bindValue(':fumante', $an['an_fumante']);
                $stmt->bindValue(':queloide', $an['an_queloide']);
                $stmt->bindValue(':gravidez', $an['an_gravidez']);
                $stmt->bindValue(':depressao', $an['an_depressao']);
                $stmt->bindValue(':hiv', $an['an_hiv']);
                $stmt->bindValue(':herpes', $an['an_herpes']);
                $stmt->bindValue(':cancer', $an['an_cancer']);
                $stmt->bindValue(':hepatite', $an['an_hepatite']);
                $stmt->bindValue(':cardiopata', $an['an_cardiopata']);
                $stmt->bindValue(':anemia', $an['an_anemia']);
                $stmt->bindValue(':hipertensao', $an['an_hipertensao']);
                $stmt->bindValue(':diabetes', $an['an_diabetes']);
                $stmt->bindValue(':pele', $an['an_doenca_pele']);
                $stmt->bindValue(':alergia', $an['an_alergia']);
                $stmt->bindValue(':medic', $an['an_medicacao_continua']);
                $stmt->bindValue(':acne', $an['an_medicacao_acne']);
                $stmt->bindValue(':outro', $an['an_outro_problema']);
                $stmt->bindValue(':data', $an['an_data'] . ':00');

                // Mostra a query preparada
                echo "SQL a ser executado:<br>";
                $stmt->debugDumpParams();
                
                $result = $stmt->execute();
                if (!$result) {
                    echo "Erro SQL:<br>";
                    print_r($stmt->errorInfo());
                    return false;
                }
                close_database($db);
                return true;
            } catch (PDOException $e) {
                close_database($db);
                $_SESSION['message'] = "Erro ao inserir anamnese: " . $e->getMessage();
                $_SESSION['type'] = 'danger';
                return false;
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Aconteceu um erro: " . $e->getMessage();
            $_SESSION['type'] = "danger";
            return false;
        }
    }
    return false;
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
	header('Location: view.php');
}

?>