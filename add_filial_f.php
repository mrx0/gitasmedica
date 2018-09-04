<?php

//add_filial_f.php
//Функция для добавления филиала

	//var_dump ($_POST);
	if ($_POST){
		if (($_POST['name'] == '') || ($_POST['address'] == '') || ($_POST['contacts'] == '')){
			echo 'Что-то не заполнено. Давайте еще разок =)<br><br>
			<a href="add_worker.php" class="b">Добавить</a>
			<a href="index.php" class="b">На главную</a>';
		}else{
			include_once 'DBWork.php';
			
			WriteFilialToDB_Edit ($_POST['session_id'], $_POST['name'], $_POST['address'], $_POST['contacts']);
		
			echo '
				Филиал добавлен в справочник.
				<br><br>
				<a href="add_filial.php" class="b">Добавить ещё</a>
				<a href="index.php" class="b">На главную</a>
				';
		}
	}
	
?>