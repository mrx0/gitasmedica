<?php

//task_reopen_f.php
//

	//var_dump ($_POST);
	if ($_POST){
		if ($_POST['task_id'] == ''){
			echo 'Что-то не заполнено. Попробуйте еще раз.<br /><br />
				<a href="task.php?id='.$_POST['task_id'].'" class="b">Вернуться в заявку</a>';
		}else{
			include_once 'DBWork.php';
			//include_once 'functions.php';
			if ($_POST['ended'] != 0){
				WriteToJournal_Update_ReOpen ($_POST['task_id'],$_POST['worker'], 'journal_it');
			}
			echo '
				<h1>Задача снова в работе.</h1>
				<a href="task.php?id='.$_POST['task_id'].'" class="b">Вернуться в заявку</a>
			';
		}
	}
	
?>