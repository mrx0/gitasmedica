<?php

//pricelistgroup.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';

			$rezult = SelDataFromDB('spr_storagegroup', $_GET['id'], 'id');
			//var_dump($rezult);
			
			$price = 0;
			
			if ($rezult != 0){
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
			
				$arr = array();
				$rez = array();
				$price = '';
					
				
				echo '
					<div id="status">
						<header>
							<h2>Карточка группы/подгруппы';
							
				/*if (($items['edit'] == 1) || $god_mode){
					echo '
								<a href="pricelistgroup_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
				}*/
				
				if (($items['edit'] == 1) || $god_mode){
					if ($rezult[0]['status'] != 9){
						echo '
									<a href="pricelistgroup_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
					}
					if (($rezult[0]['status'] == 9) && ($items['close'] == 1)){
						echo '
							<a href="#" onclick="Ajax_reopen_pricelistgroup('.$_SESSION['id'].', '.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
					}
				}
				if (($items['close'] == 1) || $god_mode){
					if ($rezult[0]['status'] != 9){
						echo '
									<a href="pricelistgroup_del.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
					}
				}
				
				echo '
							</h2>';
							
				if ($rezult[0]['status'] == 9){
					echo '<i style="color:red;">Позиция удалена (заблокирована).</i><br>';												
				}
				
				echo '
						</header>
						<a href="pricelist.php" class="b">В прайс</a><br>';
						
				echo '
						<div id="data">';

				echo '
							<div class="cellsBlock2">
								<div class="cellLeft">Название</div>
								<div class="cellRight">'.$rezult[0]['name'].'</div>
							</div>';
							
				echo '
							<div class="cellsBlock2">
								<span style="font-size:80%;">';
									
				if (($rezult[0]['create_time'] != 0) || ($rezult[0]['create_person'] != 0)){
					echo '
										Добавлен: '.date('d.m.y H:i', $rezult[0]['create_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $rezult[0]['create_person'], 'user', true).'<br>';
				}else{
					echo 'Добавлен: не указано<br>';
				}
				if (($rezult[0]['last_edit_time'] != 0) || ($rezult[0]['last_edit_person'] != 0)){
					echo '
										Последний раз редактировался: '.date('d.m.y H:i', $rezult[0]['last_edit_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $rezult[0]['last_edit_person'], 'user', true).'';
				}
				echo '
								</span>
							</div>';
					
						
				echo '
						</div>
					</div>';
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>