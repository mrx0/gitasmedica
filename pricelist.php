<?php

//pricelist.php
//

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($items['see_all'] == 1) || ($items['see_own'] == 1) || $god_mode){
			include_once 'functions.php';
            include_once 'DBWork.php';

			//тип (космет/стомат/...)
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
					<h1>Основной прайс</h1>
						<div>
							<span style="font-size: 80%; color: #AAA">Перейти к прайсу страховой</span><br>';
			echo '
							<select name="insurecompany" id="insurecompany">
								<option value="0">Выберите страховую</option>';
			$insures_j = SelDataFromDB('spr_insure', '', '');

			if ($insures_j != 0){
				for ($i=0;$i<count($insures_j);$i++){
                    echo "<option value='".$insures_j[$i]['id']."'>".$insures_j[$i]['name']."</option>";
			    }
			}
			echo '
							</select>
							<span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick="iWantThisInsurePrice()"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
						</div>
				</header>';
				
			//переменная, чтоб вкл/откл редактирование
			echo '
				<script>
					var iCanManage = false;
				</script>';
				
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
					<a href="add_pricelist_item.php" class="b">Добавить позицию</a>';
				echo '
					<a href="add_pricelist_group.php?'.$who.'" class="b">Добавить группу/подгруппу</a>';
			}

            if (($items['edit'] == 1) || $god_mode){
                echo '
								<div class="no_print"> 
								<li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
									<div style="cursor: pointer;" onclick="manageScheduler()">
										<span style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">Управление</span> <i class="fa fa-cog" title="Настройки"></i>
									</div>
    						        <div id="DIVdelCheckedItems" style="display: none; width: 400px; margin-bottom: 10px; border: 1px dotted #BFBCB5; padding: 20px 10px 10px; background-color: #EEE;">
    						            Переместить выбранные позиции в группу<br>
    	    							<!--<input type="button" class="b" value="Удалить" onclick="if (iCanManage) Ajax_change_shed()">-->
    	    							<input type="button" class="b" value="Переместить" onclick="showMoveCheckedItems();">
    	    							<div id="errrror"></div>
    							    </div>
								</li>
								</div>';
                //managePriceList
            }
			
			/*echo '					
								<p style="margin: 5px 0; padding: 2px;">
									Быстрый поиск: 
									<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
								</p>';*/
			/*echo '
								<li class="cellsBlock" style="font-weight:bold; width: auto;">
									<div class="cellPriority" style="text-align: center"></div>
									<div class="cellName" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;">Наименование</div>
									<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">Цена, руб.</div>
								</li>';*/

			//$services_j = SelDataFromDB('spr_pricelist_template', 'services', $type);
			//var_dump ($services_j);

			$arr = array();
			$rez = array();
			$arr4 = array();
			$rez4 = array();
			$arr3 = array();
			$rez3 = array();

            $msql_cnnct = ConnectToDB();
			
			//if ($services_j !=0){
				
				//Прайс		
				echo '	
					<div style="margin: 10px 0 5px; font-size: 11px; cursor: pointer;">
						<span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>
					</div>';
				
				echo '
					<div style="width: 900px; max-width: 900px; min-width: 900px;">
						<ul class="ul-tree ul-drop" id="lasttree" style="width: 850px; font-size: 12px;">';

				showTree4(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelist_template', 0, 0);		
							
				echo '
						</ul>
					</div>';
				
				
				//!!! переделать Без группы
						
				$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` NOT IN (SELECT `item` FROM `spr_itemsingroup`) AND `status` <> '9' ORDER BY `name`";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

				$number = mysqli_num_rows($res);
				if ($number != 0){
					while ($arr3 = mysqli_fetch_assoc($res)){
						array_push($rez3, $arr3);
					}
					$items_j = $rez3;
				}else{
					$items_j = 0;
				}
				
				//var_dump($items_j);
				
				if ($items_j != 0){

					echo '
                    <ul class="" style="width: 850px; font-size: 12px;">
					<li class="cellsBlock" style="width: auto;">
						<div class="cellPriority" style=""></div>
						<span class="cellOffice 4filter" style="font-weight: bold; text-align: left; width: 350px; min-width: 350px; max-width: 350px; background-color: rgba(255, 103, 97, 0.5);" id="4filter">Без группы</span>
						<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; background-color: rgba(255, 103, 97, 0.5);"></div>
					</li>';

					for ($i = 0; $i < count($items_j); $i++) {
						$price = 0;
						$price2 = 0;
						$price3 = 0;

						//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
						$query = "SELECT `price`,`price2`,`price3` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `date_from`, `create_time` DESC LIMIT 1";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

						$number = mysqli_num_rows($res);
						if ($number != 0){
							$arr4 = mysqli_fetch_assoc($res);
							$price = $arr4['price'];
							$price2 = $arr4['price2'];
							$price3 = $arr4['price3'];
						}else{
							$price = 0;
							$price2 = 0;
							$price3 = 0;
						}

						/*echo '
									<li class="cellsBlock" style="width: auto;">
										<div class="cellPriority" style=""></div>
										<a href="pricelistitem.php?id='.$items_j[$i]['id'].'" class="ahref cellOffice" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px;" id="4filter">'.$items_j[$i]['name'].'</a>
										<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">'.$price.' / '.$price2.' / '.$price3.'</div>
									</li>';*/
                        //позиции с ценами
                        echo '
										<li>
											<div class="priceitemWOGroup">';
                        //if ($_GET['id'] != 0) {
                        echo '
                            			        <div class="cellManage" style="display: none;">
											      <span style="font-size: 80%; color: #777;">
											        <input type="checkbox" name="propDel[]" value="' . $items_j[$i]['id'] . '"> отметить
											      </span>
                                                </div>';
                        //}
                        echo '
												<div class="priceitemDivname">
													<a href="pricelistitem.php?id='.$items_j[$i]['id'].'" class="ahref 4filter" id="4filter"><i>'.$items_j[$i]['code'].'</i> '.$items_j[$i]['name'].'</a>
												</div>
												<div class="priceitemDiv">
													<div class="priceitemDivcost"><b>'.$price.'</b> руб.</div>';
                        //if ($_GET['id'] == 0) {
                        echo '
                                                    <div class="priceitemDivcost" ><b > '.$price2.'</b > руб.</div >
													<div class="priceitemDivcost" ><b > '.$price3.'</b > руб.</div >';
                        // }
                        echo '

												</div>
											</div>
										</li>';

                    }
                    echo '</ul>';
				}
				
				//Пробуем показать удалённые
				//showTree(0, '', 'list', 0, FALSE, 0, TRUE, 'spr_pricelist_template');
				
				$arr = array();
				$rez = array();
				$arr4 = array();
				$rez4 = array();
				$arr3 = array();
				$rez3 = array();
				
				//Удаленные группы
				
				$query = "SELECT * FROM `spr_storagegroup` WHERE `status` = '9'";			
				//var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
				//var_dump($res);
				
				$number = mysqli_num_rows($res);
				if ($number != 0){
					while ($arr3 = mysqli_fetch_assoc($res)){
						array_push($rez3, $arr3);
					}
					$items_j = $rez3;
				}else{
					$items_j = 0;
				}
				
				//var_dump($items_j);
				
				if ($items_j != 0){
					echo '
					<li class="cellsBlock" style="width: auto; margin-top: 10px;">
						<div class="cellPriority" style="background-color: rgba(114, 114, 114, 0.5);"></div>
						<span class="cellOffice 4filter" style="font-weight: bold; text-align: left; width: 350px; min-width: 350px; max-width: 350px; background-color: rgba(114, 114, 114, 0.5);" id="4filter">Удалённые группы</span>
						<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; background-color: rgba(114, 114, 114, 0.5);"></div>
					</li>';
					for ($i = 0; $i < count($items_j); $i++) {
						$price = 0;
						
						//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
						/*$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
											
						$res = mysql_query($query) or die(mysql_error().' -> '.$query);

						$number = mysql_num_rows($res);
						if ($number != 0){
							$arr4 = mysql_fetch_assoc($res);
							$price = $arr4['price'];
						}else{
							$price = 0;
						}*/
				
						echo '
									<li class="cellsBlock" style="width: auto;">
										<div class="cellPriority" style="background-color: rgba(114, 114, 114, 0.5);"></div>
										<a href="pricelistgroup.php?id='.$items_j[$i]['id'].'" class="ahref cellOffice 4filter" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px; background-color: rgba(223, 128, 252, 0.23);" id="4filter">'.$items_j[$i]['name'].'</a>
										<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; background-color: rgba(223, 128, 252, 0.23);">
											<div class="managePriceList" style="font-style: normal; font-size: 13px;">';
						/*echo '
												<a href="pricelistgroup_edit.php?id='.$items_j[$i]['id'].'" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать"></i></a>
												<a href="add_pricelist_item.php?addinid='.$items_j[$i]['id'].'" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
												<!--<a href="pricelistgroup_del.php?id='.$items_j[$i]['id'].'" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
												<a href="pricelistgroup_del.php?id='.$items_j[$i]['id'].'" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить"></i></a>';*/
						echo '
											</div>
										</div>
									</li>';
					}
				}			
				
				$arr = array();
				$rez = array();
				$arr4 = array();
				$rez4 = array();
				$arr3 = array();
				$rez3 = array();
				
				//Удалённые позиции
					
				$query = "SELECT * FROM `spr_pricelist_template` WHERE `status` = '9'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

				$number = mysqli_num_rows($res);
				if ($number != 0){
					while ($arr3 = mysqli_fetch_assoc($res)){
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
						<div class="cellPriority" style="background-color: rgba(114, 114, 114, 0.5);"></div>
						<span class="cellOffice 4filter" style="font-weight: bold; text-align: left; width: 350px; min-width: 350px; max-width: 350px; background-color: rgba(114, 114, 114, 0.5);" id="4filter">Удалённые позиции</span>
						<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; background-color: rgba(114, 114, 114, 0.5);"></div>
					</li>';

					for ($i = 0; $i < count($items_j); $i++) {
						$price = 0;

						//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
						$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

						$number = mysqli_num_rows($res);
						if ($number != 0){
							$arr4 = mysqli_fetch_assoc($res);
							$price = $arr4['price'];
						}else{
							$price = 0;
						}

						echo '
									<li class="cellsBlock" style="width: auto;">
										<div class="cellPriority" style="background-color: rgba(114, 114, 114, 0.5);"></div>
										<a href="pricelistitem.php?id='.$items_j[$i]['id'].'" class="ahref cellOffice 4filter" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px;" id="4filter">'.$items_j[$i]['name'].'</a>
										<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">'.$price.'</div>
									</li>';
					}
				}
				
				
			//}

            CloseDB ($msql_cnnct);

			echo '
							</ul>
						</div>';
            echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>