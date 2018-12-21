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
						    <ul class="ul-tree ul-drop" id="lasttree" style="width: 850px; font-size: 11px;">';



            showTree5(0, '', 'list', 0, TRUE, 0, TRUE, 'spr_equipment', 0, 0);
            //showTree5($level, $space, $type, $sel_id, $first, $last_level, $deleted, $dbtable, $insure_id, $dtype){

            echo '
						    </ul>
					    </div>';

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