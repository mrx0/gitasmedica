<?php

//fl_normahours_personal.php
//Персональные нормы часов

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($stom);

        if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			//$offices = SelDataFromDB('spr_filials', '', '');
			//var_dump ($offices);

            require 'variables.php';

			$who = '&who=5';
			$whose = 'Стоматологов ';
			$selected_stom = ' selected';
			$selected_cosm = ' ';
			$datatable = 'scheduler_stom';

            //тип (космет/стомат/...)
            if (isset($_GET['who'])){
                if ($_GET['who'] == 5){
                    $who = '&who=5';
                    $whose = 'Стоматологи ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_stom';
                    $kabsForDoctor = 'stom';
                    $type = 5;

                    $stom_color = 'background-color: #fff261;';
                    $cosm_color = '';
                    $somat_color = '';
                }elseif($_GET['who'] == 6){
                    $who = '&who=6';
                    $whose = 'Косметологи ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_cosm';
                    $kabsForDoctor = 'cosm';
                    $type = 6;

                    $stom_color = '';
                    $cosm_color = 'background-color: #fff261;';
                    $somat_color = '';
                }elseif($_GET['who'] == 10){
                    $who = '&who=10';
                    $whose = 'Специалисты ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';
                    $type = 10;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = 'background-color: #fff261;';
                }else{
                    $who = '&who=5';
                    $whose = 'Стоматологи ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_stom';
                    $kabsForDoctor = 'stom';
                    $type = 5;

                    $stom_color = 'background-color: #fff261;';
                    $cosm_color = '';
                    $somat_color = '';
                }
            }else{
                $who = '&who=5';
                $whose = 'Стоматологи ';
                $selected_stom = ' selected';
                $selected_cosm = ' ';
                $datatable = 'scheduler_stom';
                $kabsForDoctor = 'stom';
                $type = 5;

                $stom_color = 'background-color: #fff261;';
                $cosm_color = '';
                $somat_color = '';
            }

            include_once 'ffun.php';

            $msql_cnnct = ConnectToDB2 ();

            $workers_j = array();
            $spr_percents_j = array();

            //Сотрудники этого типа
            $arr = array();
            $rez = array();

            $query = "SELECT norm.*, s_w.full_name FROM `fl_spr_normahours_personal` norm
                        LEFT JOIN `spr_workers` s_w ON s_w.id = norm.worker_id
                        WHERE s_w.status <> 8 AND s_w.status <> 9
                        ORDER BY s_w.full_name";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($rez, $arr);
                }
                $workers_j = $rez;
            }

            //Категории процентов общие
//            $arr = array();
//            $rez = array();
//
//            $query = "SELECT * FROM `fl_spr_percents` WHERE `type` = '{$type}'";
//            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//            $number = mysqli_num_rows($res);
//            if ($number != 0){
//                while ($arr = mysqli_fetch_assoc($res)){
//                    $rez[$arr['id']] = $arr;
//                }
//                $spr_percents_j = $rez;
//            }
            //var_dump($spr_percents_j);

            //переменная, чтоб вкл/откл редактирование
            echo '
                <script>
                    var iCanManage = true;
                </script>';

            echo '
                <div id="status" class="no_print">
                    <header>
                        <div class="nav">
                            <a href="fl_normahours.php" class="b">Общие</a>
                            <!--<a href="fl_salaries.php" class="b">Оклады сотрудников</a>-->
                        </div>
                        <h1>Персональные нормы часов <!--<a href="fl_percent_cats_personal_4print.php" class="b3" style="font-weight: normal; font-size: 55%;">Версия для печати <i class="fa fa-print" aria-hidden="true"></i></a>--></h1>
                        <!--<span style= "color: red; font-size: 90%;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> При расчётах, если указана "Cпец. цена", проценты будут игнорироваться.</span>-->';
            echo '			
                    </header>';


            echo '
                    <div id="infoDiv" style="display: none; position: absolute; background-color: rgba(249, 255, 171, 0.89);" class="query_neok">

                    </div>
                    <div id="data" style="margin: 8px 0 0;">';

            if (($_SESSION['permissions'] == 3) || $god_mode) {
                echo '
                        <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                            <a href="add_new_norma_hours.php" class="b" style="">Добавить норму сотруднику</a>
                        </li>';
            }

            if (!empty($workers_j)){
                echo '
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock2" style="font-weight: bold; font-size: 11px; background: #FFF;">	
								<div class="cellFullName" style="text-align: center">
								    Сотрудник';
                echo $block_fast_filter;
                echo '
                                    
								</div>
								<div class="cellName" style="text-align: center; width: 210px; min-width: 210px; padding: 4px 0 0;">
								    Норма
								</div>';

                if (($_SESSION['permissions'] == 3) || $god_mode) {
                    echo '
								<div class="cellName" style="text-align: center; width: 40px; min-width: 40px; padding: 4px 0 0;">
								    <i class="fa fa-cog" title="Настройки"></i>
								</div>';
                }

                echo '
							</li>';


                foreach ($workers_j as $worker){
//                    var_dump($worker);

                    echo '
							<li class="cellsBlock2 cellsBlockHover" style="font-weight: normal; font-size: 11px; margin-bottom: -1px;">
							    <div style="position: relative;">
								    <a href="user.php?id='.$worker['worker_id'].'" class="cellFullName ahref 4filter" id="4filter" style="text-align: left;">'.$worker['full_name'].'</a>
                                </div>
                                <div class="cellName " style="text-align: center; width: 210px; min-width: 210px; padding: 0; font-size: 120%; font-weight: bold;">
                                    <div class="cellDivide" style="width: 65px; font-size: 10px; position: relative;">
                                        <span class="changePersonalNormaHours cpp_1_'.$worker['worker_id'].'_'.$worker['id'].'" worker_id="'.$worker['worker_id'].'" norma_id="'.$worker['id'].'" type_id="1" style="cursor: pointer;">'.$worker['count'].'</span> часов                
                                    </div>
                                </div>';
                    if (($_SESSION['permissions'] == 3) || $god_mode) {
                        echo '
								<div class="cellName" style="text-align: center; width: 40px; min-width: 40px; padding: 4px 0 0;">
								    <a href="" class="info" style="font-size: 110%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
								</div>';
                    }

                    echo '
							</li>';
                }

                echo '</ul>';
            }else{
                echo 'Ничего нет...';
            }


            echo '
					</div>
				</div>';

			echo '	
			<!-- Подложка только одна -->
			<div id="overlay" class="no_print"></div>';

			
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>