<?php 

//invoice_reopen_f.php
//разблокировать наряд

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			
			WriteToDB_ReopenInvoice ($_SESSION['id'], $_POST['id']);

            //!!! @@@ Пересчет долга
            include_once 'ffun.php';
            calculateDebt ($_POST['client_id']);

			echo '
				<div class="query_ok">
					<h3>Наряд разблокирован.</h3>
				</div>';	
		}

	}
	
?>