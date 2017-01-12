<?php

//services.php
//

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($items['see_all'] == 1) || ($items['see_own'] == 1) || $god_mode){
			include_once 'functions.php';
			
			//тип график (космет/стомат/...)
			if (isset($_GET['who'])){
				if ($_GET['who'] == 'stom'){
					$who = '&who=stom';
					$whose = 'Стоматология ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
				}elseif($_GET['who'] == 'cosm'){
					$who = '&who=cosm';
					$whose = 'Косметология ';
					$selected_stom = ' ';
					$selected_cosm = ' selected';
					$datatable = 'scheduler_cosm';
					$kabsForDoctor = 'cosm';
					$type = 6;
				}else{
					$who = '&who=stom';
					$whose = 'Стоматология ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
					$_GET['who'] = 'stom';
				}
			}else{
				$who = '&who=stom';
				$whose = 'Стоматология ';
				$selected_stom = ' selected';
				$selected_cosm = ' ';
				$datatable = 'scheduler_stom';
				$kabsForDoctor = 'stom';
				$type = 5;
				$_GET['who'] = 'stom';
			}
			
			echo '
				<header>
					<h1>Прайс</h1>
					<!--'.$whose.'-->
				</header>';
			echo '
						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			/*echo '			
								<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
									<a href="?who=stom" class="b">Стоматологи</a>
									<a href="?who=cosm" class="b">Косметологи</a>
								</li>';*/
			if (($items['add_new'] == 1) || $god_mode){
				echo '
					<a href="add_pricelist_item.php?'.$who.'" class="b">Добавить позицию</a>';
				echo '
					<a href="add_pricelist_group.php?'.$who.'" class="b">Добавить группу/подгруппу</a>';
			}
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
			
			include_once 'DBWork.php';
			$services_j = SelDataFromDB('spr_pricelist', 'services', $type);
			//var_dump ($services_j);

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
			
			if ($services_j !=0){
				showTree(0, '', 'list', 0, FALSE);
				
				echo '
					<li class="cellsBlock" style="width: auto;">
						<div class="cellPriority" style=""></div>
						<span class="cellOffice" style="font-weight: bold; text-align: left; width: 350px; min-width: 350px; max-width: 350px;" id="4filter">Без группы</span>
						<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">-</div>
					</li>';
						
				$query = "SELECT * FROM `spr_pricelist` WHERE `id` NOT IN (SELECT `item` FROM `spr_itemsingroup`) ORDER BY `name`";			
				
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
					for ($i = 0; $i < count($items_j); $i++) {
						$price = 0;
						
						//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
						$query = "SELECT `price` FROM `date_from` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
											
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
			}
			
			mysql_close();

			echo '
							</ul>
						</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>