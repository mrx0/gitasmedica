<?php

//fl_tabels3.php
//Важный отчёт
//для прочее

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			include_once 'ffun.php';
            include_once 'widget_calendar.php';

            $dop = '';
            $dopWho = '';
            $dopDate = '';
            $dopFilial = '';
            //$di = 0;

            //тип график (космет/стомат/...)
            $who = '&who=4';
            $whose = 'Администраторов ';
            $selected_stom = ' selected';
            $selected_cosm = ' ';
            $datatable = 'scheduler_admin';

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
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
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
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 10){
                    $who = '&who=10';
                    $whose = 'Специалистов ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';
                    $type = 10;


                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = 'background-color: #fff261;';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 4){
                    $who = '&who=4';
                    $whose = 'Администраторов ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 4;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = 'background-color: #fff261;';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 7){
                    $who = '&who=7';
                    $whose = 'Ассистенты ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 7;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = 'background-color: #fff261;';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 11){
                    $who = '&who=11';
                    $whose = 'Прочее ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 11;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = 'background-color: #fff261;';
                    $all_color = '';
                }else{
                    $who = '&who=4';
                    $whose = 'Администраторов ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_admin';
                    $kabsForDoctor = 'admin';
                    $type = 4;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = 'background-color: #fff261;';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }
            }else{
//                $who = '';
//                $whose = 'Все ';
//                $selected_stom = ' selected';
//                $selected_cosm = ' ';
//                $datatable = 'scheduler_stom';
//                $kabsForDoctor = 'stom';
//                $type = 0;
//
//                $stom_color = '';
//                $cosm_color = '';
//                $somat_color = '';
//                $admin_color = '';
//                $assist_color = '';
//                $other_color = '';
//                $all_color = 'background-color: #fff261;';

                $who = '&who=4';
                $whose = 'Администраторов ';
                $selected_stom = ' selected';
                $selected_cosm = ' ';
                $datatable = 'scheduler_admin';
                $kabsForDoctor = 'admin';
                $type = 4;

                $stom_color = '';
                $cosm_color = '';
                $somat_color = '';
                $admin_color = 'background-color: #fff261;';
                $assist_color = '';
                $other_color = '';
                $all_color = '';
            }

            if (isset($_GET['m']) && isset($_GET['y'])){
                //операции со временем
                $month = $_GET['m'];
                $year = $_GET['y'];
            }else{
                //операции со временем
                $month = date('m');
                $year = date('Y');
            }

            //Сегодняшняя дата
            $day = date("d");
            $cur_month = date("m");
            $cur_year = date("Y");

            foreach ($_GET as $key => $value){
                if (($key == 'd') || ($key == 'm') || ($key == 'y'))
                    $dopDate  .= '&'.$key.'='.$value;
                if ($key == 'filial'){
                    $dopFilial .= '&'.$key.'='.$value;
                    $dop .= '&'.$key.'='.$value;
                }
                if ($key == 'who'){
                    $dopWho .= '&'.$key.'='.$value;
                    $dop .= '&'.$key.'='.$value;
                }
            }

            $today = date("Y-m-d");





			$workers_j = array();

			//$offices_j = SelDataFromDB('spr_filials', '', '');
            //$permissions_j = SelDataFromDB('spr_permissions', '', '');
            $filials_j = getAllFilials(false, true, true);
            //var_dump($filials_j);

            //Получили список прав
            $permissions_j = getAllPermissions(false, true);
            //var_dump($permissions_j);

            $msql_cnnct = ConnectToDB ();

            if (!isset($_SESSION['fl_calcs_tabels'])){
                $_SESSION['fl_calcs_tabels'] = array();
            }

            //var_dump($_SESSION['fl_calcs_tabels']);

			if ($_POST){
			}else{
				echo '
                    <div class="no_print"> 
					<header style="margin-bottom: 5px;">
						<h1>Важный отчёт</h1>';
                echo '
                        <div>
						    <a href="fl_tabel_print_choice.php?type='.$type.'" class="b4">Печать пачки</a>
						</div>';
                echo '    
					</header>
					</div>';

				echo '
                    <div id="data" style="margin: 10px 0 0;">';
                echo '
					    <div id="errrror"></div>';
                echo '
                        <ul style="margin-left: 6px; margin-bottom: 20px;">
                            <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                            <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                <a href="fl_tabels.php?who=5" class="b" style="">Стоматологи</a>
                                <a href="fl_tabels.php?who=6" class="b" style="">Косметологи</a>
                                <a href="fl_tabels.php?who=10" class="b" style="">Специалисты</a>
                                <a href="fl_tabels2.php?who=4" class="b" style="">Администраторы</a>
                                <a href="fl_tabels2.php?who=7" class="b" style="">Ассистенты</a>
                                <a href="fl_tabels.php?who=7" class="b" style="">Ассистенты2</a>
                                <a href="fl_tabels3.php?who=11" class="b" style="'.$other_color.'">Прочее</a>
                                <!--<a href="fl_tabels_noch.php" class="b" style="">Ночь</a>-->
                            </li>';




                //Соберем массив сотрудников
                $workers_j = array();

                //Выберем всех сотрудников с такой должностью
                //$query = "SELECT * FROM `spr_workers` WHERE `permissions`='{$type}' AND `status` <> '8'";

                $query = "SELECT sw.*, sc.name AS cat_name, sc.id AS cat_id
                FROM `spr_workers` sw  
                LEFT JOIN `journal_work_cat` jwcat ON sw.id = jwcat.worker_id
                LEFT JOIN `spr_categories` sc ON jwcat.category = sc.id
                WHERE sw.permissions = '".$type."'  AND sw.status <> '8'
                ORDER BY sw.full_name ASC";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        $workers_j[$arr['name']] = $arr;
                    }
                }

                //Сортируем по имени
                ksort($workers_j);
                //var_dump($workers_j);


                echo '<div class="no_print">';
                echo widget_calendar ($month, $year, 'fl_tabels2.php', $dop);
                echo '</div>';

                echo '
                        </ul>';


                //Процент с выручки для этого типа
//                $revenue_percent_j = array();
//
//                $arr = array();
//                $rez = array();
//
//                $query = "SELECT * FROM `fl_spr_revenue_percent` WHERE `permission` = '{$type}'";
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                $number = mysqli_num_rows($res);
//                if ($number != 0){
//                    while ($arr = mysqli_fetch_assoc($res)){
//                        if (!isset($revenue_percent_j[$arr['filial_id']])){
//                            $revenue_percent_j[$arr['filial_id']] = array();
//                        }
//                        if (!isset($revenue_percent_j[$arr['filial_id']][$arr['category']])){
//                            $revenue_percent_j[$arr['filial_id']][$arr['category']] = array();
//                        }
//                        $revenue_percent_j[$arr['filial_id']][$arr['category']] = $arr;
//                    }
//                }
                //var_dump($revenue_percent_j);

                //Получаем нормы смен для этого типа
//                $arr = array();
//                $normaSmen = array();
//
//                $query = "SELECT * FROM `fl_spr_normasmen` WHERE `type` = '$type'";
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//                $number = mysqli_num_rows($res);
//                if ($number != 0){
//                    while ($arr = mysqli_fetch_assoc($res)){
//                        //Раскидываем в массив
//                        $normaSmen[$arr['month']] = $arr['count'];
//                    }
//                }

                //Норма смен (часов)
//                $w_normaSmen = $normaSmen[(int)$month]*12;

                //Получаем оклады по категориям для всех
//                $arr = array();
//                $salariesyCategory = array();
//
//                $query = "SELECT * FROM (SELECT * FROM `fl_spr_salaries_category` WHERE `permission` = '$type' ORDER BY `date_from` DESC) AS sub GROUP BY `category`, `filial_id`";
//                //var_dump($query);
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//                $number = mysqli_num_rows($res);
//                if ($number != 0){
//                    while ($arr = mysqli_fetch_assoc($res)){
//                        //Раскидываем в массив
//                        if (!isset($salariesyCategory[$arr['filial_id']])) {
//                            $salariesyCategory[$arr['filial_id']] = array();
//                        }
//                        //if (!isset($categories[$arr['filial_id']][$arr['category']])) {
//                        $salariesyCategory[$arr['filial_id']][$arr['category']] = $arr['summ'];
//                        //}
//                    }
//                }
                //var_dump($salariesyCategory);


                //Соберём часы за месяц отовсюду для этого типа
//                $arr = array();
//                $hours_j = array();
//
//                $query = "SELECT * FROM `fl_journal_scheduler_report` WHERE `type` = '$type' AND `month` = '$month' AND `year` = '$year'";
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0){
//                    while ($arr = mysqli_fetch_assoc($res)){
//                        //Раскидываем в массив
//                        if (!isset($hours_j[$arr['worker_id']])) {
//                            $hours_j[$arr['worker_id']] = array();
//                        }
//                        if (!isset($hours_j[$arr['worker_id']][$arr['filial_id']])) {
//                            $hours_j[$arr['worker_id']][$arr['filial_id']] = 0;
//                        }
//                        //array_push($hours_j, $arr);
//                        $hours_j[$arr['worker_id']][$arr['filial_id']] += $arr['hours'];
//
//                    }
//                }
                //var_dump($hours_j);

                $block_fast_filter = '';

                //Календарная сетка
                echo '
                        <table style="border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                            <tr class="<!--sticky f-sticky-->">
                                <td style="width: 260px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>ФИО'.$block_fast_filter.'</i></td>
                                <!--<td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Категория</i></b></td>-->
                                <td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Прикреплён</i></b></td>
                                <td style="width: 80px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Оклад, руб.</i></td>
                                <!--<td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Часы</i><br><span style="color: rgb(158, 158, 158); font-size: 80%;">всего/ норма/ %</span></td>-->
                                <!--td style="width: 80px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Начислено за время</i></td>-->
                                <!--<td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Закрыто работ на сумму, руб.</i></td>-->
                                <!--<td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Надбавка от выручки, руб.(%)</i></td>-->
                                <td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Итого, руб.</i></td>
                                <td style="width: 30px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;">-</td>
                                ';
                echo '
                            </tr>';

                if (!empty($workers_j)) {
                    foreach ($workers_j as $worker_data) {
                        //var_dump($worker_data);

                        $bgColor = '';
                        //Если в декрете, выделим
                        if ($worker_data['status'] == 6){
                            $bgColor = 'background-color: rgba(213, 22, 239, 0.13)';
                        }

                        //var_dump($worker_data);
                        $haveFilial = true;
                        $haveCategory = true;
                        $worker_category_id = 0;
                        $worker_filial_id = 0;
                        $w_percentHours = 0;
                        $worker_revenue_percent = 0.00;
                        $oklad = 0.00;

                        //--
                        $w_normaSmen = 0;

                        //Специализации
                        $specializations = workerSpecialization($worker_data['id']);


                        echo '
                                <tr class="cellsBlockHover workerItem" worker_id="'.$worker_data['id'].'" style="'.$bgColor.'">
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">
                                        <b>'.$worker_data['full_name'].'</b> ';


                        //var_dump($specializations);

                        if (!empty($specializations)){
                            foreach ($specializations as $specialization_item){
                                echo ' <span class="tag" style="float: right; font-size: 90%;">'.$specialization_item['name'].'</span>';
                            }
                        }


//                        echo '
//                                    </td>
//                                    <td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">';
//
//                        //Категория
//                        if (($worker_data['cat_id'] != NUll) && ($worker_data['cat_name'] != NUll)) {
//                            echo $worker_data['cat_name'];
//                            $worker_category_id = $worker_data['cat_id'];
//                        }else{
//                            echo '<span style="color: rgb(243, 0, 0);">не указано</span>';
//                            $haveCategory =false;
//                        }

                        echo '
                                    </td>
                                    <td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">';

                        //Если есть привязка к филиалу
                        if ($worker_data['filial_id'] != 0){
                            echo $filials_j[$worker_data['filial_id']]['name2'];
                            $worker_filial_id = $worker_data['filial_id'];
                        }else{
                            echo '<span style="color: rgb(243, 0, 0);">не прикреплен</span>';
                            $haveFilial =false;
                        }
                        echo '
                                    </td>
                                    
                                    <td style="width: 80px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; ">';


                        //Получаем оклад конкретного сотрудника
                        $arr = array();
                        $salary = 0;

                        $query = "SELECT * FROM `fl_spr_salaries` WHERE `worker_id` = '{$worker_data['id']}' ORDER BY `date_from` DESC LIMIT 1";
                        //var_dump($query);
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0){
//                    while ($arr = mysqli_fetch_assoc($res)){
//                        //Раскидываем в массив
//                        if (!isset($salariesyCategory[$arr['filial_id']])) {
//                            $salariesyCategory[$arr['filial_id']] = array();
//                        }
//                        //if (!isset($categories[$arr['filial_id']][$arr['category']])) {
//                        $salariesyCategory[$arr['filial_id']][$arr['category']] = $arr['summ'];
//                        //}
//                    }
                            $arr = mysqli_fetch_assoc($res);
                            $salary = $arr['summ'];
                        }
                        //var_dump($salary);

                        $oklad = $salary;


                        //Оклад
//                        if (isset($salariesyCategory[$worker_filial_id])){
//                            if (isset($salariesyCategory[$worker_filial_id][$worker_data['cat_id']])) {
//                                //Администраторы
//                                $oklad = $salariesyCategory[$worker_filial_id][$worker_data['cat_id']];
//                            }
//                        }

//                        //Ассистенты
//                        if ($type == 7) {
//                            $oklad = $oklad * $w_normaSmen;
//                        }

                        echo number_format($salary, 2, '.', ' ');

                        //Итого
//                        echo '
//                                    </td>
//
//                                    <td style="width: 120px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">';

                        $w_hours = 0;
                        $w_percentHours = 0;


                        //Смены часы
//                        if (isset($hours_j[$worker_data['id']])){
//
//                            $w_hours = array_sum($hours_j[$worker_data['id']]);
//                            $w_percentHours = number_format($w_hours * 100 / $w_normaSmen, 2, '.', '');
//
//                            echo '
//                                        <div id="w_hours_'.$worker_data['id'].'" style="margin-bottom: 15px; box-shadow: 0 0 3px 1px rgb(197, 197, 197); text-align: center;">'.$w_hours.'/ <span id="w_norma_'.$worker_data['id'].'">'.$w_normaSmen.'</span>/ '.$w_percentHours.'%</div>';
//
//                            //Нарисуем табличку со всеми филиалами
//                            echo '
//                                        <table style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; margin:5px; font-size: 80%;">';
//                            foreach ($hours_j[$worker_data['id']] as $filial_id => $hours_data){
//
//                                echo '
//                                            <tr>
//                                                <td>
//                                                    '.$filials_j[$filial_id]['name2'].'
//                                                </td>
//                                                <td style="text-align: right; width: 39px;">
//                                                    '.$hours_data.'
//                                                </td>
//                                            </tr>';
//                            }
//                            echo '
//                                        </table>';
//
//                        }else{
//                            echo '
//                                        <span style="color: rgb(243, 0, 0);">нет данных</span>';
//                        }
//
//                        echo '
//                                    </td>
//                                    <td style="width: 80px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
//                                    <div id="zp_temp_'.$worker_data['id'].'" style="">
//                                    </div>
//                                    </td>
//                                    <td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">';
//                        //Выручка
//                        echo '
//                                        <div class="filialMoney" w_id="'.$worker_data['id'].'" filial_id="'.$worker_filial_id.'">
//                                        </div>';
//                        echo '
//                                    </td>
//                                    <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">';
//
//                        //% от выручки
//                        if ($haveCategory && $haveFilial){
//                            echo '
//                                    <div id="w_revenue_summ_'.$worker_data['id'].'" style="display: inline;">
//                                    </div>';
//
//
//                            $worker_revenue_percent = $revenue_percent_j[$worker_filial_id][$worker_category_id]['value'];
//
//                            echo '
//                                    <div style="display: inline;">
//                                        ('.number_format($worker_revenue_percent, 1, '.', ' ').'%)
//                                    </div>';
//                        }
                        echo '
                                        
                                    </td>
                                    <td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; font-weight: bold;">
                                        <div id="w_id_'.$worker_data['id'].'" class="itogZP" w_id="'.$worker_data['id'].'" f_id="'.$worker_filial_id. '" oklad="'.$oklad.'" w_hours="'.$w_hours.','.$w_normaSmen.'" w_percentHours="'.$w_percentHours.'" worker_revenue_percent="'.$worker_revenue_percent.'" filialMoney="0" worker_category_id="'.$worker_category_id.'" style="">
                                        </div>';

                        echo '
                                    </td> 
                                    <td id="worker_'.$worker_data['id'].'" class="workerTabel" f_id="'.$worker_filial_id. '" style="width: 30px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center; font-size: 120%;">
                                        <i class="fa fa-file-text" aria-hidden="true" style="color: rgba(0, 0, 0, 0.30); font-size: 130%;" title="Нет табеля"></i>
                                        <i class="fa fa-plus" style="color: green; font-size: 100%; cursor: pointer;" title="Добавить" onclick="addNewTabelForWorkerFromSchedulerReport('.$worker_data['id'].', '.$worker_filial_id.', '.$type.');"></i>
                                    </td>
                                </tr>';
                    }
                }
                echo '
                        </table>';
                echo '
                    </div>';

                echo '
		            <div id="doc_title">Важный отчёт - Асмедика</div>';

				echo '

				<script type="text/javascript">

				$(document).ready(function() {
				    //Соберём выручку филиала
                    //fl_calculateZP ('.$month.', '.$year.',0);
				    //setTimeout(fl_getAllTabels ('.$month.', '.$year.', '.$type.'), 7000);
				    
				    
                    wait(function(runNext){

                        setTimeout(function(){
            
                            fl_calculateZP2 ('.$month.', '.$year.', '.$type.');
            
                            runNext();
            
                        }, 100);
            
                    }).wait(function(){
            
                        setTimeout(function(){
            
                            fl_getAllTabels ('.$month.', '.$year.', '.$type.')
            
                        }, 1000);
            
                       
            
                    });
				    
				    
                    //$.when(fl_calculateZP ('.$month.', '.$year.',0)).then(fl_getAllTabels ('.$month.', '.$year.', '.$type.'));  
                    
                    //!!! из файла fl_tabels.php
                    //посмотреть по ходу, надо ли это тут будет
				    var ids = "0_0_0";
				    var ids_arr = {};
				    var permission = 0;
				    var worker = 0;
				    var office = 0;


                    //Табели
				    $(".tableTabels").each(function() {
                        //console.log($(this).attr("id"));
                        
                        var thisObj = $(this);

                        ids = $(this).attr("id");
                        ids_arr = ids.split("_");
                        //console.log(ids_arr);
                         
                        permission = ids_arr[0];
                        worker = ids_arr[1];
                        office = ids_arr[2];
                        
                        var certData = {
                            permission: permission,
                            worker: worker,
                            office: office,
                            month: "'.date("m").'",
                            year: "'.date("Y").'",
                            own_tabel: false
                        };
                        
                        
                        getTabelsfunc (thisObj, certData);
                    });

				    //Необработанные расчеты
				    $(".tableDataNPaidCalcs").each(function() {
                        //console.log($(this).attr("id"));
                        
                        var thisObj = $(this);

                        ids = $(this).attr("id");
                        ids_arr = ids.split("_");
                        //console.log(ids_arr);
                         
                        permission = ids_arr[0];
                        worker = ids_arr[1];
                        office = ids_arr[2];
                        
                        var certData = {
                            permission: permission,
                            worker: worker,
                            office: office,
                            month: "'.date("m").'",
                            year: "'.date("Y").'",
                            own_tabel: false
                        };
                        
                        
                        getCalculatesfunc (thisObj, certData);
                    });
                    
				});
				
                
				</script>';
			}
			//mysql_close();
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>