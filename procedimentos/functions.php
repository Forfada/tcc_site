<?php

	include("../config.php");
	include(DBAPI);

	$proc = null;
	$procedimentos = null;

// Listagem de procedimentos
   function index() {
		global $procedimentos;
		if (!empty($_POST['proc'])) {
			$procedimentos = filter("procedimentos","p_nome like '%" . $_POST['proc'] . "%';");
		}
		else {
			$procedimentos = find_all ("procedimentos");
		}
	}

	/* mostrar todos os procedimentos por id
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=bancolu;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->query("SELECT * FROM procedimentos ORDER BY id_p ASC");
        $procedimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao conectar ao banco: " . $e->getMessage();
        $procedimentos = [];
    }*/

    

    //  Visualização de um procedimento
	function view($id_p = null) {
		global $proc;
		$proc = find("procedimentos", $id_p);
	}

    //  Cadastro de procedimentos
	function add() {

		if (!empty($_POST["proc"])) {

			$today = 
			new DateTime("now", new DateTimeZone("America/Sao_Paulo"));

			$proc = $_POST["proc"];
			$proc["modified"] = $proc["created"] = $today->format("Y-m-d H:i:s");

			save("procedimentos", $proc);
			header("location: index.php");
		}
	}

    //Atualizacao/Edicao de procedimento
	function edit() {

		$now = new DateTime("now", new DateTimeZone("America/Sao_Paulo"));

		if (isset($_GET["id_p"])) {

			$id_p = $_GET["id_p"];

			if (isset($_POST["proc"])) {

				$proc = $_POST["proc"];
				$proc["modified"] = $now->format("Y-m-d H:i:s");

				update("procedimentos", $id_p, $proc);
				header("location: index.php");
			} else {
				global $proc;
				$proc = find("procedimentos", $id_p);
			} 
		} else {
			header("location: index.php");
		}
	}

    // Exclusão de um procedimento
	function delete($id_p = null) {

		global $proc;
		$proc = remove("procedimentos", $id_p);

		header("location: index.php");
	}
?>