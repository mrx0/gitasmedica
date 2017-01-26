<?php 

//insure_reopen_f.php
//

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			
			WriteToDB_ReopenInsure ($_SESSION['id'], $_POST['id']);

			echo '
				<div class="query_ok">
					<h3>Карточка разблокирована.</h3>
				</div>';	
		}

	}
	
?>