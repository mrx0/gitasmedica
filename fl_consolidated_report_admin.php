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

            $have_target_filial = true;

            $filials_j = getAllFilials(false, false);
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
            if (isset($_GET['m'])) {
                $m = $_GET['m'];
            }
            if (isset($_GET['y'])) {
                $y = $_GET['y'];
            }

            //Филиал
            if (isset($_GET['filial_id'])) {
                $filial_id = $_GET['filial_id'];
            }else{
                if (isset($_SESSION['filial'])) {
                    $filial_id = $_SESSION['filial'];
                }else{
                    $have_target_filial = false;
                }
            }

            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                            <a href="stat_cashbox.php" class="b">Касса</a>
                        </div>
                        <h2 style="padding: 0;">Сводный отчёт админ-ов</h2>
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

                echo '
			    <div id="report" class="report" style="margin-top: 10px;">';
                echo '
                    <ul class="live_filter" id="livefilter-list" style="margin-left:6px; background-color: #FFF;">';

                echo '
                        <li class="cellsBlock" style="font-weight:bold;">';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Дата
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Z-отчёт
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Всего
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Наличные
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Безнал.
                            </div>';
                /*echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Аренда
                            </div>';*/
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Серт-ты<br>нал
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Серт-ты безнал
                            </div>';
                /*echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                ЛЕЧЕНИЕ<br>нал
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                ЛЕЧЕНИЕ<br>б/н + серт
                            </div>';*/
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18);">
                                ОРТО<br>нал
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center;  background-color: rgba(63, 0, 255, 0.18);">
                                ОРТО<br>безнал
                            </div>';
                /*echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                ? ОРТО<br>кол-во
                            </div>';*/
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18);">
                                Спец-ты<br>нал
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18);">
                                Спец-ты безнал
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18);">
                                Анализы нал
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18);">
                                Анализы безнал
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18);">
                                Солярий<br>нал
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center; background-color: rgba(63, 0, 255, 0.18);">
                                Солярий<br>безнал
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Расход
                            </div>';
                echo '
                            <div class="cellText">
                            </div>';

                echo '
                        </li>';

                //С первого дня месяца по последний
                for ($d = 1; $d <= $day_count; $d++) {
                    //приводим дату в норм вид
                    $data = dateTransformation($d) . '.' . dateTransformation($month) . '.' . $year;
                    //день недели
                    $week_day = date("w", strtotime($year . '-' . $month . '-' . $d));
                    //var_dump($dayWeek_arr[$week_day]);

                    //цвет дня на выходных
                    $weekend_block = 'cellsBlock';
                    if (($week_day == 6) || ($week_day == 0)) {
                        $weekend_block = 'cellsBlock6';
                    }

                    //Цвет текущего дня
                    $today_color = '';
                    if ($data === date('d') . '.' . date('m') . '.' . date('Y')) {
                        //$today_color = 'background-color: green;';
                        $today_color = ' outline: 1px solid red;';
                    }
                    //var_dump($data);
                    //var_dump(date('d') . '.' . date('m') . '.' . date('Y'));

                    echo '
                        <li class="' . $weekend_block . ' cellsBlockHover blockControl" style="font-weight: bold; font-size: 12px; color: #949393; ' . $today_color . '">';
                    echo '
                            <div class="cellTime cellsTimereport reportDate" style="text-align: center; color: #333;">
                                ' . $data . '
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport zReport" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport allSumm" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport SummNal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport SummBezal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport SummCertNal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport SummCertBeznal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport ortoSummNal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport ortoSummBeznal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    /*echo '
                            <div class="cellTime cellsTimereport " style="text-align: center; font-weight: normal;">
                                
                            </div>';*/
                    echo '
                            <div class="cellTime cellsTimereport specialistSummNal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport specialistSummBeznal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport analizSummNal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport analizSummBeznal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport solarSummNal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport solarSummBeznal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellTime cellsTimereport summMinusNal" style="text-align: center; font-weight: normal;">
                                -
                            </div>';
                    echo '
                            <div class="cellText">
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

                        //Выделить в отдельную функцию?
                        $(".blockControl").each(function(){
                            //console.log(1);
                        
                            //Дата
                            //var date = ($(this).find(".reportDate").html());
                            //console.log(date);
                            
                            fl_getDailyReports($(this));
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