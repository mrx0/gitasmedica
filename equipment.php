<?php

//equipment.php
//Номенлатура

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($items['see_all'] == 1) || ($items['see_own'] == 1) || $god_mode){
			include_once 'functions.php';
            include_once 'DBWork.php';

			//тип (космет/стомат/...)
			/*if (isset($_GET['who'])){
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
			}*/
			
			echo '
				<header>
					<h1>Номенлатура</h1>
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
					    <a href="add_equipment_item.php" class="b">Добавить позицию</a>';
				echo '
					    <a href="add_equipment_group.php" class="b">Добавить группу/подгруппу</a>';
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
            }

            $msql_cnnct = ConnectToDB();


            echo '	
                        <div style="margin: 10px 0 5px; font-size: 11px; cursor: pointer;">
                            <span class="dotyel a-action lasttreedrophide">скрыть всё</span><!--, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>-->
                        </div>';
				
            echo '
					    <div style="width: 900px; max-width: 900px; min-width: 900px;">
						    <ul class="ul-tree ul-drop" id="lasttree" style="width: 850px; font-size: 12px;">';

            //Основные группы
            $arr = array();
            $main_groups_j = array();

            $query = "SELECT * FROM `spr_equipment` WHERE `parent_id` IS NULL AND `is_group` IS NOT NULL ORDER BY `name`";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($main_groups_j, $arr);
                }
            }
            //var_dump($main_groups_j);


            //Если что-то найдено
            if (!empty($main_groups_j)){

                $style_name = 'font-size: 110%; font-style: oblique;';

                foreach ($main_groups_j as $main_grops_item){
                    echo '
                                <li style="border: none; position: relative;">
                                    <div class="drop" style="background-position: 0px 0px;"></div>
                                    <p class="drop" style="font-size: 130%; background-color: rgba(255, 236, 24, 0.5);">
                                        <b>
                                            '.$main_grops_item['name'].'
                                        </b>
                                    </p>';

                    //Редактирование, кнопки, иконки
                    if (true) {
                        echo '
                                    <div style="position: absolute; top: 0; right: 3px;">
                                       <a href="pricelistgroup.php?id=' . $main_grops_item['id'] . '" class="ahref" style="font-weight: bold;" title="Открыть карточку группы">
                                            <i class="fa fa-folder-open" aria-hidden="true"></i>								    
                                       </a>
                                        <div style="font-style: normal; font-size: 13px; display: inline-block;">
                                            <div class="managePriceList">
                                                <a href="pricelistgroup_edit.php?id=' . $main_grops_item['id'] . '" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать карточку группы"></i></a>
                                                <a href="add_pricelist_item.php?addinid=' . $main_grops_item['id'] . '" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
                                                <!--<a href="pricelistgroup_del.php?id=' . $main_grops_item['id'] . '" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
                                                <a href="pricelistgroup_del.php?id=' . $main_grops_item['id'] . '" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить эту группу"></i></a>
                                            </div>
                                        </div>
                                    </div>';
                    }

                    //Подгруппы
                    $arr = array();
                    $groups_j = array();

                    $query = "SELECT * FROM `spr_equipment` WHERE `parent_id` IS NULL AND `is_group` IS NOT NULL ORDER BY `name`";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            array_push($main_groups_j, $arr);
                        }
                    }
                    //var_dump($main_groups_j);


                    //Если что-то найдено
                    if (!empty($main_groups_j)){

                    /*echo '
                                    <ul style="display: none;">';*/


                    echo '
                                    <ul>';

                    //позиции с ценами
                    echo '
                                    <li>
                                        <div class="priceitem">';
                    if ($insure_id != 0) {
                        echo '
                                            <div class="cellManage" style="display: none;">
                                              <span style="font-size: 80%; color: #777;">
                                                <input type="checkbox" name="propDel[]" value="' . $items_j[$i]['id'] . '"> пометить на удаление
                                              </span>
                                            </div>';
                    }
                    echo '
                                            <div class="priceitemDivname">
                                                <a href="'.$link.'&id='.$items_j[$i]['id'].'" class="ahref" id="4filter"><span style="font-size: 75%; font-weight: bold;">[#'.$items_j[$i]['id'].']</span> <i>'.$items_j[$i]['code'].'</i> '.$items_j[$i]['name'].'</a>
                                            </div>
                                            <div class="priceitemDiv">
                                                <div class="priceitemDivcost"><b>'.$price.'</b> руб.</div>';
                    if ($insure_id == 0) {
                        echo '
                                                <div class="priceitemDivcost" ><b > '.$price2.'</b > руб.</div >
                                                <div class="priceitemDivcost" ><b > '.$price3.'</b > руб.</div >';
                    }
                    echo '

                                            </div>
                                        </div>
                                    </li>';



                    echo '
                        </ul>';

                    echo '
                    </li>';


                }
            }


				//showTree4(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelist_template', 0, 0);
							
				echo '
						</ul>
					</div>';


				


            CloseDB ($msql_cnnct);

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