<?php 

//cert_reopen_f.php
//

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			
			WriteToDB_ReopenCert ($_SESSION['id'], $_POST['id']);

			echo '
				<div class="query_ok">
					<h3>Сертификат разблокирован.</h3>
				</div>';	
		}

	}
	
?>