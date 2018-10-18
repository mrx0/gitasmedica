<?php

//fl_percent_cats_personal.php
//

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

            $query = "SELECT * FROM `spr_workers` WHERE `permissions` = '{$type}' AND `fired` <> '1'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($rez, $arr);
                }
                $workers_j = $rez;
            }

            //Категории процентов общие
            $arr = array();
            $rez = array();

            $query = "SELECT * FROM `fl_spr_percents` WHERE `type` = '{$type}'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    $rez[$arr['id']] = $arr;
                }
                $spr_percents_j = $rez;
            }
            //var_dump($spr_percents_j);

            //переменная, чтоб вкл/откл редактирование
            echo '
                <script>
                    var iCanManage = true;
                </script>';

            echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <a href="fl_percent_cats.php" class="b">Общие</a>
                        </div>
                        <h1>Персональные категории процентов</h1>';
            echo '			
                    </header>';


            echo '
                    <div id="infoDiv" style="display: none; position: absolute; background-color: rgba(249, 255, 171, 0.89);" class="query_neok">

                    </div>
                    <div id="data">';

            echo '		
                        <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                        <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                            <a href="?who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
                            <a href="?who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
                            <a href="?who=10" class="b" style="'.$somat_color.'">Специалисты</a>
                        </li>';

            if ((!empty($workers_j)) && (!empty($spr_percents_j))){
                //var_dump($rezult2);
                echo '
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock2" style="font-weight: bold; font-size: 11px; background: #FFF;">	
								<div class="cellFullName" style="text-align: center">
								    Полное имя';
                echo $block_fast_filter;
                echo '
                                    
								</div>';
                foreach ($spr_percents_j as $percent_cat){
                    echo '
                                <div class="cellName" style="text-align: center; width: 140px; min-width: 140px; padding: 4px 0 0; background-color: rgba('.$percent_cat['color'].', 0.7)">
                                    '.$percent_cat['name'].'<br>
                                    <div class="cellDivide" style="width: 65px; font-size: 10px;">Работа</div>
                                    <div class="cellDivide" style="width: 65px; font-size: 10px;">Материал</div>
                                </div>';
                }

                echo '
							</li>';

                foreach ($workers_j as $worker){
                    echo '
							<li class="cellsBlock2 cellsBlockHover" style="font-weight: normal; font-size: 11px; margin-bottom: -1px;">
							    <div style="position: relative;">
								    <a href="user.php?id='.$worker['id'].'" class="cellFullName ahref 4filter" id="4filter" style="text-align: left;">'.$worker['full_name'].'</a>
                                    <div onclick="fl_changePersonalPercentCatdefault('.$worker['id'].');" id ="changePersonalPercentCatdefault" style="position: absolute; right: 0px; top: 2px; font-size: 12px; color: green; border: 1px solid #BFBCB5; background-color: #FFF; padding: 0 6px; cursor: pointer;">
                                        <i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>
                                    </div>
                                </div>';

                    foreach ($spr_percents_j as $cat_id => $percent_cat){

                        $percents_personal_j = array();

                        $query = "SELECT * FROM `fl_spr_percents_personal` WHERE `worker_id` = '{$worker['id']}' AND `percent_cats` = '{$cat_id}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                $percents_personal_j[$arr['percent_cats']][$arr['type']] = $arr;
                            }
                        }
                        //var_dump($percents_personal_j);

                        /*if ($percent_cat['color'] == '255,255,255') {
                            $alpha = '0.5';
                        }else{
                            $alpha = '1';
                        }*/

                        echo '
                                <div class="cellName" style="text-align: center; width: 140px; min-width: 140px; padding: 0;background-color: rgba('.$percent_cat['color'].', 0.7); font-size: 120%; font-weight: bold;">
                                    <div class="cellDivide" style="width: 65px; font-size: 10px; position: relative;">';
                        echo '
                                        <span class="changePersonalPercentCat" worker_id="'.$worker['id'].'" cat_id="'.$cat_id.'" type_id="1" style="cursor: pointer;">';

                        //Если есть индивидуальные
                        if (isset($percents_personal_j[$cat_id][1])){
                            echo $percents_personal_j[$cat_id][1]['percent'];
                        }else{
                            echo $percent_cat['work_percent'];
                        }

                        echo '</span>%';
                        echo '                
                                    </div>
                                    <div class="cellDivide" style="width: 65px; font-size: 10px; position: relative;">';
                        echo '
                                        <span class="changePersonalPercentCat" worker_id="'.$worker['id'].'" cat_id="'.$cat_id.'" type_id="2" style="cursor: pointer;">';

                        //Если есть индивидуальные
                        if (isset($percents_personal_j[$cat_id][2])){
                            echo $percents_personal_j[$cat_id][2]['percent'];
                        }else{
                            echo $percent_cat['material_percent'];
                        }

                        echo '</span>%';
                        echo '            
                                    </div>
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
			<div id="overlay"></div>';

			
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>