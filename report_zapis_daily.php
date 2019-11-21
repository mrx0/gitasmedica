<?php

//report_zapis_daily.php
//Отчёт для ежедневной сверки

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'ffun.php';
            require 'variables.php';

            $have_target_filial = false;
            $href_str = '';

            $filials_j = getAllFilials(false, false, false);
            $permissions_j = getAllPermissions(false, true);
            //var_dump($permissions_j);

            //Дата
            //операции со временем
            $day = date('d');
            $month = date('m');
            $year = date('Y');

            //Или если мы смотрим другой месяц
            if (isset($_GET['m']) && isset($_GET['y']) && isset($_GET['d'])) {
                $day = $_GET['d'];
                $month = $_GET['m'];
                $year = $_GET['y'];
            }

            //Филиал
            if (isset($_SESSION['filial'])) {
                $filial_id = $_SESSION['filial'];
                $have_target_filial = true;
            } else {
                $filial_id = 15;
                $have_target_filial = true;
            }

            if (($finances['see_all'] == 1) || $god_mode) {
                if (isset($_GET['filial_id'])) {
                    $filial_id = $_GET['filial_id'];
                    $have_target_filial = true;
                }
            }

            if ($have_target_filial) {
                $href_str = '?filial_id=' . $filial_id . '&d=' . $day . '&m=' . $month . '&y=' . $year;
                //var_dump($href_str);
            }

            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                            <!--<a href="" class="b"></a>-->
                        </div>
                        <h2 style="">Отчёт - сверка</h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';

            //Выбор филиала
            echo '
                        <div style="font-size: 90%; margin-bottom: 20px;">
                            Филиал: ';

            if (($finances['see_all'] == 1) || $god_mode) {

                echo '
                            <select name="SelectFilial" id="SelectFilial">';

                foreach ($filials_j as $filial_item) {

                    $selected = '';

                    if ($filial_id == $filial_item['id']) {
                        $selected = 'selected';
                    }

                    echo '
                                <option value="' . $filial_item['id'] . '" ' . $selected . '>' . $filial_item['name'] . '</option>';
                }

                echo '
                            </select>';
            } else {

                echo $filials_j[$_SESSION['filial']]['name'] . '<input type="hidden" id="SelectFilial" name="SelectFilial" value="' . $_SESSION['filial'] . '">';

            }


            echo '
                        </div>';

            if ($filial_id > 0) {
                //Календарик
                echo '
	
                        <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: left; margin-bottom: 10px;">
                            <div style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="report_zapis_daily.php?filial_id=' . $filial_id . '" class="ahref">' . date("d") . ' ' . $monthsName[date("m")] . ' ' . date("Y") . '</a></div>
                            <div>
                                <span style="color: rgb(125, 125, 125);">
                                    Изменить дату:
                                    <a href="report_zapis_daily.php?&filial_id='.$filial_id.'&d='.explode('.', date('d.m.Y', strtotime('-1 days', gmmktime(0, 0, 0, $month, $day, $year))))[0].'&m='.explode('.', date('d.m.Y', strtotime('-1 days', gmmktime(0, 0, 0, $month, $day, $year))))[1].'&y='.explode('.', date('d.m.Y', strtotime('-1 days', gmmktime(0, 0, 0, $month, $day, $year))))[2].'" class="b4" title="Пред. день"><i class="fa fa-caret-left" aria-hidden="true"></i></a>
                                    <a href="report_zapis_daily.php?&filial_id='.$filial_id.'&d='.explode('.', date('d.m.Y', strtotime('+1 days', gmmktime(0, 0, 0, $month, $day, $year))))[0].'&m='.explode('.', date('d.m.Y', strtotime('+1 days', gmmktime(0, 0, 0, $month, $day, $year))))[1].'&y='.explode('.', date('d.m.Y', strtotime('+1 days', gmmktime(0, 0, 0, $month, $day, $year))))[2].'" class="b4" title="След. день"><i class="fa fa-caret-right" aria-hidden="true"></i></a>
                                    <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.$day.'.'.$month.'.'.$year.'" onfocus="this.select();_Calendar.lcs(this)" 
                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off"> 
                                    <span class="button_tiny" style="font-size: 100%; cursor: pointer" onclick="iWantThisDate2(\'report_zapis_daily.php?filial_id=' . $filial_id . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
                                </span>
                            </div>
                        </li>';
            }

            //Если определён филиал
            if ($have_target_filial) {

                $rezult = array();

                $msql_cnnct = ConnectToDB ();

                //запись
                $zapis_j = array();
                //наряды
                $invoice_j = array();

                $query = "SELECT * FROM `zapis` WHERE `office`='{$filial_id}' AND `year` = '{$year}' AND `month` = '{$month}'  AND `day` = '{$day}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `start_time` ASC";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        //var_dump($arr);

                        //array_push($zapis_j, $arr);
                        if (!isset($zapis_j[$arr['type']])){
                            $zapis_j[$arr['type']] = array();
                        }
                        if (!isset($zapis_j[$arr['type']][$arr['worker']])){
                            $zapis_j[$arr['type']][$arr['worker']] = array();
                        }
                        array_push($zapis_j[$arr['type']][$arr['worker']], $arr);

                        //Наряды (к записи которые) сразу выберем
                        $query1 = "
                          SELECT ji.*, GROUP_CONCAT(DISTINCT ji_ex.percent_cats ORDER BY ji_ex.percent_cats ASC SEPARATOR ',') AS percent_cats
                          FROM `journal_invoice` ji
                          LEFT JOIN `journal_invoice_ex` ji_ex ON ji.id = ji_ex.invoice_id
                          WHERE ji.zapis_id = '{$arr['id']}' AND ji.status <> '9'";


                        $res1 = mysqli_query($msql_cnnct, $query1) or die(mysqli_error($msql_cnnct).' -> '.$query1);

                        $number = mysqli_num_rows($res1);
                        if ($number != 0) {
                            while ($arr1 = mysqli_fetch_assoc($res1)) {
                                //var_dump($arr1);

                                if ($arr1['id'] != null) {
                                    if (!isset($order_j[$arr['id']])) {
                                        $invoice_j[$arr['id']] = array();
                                    }
                                    array_push($invoice_j[$arr['id']], $arr1);
                                }
                                //оплаты по этим нарядам
                                //!!! не доделано, доделать  ?
//                                SELECT *
//                                FROM `journal_payment`
                            }
                        }
                        //var_dump($invoice_j);
                    }
                }
                //var_dump($zapis_j);

                //Ордеры
                $order_j = array();

                $query = "SELECT * FROM `journal_order` WHERE `office_id`='{$filial_id}' AND YEAR(`date_in`) = '{$year}' AND MONTH(`date_in`) = '{$month}'  AND DAY(`date_in`) = '{$day}' AND `status` <> 9";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        //array_push($order_j, $arr);
                        if (!isset($order_j[$arr['client_id']])){
                            $order_j[$arr['client_id']] = array();
                        }
//                        if (!isset($zapis_j[$arr['type']][$arr['worker']])){
//                            $zapis_j[$arr['type']][$arr['worker']] = array();
//                        }
                        array_push($order_j[$arr['client_id']], $arr);
                    }
                }
                //var_dump($order_j);

                echo '<table border="0" style="border: 1px solid #e0e0e0;">';

                if (!empty($zapis_j)){

                    //Категории процентов
                    $percent_cats_j = array();
                    $query = "SELECT `id`, `name` FROM `fl_spr_percents`";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            $percent_cats_j[$arr['id']] = $arr['name'];
                        }
                    }
                    //var_dump($percent_cats_j);

                    // !!! **** тест с записью
                    //include_once 'showZapisRezult.php';

                    foreach ($zapis_j as $type => $type_data){
                        //var_dump($type_data);

                        echo '
                            <tr style="">
                                <td colspan="6" style="padding-top: 10px; border: 1px solid #e0e0e0;">'.$permissions_j[$type]['name'].'</td>
                            </tr>';
                        foreach ($type_data as $worker_id => $worker_zapis_data) {
                            //var_dump($worker_zapis_data);

                            echo '
                            <tr>
                                <td colspan="6" style="font-style: italic; font-weight: bold; color: rgb(36, 59, 158); border: 1px solid #e0e0e0;">'.WriteSearchUser('spr_workers', $worker_id, 'user', false).'</td>
                            </tr>';

                            foreach ($worker_zapis_data as $item) {
                                //Время начала - конца приема
                                $start_time_h = floor($item['start_time'] / 60);
                                $start_time_m = $item['start_time'] % 60;
                                if ($start_time_m < 10) $start_time_m = '0' . $start_time_m;
                                $end_time_h = floor(($item['start_time'] + $item['wt']) / 60);
                                if ($end_time_h > 23) $end_time_h = $end_time_h - 24;
                                $end_time_m = ($item['start_time'] + $item['wt']) % 60;
                                if ($end_time_m < 10) $end_time_m = '0' . $end_time_m;

                                echo '
                                <tr>
                                    <td style="width: 150px; padding: 5px; font-size: 80%; border: 1px solid #e0e0e0;"><i>'.$start_time_h . ':' . $start_time_m . ' - ' . $end_time_h . ':' . $end_time_m.'</i><br>
                                        <span style="font-size: 90%;">Пациент:</span><br>
                                        <span style="font-size: 120%;"><b>'.WriteSearchUser('spr_clients', $item['patient'], 'user', false).'</b></span>
                                    </td>
                                    <td style="width: 180px; padding: 5px; border: 1px solid #e0e0e0;">
                                        <span style="font-size: 80%;">Внесённые средства</span>';

                                //Если есть ордеры этого человека
                                if (isset($order_j[$item['patient']])){
                                    echo '
                                    <ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">';

                                    //Выведем их
                                    foreach ($order_j[$item['patient']] as $order_item){

                                        $order_type_mark = '';

                                        if ($order_item['summ_type'] == 1) {
                                            $order_type_mark = '<i class="fa fa-money" aria-hidden="true" title="Нал" style="font-size: 15px; color: darkgreen;"></i>';
                                        }

                                        if ($order_item['summ_type'] == 2) {
                                            $order_type_mark = '<i class="fa fa-credit-card" aria-hidden="true" title="Безнал" style="font-size: 15px; color: dodgerblue;"></i>';
                                        }

                                        echo '
                                        <li class="cellsBlock" style="width: auto; position: relative;">
                                            <div class="cellName">
                                                <div href="order.php?id='.$order_item['id'].'" class="" style="margin-bottom: 4px;">
                                                    '.$order_type_mark.' <b>Ордер #'.$order_item['id'].'</b><br>
                                                </div>
                                                <div style="border-top: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                    Сумма:<br>
                                                    <span class="calculateOrder" style="font-size: 13px">'.$order_item['summ'].'</span> руб.
                                                </div>
                                            </div>
                                            <!--<span style="position: absolute; top: 2px; right: 5px;">'. $order_type_mark.'</span>-->
                                        </li>';
                                    }
                                    echo '
                                    </ul>';

                                }else{
                                    echo '<br><span style="color: red; font-style: italic;">не вносилось</span>';
                                }

                                echo '
                                    </td>
                                    <td style="width: 180px; padding: 5px; border: 1px solid #e0e0e0;">
                                        <span style="font-size: 80%;">Наряды</span>';

                                if (!empty($invoice_j)){
                                    if (isset($invoice_j[$item['id']])){
                                        echo '
                                        <ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">';
                                        foreach ($invoice_j[$item['id']] as $invoice_item) {
                                            echo '
                                            <!--<div class="cellsBlockHover calculateBlockItem" worker_mark="" style=" /*width: 217px;*/ display: inline-block; border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">-->
                                            <li class="cellsBlock" style="width: auto; position: relative;">
                                                <div class="cellName">
                                                    <div style="display: inline-block; /*width: 190px;*/">
                                                        <div>
                                                            <a href="invoice.php?id=' . $invoice_item['id'] . '" class="ahref">
                                                                <div>
                                                                    <div style="display: inline-block; vertical-align: middle; font-size: 15px; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                                    </div>
                                                                    <div style="display: inline-block; vertical-align: middle;">
                                                                            <b>Наряд #' . $invoice_item['id'] . '</b>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                            <div>
                                                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                                    <div>
                                                                        <div style="width: 70px; display: inline-block;">Сумма:</div>
                                                                        <div style="display: inline-block;">
                                                                            <span class="calculateInvoice calculateCalculateN" style="font-size: 11px;">' . $invoice_item['summ'] . ' руб.</span>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div style="width: 70px; display: inline-block;">Оплачено:</div>
                                                                        <div style="display: inline-block;">
                                                                            <span class="calculateInvoice calculateCalculateN" style="font-size: 11px; color: #333;">' . $invoice_item['summ'] . ' руб.</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                                
                                                            
                                                        </div>
                                                        <div style="margin: 5px 0 5px 3px; font-size: 90%;">';

                                            //Категории процентов(работ)
                                            $percent_cats_arr = explode(',', $invoice_item['percent_cats']);
                                            //var_dump($percent_cats_arr);
                                            //var_dump($percent_cats_j);

                                            foreach ($percent_cats_arr as $percent_cat) {
                                                //var_dump($percent_cat);


                                                if ($percent_cat > 0) {
                                                    echo '<i style="color: rgb(15, 6, 142); font-size: 110%;">' . $percent_cats_j[$percent_cat] . '</i><br>';
                                                } else {
                                                    echo '<i style="color: red; font-size: 100%;">Ошибка #17</i><br>';
                                                }
                                            }

                                            echo '
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>';
                                        }
                                        echo '
                                        </ul>';
                                    }
                                }

                                echo '
                                    </td>
                                    <!--<td style="width: 180px; border: 1px solid #e0e0e0;">
                                        проведенные оплаты по наряду
                                    </td>-->
                                    
                                    <!--<td style="width: 180px; border: 1px solid #e0e0e0;">р/л + отметка врача</td>-->
                                </tr>';
                            }

//                            echo '
//                            <tr>
//                                <td colspan="6">';
//
//                            //echo showZapisRezult($worker_zapis_data, false, false, false, false, false, false, 0, true, false);
//
//                            echo '
//                                </td>
//                            </tr>';
                        }
                    }
                }else{
                    echo '<span style="color: red;">В записи ничего не найдено</span>';
                }


                echo '</table>';

            }

            echo '
                    </div>
                </div>
                <div id="doc_title">Расходные ордеры - Асмедика</div>';

            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';
			echo '

				<script type="text/javascript">

                    $(function() {
                        $("#SelectFilial").change(function(){
                            
                            blockWhileWaiting (true);
                            
                            var get_data_str = "";
                            
                            var params = window
                                .location
                                .search
                                .replace("?","")
                                .split("&")
                                .reduce(
                                    function(p,e){
                                        var a = e.split(\'=\');
                                        p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                                        return p;
                                    },
                                    {}
                                );
                            //console.log(params);
                                                            
                            for (key in params) {
                                //console.log(key.length);  
                                                              
                                if (key.length > 0){
                                    if (key.indexOf("filial_id") == -1){
                                        get_data_str = get_data_str + "&" + key + "=" + params[key];
                                    }
                                }
                            }
                            //console.log(get_data_str);
                            
                            document.location.href = "?filial_id="+$(this).val() + "&" + get_data_str;
                        });
                    });
                    
                    			
                
				</script>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>