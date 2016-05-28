<?php 

//add_task_soft_f.php
//Функция для добавления задачи ПО в журнал
	
	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (($_POST['description'] == '') || ($_POST['full_description'] == '')){
				echo 'Не заполнено описание.<br /><br />
				<a href="add_task_soft.php" class="b">Попробовать еще</a>';
			}else{
				include_once 'DBWork.php';
				
				WriteToDB_EditSoft ($_POST['description'], $_POST['full_description'], time(), $_SESSION['id'], time(), $_SESSION['id'], $_SESSION['id'], 0, $_POST['priority']);
			
				echo '
					Задача добавлена в журнал.
					<br /><br />
					<a href="add_task_soft.php" class="b">Добавить ещё</a>';
			}
		}
	}
	
?>