<?php 

//task_soft_edit_f.php
//Функция для редактирования задачи Soft

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['description'] == ''){
				echo 'Не заполнено описание. Давайте еще разок =)<br /><br />
				<a href="task_soft_edit.php?id='.$_POST['id'].'" class="b">Вернуться</a>';
			}else{
				include_once 'DBWork.php';
				WriteToJournal_Update ($_POST['id'], '', $_POST['description'], $_SESSION['id'], '', 'journal_soft');
			
				echo '
					Заявка отредактирована.
					<br /><br />
					<a href="task_soft.php?id='.$_POST['id'].'" class="b">Вернуться в заявку</a>
					';
			}
		}

	}
	
?>