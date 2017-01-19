<?php 

//pricelistgroup_edit_f.php
//Изменение

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['pricelistgroupname'] == ''){
				echo '
					<div class="query_neok">
						Что-то не заполнено.<br><br>
					</div>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				//$name = trim($_POST['pricelistgroupname']);
				
				$name = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['pricelistgroupname']))));
				
				//Проверяем есть ли такая услуга
				$rezult = SelDataFromDB('spr_storagegroup', $name, 'name');
				//var_dump($rezult);
				
				if (($rezult != 0) && ($rezult[0]['id'] != $_POST['id'])){
					echo '
						<div class="query_neok">
							Такая позиция уже есть.<br><br>
						</div>';
				}else{
					if (!isset($_POST['group'])) $_POST['group'] = 0;
					
					WriteToDB_UpdatePriceGroup ($name, $_POST['id'], $_POST['group'], $_SESSION['id']);

					echo '
						<div class="query_ok">
							Изменено.<br><br>
						</div>';
				}
			}
		}
	}
?>