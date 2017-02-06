<?php

//insure_price.php
//

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['see_all'] == 1) || ($items['see_own'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
			
				$insure_j = SelDataFromDB('spr_insure', $_GET['id'], 'id');

				if ($insure_j != 0){
					echo '
						<header>
							<h1 style="padding: 0;">Прайс <a href="insure.php?id='.$_GET['id'].'" class="ahref" style="color: green; font-size: 90%; font-weight: bold;">'.$insure_j[0]['name'].'</a></h1>
						</header>';
				
					//переменная, чтоб вкл/откл редактирование
					echo '
						<script>
							var iCanManage = false;
						</script>';
				
					echo '
						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';


					if (($items['add_new'] == 1) || $god_mode){
						echo '
								<a href="add_insure_price_item.php?id='.$_GET['id'].'" class="b">Добавить позицию/группу</a>';
						/*echo '
								<a href="add_pricelist_group.php?" class="b">Добавить группу/подгруппу</a>';*/
						echo '
								<a href="insure_price_fill.php?id='.$_GET['id'].'" class="b">Заполнить</a>';
						echo '
								<a href="clear_insure_price.php?id='.$_GET['id'].'" class="b">Очистить полностью</a>';
					}
			
					/*if (($items['edit'] == 1) || $god_mode){
						echo '
								<div class="no_print"> 
								<li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
									<div style="cursor: pointer;" onclick="manageScheduler()">
										<span style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">Управление</span> <i class="fa fa-cog" title="Настройки"></i>
									</div>
								</li>
								</div>';
								//managePriceList
					}*/
			
					echo '					
								<p style="margin: 5px 0; padding: 2px;">
									Быстрый поиск: 
									<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
								</p>';
					echo '
								<li class="cellsBlock" style="font-weight:bold; width: auto;">
									<div class="cellPriority" style="text-align: center"></div>
									<div class="cellName" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;">Наименование</div>
									<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">Цена, руб.</div>
								</li>';

					$arr = array();
					$rez = array();
					$arr4 = array();
					$rez4 = array();
					$arr3 = array();
					$rez3 = array();
					
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
			
					//if ($services_j !=0){
						showTree(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelists_insure', $_GET['id']);
						
						
						//Без группы
								
						$query = "SELECT * FROM `spr_pricelists_insure` WHERE `id` NOT IN (SELECT `item` FROM `spr_itemsingroup`) AND `status` <> '9' AND `insure`='{$_GET['id']}' ORDER BY `name`";			
						
						$res = mysql_query($query) or die(mysql_error().' -> '.$query);

						$number = mysql_num_rows($res);	
						if ($number != 0){
							while ($arr3 = mysql_fetch_assoc($res)){
								array_push($rez3, $arr3);
							}
							$items_j = $rez3;
						}else{
							$items_j = 0;
						}
				
					//var_dump($items_j);
					
					if ($items_j != 0){
						
						echo '
							<li class="cellsBlock" style="width: auto;">
								<div class="cellPriority" style=""></div>
								<span class="cellOffice" style="font-weight: bold; text-align: left; width: 350px; min-width: 350px; max-width: 350px; background-color: rgba(255, 103, 97, 0.5);" id="4filter">Без группы</span>
								<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; background-color: rgba(255, 103, 97, 0.5);"></div>
							</li>';
						
						for ($i = 0; $i < count($items_j); $i++) {
							$price = 0;
							
							//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
							$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
												
							$res = mysql_query($query) or die(mysql_error().' -> '.$query);

							$number = mysql_num_rows($res);
							if ($number != 0){
								$arr4 = mysql_fetch_assoc($res);
								$price = $arr4['price'];
							}else{
								$price = 0;
							}
					
							echo '
										<li class="cellsBlock" style="width: auto;">
											<div class="cellPriority" style=""></div>
											<a href="pricelistitem.php?id='.$items_j[$i]['id'].'" class="ahref cellOffice" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px;" id="4filter">'.$items_j[$i]['name'].'</a>
											<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">'.$price.'</div>
										</li>';
						}
					}

					//}
				
					mysql_close();

					echo '
								</ul>
							</div>';
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