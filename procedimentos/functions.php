<?php

	include("../config.php");
	include(DBAPI);

	$proc = null;
	$procedimentos = null;

	 // Conexão com o banco usando PDO
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=bancolu;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->query("SELECT * FROM procedimentos ORDER BY id_p ASC");
        $procedimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao conectar ao banco: " . $e->getMessage();
        $procedimentos = [];
    }

    function index() {
		global $proc;
		$proc = find_all("procedimentos");
	}
?>