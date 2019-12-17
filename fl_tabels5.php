<?php

//fl_tabels5.php
//Отчёт по часам
//для Другие

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
        include_once 'variables.php';
		//var_dump($_SESSION);

        if (in_array($_SESSION['permissions'], $workers_target_arr) || ($_SESSION['id'] == 270) || $god_mode) {
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			include_once 'ffun.php';
            include_once 'widget_calendar.php';
            //include_once 'variables.php';

            $dop = '';
            $dopWho = '';
            $dopDate = '';
            $dopFilial = '';
            //$di = 0;

            //тип (космет/стомат/...)
            if (isset($_GET['who'])) {
                $getWho = returnGetWho($_GET['who'], 11, array(4,7,13,14,15,11));
            }else{
                $getWho = returnGetWho(11, 11, array(4,7,13,14,15,11));
            }
            //var_dump($getWho);

            $who = $getWho['who'];
            $whose = $getWho['whose'];
            $selected_stom = $getWho['selected_stom'];
            $selected_cosm = $getWho['selected_cosm'];
            $datatable = $getWho['datatable'];
            $kabsForDoctor = $getWho['kabsForDoctor'];
            $type = $getWho['type'];

            $stom_color = $getWho['stom_color'];
            $cosm_color = $getWho['cosm_color'];
            $somat_color = $getWho['somat_color'];
            $admin_color = $getWho['admin_color'];
            $assist_color = $getWho['assist_color'];
            $sanit_color = $getWho['sanit_color'];
            $ubor_color = $getWho['ubor_color'];
            $dvornik_color = $getWho['dvornik_color'];
            $other_color = $getWho['other_color'];
            $all_color = $getWho['all_color'];

            //Массив типов сотрудников, которые никуда не входят
            //$workers_target_arr = [1, 9, 12, 777];

            //Костыль для "Другие"
            $type = 999;

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

            $month_stamp = mktime(0, 0, 0, $month, 1, $year);
            //var_dump($month_stamp);

            //Дней в месяце
            $day_count = date("t", $month_stamp);
            //var_dump($day_count);


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
            $filials_j = getAllFilials(true, true, true);
            //var_dump($filials_j);

            //Получили список прав
            $permissions_j = getAllPermissions(false, true);
            //var_dump($permissions_j);

            $msql_cnnct = ConnectToDB ();

            //Получаем календарь выходных на указанный год
            $holidays_arr = array();

            $query = "SELECT * FROM `spr_proizvcalendar_holidays` WHERE `year` = '$year' AND `month` = '$month'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив
                    if (!isset($holidays_arr[$arr['month']])){
                        $holidays_arr[$arr['month']] = array();
                        array_push($holidays_arr[$arr['month']], $arr['day']);
                    }else{
                        array_push($holidays_arr[$arr['month']], $arr['day']);
                    }
                }
            }
//            var_dump($number);
//            var_dump($holidays_arr);

            //Рабочие дни месяца
            $work_days_norma = $day_count - $number;
//            var_dump($work_days_norma);

            if (!isset($_SESSION['fl_calcs_tabels'])){
                $_SESSION['fl_calcs_tabels'] = array();
            }

            //var_dump($_SESSION['fl_calcs_tabels']);

			if ($_POST){
			}else{
                echo '
                    <div class="no_print"> 
					<header style="margin-bottom: 5px;">
                        <div class="nav">';
//                if ($tabel_j[0]['worker_id'] == $_SESSION['id']){
//                    echo '
//                            <a href="fl_my_tabels.php" class="b">Табели</a>';
//                }else {
                    echo '
                            <a href="fl_tabels.php?'.$who.'" class="b">Важный отчёт</a>';
//                }
                echo '
                        </div>
						<h1>Отчёт по часам</h1>';
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
                                <a href="fl_tabels2.php?who=4'.$dopDate.'" class="b" style="'.$admin_color.'">Администраторы</a>
                                <a href="fl_tabels2.php?who=7'.$dopDate.'" class="b" style="'.$assist_color.'">Ассистенты</a>
                                <a href="fl_tabels3.php?who=13'.$dopDate.'" class="b" style="'.$sanit_color.'">Санитарки</a>
                                <a href="fl_tabels3.php?who=14'.$dopDate.'" class="b" style="'.$ubor_color.'">Уборщицы</a>
                                <a href="fl_tabels3.php?who=15'.$dopDate.'" class="b" style="'.$dvornik_color.'">Дворники</a>
                                <a href="fl_tabels4.php?who=11'.$dopDate.'" class="b" style="">Прочие</a>';

                if (in_array($_SESSION['permissions'], $workers_target_arr) || ($_SESSION['id'] == 270) || $god_mode) {
                    echo '
                                <a href="fl_tabels5.php?who=999' . $dopDate . '" class="b" style="background-color: #fff261;">Другие</a>';
                }
                echo '
                            </li>';


                //Соберем массив сотрудников
                $workers_j = array();
                //ID сотрудников
                $w_id_arr = array();
                $w_id_str = '';

                //Выберем всех сотрудников с такой должностью
                $workers_target_str = implode(',', $workers_target_arr);

                //$query = "SELECT * FROM `spr_workers` WHERE `permissions`='{$type}' AND `status` <> '8'";

//                $query = "SELECT sw.*, sc.name AS cat_name, sc.id AS cat_id
//                FROM `spr_workers` sw
//                LEFT JOIN `journal_work_cat` jwcat ON sw.id = jwcat.worker_id
//                LEFT JOIN `spr_categories` sc ON jwcat.category = sc.id
//                WHERE sw.permissions = '".$type."'  AND sw.status <> '8'
//                ORDER BY sw.full_name ASC";

                $query = "SELECT * FROM `spr_workers` WHERE `permissions` IN ($workers_target_str) AND `status` = '0' ORDER BY `full_name` ASC";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        $workers_j[$arr['name']] = $arr;

                        //
                        array_push($w_id_arr, $arr['id']);
                    }
                }



                //Сортируем по имени
                ksort($workers_j);
//                var_dump($workers_j);
//                var_dump($w_id_arr);
                $w_id_str = implode(',', $w_id_arr);
//                var_dump($w_id_str);

//                $values  = join(",", array_map("intval", $massiv));
//                $query = "SELECT * FROM `fl_spr_salaries` WHERE `worker_id` IN ($w_id_str)";

                echo '<div class="no_print">';
                echo widget_calendar ($month, $year, 'fl_tabels5.php', $dop);
                echo '</div>';


                //Фильтр по филиалам
//                echo '
//                            <div style="margin-top: 5px; font-size: 80%;">
//                                Показывать только филиал:
//
//                                <select name="filterFilial" id="filterFilial" style="font-size: 93%;">
//                                    <option value="-1">Все</option>';
//
//                if (!empty($filials_j)){
//                    foreach ($filials_j as $f_id => $filial_item){
//                        echo "<option value='".$f_id."'>".$filial_item['name']."</option>";
//                    }
//                }
//                echo '
//                                    <option value="0">Без филиала</option>
//                                </select>';
                //var_dump($filials_j);

                echo '
                            </div>';



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
                $arr = array();
                $normaSmen = array();

//                $query = "SELECT * FROM `fl_spr_normasmen` WHERE `type` = '$type'";
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//                $number = mysqli_num_rows($res);
//                if ($number != 0){
//                    while ($arr = mysqli_fetch_assoc($res)){
//                        //Раскидываем в массив
//                        $normaSmen[$arr['month']] = $arr['count'];
//                    }
//                }
                //var_dump($normaSmen);

                //Норма смен (часов)
                //!!!Норма часов
//                if ($type == 15){
//                    $normaHours = 2;
//                }else{
//                    $normaHours = 12;
//                }
//                $w_normaSmen = $normaSmen[(int)$month] * $normaHours;

                //Получаем оклады по категориям для всех
//                $arr = array();
//                $salariesyCategory = array();
//
//                $query = "SELECT * FROM (SELECT * FROM `fl_spr_salaries` WHERE `permission` = '$type' ORDER BY `date_from` DESC) AS sub GROUP BY `category`, `filial_id`";
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

                //Получаем оклады по сотрудникам
                $arr = array();
                $salariesWorkers = array();

                $query = "SELECT * FROM (SELECT * FROM `fl_spr_salaries` WHERE `worker_id` IN ($w_id_str) ORDER BY `date_from` DESC) AS sub";
                //var_dump($query);
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        //Раскидываем в массив
                        if (!isset($salariesyCategory[$arr['worker_id']])) {
                            $salariesWorkers[$arr['worker_id']] = 0;
                        }
                        $salariesWorkers[$arr['worker_id']] = $arr['summ'];

                    }
                }
                //var_dump($salariesWorkers);


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
////                        if (!isset($hours_j[$arr['worker_id']])) {
////                            $hours_j[$arr['worker_id']] = array();
////                        }
////                        if (!isset($hours_j[$arr['worker_id']][$arr['day']])) {
////                            $hours_j[$arr['worker_id']][$arr['day']] = array();
////                        }
////                        if (!isset($hours_j[$arr['worker_id']][$arr['day']][$arr['filial_id']])) {
////                            $hours_j[$arr['worker_id']][$arr['day']][$arr['filial_id']] = array();
////                        }
////                        //array_push($hours_j, $arr);
////                        //$hours_j[$arr['worker_id']][$arr['filial_id']] += $arr['hours'];
////                        $hours_j[$arr['worker_id']][$arr['day']][$arr['filial_id']] = $arr['hours'];
//
//
//                        if (!isset($hours_j[$arr['worker_id']])) {
//                            $hours_j[$arr['worker_id']] = array();
//                        }
//                        if (!isset($hours_j[$arr['worker_id']][$arr['filial_id']])) {
//                            $hours_j[$arr['worker_id']][$arr['filial_id']] = 0;
//                        }
//                        //array_push($hours_j, $arr);
//                        $hours_j[$arr['worker_id']][$arr['filial_id']] += $arr['hours'];
//
//
//                    }
//                }
//                var_dump($query);
//                var_dump($hours_j);


                $arr = array();
                $hours_j = array();

                $workers_target_str = implode(',', $workers_target_arr);

                $query = "SELECT * FROM `fl_journal_scheduler_report` WHERE `type` IN ($workers_target_str) AND `month` = '$month' AND `year` = '$year'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        //Раскидываем в массив
//                        if (!isset($hours_j[$arr['worker_id']])) {
//                            $hours_j[$arr['worker_id']] = array();
//                        }
//                        if (!isset($hours_j[$arr['worker_id']][$arr['day']])) {
//                            $hours_j[$arr['worker_id']][$arr['day']] = array();
//                        }
//                        if (!isset($hours_j[$arr['worker_id']][$arr['day']][$arr['filial_id']])) {
//                            $hours_j[$arr['worker_id']][$arr['day']][$arr['filial_id']] = array();
//                        }
//                        //array_push($hours_j, $arr);
//                        $hours_j[$arr['worker_id']][$arr['day']][$arr['filial_id']] = $arr['hours'];

                        if (!isset($hours_j[$arr['worker_id']])) {
                            $hours_j[$arr['worker_id']] = array();
                        }
                        if (!isset($hours_j[$arr['worker_id']][$arr['filial_id']])) {
                            $hours_j[$arr['worker_id']][$arr['filial_id']] = 0;
                        }
                        //array_push($hours_j, $arr);
                        $hours_j[$arr['worker_id']][$arr['filial_id']] += $arr['hours'];


                    }
                }
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
                                <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Часы</i><br><span style="color: rgb(158, 158, 158); font-size: 80%;">всего/ норма/ %</span></td>
                                <td style="width: 80px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Начислено</i></td>
                                <!--<td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Закрыто работ на сумму, руб.</i></td>-->
                                <!--<td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Надбавка от выручки, руб.(%)</i></td>-->
                                <td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Итого, руб.</i></td>
                                <td style="width: 30px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;">
                                    <div class="button_tiny" style="margin-top: 5px; font-size: 120%; display: inline-block; cursor: pointer;" title="Обновить все" onclick="refreshAllTabelsForWorkerFromSchedulerReport();">
                                        <i class="fa fa-refresh" aria-hidden="true" style="color: rgb(218, 133, 9);"></i>
                                    </div>
                                </td>
                                ';
                echo '
                            </tr>';

                if (!empty($workers_j)) {
                    foreach ($workers_j as $worker_data) {
                        //var_dump($worker_data);

                        if (($_SESSION['id'] == $worker_data['id']) || ($_SESSION['id'] == 270) || $god_mode || ($worker_data['permissions'] == 777)) {

                            $normaHours = getNormaHours($worker_data['id']);

                            $bgColor = '';
                            //Если в декрете, выделим
                            if ($worker_data['status'] == 6) {
                                $bgColor = 'background-color: rgba(213, 22, 239, 0.13)';
                            }

                            //var_dump($worker_data);
                            $haveFilial = true;
                            $haveCategory = true;
                            $worker_category_id = 0;
                            $worker_filial_id = 0;
                            $w_percentDays = 0;
                            $worker_revenue_percent = 0.00;
                            $worker_revenue_solar_percent = 0.00;
                            $worker_revenue_realiz_percent = 0.00;
                            $worker_revenue_abon_percent = 0.00;
                            $oklad = 0.00;

                            //--
                            //$w_normaSmen = 0;

                            //Специализации
                            //$specializations = workerSpecialization($worker_data['id']);
                            //var_dump($specializations);

                            //if (!empty($specializations)) {
                            //    foreach ($specializations as $worker_specializ_data) {

                            echo '
                                        <tr class="cellsBlockHover workerItem" worker_id="' . $worker_data['id'] . '" filial_id="' . $worker_data['filial_id'] . '" style="' . $bgColor . '">
                                            <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">
                                                <b>' . $worker_data['full_name'] . '</b> ';


                            //var_dump($specializations);

                            //if (!empty($specializations)){
                            //foreach ($specializations as $specialization_item){
                            //echo ' <span class="tag" style="float: right; font-size: 90%;">' . $worker_specializ_data['name'] . '</span>';
                            //}
                            //}


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
                            if ($worker_data['filial_id'] != 0) {
                                echo $filials_j[$worker_data['filial_id']]['name2'];
                                $worker_filial_id = $worker_data['filial_id'];
                            } else {
                                echo '<span style="color: rgb(243, 0, 0);">не прикреплен</span>';
                                $haveFilial = false;
                            }
                            echo '
                                            </td>
                                            
                                            <td style="width: 80px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; ">';


                            //Получаем оклад конкретного сотрудника
                            //                                $arr = array();
                            //                                $salary = 0;
                            //
                            //                                $query = "SELECT * FROM `fl_spr_salaries` WHERE `worker_id` = '{$worker_data['id']}' ORDER BY `date_from` DESC LIMIT 1";
                            //                                //var_dump($query);
                            //                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                            //                                $number = mysqli_num_rows($res);
                            //                                if ($number != 0) {
                            //                                    //                    while ($arr = mysqli_fetch_assoc($res)){
                            //                                    //                        //Раскидываем в массив
                            //                                    //                        if (!isset($salariesyCategory[$arr['filial_id']])) {
                            //                                    //                            $salariesyCategory[$arr['filial_id']] = array();
                            //                                    //                        }
                            //                                    //                        //if (!isset($categories[$arr['filial_id']][$arr['category']])) {
                            //                                    //                        $salariesyCategory[$arr['filial_id']][$arr['category']] = $arr['summ'];
                            //                                    //                        //}
                            //                                    //                    }
                            //                                    $arr = mysqli_fetch_assoc($res);
                            //                                    $salary = $arr['summ'];
                            //                                }
                            //                                //var_dump($salary);
                            //
                            //                                $oklad = $salary;

                            //var_dump($salariesyCategory);
                            //var_dump($worker_filial_id);
                            //var_dump($salariesyCategory[$worker_filial_id]);
                            //Оклад
                            if (isset($salariesWorkers[$worker_data['id']])) {
                                if (isset($salariesWorkers[$worker_data['id']])) {
                                    //Администраторы
                                    $oklad = $salariesWorkers[$worker_data['id']];
                                }
                            }

                            //Ассистенты
                            //                                                        if ($type == 7) {
                            //                                                            $oklad = $oklad * $w_normaSmen;
                            //                                                        }

                            echo number_format($oklad, 2, '.', ' ');

                            //Итого
                            echo '
                                                                        </td>
                                    
                                                                        <td style="width: 120px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">';

                            $w_hours = 0;
                            $w_percentHours = 0;

                            //$normaHours = getNormaHours($worker_data['id']);

                            $w_normaHours = $work_days_norma * $normaHours;

                            //Смены часы
                            if (isset($hours_j[$worker_data['id']])) {
                                //var_dump($hours_j[$worker_data['id']]);

                                //Норма смен (часов) по специализациям
                                //$w_normaSmen = $normaSmen[$worker_specializ_data['id']][(int)$month]*12;

                                $w_hours = array_sum($hours_j[$worker_data['id']]);
//                                var_dump($worker_data['id']);
//                                var_dump($w_hours);

                                $w_percentHours = number_format($w_hours * 100 / $w_normaHours, 5, '.', '');

                                echo '
                                                    <div id="w_hours_' . $worker_data['id'] . '" style="margin-bottom: 15px; box-shadow: 0 0 3px 1px rgb(197, 197, 197); text-align: center;">' . $w_hours . '/ <span id="w_norma_' . $worker_data['id'] . '">' . $w_normaHours . '</span>/ ' . number_format($w_percentHours, 2, '.', '') . '%</div>';

                                //Нарисуем табличку со всеми филиалами
                                //                                    echo '
                                //                                                <table style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; margin:5px; font-size: 80%;">';
                                //                                    foreach ($hours_j[$worker_data['id']] as $filial_id => $hours_data) {
                                //
                                //                                        echo '
                                //                                                    <tr>
                                //                                                        <td>
                                //                                                            ' . $filials_j[$filial_id]['name2'] . '
                                //                                                        </td>
                                //                                                        <td style="text-align: right; width: 39px;">
                                //                                                            ' . $hours_data . '
                                //                                                        </td>
                                //                                                    </tr>';
                                //                                    }
                                //
                                //                                    echo '
                                //                                                </table>';

                            } else {
                                echo '
                                                    <span style="color: rgb(243, 0, 0);">нет данных</span>';
                            }

                            echo '
                                                                        </td>
                                                                        <td style="width: 80px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
                                                                        <div id="zp_temp_' . $worker_data['id'] . '" style="">
                                                                        </div>
                                                                        </td>';
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
                                                <div id="w_id_' . $worker_data['id'] . '" class="itogZP" w_id="' . $worker_data['id'] . '" f_id="' . $worker_filial_id . '" oklad="' . $oklad . '" w_hours="' . $w_hours . ',' . $normaHours . '" w_percentHours="' . $w_percentHours . '" worker_revenue_percent="' . $worker_revenue_percent . '" worker_revenue_solar_percent="' . $worker_revenue_solar_percent . '" worker_revenue_realiz_percent="' . $worker_revenue_realiz_percent . '" worker_revenue_abon_percent="' . $worker_revenue_abon_percent . '" filialMoney="0" filialSolar="0" filialRealiz="0" filialAbon="0" worker_category_id="' . $worker_category_id . '" style="">
                                                </div>';

                            echo '
                                            </td> 
                                            <td id="worker_' . $worker_data['id'] . '" class="workerTabel" f_id="' . $worker_filial_id . '" style="width: 30px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center; font-size: 120%;">
                                                <i class="fa fa-file-text" aria-hidden="true" style="color: rgba(0, 0, 0, 0.30); font-size: 130%;" title="Нет табеля"></i>
                                                <i class="fa fa-plus" style="color: green; font-size: 100%; cursor: pointer;" title="Добавить" onclick="addNewTabelForWorkerFromSchedulerReport(' . $worker_data['id'] . ', ' . $worker_filial_id . ', ' . $worker_data['permissions'] . ');"></i>
                                            </td>
                                        </tr>';
                            //    }
                            //}
                        }
                    }
                }
                echo '
                        </table>';
                echo '
                    </div>';

                echo '
		            <div id="doc_title">Отчёт по часам - Асмедика</div>';

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
				
				
                //Если хотим видеть только один филиал
                $(function() {
                    $("#filterFilial").change(function(){
                        
                        blockWhileWaiting (true);
                        
                        var filter_filial_id = $(this).val();

                        $(".cellsBlockHover").each(function(){
                            if (filter_filial_id == -1){
                                $(this).show();
                            }else{
                                if ($(this).attr("filial_id") == filter_filial_id){
                                    $(this).show();
                                }else{
                                    $(this).hide();
                                }
                            }
                        });
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