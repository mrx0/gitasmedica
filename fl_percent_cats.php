<?php

//fl_percent_cats.php
//

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finances['see_all'] == 1) || $god_mode){

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

			
			echo '
				<header>
        			<div class="nav">
					    <a href="fl_percent_cats_personal.php" class="b">Персональные</a>
					    <a href="fl_salaries.php" class="b">Оклады сотрудников</a>
					</div>
					<h1>Категории процентов</h1>
					<span style= "color: red; font-size: 90%;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> При расчётах, если указана "Cпец. цена", проценты будут игнорироваться.</span>
				</header>';


            echo '	
	            <div id="data">	
                        <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                        <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                            <a href="?who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
                            <a href="?who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
                            <a href="?who=10" class="b" style="'.$somat_color.'">Специалисты</a>
                        </li>';


		    if (($finances['add_new'] == 1) || $god_mode){
				echo '
					<a href="fl_percent_cat_add.php" class="b">Добавить</a>';
			}
			echo '
						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
						<li class="cellsBlock2" style="font-weight: bold; font-size: 11px;">	
							<div class="cellPriority" style="text-align: center"></div>
							<div class="cellName" style="text-align: center; width: 180px; min-width: 180px;">
                                Название';
            echo $block_fast_filter;
            echo '
							</div>
							<div class="cellTime" style="text-align: center">Процент за работу (общий)</div>
							<div class="cellTime" style="text-align: center;">Процент за материал (общий)</div>
							<div class="cellTime" style="text-align: center;">
							    Спец. цена<br>
							    <span style="font-size:80%; color: #999; ">фиксированная</span>
							</div>
							<div class="cellText" style="text-align: center;">Персонал</div>
						</li>';



            include_once 'DBWork.php';
            //!!! @@@
            include_once 'ffun.php';


			//$percents_j = SelDataFromDB('fl_spr_percents', '', '');
			//var_dump ($percents_j);

            $msql_cnnct = ConnectToDB2 ();

            $percents_j = array();

            //$query = "SELECT * FROM `fl_spr_percents` ORDER BY `type`";
            $query = "SELECT * FROM `fl_spr_percents` WHERE `type`='$type'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($percents_j, $arr);
                }
            }

            if (!empty($percents_j)){

                $permissions_j = array();

                $query = "SELECT `id`, `name` FROM `spr_permissions`";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        $permissions_j[$arr['id']] = $arr['name'];
                    }
                }


                $prev_type = 0;

				for ($i = 0; $i < count($percents_j); $i++) {

					if ($percents_j[$i]['status'] == 9){
						$bgcolor = 'background-color: rgba(161,161,161,1);';
					}else{
						$bgcolor = 'background-color: rgba('.$percents_j[$i]['color'].',1);';
					}

					//Разделитель
                    if ($prev_type != $percents_j[$i]['type']){
                        echo '
							<li class="cellsBlock2" style="font-weight: bold; font-size: 11px;">
                                <div class="cellText" style="text-align: left; border: none; font-size: 110%; color: #929292;">Тип: '.$permissions_j[$percents_j[$i]['type']].'</div>
							</li>';

                        $prev_type = $percents_j[$i]['type'];
                    }

					echo '
							<li class="cellsBlock2 cellsBlockHover" style="font-weight: bold; font-size: 11px;'.$bgcolor.'">
								<div class="cellPriority"></div>
								<a href="fl_percent_cat.php?id='.$percents_j[$i]['id'].'" class="cellName ahref 4filter" style="text-align: left; width: 180px; min-width: 180px;" id="4filter">'.$percents_j[$i]['name'].'</a>';
                    if ($percents_j[$i]['summ_special'] > 0){
                        echo '
                                <div class="cellTime" style="text-align: center">-</div>
                                <div class="cellTime" style="text-align: center;">-</div>
                                <div class="cellTime" style="text-align: center;">'.$percents_j[$i]['summ_special'].'</div>';
                    }else{
                        echo '
                                <div class="cellTime" style="text-align: center">'.$percents_j[$i]['work_percent'].'</div>
                                <div class="cellTime" style="text-align: center;">'.$percents_j[$i]['material_percent'].'</div>
                                <div class="cellTime" style="text-align: center;">-</div>';
                    }
                    echo '                                
                                <div class="cellText" style="text-align: center;">'.$permissions_j[$percents_j[$i]['type']].'</div>
							</li>';
				}
			}

			echo '
					</ul>
				</div>
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>