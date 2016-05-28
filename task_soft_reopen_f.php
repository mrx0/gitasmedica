<?php 
	//var_dump ($_POST);
	if ($_POST){
		if ($_POST['task_id'] == ''){
			echo 'Что-то не заполнено. Давайте еще разок =)<br /><br />
				<a href="task_soft.php?id='.$_POST['task_id'].'" class="b">Вернуться в заявку</a>';
		}else{
			include_once 'DBWork.php';
			//include_once 'functions.php';
			if ($_POST['ended'] != 0){
				WriteToJournal_Update_ReOpen ($_POST['task_id'],$_POST['worker'], 'journal_soft');
			}
			echo '
				<h1>Задача снова в работе.</h1>
				<a href="task_soft.php?id='.$_POST['task_id'].'" class="b">Вернуться в заявку</a>
			';
		}
	}
	
?>