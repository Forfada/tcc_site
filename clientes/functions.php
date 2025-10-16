<?php

    include("../config.php");
	include(DBAPI);

	$cli = null;
	$clientes = null;

    // Listagem de clientes
    function index() {
            global $clientes;
            if (!empty($_POST['cli'])) {
                $clientes = filter("clientes","cli_nome like '%" . $_POST['cli'] . "%';");
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

?>