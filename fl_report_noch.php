<?php

//fl_report_noch.php
//Отчёт ночь

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
            $type = 0;

            if (isset($_GET['m']) && isset($_GET['y'])){
                //операции со временем
                $month = $_GET['m'];
                $year = $_GET['y'];
            }else{
                //операции со временем
                $month = date('m');
                $year = date('Y');
            }
            //var_dump($month);

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
						<h1>Отчёт ночь</h1>';
//                echo '
//                        <div>
//						    <a href="fl_tabel_print_choice.php?type='.$type.'" class="b4">Печать пачки</a>
//						</div>';
                echo '    
					</header>
					</div>';

				echo '
                    <div id="data" style="margin: 10px 0 0;">';
                echo '
					    <div id="errrror"></div>';
                echo '
                        <ul style="margin-left: 6px; margin-bottom: 20px;">
                            <!--<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                            <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                <a href="fl_tabels.php?who=5" class="b" style="">Стоматологи</a>
                                <a href="fl_tabels.php?who=6" class="b" style="">Косметологи</a>
                                <a href="fl_tabels.php?who=10" class="b" style="">Специалисты</a>
                                <a href="fl_tabels2.php?who=4" class="b" style="">Администраторы</a>
                                <a href="fl_tabels2.php?who=7" class="b" style="">Ассистенты</a>
                                <a href="fl_tabels3.php?who=11" class="b" style="">Прочие</a>
                                <a href="fl_tabels_noch.php" class="b" style="background-color: #fff261;">Ночь</a>
                            </li>-->';

                echo '<div class="no_print">';
                echo widget_calendar ($month, $year, 'fl_report_noch.php', $dop);
                echo '</div>';

                echo '
                        </ul>';


                $rezultShed = array();

                //Выборка врачей по графику
                $query = "SELECT `day`, `filial`, `worker` FROM `scheduler` WHERE `smena`='3' AND `month` = '$month' AND `year` = '$year' ORDER BY `day`";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //Раскидываем в массив
                        //array_push($rezultShed, $arr);

//                        if (!isset($rezultShed[$arr['day']])) {
//                            $rezultShed[$arr['day']] = array();
//                        }
//                        if (!isset($rezultShed[$arr['day']][$arr['worker']])) {
//                            $rezultShed[$arr['day']][$arr['worker']] = array();
//                        }

                        if (!isset($rezultShed[$arr['day']])) {
                            $rezultShed[$arr['day']] = array();
                        }
                        if (!isset($rezultShed[$arr['day']][$arr['filial']])) {
                            $rezultShed[$arr['day']][$arr['filial']] = array();
                        }
                        if (!isset($rezultShed[$arr['day']][$arr['filial']][$arr['worker']])) {
                            $rezultShed[$arr['day']][$arr['filial']][$arr['worker']] = array();
                        }
                        //$arr['in_shed'] = TRUE;
                        //array_push($rezultShed[$arr['day']], $arr);
                    }
                }
                //var_dump($rezultShed[2][15]);


                //Выберем данные по записи в ночные смены
                //$zapis_j = array();
                //$invoice_j = array();

                // + наряды
                $query = "SELECT
                    z.office AS filial_id, z.worker AS worker_id, z.day,
                    ji.id, ji.summ, ji.summins, ji.paid, ji.status, ji.create_time, ji.create_person, ji.last_edit_time, ji.last_edit_person
                            FROM `zapis` z
                            LEFT JOIN `journal_invoice` ji ON ji.zapis_id = z.id
                            WHERE z.noch='1' AND z.enter='1' AND z.month='$month' AND z.year='$year';";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){

                        //Сравниваем полученные данные по записи с тем, что у нас есть по графику
                        //Дополняем данные, если есть соответствие
                        //Вставляем новые данные, если соответствия нет

                        //Метка, были ли эти данные в графике
                        $arr['in_shed'] = TRUE;

                        if (!isset($rezultShed[$arr['day']])){
                            $rezultShed[$arr['day']] = array();
                            $arr['in_shed'] = FALSE;
                        }
                        if (!isset($rezultShed[$arr['day']][$arr['filial_id']])) {
                            $rezultShed[$arr['day']][$arr['filial_id']] = array();
                            $arr['in_shed'] = FALSE;
                        }
                        if (!isset($rezultShed[$arr['day']][$arr['filial_id']][$arr['worker_id']])) {
                            $rezultShed[$arr['day']][$arr['filial_id']][$arr['worker_id']] = array();
                            $arr['in_shed'] = FALSE;
                        }

                        array_push($rezultShed[$arr['day']][$arr['filial_id']][$arr['worker_id']], $arr);
                        //array_push($invoice_j, $arr);
                    }
                }
                //var_dump($rezultShed);

                ksort($rezultShed);

                // + РЛ
//                $calculate_j = array();
//                $query = "SELECT
//                    z.office AS filial_id, z.worker AS worker_id, z.day,
//                    jc.id AS calculate_id, jc.summ_inv, jc.worker_id AS calc_worker_id
//                        FROM `zapis` z
//                        LEFT JOIN `fl_journal_calculate` jc ON jc.zapis_id = z.id
//                        WHERE z.noch='1' AND z.enter='1' AND z.month='$month' AND z.year='$year';";
//                //var_dump($query);
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                $number = mysqli_num_rows($res);
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//
//                        //Сравниваем полученные данные по записи с тем, что у нас есть по графику
//                        //Дополняем данные, если есть соответствие
//                        //Вставляем новые данные, если соответствия нет
//
//                        //Метка, были ли эти данные в графике
//                        $arr['in_shed'] = TRUE;
//
//                        if (!isset($rezultShed[$arr['day']])){
//                            $rezultShed[$arr['day']] = array();
//                            $arr['in_shed'] = FALSE;
//                        }
//                        if (!isset($rezultShed[$arr['day']][$arr['filial_id']])) {
//                            $rezultShed[$arr['day']][$arr['filial_id']] = array();
//                            $arr['in_shed'] = FALSE;
//                        }
//                        if (!isset($rezultShed[$arr['day']][$arr['filial_id']][$arr['worker_id']])) {
//                            $rezultShed[$arr['day']][$arr['filial_id']][$arr['worker_id']] = array();
//                            $arr['in_shed'] = FALSE;
//                        }
//
//                        array_push($rezultShed[$arr['day']][$arr['filial_id']][$arr['worker_id']], $arr);
//                        //array_push($calculate_j, $arr);
//                    }
//                }
                //var_dump($rezultShed[2][15]);

                //Соберём уже оформленные табели за месяц
                $tabels_j = array();

                $query = "SELECT * FROM `fl_journal_tabels_noch` WHERE `month`='$month' AND `year` = '$year' ORDER BY `day`";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //Раскидываем в массив
//                        array_push($tabels_j, $arr);

                        if (!isset($tabels_j[$arr['day']])) {
                            $tabels_j[$arr['day']] = array();
                        }
                        if (!isset($tabels_j[$arr['day']][$arr['filial_id']])) {
                            $tabels_j[$arr['day']][$arr['filial_id']] = array();
                        }
                        if (!isset($tabels_j[$arr['day']][$arr['filial_id']][$arr['worker_id']])) {
                            $tabels_j[$arr['day']][$arr['filial_id']][$arr['worker_id']] = array();
                        }
                        //array_push($tabels_j[$arr['day']][$arr['filial_id']][$arr['worker_id']], $arr);
                        $tabels_j[$arr['day']][$arr['filial_id']][$arr['worker_id']] = $arr;
                    }
                }
                //var_dump($tabels_j);


                //Табличка с данными графика работы
                echo '
                        <table style="table-layout: fixed; border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                            <tr class="<!--sticky f-sticky-->">
                                <td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Дата</i></b></td>
                                <td style="width: 90px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Сумма, руб.</i></td>
                                <td style="width: 260px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>ФИО</i></td>
                                <!--<td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>% от выручки</i></td>-->
                                <td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>К выплате, руб.</i></td>
                                <td style="width: 260px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>Ассистент</i></td>
                                <!--<td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>% от выручки</i></td>-->
                                <td style="width: 70px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;"><i>К выплате, руб.</i></td>
                                <!--<td style="width: 30px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;">-</td>-->
                                ';
                echo '
                            </tr>';


                if (!empty($rezultShed)) {

                    //Просто маркер для блоков показать/скрыть наряды
                    $markerInd = 0;

                    //!!!!! ID категорий, суммы которых мы вычтем из общей суммы потом, типа орто и кт
                    //!!! должно быть не тут
                    $minus_ids_arr = [58,59,61,62];

                    //Выводим дни, в которые у нас врачи были по графику
                    foreach ($rezultShed as $day => $day_data) {

                        if ((int)$day < 10) $day = '0'.(int)$day;
                        //if ((int)$month < 10) $month = '0'.(int)$month;

                        echo '
                            <!--<tr class="cellsBlockHover workerItem" worker_id="$worker_data[id]" style="$bgColor">-->
                            <tr class="workerItem" worker_id="" style="box-shadow: 3px -2px 2px rgba(125, 125, 125, 0.27);">
                                <td colspan="8" style="background-color:rgba(234, 253, 194, 0.22); border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">
                                    '.$day.'.'.$month.'.'.$year.'
                                </td>
                            </tr>';


                        foreach ($day_data as $filial_id => $filial_data) {

                            //Выбрать ассистов по графику
                            //!!! попробовать переделать это потом, чтоб данные сразу брались в первом запросе
                            $assist_j = array();

                            $query = "SELECT `worker` FROM `scheduler` WHERE `type` = '7' AND `day` = '$day' AND `month` = '$month' AND `year` = '$year' AND `filial`='{$filial_id}'";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0) {
                                while ($arr = mysqli_fetch_assoc($res)) {
                                    if (!array_key_exists($arr['worker'], $assist_j)) {
                                        $assist_j[$arr['worker']] = WriteSearchUser('spr_workers', $arr['worker'], 'user_full', false);
                                    }
                                }
                            }
                            //var_dump($day . '.' . $month . '.' . $year);
                            //var_dump($assist_j);

                            foreach ($filial_data as $worker_id => $worker_data) {

                                //Сумма для вычета
                                $summ_minus = 0;

                                //var_dump($worker_data);

                                $rezultInvoices = showInvoiceDivRezult($worker_data, true, true, true, false, false, false);
                                //var_dump($rezultInvoices);

                                $summ = 0;
                                $bgColor = '';
                                $notInShedStr = '';

                                //$calculate_ids_arr = array();
                                $invoice_ids_arr = array();

                                foreach ($worker_data as $data) {
                                    //var_dump($data);
                                    //var_dump($data['in_shed']);

                                    //Сумма рассчетников, сумму которых мы берем в рассчет
                                    //Если исполнитель в расчетнике соответствует врачу $worker_id
//                                    if ($worker_id == $data['calc_worker_id']) {
//                                        $summ += $data['summ_inv'];
//                                    }


                                    //Выберем позиции нарядов, чтобы вычесть орто и кт из общей выручки потом
                                    $query = "SELECT `itog_price`, `percent_cats` FROM `journal_invoice_ex` WHERE `invoice_id` = '{$data['id']}'";
                                    //var_dump($query);

                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                    $number = mysqli_num_rows($res);
                                    if ($number != 0) {
                                        while ($arr = mysqli_fetch_assoc($res)) {
                                            if (in_array($arr['percent_cats'], $minus_ids_arr)){
                                                $summ_minus += $arr['itog_price'];
                                            }
                                        }
                                    }
                                    //var_dump($summ_minus);

                                    //Сумма нарядов
                                    $summ += $data['summ'] + $data['summins'];

                                    //var_dump($data['in_shed']);
                                    if (!$data['in_shed']){
                                        $bgColor = 'background-color: rgba(255, 85, 85, 0.25);';
                                        $notInShedStr = '<span style="color: red; font-size: 85%;">Ошибка #47. Нет в графике</span>';
                                    }

                                    //ID РЛов
//                                    if ($worker_id == $data['calc_worker_id']) {
//                                        array_push($calculate_ids_arr, $data['calculate_id']);
//                                    }
                                    //ID нарядов
                                    array_push($invoice_ids_arr, $data['id']);

                                }
                                //var_dump($calculate_ids_arr);
                                //var_dump($invoice_ids_arr);

                                //Вычитаем лишнее из суммы рассчета
                                $summ = $summ - $summ_minus;

                                //ЗП врача
                                $docZP = $summ/100 * 40;

                                if ($docZP < 1000){
                                    $docZP = 1000;
                                }

                                $docZP = number_format($docZP, 0, '.', '');

                                //Смотрим, оформляли ли этот день уже в табель
                                //$tabels_j[$arr['day']][$arr['filial_id']][$arr['worker_id']]
                                if (isset($tabels_j[$day][$filial_id][$worker_id])){
                                    //var_dump($tabels_j[$day][$filial_id][$worker_id]);

                                    $tabel_mark = "
                                        <div style='display: inline-block; float: right; cursor: pointer;'>
                                            <a href='fl_tabel.php?id=" . $tabels_j[$day][$filial_id][$worker_id]['tabel_id'] . "' class='ahref'><i class='fa fa-file-text' aria-hidden='true' style='color: rgba(215, 34, 236, 0.98); font-size: 130%;' title='Табель не проведён'></i></a>
                                        </div>";
                                }else{
                                    $tabel_mark = "
                                        <div style='display: inline-block; float: right; cursor: pointer; /*border: 1px solid #CCC;*/' onclick='fl_addReportNoch({$day}, {$month}, {$year}, 5, {$worker_id}, {$filial_id}, {$summ}, {$docZP}, ".json_encode($invoice_ids_arr).");'>
                                            <i class='fa fa-times'' aria-hidden='true' style='color: rgb(187, 185, 185); font-size: 130%;'></i>
                                        </div>";
                                }



                                echo '
                                            <tr class="workerItem" worker_id="" style="box-shadow: 3px -2px 2px rgba(125, 125, 125, 0.27); '.$bgColor.'">
                                                <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px;">
                                                    '.$notInShedStr.'
                                                </td>';

                                //Выручка
                                echo '
                                                <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
                                                    ' . $summ . '
                                                </td>';

                                echo "
                                                <td style='/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px;'>
                                                    <div style='margin-bottom: 10px;'>
                                                        <div style='font-weight: bold; display: inline-block; /*border: 1px solid #CCC;*/'>
                                                            " . WriteSearchUser('spr_workers', $worker_id, 'user_full', false) . "
                                                        </div>";

                                //Отметка про табель
                                echo $tabel_mark;

                                echo "
                                                    </div>
                                                    <div>
                                                        <span style=''><a href='zapis.php?filial=".$filial_id."&who=stom&d=".$day."&m=".$month."&y=".$year."#tabs-3' class='ahref button_tiny'>К записи</a></span>
                                                    </div>
                                                </td>";

                                //% от выручки
//                                echo '
//                                               <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
//                                                    %
//                                               </td>';

                                //Итого
                                //ЗП врача
                                echo '
                                               <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; font-weight: bold;">
                                                    '.$docZP.'
                                               </td>';

                                //Ассистент
                                echo '
                                               <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding-left: 5px; text-align: left;" colspan="3">';
                                if (!empty($assist_j)) {
                                    echo '
                                                    <table style="">';
                                    foreach ($assist_j as $assist_id => $full_name) {
                                        //Итого
                                        //ЗП ассистента
                                        $assistZP = ($summ - $summ/100 * 6)/100 * 12 + 500;

                                        $assistZP = number_format($assistZP, 0, '.', '');

                                        //Смотрим, оформляли ли этот день уже в табель?
                                        //$tabels_j[$arr['day']][$arr['filial_id']][$arr['worker_id']]
                                        if (isset($tabels_j[$day][$filial_id][$assist_id])){
                                            //var_dump($tabels_j[$day][$filial_id][$assist_id]);

                                            $tabel_mark = "
                                                <div style='display: inline-block; float: right; cursor: pointer;'>
                                                    <a href='fl_tabel.php?id=" . $tabels_j[$day][$filial_id][$assist_id]['tabel_id'] . "' class='ahref'><i class='fa fa-file-text' aria-hidden='true' style='color: rgba(215, 34, 236, 0.98); font-size: 130%;' title='Табель не проведён'></i></a>
                                                </div>";
                                        }else{
                                            $tabel_mark = "
                                                <div style='display: inline-block; float: right; cursor: pointer; /*border: 1px solid #CCC;*/' onclick='fl_addReportNoch({$day}, {$month}, {$year}, 7, {$assist_id}, {$filial_id}, {$summ}, {$assistZP}, ".json_encode($invoice_ids_arr).");'>
                                                    <i class='fa fa-times'' aria-hidden='true' style='color: rgb(187, 185, 185); font-size: 130%;'></i>
                                                </div>";
                                        }

                                        echo "
                                                        <tr class=''>
                                                            <td style='width: 276px; border-bottom: 1px solid #BFBCB5; padding-left: 5px; text-align: left; font-weight: bold;'>
                                                                <div style='font-weight: bold; display: inline-block; /*border: 1px solid #CCC;*/'>
                                                                    " . $full_name . "
                                                                </div>";

                                        //Отметка про табель
                                        echo $tabel_mark;

                                        echo "
                                                            </td>";
//                                        //% от выручки
//                                        echo '
//                                                            <td style="width: 70px; border-bottom: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
//                                                                %
//                                                            </td>';

                                        //Итого
                                        //ЗП ассистента
                                        echo '
                                                            <td style="width: 70px; border-bottom: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; font-weight: bold;">
                                                                '.$assistZP.'
                                                            </td>';
                                        echo '
                                                        </tr>';
                                    }

                                    echo '
                                                    </table>';
                                }else{
                                    echo '<span style="color: red; font-size: 85%;">Нет ассистентов в графике</span>';
                                }
                                echo'
                                                </td>';


                                //---
//                                        echo '
//                                               <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
//
//                                               </td>';


                                echo '
                                            </tr>';

                                echo '
                                            <tr class="workerItem" worker_id="" style="' . $bgColor . ' box-shadow: 2px 1px 4px rgba(125, 125, 125, 0.27);">   
                                                <td style="/*border-top: 1px solid #BFBCB5;*/ border-left: 1px solid #BFBCB5; padding: 5px;" colspan="8">';

//                                if (!empty($calculate_ids_arr)){
//                                    echo '
//                                                    <div id="allCalculatesIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allCalculatesIsHere_'.$markerInd.'\');">показать/скрыть РЛ</div>
//                                                    <div id="allCalculatesIsHere_'.$markerInd.'" style="display: none;">';
//                                    foreach ($calculate_ids_arr as $calculate_id) {
//                                        echo '
//                                                        <div class="cellsBlockHover calculateBlockItem" style="width: 217px; display: inline; background-color: #FFF; border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
//                                                            <div style="display: inline-block; width: 190px;">
//                                                                <div>
//                                                                    <a href="fl_calculate.php?id=' . $calculate_id . '" class="ahref">
//                                                                    <div>
//                                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
//                                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
//                                                                    </div>
//                                                                    <div style="display: inline-block; vertical-align: middle;">
//                                                                        <b>#' . $calculate_id . '</b> <span style="font-size: 70%; color: rgb(115, 112, 112);"></span>
//                                                                    </div>
//                                                                </div>
//                                                            </div>
//                                                        </div>';
//                                    }
//
//                                    echo '
//                                                    </div>';
//                                }else{
//                                    echo '<span style="color: red;">нет закрытых работ</span>';
//                                }
                                if ($rezultInvoices['count'] > 0) {
                                    echo '
                                                    <div id="allINvoicesIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allINvoicesIsHere_'.$markerInd.'\');">показать/скрыть наряды</div>
                                                    <div id="allINvoicesIsHere_'.$markerInd.'" style="display: none;">
                                                    ' . $rezultInvoices['data'] . '
                                                    </div>';
                                }else{
                                    echo '<span style="color: red;">нет нарядов</span>';
                                }
                                echo '
                                                </td>
                                            </tr>';

                                $markerInd++;
                            }

                            $markerInd++;
                        }

                        $markerInd++;
                    }
                }

                echo '
                        </table>';

                echo '
                    </div>';

                echo '	
                    <!-- Подложка только одна -->
                    <div id="overlay"></div>';

                echo '
		            <div id="doc_title">Важный отчёт ночь - Асмедика</div>';



				/*echo '

				<script type="text/javascript">

				$(document).ready(function() {
				    //Соберём выручку филиала
                    //fl_calculateZP ('.$month.', '.$year.',0);
				    //setTimeout(fl_getAllTabels ('.$month.', '.$year.', '.$type.'), 7000);
				    
				    
                    wait(function(runNext){

                        setTimeout(function(){
            
                            fl_calculateZP ('.$month.', '.$year.', '.$type.');
            
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
				
                
				</script>';*/
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