<?php 
	include("functions.php"); 
    /*if (!isset($_SESSION)) session_start();
    if (isset($_SESSION['user'])) { // Verifica se tem um usuário logado
        if ($_SESSION['user'] != "mazi") {
            $_SESSION["message"] = "Você precisa ser administrador para acessar esse recurso!";
            $_SESSION['type'] = "danger";
            header("Location:" . BASEURL ."index.php");
        }
    } else {
        $_SESSION["message"] = "Você precisa estar logado e ser administrador para acessar esse recurso!";
        $_SESSION["type"] = "danger";
        header("Location:" . BASEURL . "index.php");
    }*/
	view($_GET['id']);
    include(HEADER_TEMPLATE);
    include(INIT);
?>  
<?php if (!empty($_SESSION['message'])) : ?>
    <div class="alert alert-<?php echo $_SESSION['type']; ?> alert-dismissible" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php else : ?>
<?php 
	if (isset($_GET['id'])){
		try{
			$proc = find ("procedimentos", $_GET['id']);
			delete($_GET['id']);
			
			if ($proc['p_foto'] !== "noimg.jpg") {
				unlink("imagens/" . $proc['p_foto']);
			}
		} catch (Exception $e){
			$_SESSION['message'] = "Não foi possível realizar a operação: " . $e->getMessage();
			$_SESSION['type'] = "danger";
		}
	} 	
?>
 <?php endif; ?>