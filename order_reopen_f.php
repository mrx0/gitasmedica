<?php 

//order_reopen_f.php
//разблокировать ордер

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			
			WriteToDB_ReopenOrder ($_SESSION['id'], $_POST['id']);

            //!!! @@@ Пересчет баланса
            include_once 'ffun.php';
            calculateBalance ($_POST['client_id']);

			echo '
				<div class="query_ok">
					<h3>Ордер разблокирован.</h3>
				</div>';	
		}

	}
	
?>