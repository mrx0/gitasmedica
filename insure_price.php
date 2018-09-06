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
							<div class="nav">
								<a href="pricelist.php" class="b">Основной прайс</a>
							</div>
							<h1 style="padding: 0;">
							    Прайс <a href="insure.php?id='.$_GET['id'].'" class="ahref" style="color: green; font-size: 90%; font-weight: bold;">'.$insure_j[0]['name'].'</a>
							</h1>
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


					if (($items['add_new'] == 1) || $god_mode){
						/*echo '
								<a href="add_insure_price_item.php?id='.$_GET['id'].'" class="b">Добавить позицию</a>';*/
						/*echo '
								<a href="add_pricelist_group.php?" class="b">Добавить группу/подгруппу</a>';*/
						echo '
								<a href="insure_price_fill.php?id='.$_GET['id'].'" class="b">Заполнить</a>';
                        echo '
								<a href="insure_price_copy.php?id='.$_GET['id'].'" class="b">Копировать</a>';
						echo '
								<a href="insure_price_clear.php?id='.$_GET['id'].'" class="b">Очистить полностью</a>';
					}
			
					if (($items['edit'] == 1) || $god_mode){
						echo '
								<div class="no_print"> 
								<li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
									<div style="cursor: pointer;" onclick="manageScheduler()">
										<span style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">Управление</span> <i class="fa fa-cog" title="Настройки"></i>
									</div>
    						        <div id="DIVdelCheckedItems" style="display: none; width: 400px; margin-bottom: 10px; border: 1px dotted #BFBCB5; padding: 20px 10px 10px; background-color: #EEE;">
    						            Удалить отмеченные позиции из прайса страховой<br>
    	    							<!--<input type="button" class="b" value="Удалить" onclick="if (iCanManage) Ajax_change_shed()">-->
    	    							<input type="button" class="b" value="Удалить" onclick="delCheckedItems('.$_GET['id'].');">
    	    							<div id="errrror"></div>
    							    </div>
								</li>
								</div>';
								//managePriceList
					}
			
					echo '					
								<p style="margin: 5px 0; padding: 2px;">
									Быстрый поиск: 
									<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
								</p>';
					/*echo '
								<li class="cellsBlock" style="font-weight:bold; width: auto;">
									<div class="cellPriority" style="text-align: center"></div>
									<div class="cellName" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;">Наименование</div>
									<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">Цена, руб.</div>
								</li>';*/

					$arr = array();
					$rez = array();
					$arr4 = array();
					$rez4 = array();
					$arr3 = array();
					$rez3 = array();


                    $msql_cnnct = ConnectToDB ();

					$query = "SELECT * FROM `spr_pricelists_insure` WHERE `insure`='".$_GET['id']."' LIMIT 1";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

					$number = mysqli_num_rows($res);

					if ($number != 0){
						//if ($services_j !=0){
							
							
							//showTree(15, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelists_insure', $_GET['id']);
							
							//Прайс		
							echo '	
								<div style="margin: 10px 0 5px; font-size: 11px; cursor: pointer;">
									<span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>
								</div>';
							
							echo '
								<div style="width: 900px; max-width: 900px; min-width: 900px;">
									<ul class="ul-tree ul-drop" id="lasttree" style="width: 850px; font-size: 12px;">';

							showTree4(15, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelists_insure', $_GET['id'], 0);	
										
							echo '
									</ul>
								</div>';
								
							//Без группы
									
							//$query = "SELECT * FROM `spr_pricelists_insure` WHERE `id` NOT IN (SELECT `item` FROM `spr_itemsingroup`) AND `status` <> '9' AND `insure`='{$_GET['id']}' ORDER BY `name`";
							$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` IN (SELECT `item` FROM `spr_pricelists_insure` WHERE `item` NOT IN (SELECT `item` FROM `spr_itemsingroup`) AND `status` <> '9' AND `insure`='{$_GET['id']}') ORDER BY `name`";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

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
									//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `date_from`, `create_time` DESC LIMIT 1";
									$query = "SELECT `price`,`price2`,`price3` FROM `spr_priceprices_insure` WHERE `item`='".$items_j[$i]['id']."' AND `insure`='".$_GET['id']."' ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";

                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

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
													<a href="pricelistitem_insure.php?insure='.$_GET['id'].'&id='.$items_j[$i]['id'].'" class="ahref 4filter" id="4filter"><i>'.$items_j[$i]['code'].'</i> '.$items_j[$i]['name'].'</a>
												</div>
												<div class="priceitemDiv">
													<div class="priceitemDivcost"><b>'.$price.'</b> руб.</div>';
                                    /*if ($_GET['id'] == 0) {
                                        echo '
                                                    <div class="priceitemDivcost" ><b > '.$price2.'</b > руб.</div >
													<div class="priceitemDivcost" ><b > '.$price3.'</b > руб.</div >';
                                    }*/
                                    echo '

												</div>
											</div>
										</li>';
									
								}
								echo '</ul>';
							}

						//}
					}else{
						echo '<i style="color:red;">Прайс не заполнен</i>';
					}

                    CloseDB ($msql_cnnct);

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