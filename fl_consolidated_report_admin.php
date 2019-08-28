<?php

//fl_consolidated_report_admin.php
//Сводный отчёт админов

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'ffun.php';
            require 'variables.php';

            //Опция доступа к филиалам конкретных сотрудников
            $optionsWF = getOptionsWorkerFilial($_SESSION['id']);
            //var_dump($optionsWF);

            $have_target_filial = true;

            $filials_j = getAllFilials(false, false, false);
            //var_dump($filials_j);

            //$msql_cnnct = ConnectToDB ();

            //Дата
            if (isset($_GET['m']) && isset($_GET['y'])){
                //операции со временем
                $month = $_GET['m'];
                $year = $_GET['y'];
            }else{
                //операции со временем
                $month = date('m');
                $year = date('Y');
            }
            $day = date("d");

            //Или если мы смотрим другой месяц
//            if (isset($_GET['m'])) {
//                $m = $_GET['m'];
//            }
//            if (isset($_GET['y'])) {
//                $y = $_GET['y'];
//            }

            //Филиал
            if (isset($_GET['filial_id'])) {
                $filial_id = $_GET['filial_id'];
            }else{
                if (isset($_SESSION['filial'])) {
                    $filial_id = $_SESSION['filial'];
                }else{
                    if (($finances['see_all'] == 1) || $god_mode){
                        $have_target_filial = true;
                        $filial_id = 15;
                    }else {
                        $have_target_filial = false;
                        $filial_id = 0;
                    }
                }
            }


            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                            <a href="stat_cashbox.php" class="b">Касса</a>';
            if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){
                echo '
                <a href="fl_paidout_another_test_in_tabel_add.php" class="b">Добавить расход вручную</a>';
            }

            echo '
                        </div>
                        <h2 style="padding: 0;">Сводный отчёт по филиалу</h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';

            //Выбор филиала
            echo '
                        <div style="font-size: 90%; ">
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

            //Выбор месяц и год
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата: ';
            echo '
			                <select name="iWantThisMonth" id="iWantThisMonth" style="margin-right: 5px;">';
            foreach ($monthsName as $mNumber => $mName){
                $selected = '';
                if ((int)$mNumber == (int)$month){
                    $selected = 'selected';
                }
                echo '
				                <option value="'.$mNumber.'" '.$selected.'>'.$mName.'</option>';
            }
            echo '
			                </select>
			                <select name="iWantThisYear" id="iWantThisYear">';
            for ($i = 2017; $i <= (int)date('Y')+2; $i++){
                $selected = '';
                if ($i == (int)date('Y')){
                    $selected = 'selected';
                }
                echo '
				                <option value="'.$i.'" '.$selected.'>'.$i.'</option>';
            }
            echo '
			                </select>
			                <span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick="iWantThisDate(\'fl_consolidated_report_admin.php?filial_id='. $filial_id . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
			                <div style="font-size: 90%; color: rgb(125, 125, 125); float: right;">Сегодня: <a href="fl_consolidated_report_admin.php" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></div>
			            </div>';

            //Если определён филиал
            if ($have_target_filial) {

                //Количество дней в месяце
                $month_stamp = mktime(0, 0, 0, $month, 1, $year);
                $day_count = date("t", $month_stamp);

                //или так
                //$day_count = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                $report_header = '';

                echo '
			    <div id="report" class="report" style="margin-top: 10px;">';
                echo '
                    <ul class="live_filter" id="livefilter-list" style="margin-left:6px; background-color: #FFF;">';

                $report_header .= '
                        <li class="cellsBlock" style="font-weight:bold;">';
                $report_header .= ' 
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Дата
                            </div>';

                if (($finances['see_all'] == 1) || $god_mode){
                    $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Итог
                            </div>';

                    $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Итог нал
                            </div>';
                }

                if (($finances['see_all'] == 1) || $god_mode) {
                    $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Аренда
                            </div>';
                }

                $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Z-отчёт
                            </div>';

                $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5; /*display: none !important;*/">
                                Всего в кассе
                            </div>';

//                $report_header .= '
//                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
//                                Наличных в кассе
//                            </div>';

                $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Всего нал
                            </div>';

                $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Всего безнал
                            </div>';
                /*$report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Наличные ордеры
                            </div>';*/
                /*$report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Безнал. ордеры
                            </div>';*/
                $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Серт-ты<br>нал
                            </div>';
                $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Серт-ты безнал
                            </div>';
                /*$report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18); border-top: 1px solid #BFBCB5;">
                                ОРТО<br>нал
                            </div>';*/
                /*$report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center;  background-color: rgba(63, 0, 255, 0.18); border-top: 1px solid #BFBCB5;">
                                ОРТО<br>безнал
                            </div>';*/
                /*$report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                ? ОРТО<br>кол-во
                            </div>';*/
               /* $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18); border-top: 1px solid #BFBCB5;">
                                Спец-ты<br>нал
                            </div>';*/
                /*$report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18); border-top: 1px solid #BFBCB5;">
                                Спец-ты безнал
                            </div>';*/
                /*$report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18); border-top: 1px solid #BFBCB5;">
                                Анализы нал
                            </div>';*/
                /*$report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18); border-top: 1px solid #BFBCB5;">
                                Анализы безнал
                            </div>';*/
                /*$report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18); border-top: 1px solid #BFBCB5;">
                                Солярий<br>нал
                            </div>';*/
                /*$report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18); border-top: 1px solid #BFBCB5;">
                                Солярий<br>безнал
                            </div>';*/
                $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(255,137,28,0.18); border-top: 1px solid #BFBCB5;">
                                Расход
                            </div>';
                if (($finances['see_all'] == 1) || $god_mode) {
                    $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Банк
                            </div>';
                    $report_header .= '
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                АН
                            </div>';
                }

                $report_header .= ' 
                            <div class="cellTime cellsTimereport" style="text-align: center; border-top: 1px solid #BFBCB5;">
                                Дата
                            </div>';

                $report_header .= '
                            <div class="cellText">
                            </div>';

                $report_header .= '
                        </li>';


                echo $report_header;

                //С первого дня месяца по последний
                for ($d = 1; $d <= $day_count; $d++) {
                    //приводим дату в норм вид
                    $data = dateTransformation($d) . '.' . dateTransformation($month) . '.' . $year;
                    //день недели
                    $week_day = date("w", strtotime($year . '-' . $month . '-' . $d));
                    //var_dump($dayWeek_arr[$week_day]);

                    $weekend_block = 'cellsBlock';
                    $today_color = 'color: #333;';
                    $today_outline = '';
                    $today_border = '';
                    $today_border_l = '';

                    //цвет дня на выходных
                    if (($week_day == 6) || ($week_day == 0)) {
                        $weekend_block = 'cellsBlock6';
                        //$today_color = ' outline: 1px solid red;';
                        $today_color = 'color: rgb(255, 18, 246);';

                        if ($week_day == 6){
                            $today_border = 'border-top: 1px solid rgb(255, 18, 246);';
                            $today_border_l = 'border-left: 1px solid rgb(255, 18, 246); ';
                        }else{
                            $today_border = 'border-bottom: 1px solid rgb(255, 18, 246);';
                            $today_border_l = 'border-left: 1px solid rgb(255, 18, 246);';
                        }
                    }

                    //Цвет текущего дня
                    if ($data === date('d') . '.' . date('m') . '.' . date('Y')) {
                        //$today_color = 'background-color: green;';
                        $today_outline = ' outline: 1px solid red; background-color: rgba(255, 243, 77, 0.47);';
                    }
                    //var_dump($data);
                    //var_dump(date('d') . '.' . date('m') . '.' . date('Y'));

                    echo '
                        <li class="' . $weekend_block . ' cellsBlockHover blockControl" style="font-weight: bold; font-size: 12px; color: #949393; ' . $today_outline . '">';
                    echo '
                            <div class="cellTime cellsTimereport reportDate" status="4" report_id="0" filial_id="'.$filial_id.'" style="text-align: center; cursor: pointer; ' . $today_color . ' '. $today_border .''. $today_border_l .'">
                                ' . $data . '
                            </div>';

                    if (($finances['see_all'] == 1) || $god_mode){
                        echo '
                            <div class="cellTime cellsTimereport itogSumm" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';
                        echo '
                            <div class="cellTime cellsTimereport itogSummNal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';
                    }

                    if (($finances['see_all'] == 1) || $god_mode) {
                        echo '
                            <div class="cellTime cellsTimereport arenda" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';
                    }

                    echo '
                            <div class="cellTime cellsTimereport zReport" style="text-align: center; font-weight: normal;'. $today_border .'">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport allSumm" style="text-align: center; font-weight: normal; '. $today_border .' /*display: none !important;*/">
                                -
                            </div>';

//                    echo '
//                            <div class="cellTime cellsTimereport SummNalOstatok" style="text-align: center; font-weight: normal; '. $today_border .'">
//                                -
//                            </div>';

                    echo '
                            <div class="cellTime cellsTimereport SummNal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport SummBeznal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';
                    /*echo '
                            <div class="cellTime cellsTimereport SummNalStomCosm" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';*/
                    /*echo '
                            <div class="cellTime cellsTimereport SummBeznalStomCosm" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';*/
                    echo '
                            <div class="cellTime cellsTimereport SummCertNal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport SummCertBeznal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';
                    /*echo '
                            <div class="cellTime cellsTimereport ortoSummNal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport ortoSummBeznal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';*/
                    /*echo '
                            <div class="cellTime cellsTimereport " style="text-align: center; font-weight: normal;">
                                
                            </div>';*/
                    /*echo '
                            <div class="cellTime cellsTimereport specialistSummNal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport specialistSummBeznal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';*/
                    /*echo '
                            <div class="cellTime cellsTimereport analizSummNal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport analizSummBeznal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';*/
                    /*echo '
                            <div class="cellTime cellsTimereport solarSummNal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';*/
                    /*echo '
                            <div class="cellTime cellsTimereport solarSummBeznal" style="text-align: center; font-weight: normal; '. $today_border .'">
                                -
                            </div>';*/
                    echo '
                            <a href="giveout_cash.php?filial_id='.$filial_id.'&d='.dateTransformation($d).'&m='.dateTransformation($month).'&y='.$year.'" class="ahref cellTime cellsTimereport summMinusNal" style="text-align: center; font-weight: normal; cursor: pointer; '. $today_border .'">
                                -
                            </a>';
                    if (($finances['see_all'] == 1) || $god_mode) {
                        echo '
                            <a href="fl_in_bank_add.php?filial_id='.$filial_id.'&d='.dateTransformation($d).'&m='.dateTransformation($month).'&y='.$year.'" class="ahref cellTime cellsTimereport giveout_inBank" style="text-align: center; font-weight: normal; ' . $today_border . '">
                                -
                            </a>';
                        echo '
                            <a href="fl_to_director_add.php?filial_id='.$filial_id.'&d='.dateTransformation($d).'&m='.dateTransformation($month).'&y='.$year.'" class="ahref cellTime cellsTimereport giveout_director" style="text-align: center; font-weight: normal; ' . $today_border . '">
                                -
                            </a>';
                    }

                    echo '
                            <div class="cellTime cellsTimereport" style="text-align: center; cursor: pointer; ' . $today_color . ' '. $today_border .''. $today_border_l .'">
                                ' . $data . '
                            </div>';

                    echo '
                            <div class="cellText" style="text-align: center; font-weight: normal; '. $today_border .'">
                            </div>';
                    echo '
                        </li>';

                }

                if (($finances['see_all'] == 1) || $god_mode) {
                    echo '
                        <li class="' . $weekend_block . ' cellsBlockHover" style="font-weight: bold; font-size: 12px; background-color: rgb(255, 193, 7);">';
                    echo '
                            <div class="cellTime cellsTimereport" style="text-align: right; cursor: pointer; ' . $today_color .'"
                             onclick="fl_getDailyReportsSummAllMonth('.$filial_id.', '.$month.', '.$year.');">
                                Всего
                                <span style="font-size: 50%;"></span> <i class="fa fa-refresh" aria-hidden="true" style=" color: red;"></i>
                            </div>';

                    echo '
                            <div id="itogSummAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';

                    echo '
                            <div id="itogSummNalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';

                    echo '
                            <div id="arendaAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';

                    echo '
                            <div id="zReportAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold; background-color: rgb(80, 241, 255);">
                                0
                            </div>';
                    echo '
                            <div id="allSummAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';

//                    echo '
//                            <div class="cellTime cellsTimereport SummNalOstatokAllMonth" style="text-align: center; font-weight: normal; '. $today_border .'">
//                                -
//                            </div>';

                    echo '
                            <div id="SummNalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
                    echo '
                            <div id="SummBeznalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
//                    echo '
//                            <div id="SummNalStomCosmAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
//                                0
//                            </div>';
//                    echo '
//                            <div id="SummBeznalStomCosmAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
//                                0
//                            </div>';
                    echo '
                            <div id="SummCertNalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
                    echo '
                            <div id="SummCertBeznalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
                    /*echo '
                            <div id="ortoSummNalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
                    echo '
                            <div id="ortoSummBeznalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';*/
                    /*echo '
                            <div class="cellTime cellsTimereport " style="text-align: right; font-weight: bold;">

                            </div>';*/
                    /*echo '
                            <div id="specialistSummNalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
                    echo '
                            <div id="specialistSummBeznalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';*/
                    /*echo '
                            <div id="analizSummNalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
                    echo '
                            <div id="analizSummBeznalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';*/
//                    echo '
//                            <div id="solarSummNalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
//                                0
//                            </div>';
//                    echo '
//                            <div id="solarSummBeznalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
//                                0
//                            </div>';
                    echo '
                            <div id="summMinusNalAllMonth" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
                    echo '
                            <div id="summGiveoutInBank" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
                    echo '
                            <div id="summGiveoutDirector" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
                    echo '
                            <div id="ostatokFinalNalAllMonth2" class="cellTime cellsTimereport" style="text-align: right; font-weight: bold;">
                                0
                            </div>';
                    echo '
                            <div class="cellText" style="text-align: center; font-weight: normal;">
                            </div>';
                    echo '
                        </li>';

                }

                echo $report_header;

                if (($finances['see_all'] == 1) || $god_mode) {
                    echo '
                        <li id="interimReport" class="" style="margin: 10px; font-size: 12px;/*border-right: 1px solid #BFBCB5;*/ display: none; "">';
                    echo '
                            <div style="width: 320px; display: inline-block; box-shadow: rgb(139, 139, 139) 2px 2px 6px; margin-right: 10px;">
                                <div class="cellsBlock">
                                    <div class="" style="font-size: 15px; margin: 5px;">
                                        <a href="fl_main_report2.php?filial_id='.$filial_id.'&m='.$month.'&y='.$year.'" class="ahref button_tiny">Перейти в фин. отчёт</a>
                                    </div>
                                    <div class="" style="font-size: 15px; margin: 5px; font-weight: bold;">
                                        Предварительные итоги:
                                    </div>
                                    <div class="" style="font-size: 15px; margin: 5px; font-weight: bold;">Выручка</div>
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">наличные касса</div>
                                    <div id="SummNalAllMonthItog" class="cellRight" style="text-align: right;">-</div>   
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">аренда</div>
                                    <div id="arendaAllMonthItog" class="cellRight" style="text-align: right;">-</div>    
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">безнал</div>
                                    <div id="SummBeznalAllMonthItog" class="cellRight" style="text-align: right;">-</div>    
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft" style="font-weight: bold; background-color: rgba(255, 0, 0, 0.37);">выручка вся:</div>
                                    <div id="summAllMonth" class="cellRight" style="font-weight: bold; text-align: right; background-color: rgba(255, 0, 0, 0.37);">-</div>
                                </div>
                                
                                <div class="cellsBlock">
                                    <div class="" style="font-size: 15px; margin: 5px; font-weight: bold;">Наличные</div>    
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">остаток за прошлый месяц</div>
                                    <div id="prev_month_filial_summ" class="cellRight" style="text-align: right;">-</div>
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">наличные касса</div>
                                    <div id="SummNalAllMonthItog2" class="cellRight" style="text-align: right;">-</div>
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">аренда</div>
                                    <div id="arendaAllMonthItog2" class="cellRight" style="text-align: right;">-</div>
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">расход</div>
                                    <div id="summMinusAllMonth" class="cellRight" style="text-align: right;">-</div>   
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft" style="font-weight: bold; background-color: rgba(255, 0, 0, 0.37);">остаток в кассе</div>
                                    <div id="ostatokNalAllMonth" class="cellRight" style="font-weight: bold; text-align: right; background-color: rgba(255, 0, 0, 0.37);">-</div>  
                                </div>
                            </div>';

                    //Выдачи денег в ЗП и тп
                    echo '
                            <div style="width: 320px; display: inline-block; margin-right: 10px; box-shadow: rgb(139, 139, 139) 2px 2px 6px;  vertical-align: top;">
                                <div class="cellsBlock">
                                    <!--<div class="" style="font-size: 15px; margin: 5px; font-weight: bold;"></div>-->
                                    <div class="" style="font-size: 15px; margin: 5px; font-weight: bold;">Выдано</div>
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">авансы</div>
                                    <div id="SummPrepaymentGiveout" class="cellRight" style="text-align: right;">-</div>   
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">зп</div>
                                    <div id="SummSalaryGiveout" class="cellRight" style="text-align: right;">-</div>    
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">отпускные</div>
                                    <div id="SummHolidayPayGiveout" class="cellRight" style="text-align: right;">-</div>    
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">больничные</div>
                                    <div id="SummHospitalPayGiveout" class="cellRight" style="text-align: right;">-</div>
                                </div>
                                
                                <!--<div class="cellsBlock">
                                    <div class="" style="font-size: 15px; margin: 5px; font-weight: bold;"></div>    
                                </div>-->
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">возвраты</div>
                                    <div id="SummRefundGiveout" class="cellRight" style="text-align: right;">-</div>
                                </div>
                                <!--<div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">материалы</div>
                                    <div id="SummMaterialGiveout" class="cellRight" style="text-align: right;">-</div>
                                </div>-->
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft" style="font-weight: bold;">итого</div>
                                    <div id="SummGiveoutMonth" class="cellRight" style="font-weight: bold; text-align: right;">-</div>   
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft" style="font-weight: bold; background-color: rgba(255, 0, 0, 0.37);">остаток в кассе</div>
                                    <div id="ostatokFinalNalAllMonth" class="cellRight" style="font-weight: bold; text-align: right; background-color: rgba(255, 0, 0, 0.37);">-</div>  
                                </div>
                            </div>';

                    //Расходы
                    echo '
                            <div id="giveout_cash" style="width: 520px; display: inline-block; box-shadow: rgb(139, 139, 139) 2px 2px 6px;  vertical-align: top;">
                                <!--<div class="cellsBlock">
                                    &lt;!&ndash;<div class="" style="font-size: 15px; margin: 5px; font-weight: bold;"></div>&ndash;&gt;
                                    <div class="" style="font-size: 15px; margin: 5px; font-weight: bold;">Выдано</div>
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">авансы</div>
                                    <div id="SummPrepaymentGiveout" class="cellRight" style="text-align: right;">-</div>   
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">зп</div>
                                    <div id="SummSalaryGiveout" class="cellRight" style="text-align: right;">-</div>    
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">отпускные</div>
                                    <div id="SummHolidayPayGiveout" class="cellRight" style="text-align: right;">-</div>    
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">больничные</div>
                                    <div id="SummHospitalPayGiveout" class="cellRight" style="text-align: right;">-</div>
                                </div>
                                
                                &lt;!&ndash;<div class="cellsBlock">
                                    <div class="" style="font-size: 15px; margin: 5px; font-weight: bold;"></div>    
                                </div>&ndash;&gt;
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">возвраты</div>
                                    <div id="SummRefundGiveout" class="cellRight" style="text-align: right;">-</div>
                                </div>
                                &lt;!&ndash;<div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft">материалы</div>
                                    <div id="SummMaterialGiveout" class="cellRight" style="text-align: right;">-</div>
                                </div>&ndash;&gt;
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft" style="font-weight: bold;">итого</div>
                                    <div id="SummGiveoutMonth" class="cellRight" style="font-weight: bold; text-align: right;">-</div>   
                                </div>
                                <div class="cellsBlock" style="font-size: 14px;">
                                    <div class="cellLeft" style="font-weight: bold; background-color: rgba(255, 0, 0, 0.37);">остаток в кассе</div>
                                    <div id="ostatokFinalNalAllMonth" class="cellRight" style="font-weight: bold; text-align: right; background-color: rgba(255, 0, 0, 0.37);">-</div>  
                                </div>-->
                            </div>';
                    echo '
                        </li>';

                }

                echo '
                    </ul>
			    </div>';

            }else{
                echo '
                         <span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
            }
            echo '
                    </div>
                </div>
                <div id="doc_title">Сводный отчёт - Асмедика</div>';

            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';
			echo '

				<script type="text/javascript">
				
				    //Изменили тут стили основные, чтоб умещалось
                    $(document).ready(function() {
                        $("#main").css({margin: \'0\', padding: \'10px 0 20px\'});                        
                        $("#header").css({"padding-left": \'10px\'});                        
                        $("#data").css({margin: \'10px \'});                        
                        $("#livefilter-list").css({width: \'min-content\'});                        
                    });

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
                                if (key.indexOf("filial_id") == -1){
                                    get_data_str = get_data_str + "&" + key + "=" + params[key];
                                }
                            }
                            //console.log(get_data_str);
                            
                            document.location.href = "?filial_id="+$(this).val() + "&" + get_data_str;
                        });
                    });
                    
                    $(document).ready(function(){

                        var elems = $(".blockControl"), count = elems.length;

                        //Проходимся по дням
                        elems.each( function() {
                            
                            fl_getDailyReports($(this));
                            
                            if (!--count){
                                //console.log(count);
                                
                                //fl_getDailyReportsSummAllMonth ();
                            }

                        });
/*                        
                        //Выделить в отдельную функцию?
                        $(".blockControl").each(function(){
                            //console.log(1);
                        
                            //Дата
                            //var date = ($(this).find(".reportDate").html());
                            //console.log(date);
                            
                            fl_getDailyReports($(this));
                            
                        }).promise().done( function(){ 
                            //Суммы за месяц
                            alert(1); 
                            fl_getDailyReportsSummAllMonth (); 
                        });*/
                        

                        

                        //Клик на дате
                        $("body").on("click", ".reportDate", function(event){

                            // Проверяем нажата ли именно левая кнопка мыши:
                            if (event.which === 1){
                                
                                // Получаем элемент на котором был совершен клик:
                                var target = $(event.target);
                                //console.log(target.attr(\'status\'));                            
                                
                                contextMenuShow(target.attr(\'report_id\'), target.attr(\'status\'), event, \'consRepAdm\');
                            }
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