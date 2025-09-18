<?php

	function open_database() {
		try {
			$conn = new PDO("mysql:host=". DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		} catch (Exception $e) {
			throw $e;
			return null;
		}
	}


	function close_database($conn) {
        try {
            $conn = null;
        } catch (PDOException $e) {
            throw $e;
        }
    }
	
	//  Pesquisa um Registro pelo ID em uma Tabela
    function find($table = null, $id = null) {
        $database = open_database();
        $found = null;

        try {
            if ($id) {
                $pk = 'id';
                if ($table == 'procedimentos') $pk = 'id_p';
                if ($table == 'clientes') $pk = 'id_cli';
                if ($table == 'usuarios') $pk = 'id_u';
                if ($table == 'agendamento') $pk = 'id_ag';
                if ($table == 'anamnese') $pk = 'id_an';

                $sql = "SELECT * FROM " . $table . " WHERE $pk = :id";
                $stmt = $database->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $found = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $sql = "SELECT * FROM " . $table;
                $stmt = $database->query($sql);
                $found = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['type'] = 'danger';
        }

        close_database($database);
        return $found;
    }
	
	// criptografia
	function cri($senha) {
        $custo = "08";
        $salt = "Cf1f11ePArKlBJomM0F6aJ";
        
        // Gera um hash baseado em bcrypt
        $hash = crypt($senha, "$2a$" . $custo . "$" . $salt . "$");

        return $hash;
    }
	
	//  Pesquisa Todos os Registros de uma Tabela
	function find_all( $table ) {
		return find($table);
	}

	
	// Insere um registro no BD
	
	function save($table = null, $data = null) {
        $database = open_database();

        $columns = null;
        $values = null;

        foreach ($data as $key => $value) {
            $columns .= trim($key, "'") . ",";
            $values .= "'$value',";
        }

        $columns = rtrim($columns, ',');
        $values = rtrim($values, ',');

        $sql = "INSERT INTO " . $table . "($columns)" . "VALUES" . "($values)";
        $stmt = $database->prepare($sql);

        try {
            $stmt->execute();
            $_SESSION['message'] = 'Registro cadastrado com sucesso.';
            $_SESSION['type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Não foi possível realizar o cadastro.';
            $_SESSION['type'] = 'danger';
        }

        close_database($database);
    }
	
	//  Atualiza um registro em uma tabela, por ID
	
	function update($table = null, $id = 0, $data = null) {
		$database = open_database();

		$items = null;
	
        foreach ($data as $key => $value) {
            $items .= trim($key, "'") ."='$value',";
        }

		// remove a ultima virgula
		$items = rtrim($items, ",");

		$sql = "UPDATE " . $table . " SET $items WHERE $pk=:id";
        $stmt = $database->prepare($sql);
		
        $stmt->bindParam(':id', $id);

		 try {
            $stmt->execute();
            $_SESSION['message'] = 'Registro atualizado com sucesso.';
            $_SESSION['type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Não foi possível realizar a atualização.';
            $_SESSION['type'] = 'danger';
        }
		close_database($database);
	}


	//Remove uma linha de uma tabela pelo ID do registro

	function remove( $table = null, $id = null ) {
		$database = open_database();
		
		try {
			if ($id) {
                $sql = "DELETE FROM " . $table . " WHERE $pk = :id";
                $stmt = $database->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                $_SESSION['message'] = "Registro removido com Sucesso.";
                $_SESSION['type'] = 'success';
            }
		}catch (PDOException $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['type'] = 'danger';
        }
		close_database($database);
	}
	
	// filtrar 
	function filter( $table = null, $p = null ) {
        $database = open_database();
        $found = null;
 
        try {
            if ($p) {
                $sql = "SELECT * FROM " . $table . " WHERE " . $p;
                $stmt = $database->query($sql);
                $found = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }else {
                throw new Exception("Não foram encontrados registros de dados!");
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Ocorreu um erro: " . $e->getMessage();
            $_SESSION['type'] = 'danger';
        }
 
        close_database($database);
        return $found;
    }
	
	
	//  Funções para formatar dados

	function telefone( $dado ) {
		$tel = "(" . substr($dado, 0, 2) . ") " . substr($dado, 2, 5) . "-" .  substr($dado, 7, 4);	
		return $tel; 
	}
	
	function formatadata($date, $formato) {
        $dt = new DateTime($date, new DateTimeZone("America/Sao_Paulo"));
        return $dt->format($formato);
    }
	
	function cep($cepdado) {
        $cp = substr($cepdado, 0, 5) . "-" . substr($cepdado, 5);
        return $cp;
    }
	
	 function cpf($cpf_cnpj) {
        $cpf_cnpj = substr($cpf_cnpj, 0, 3) . "." . substr($cpf_cnpj, 3, 3) . "." . substr($cpf_cnpj, 6, 3) . "-" . substr($cpf_cnpj, 9, 2);
        return $cpf_cnpj;
    }
	
	function clear_messages() {
        $_SESSION['message'] = null;
        $_SESSION['type'] = null;
    }
?>