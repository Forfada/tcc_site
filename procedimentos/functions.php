<?php

	include("../config.php");
	include(DBAPI);

	$pro = null;
	$procedimento = null;

    function index() {
		global $pro;
		$pro = find_all("procedimentos");
	}
?>