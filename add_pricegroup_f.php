<?php 

// !!!!!! Доделать !!! 
//Функция для добавления новой группы/подгруппы

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['groupname'] == ''){
				echo '
					<div class="query_neok">
						Что-то не заполнено.<br><br>
					</div>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';

				//$name = trim($_POST['groupname']);
				
				$name = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['groupname']))));
				
				//Проверяем есть ли такая группа
				$rezult = SelDataFromDB('spr_storagegroup', $name, 'name');
				//var_dump($rezult);
				
				if ($rezult == 0){
					$GroupName = WriteToDB_EditPriceGroup ($name, $_POST['group'], $_SESSION['id']);
					echo '
						<div class="query_ok">
							Группа/подгруппа добавлена в базу.<br><br>
						</div>';
				}else{
					echo '
						<div class="query_neok">
							Такая группа/подгруппа уже есть.<br><br>
						</div>';
				}
			}
		}
	}
?>