<?php 

//labor_edit_f.php
//Функция для редактирования лаборатории

	session_start();
	
	$god_mode = FALSE;
	//var_dump ($_POST);
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			if (($_POST['name'] == '')||($_POST['contract'] == '')){
				echo '
					<div class="query_neok">
						Что-то не заполнено
					</div>';
			}else{
				
				WriteLaborToDB_Update ($_SESSION['id'], $_POST['id'], $_POST['name'], $_POST['contract'], $_POST['contacts']);
				
				echo '
					<div class="query_ok">
						<h3>Отредактировано</h3>
						<a href="labor.php?id='.$_POST['id'].'" class="b">Вернуться в карточку</a>
					</div>';	
			}
		}
	}
	
?>