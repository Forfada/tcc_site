<?php 
	include("functions.php"); 
?>

<?php 
	if (isset($_GET['id'])) {
		try {
			$cli = find("clientes", $_GET['id']);
			delete($_GET['id']);
		} catch (Exception $e) {
			$_SESSION ['message'] = "Não foi possivel realizar a operação: " . $e->getMessage();
			$_SESSION['type'] = "danger";
		}
	}
	else {
		die("ERRO: ID não definido.");
	}
?>