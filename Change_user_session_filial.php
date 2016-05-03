<?php

//Change_user_session_filial.php
//

	//Запускаем сессию для работы с куками
	session_start();
	
	if ($_POST){
		if (isset($_POST['data'])){
			if ($_POST['data'] > 0){
				$_SESSION['filial'] = $_POST['data'];
			}else{
				unset($_SESSION['filial']);
			}
		}else{
			unset($_SESSION['filial']);
		}
	}

?>