<?php

//header.php
//Заголовок страниц сайта
	
	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		$enter_ok = FALSE;
	}else{
		$enter_ok = TRUE;
	}

?>