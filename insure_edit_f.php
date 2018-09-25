<?php 

//insure_edit_f.php
//Функция для редактирования страховой

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
				
				WriteInsureToDB_Update ($_SESSION['id'], $_POST['id'], $_POST['name'], $_POST['contract'], $_POST['contract2'], $_POST['contacts']);
				
				echo '
					<div class="query_ok">
						<h3>Отредактировано</h3>
						<a href="insure.php?id='.$_POST['id'].'" class="b">Вернуться в карточку</a>
					</div>';	
			}
		}
	}
	
?>