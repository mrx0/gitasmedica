<?php 

//pricelistgroup_del_f.php
//Функция для Удаление(блокирование) 

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		include_once 'functions.php';
		if ($_POST){
			if (isset($_POST['deleteallin'])){
				if ($_POST['deleteallin'] == 1) $deleteallin = 1; else $deleteallin = 0;
			}else{
				$deleteallin = 0;
			}
				DeleteTree($_POST['id'], '', 'clear', 0, TRUE, 0, FALSE, $deleteallin);

			echo '
				<div class="query_ok" style="padding-bottom: 10px;">
					<h3>Группа удалена (заблокирована).</h3>';
			//if ($DeleteAll){
			//	echo 'Всё содержимое откреплено и удалено';
			//}
			echo '
				</div>';	
		}

	}
	
?>