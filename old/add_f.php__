<?php 

//add_f.php
//Функция для добавления задачи IT в журнал
	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['description'] == ''){
				echo 'Не заполнено описание. Давайте еще разок =)<br /><br />
				<a href="add_task.php" class="b">Добавить</a>
				<a href="it.php" class="b">В журнал</a>';
			}else{
			
				WriteToDB_Edit ($_POST['filial'], $_POST['description'], time(), $_POST['author'], time(), 'vav', 0, 0, $_POST['priority'], 0);
			
				echo '
					Заявка добавлена в журнал.
					<br /><br />
					<a href="add_task.php" class="b">Добавить ещё</a>
					<a href="it.php" class="b">В журнал</a>
					';
			}
		}
	}
	
?>