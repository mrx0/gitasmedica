<?php

//header.php
//Заголовок страниц сайта
//
    //phpinfo();

	session_start();
	
	$enter_ok = FALSE;

//    ini_set('error_reporting', E_ALL);
//    ini_set('display_errors', 1);
//    ini_set('display_startup_errors', 1);

	//Время начала работы скрипта PHP
    $script_start = microtime(true);

    //текущая страница
    $_SESSION['current_page'] = $_SERVER['REQUEST_URI'];
    //var_dump($_SESSION);
	
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