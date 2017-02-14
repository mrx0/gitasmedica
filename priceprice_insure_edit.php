<?php

//priceprice_insure_edit.php
//Редактирование цены позиции страховые

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($god_mode || $_SESSION['permissions'] == 3 || ($clients['add_own'] == 1)){
			if ($_GET){
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
					
							//операции со временем						
							$day = date('d');		
							$month = date('m');		
							$year = date('Y');

							echo '
								<div id="status">
									<header>
										<h2>Изменить цену</h2>
										<a href="insure.php?id='.$_GET['insure'].'" class="ahref" style="color: green; font-size: 90%; font-weight: bold;">'.$insure_j[0]['name'].'</a>
									</header>';

							echo '
									<div id="data">';
							echo '
										<div id="errror"></div>';
							echo '
										<form action="priceprice_edit_f.php">
							
											<div class="cellsBlock2">
												<div class="cellLeft">Название</div>
												<div class="cellRight">
													<a href="pricelistitem_insure.php?insure='.$_GET['insure'].'&id='.$_GET['id'].'" class="ahref">'.$rezult2[0]['name'].'</a>
												</div>
											</div>';
											
							require 'config.php';
							mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
							mysql_select_db($dbName) or die(mysql_error()); 
							mysql_query("SET NAMES 'utf8'");
						
							$arr = array();
							$rez = array();
							$price = 0;
								
							//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `create_time` DESC LIMIT 1";
							$query = "SELECT `price` FROM `spr_priceprices_insure` WHERE `item`='".$_GET['id']."' AND `insure`='".$_GET['insure']."' ORDER BY `date_from` DESC LIMIT 1";
													
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
											<div class="cellsBlock2">
												<div class="cellLeft">Цена</div>
												<div class="cellRight">
													<input type="text" name="price" id="price" value="'.$price.'"  style="width: 50px;"> руб.
													<label id="price_error" class="error"></label>
												</div>
											</div>';
											
							//Календарик	
							echo '
			
										<div class="cellsBlock2">
											<div class="cellLeft">С какого числа:</div>
											<div class="cellRight">
												<input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
											</div>
										</div>';
										
							echo '				
											<input type="button" class="b" value="Применить" onclick="Ajax_edit_price('.$_GET['id'].', '.$_SESSION['id'].')">
										</form>';

								
							echo '
										<div class="cellsBlock2">
											<span style="font-size:80%;">';
												
							if (($rezult[0]['create_time'] != 0) || ($items_j[0]['create_person'] != 0)){
								echo '
													Добавлен: '.date('d.m.y H:i', $rezult[0]['create_time']).'<br>
													Кем: '.WriteSearchUser('spr_workers', $rezult[0]['create_person'], 'user', true).'<br>';
							}else{
								echo 'Добавлен: не указано<br>';
							}
							if (($rezult[0]['last_edit_time'] != 0) || ($rezult[0]['last_edit_person'] != 0)){
								echo '
													Последний раз редактировался: '.date('d.m.y H:i', $rezult[0]['last_edit_time']).'<br>
													Кем: '.WriteSearchUser('spr_workers', $rezult [0]['last_edit_person'], 'user', true).'';
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
							
							$query = "SELECT * FROM `spr_priceprices_insure` WHERE `item`='".$_GET['id']."' AND `insure`='".$_GET['insure']."' ORDER BY `create_time` DESC";
												
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
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>