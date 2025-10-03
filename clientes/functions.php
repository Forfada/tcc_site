<?php

    include("../config.php");
	include(DBAPI);

	$cli = null;
	$clientes = null;

    // Listagem de procedimentos
    function index() {
            global $clientes;
            if (!empty($_POST['cli'])) {
                $clientes = filter("clientes","cli_nome like '%" . $_POST['cli'] . "%';");
            }
            else {
                $procedimentos = find_all ("clientes");
            }
        }

?>