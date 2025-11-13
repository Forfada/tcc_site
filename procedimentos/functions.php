<?php

	include("../config.php");
	include(DBAPI);

	$proc = null;
	$procedimentos = null;

// Listagem de procedimentos
   function index() {
		global $procedimentos;
		if (!empty($_POST['proc'])) {
			$search = $_POST['proc'];
			$procedimentos = filter("procedimentos","p_nome like :search", ['search' => "%{$search}%"]);
		}
		else {
			$procedimentos = find_all ("procedimentos");
		}
	}

	//  Upload de imagem
    function upload ($pasta_destino, $arquivo_destino, $tipo_arquivo, $nome_temp, $tamanho_arquivo) {
        try {
            $nomearquivo = basename($arquivo_destino);
            $uploadOk = 1;
            if(isset($_POST["submit"])) {
                $check = getimagesize($nome_temp);
                if($check !== false) {
                    $_SESSION['message'] = "File is an image - " . $check["mime"] . ".";
                    $_SESSION['type'] = "info";
                    $uploadOk = 1;
                } else {
                    $uploadOk = 0;
                    throw new Exception("O arquivo não é uma imagem!");
                }
            }

            if (file_exists($arquivo_destino)) {
                $uploadOk = 0;
                throw new Exception("Desculpe, mas o arquivo já existe!");
            }

            if ($tamanho_arquivo > 5000000) {
                $uploadOk = 0;
                throw new Exception("Desculpe, mas o arquivo é muito grande!");
            }

            if ($tipo_arquivo != "jpg" && $tipo_arquivo != "png" && $tipo_arquivo != "jpeg" && $tipo_arquivo != "gif") {
                $uploadOk = 0;
                throw new Exception("Desculpe, mas só são permitidos arquivos de imagem JPG, PNG, JPEG E GIF!");
            }

            if ($uploadOk == 0) {
                throw new Exception("Desculpe, mas o arquivo não pode ser enviado!");
            } else {
                if (move_uploaded_file($_FILES["p_foto"] ["tmp_name"], $arquivo_destino)) {
                    $_SESSION['message'] = "O arquivo " . htmlspecialchars($nomearquivo) . " foi armazenado.";
                    $_SESSION["type"] = "success";
                } else {
                    throw new Exception("Desculpe, mas o arquivo não pode ser enviado!");
                }
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Aconteceu algum erro: " . $e->getMessage();
            $_SESSION["type"] = "danger";
        }
    }

    //  Visualização de um procedimento
	function view($id = null) {
    global $proc;
    	$proc = find("procedimentos", $id);
}

    //  Cadastro de procedimentos
	function add() {

		if (!empty($_POST['proc'])) {
			try{
				$proc = $_POST['proc'];
				
				if (!empty($_FILES["p_foto"]["name"])){
					//upload de foto
					$pasta_detino = "imagens/";
					$arquivo_destino = $pasta_detino . basename($_FILES["p_foto"]["name"]);
					$nomearquivo = basename($_FILES["p_foto"]["name"]);
					$resolucao_arquivo = getimagesize($_FILES["p_foto"]["tmp_name"]);
					$tamanho_arquivo = $_FILES["p_foto"]["size"];
					$nome_temp = $_FILES["p_foto"]["tmp_name"];
					$tipo_arquivo = strtolower(pathinfo($arquivo_destino,PATHINFO_EXTENSION));
					//gravar img
					upload($pasta_detino, $arquivo_destino, $tipo_arquivo, $nome_temp, $$tamanho_arquivo);
					$proc['p_foto'] = $nomearquivo;
				}
				else {
					$proc["p_foto"] = "noimg.jpg";
				}
				
				save('procedimentos', $proc);
				return header('location: index.php');
			} catch (Exception $e) {
				$_SESSION['message'] = "Aconteceu um erro: " . $e->getMessage();
				$_SESSION['type'] = "danger";
			}
		}
	}

    //Atualizacao/Edicao de procedimento
	function edit() {
		try {

			if (isset($_GET['id'])) {
 
				$id = $_GET['id'];
				
				if (isset($_POST['proc'])) {
					$proc = $_POST["proc"];
			
					if(!empty($_FILES['p_foto'] ['name'])) {
                        $pasta_destino = "imagens/";
                        $arquivo_destino = $pasta_destino . basename($_FILES["p_foto"]["name"]);
                        $nomearquivo = basename($_FILES["p_foto"]["name"]);
                        $resolucao_arquivo = getimagesize($_FILES["p_foto"]["tmp_name"]);
                        $tamanho_arquivo = $_FILES["p_foto"] ["size"];
                        $nome_temp = $_FILES["p_foto"] ["tmp_name"];
                        $tipo_arquivo = strtolower(pathinfo($arquivo_destino, PATHINFO_EXTENSION));

                        upload($pasta_destino, $arquivo_destino, $tipo_arquivo, $nome_temp, $tamanho_arquivo);

                        $proc['p_foto'] = $nomearquivo;
                    }
 
					update("procedimentos", $id, $proc);
                    header("Location: index.php");
				} else {
					global $proc;
					$proc = find("procedimentos", $id);
				} 
			} else {
				header('Location: index.php');
			}
		} catch (Exception $e) {
			$_SESSION['message'] = "Aconteceu um erro: " . $e->getMessage();
			$_SESSION['type'] = "danger";
		}
	}

    // Exclusão de um procedimento
	function delete($id = null) {
		global $procedimentos;
		$procedimentos = remove("procedimentos", $id);

		header("location: index.php");
	}
?>
