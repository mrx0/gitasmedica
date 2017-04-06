<?php

//header.php
//Заголовок страниц сайта
//

	session_start();
	
	$enter_ok = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		$enter_ok = FALSE;
	}else{
		if (($_SESSION['permissions'] != 4) || (($_SESSION['permissions'] == 4) && (isset($_SESSION['filial'])))){
			$enter_ok = TRUE;
		}else{
			$enter_ok = FALSE;
		}
	}

?>