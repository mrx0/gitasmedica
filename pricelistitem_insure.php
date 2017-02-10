<?php

//pricelistitem_insure.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			//var_dump($_GET);
			
			include_once 'DBWork.php';
			include_once 'functions.php';

			$insure_j = SelDataFromDB('spr_insure', $_GET['insure'], 'id');
			
			if ($insure_j != 0){
			
				$arr = array();
				$rez = array();
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				
				//$rezult = SelDataFromDB('spr_pricelist_template', $_GET['id'], 'id');
				//$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = (SELECT `item` FROM `spr_pricelists_insure` WHERE `item`='{$_GET['id']}') LIMIT 1";			
				$query = "SELECT * FROM `spr_pricelists_insure` WHERE `item`='{$_GET['id']}' LIMIT 1";			
				
				$res = mysql_query($query) or die(mysql_error().' -> '.$query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($rez, $arr);
					}
					$rezult = $rez;
				}else{
					$rezult = 0;
				}
				//var_dump($rezult);
				
				$price = 0;
				
				if ($rezult != 0){
					
					$arr = array();
					$rez = array();
					
					$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$rezult[0]['item']}'";			
					
					$res = mysql_query($query) or die(mysql_error().' -> '.$query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($rez, $arr);
						}
						$rezult2 = $rez;
					}else{
						$rezult2 = 0;
					}
					//var_dump($rezult2);
					
					if ($rezult2 != 0){
				
						$arr = array();
						$rez = array();
						$price = 0;
							
						//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `create_time` DESC LIMIT 1";
						$query = "SELECT `price` FROM `spr_priceprices_insure` WHERE `item`='".$_GET['id']."' ORDER BY `date_from` DESC LIMIT 1";
												
						$res = mysql_query($query) or die($query);

						$number = mysql_num_rows($res);
						if ($number != 0){
							$arr = mysql_fetch_assoc($res);
							$price = $arr['price'];
						}else{
							$price = 0;
						}

						mysql_close();
						
						echo '
							<div id="status">
								<header>
									<h2>Карточка позиции ';
									
						/*if (($items['edit'] == 1) || $god_mode){
							echo '
										<a href="pricelistitem_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}*/
						
						/*if (($items['edit'] == 1) || $god_mode){
							/*if ($rezult[0]['status'] != 9){
								echo '
											<a href="pricelistitem_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
							}*/
							/*if (($rezult[0]['status'] == 9) || ($items['close'] == 1)){
								echo '
									<a href="#" onclick="Ajax_reopen_pricelistitem('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
							}*/
						//}*/
						if (($items['close'] == 1) || $god_mode){
							//if ($rezult[0]['status'] != 9){
								echo '
											<a href="pricelistitem_insure_del.php?id='.$_GET['id'].'&insure='.$_GET['insure'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
							//}
						}
						
						echo '
									</h2>
									<a href="insure.php?id='.$_GET['insure'].'" class="ahref" style="color: green; font-size: 90%; font-weight: bold;">'.$insure_j[0]['name'].'</a>';
									
						if ($rezult[0]['status'] == 9){
							echo '<i style="color:red;">Позиция удалена (заблокирована).</i><br>';												
						}
						
						echo '
								</header>
								<a href="insure_price.php?id='.$_GET['insure'].'" class="b">В прайс</a><br>';
								
						echo '
								<div id="data">';

						echo '
									<div class="cellsBlock2">
										<div class="cellLeft">Название</div>
										<div class="cellRight">'.$rezult2[0]['name'].'</div>
									</div>';
						echo '
									<div class="cellsBlock2">
										<div class="cellLeft">Цена</div>
										<div class="cellRight">'.$price.' руб. ';
						if (($items['edit'] == 1) || $god_mode){
							if ($rezult[0]['status'] != 9){
								echo '
											<a href="priceprice_insure_edit.php?id='.$_GET['id'].'" class="info b2" style="font-size: 100%;" title="Редактировать цену"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
							}
						}
						echo '
										</div>
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
							
						$arr = array();
						$rez = array();
							
						require 'config.php';
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						
						$query = "SELECT * FROM `spr_priceprices_insure` WHERE `item`='".$_GET['id']."' ORDER BY `create_time` DESC";
						//$query = "SELECT * FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `date_from` DESC";
											
						$res = mysql_query($query) or die($query);

						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($rez, $arr);
							}
						}else{
							$rez = 0;
						}
						
						mysql_close();
						//var_dump($rez);				
						
						echo '
									<ul style="margin-bottom: 10px; margin-top: 20px;">
										<li style="width: auto; color:#777; font-size: 90%;">
											История изменения цены
										</li>
									</ul>
									<div style="margin-bottom: 20px;">
										<div class="cellsBlock">';
								
						if ($rez != 0){
							for($i=0; $i < count($rez); $i++){
								echo '<div>'.$rez[$i]['price'].' руб. c '.date('d.m.y H:i', $rez[$i]['date_from']).' | '.date('d.m.y H:i', $rez[$i]['create_time']).'  |  '.WriteSearchUser('spr_workers', $rez[$i]['create_person'], 'user', true).'</div>';
								//echo '<div>'.$rez[$i]['price'].' руб. |  '.date('d.m.y H:i', $rez[$i]['create_time']).'  |  '.WriteSearchUser('spr_workers', $rez[$i]['create_person'], 'user', true).'</div>';
							}
						}
						
						echo '
										</div>
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