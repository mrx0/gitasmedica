<?php 

//edit_task_cosmet_f.php
//Функция для редактирования посещения косметолога

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['description'] == ''){
				echo 'Не заполнено описание. Давайте еще разок =)<br /><br />
				<a href="task_edit.php?id='.$_POST['id'].'" class="b">Добавить</a>
				<a href="it.php" class="b">В журнал</a>';
			}else{
				include_once 'DBWork.php';
				WriteToJournal_Update ($_POST['id'], $_POST['filial'], $_POST['description'], $_SESSION['id'], $_POST['priority']);
			
				echo '
					Заявка отредактирована.
					<br /><br />
					<a href="cosmet.php" class="b">В журнал</a>
					';
			}
		}

	}
	
?>