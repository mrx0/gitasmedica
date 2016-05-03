<?php 

//add_task_f.php
//Функция для добавления задачи в журнал
	
	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['description'] == ''){
				echo 'Не заполнено описание. Давайте еще разок =)<br /><br />
				<a href="add_task.php" class="b">Добавить</a>
				<a href="index.php" class="b">На главную</a>';
			}else{
				include_once 'DBWork.php';
				
				WriteToDB_Edit ($_POST['filial'], $_POST['description'], time(), $_SESSION['id'], time(), $_SESSION['id'], 0, 0, $_POST['priority']);
			
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