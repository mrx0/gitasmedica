<?php

//fl_main_report_average.php
//Отчет подсчета среднего за период

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		//if (($finances['see_all'] == 1) || $god_mode){
        //if (($_SESSION['id'] == 270) || ($god_mode)){
        include_once 'DBWork.php';
        include_once 'functions.php';
        //include_once 'widget_calendar.php';
        include_once 'ffun.php';
        require 'variables.php';

        //Опция доступа к филиалам конкретных сотрудников
        $optionsWF = getOptionsWorkerFilial($_SESSION['id']);
        //var_dump($optionsWF);

        if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){


            $permissions_sort_method = [5,6,10,7,4,13,14,15,9,12,11,777];

            $filials_j = getAllFilials(false, false, false);
            //var_dump($filials_j);

            //Получили список прав
            $permissions_j = getAllPermissions(false, true);
            //var_dump($permissions_j);

            //!!! костыль для меня =)
            //array_push($permissions_j, array('id' => '777', 'name' => 'Сис.админ'));
            $permissions_j[777] = array('id' => '777', 'name' => 'Сис.админ');
            //var_dump($permissions_j);


            //$msql_cnnct = ConnectToDB ();

            //Даты
            if (isset($_GET['m_start']) && isset($_GET['y_start'])){
                //операции со временем
                $month_start = $_GET['m_start'];
                $year_start = $_GET['y_start'];
            }else{
                //операции со временем
                $month_start = date('m');
                $year_start = date('Y');
            }

            if (isset($_GET['m_end']) && isset($_GET['y_end'])){
                //операции со временем
                $month_end = $_GET['m_end'];
                $year_end = $_GET['y_end'];
            }else{
                //операции со временем
                $month_end = date('m');
                $year_end = date('Y');
            }

            $day_start = date("d");

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
                $filial_id = 15;
            }

            if (!$god_mode) {
                if (!in_array($filial_id, $optionsWF[$_SESSION['id']])) {
                    $filial_id = $optionsWF[$_SESSION['id']][0];
                }
            }

            $dop = 'filial_id='.$filial_id;

            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                            <a href="fl_consolidated_report_admin.php?filial_id='.$filial_id.'&m='.$month_end.'&y='.$year_end.'" class="b">Сводный отчёт по филиалу</a>
                        </div>
                        <h2 style="padding: 0;">Усреднённый отчёт <span id="filial_name"></span> / c <span id="date_start_name">____</span> по <span id="date_end_name">____</span></h2>
                    </header>';



            echo '
                    <div id="data">';

//            var_dump(strtotime('-2 month', gmmktime(0, 0, 0, 1, date('m', time()), date('Y', time()))));
//            var_dump(strtotime('-2 month', gmmktime(0, 0, 0, 1, $month, $year)));
//            var_dump(time());


            echo '				
                        <div id="errrror"></div>';

            echo '
                        <div class="no_print">';
            //echo widget_calendar ($month, $year, 'fl_main_report2.php', $dop);

            echo '
                        <!--<span id="getThisCalendar">-->
                            Выберите даты: от <select name="month_start" id="month_start" style="margin-right: 5px;">';
            foreach ($monthsName as $mNumber => $mName){
                $selected = '';
                if ((int)$mNumber == $month_start){
                    $selected = 'selected';
                }
                echo '
                                <option value="'.$mNumber.'" '.$selected.'>'.$mName.'</option>';
            }
            echo '
                            </select>
                            <select name="year_start" id="year_start">';
            for ($i = (int)date('Y')-2; $i <= (int)date('Y')+1; $i++){
                $selected = '';
                if ($i == $year_start){
                    $selected = 'selected';
                }
                echo '
                                <option value="'.$i.'" '.$selected.'>'.$i.'</option>';
            }
            echo '
                            </select>
                        <!--</span>-->';
            echo '
                        <!--<span id="getThisCalendar">-->
                            и до <select name="month_end" id="month_end" style="margin-right: 5px;">';
            foreach ($monthsName as $mNumber => $mName){
                $selected = '';
                if ((int)$mNumber == $month_end){
                    $selected = 'selected';
                }
                echo '
            <option value="'.$mNumber.'" '.$selected.'>'.$mName.'</option>';
            }
            echo '
                            </select>
                            <select name="year_end" id="year_end">';
            for ($i = (int)date('Y')-2; $i <= (int)date('Y')+1; $i++){
                $selected = '';
                if ($i == $year_end){
                    $selected = 'selected';
                }
                echo '
            <option value="'.$i.'" '.$selected.'>'.$i.'</option>';
            }
            echo '
                            </select>
                        <!--</span>-->';

            echo '
                        </div><br>';

            //Выбор филиала
            echo '
                        <div class="no_print" style="font-size: 90%; ">
                            Филиал: ';

            //if (($finances['see_all'] == 1) || $god_mode) {

            echo '
                            <select name="SelectFilial" id="SelectFilial">';

            foreach ($filials_j as $filial_item) {

                $selected = '';

                if (in_array($filial_item['id'], $optionsWF[$_SESSION['id']]) || $god_mode) {
                    if ($filial_id == $filial_item['id']) {
                        $selected = 'selected';
                    }


                    echo '
                                <option value="' . $filial_item['id'] . '" ' . $selected . '>' . $filial_item['name'] . '</option>';
                }
            }

            echo '
                            </select>';

            echo ' <span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick="fl_mainReportAverage();"><i class="fa fa-check-square" style=" color: green;"></i> Применить</span>';

            echo '
                        </div>';

            //Количество дней в месяце
//                $month_stamp = mktime(0, 0, 0, $month, 1, $year);
//                $day_count = date("t", $month_stamp);

            //или так
            //$day_count = cal_days_in_month(CAL_GREGORIAN, $month, $year);


            //Табличка с результатами
            echo '
                        <div id="res_table_tmpl" style="margin-top: 10px;"></div>';



            echo '
                    </div>
                </div>';

            echo '
                            <div class="no_print" style="position: fixed; top: 45px; right: 10px; border: 1px solid #0C0C0C; border-radius: 5px; padding: 5px 5px; background-color: #FFFFFF">
                                <div class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;"
                                onclick="window.print();">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                </div>
                            </div>';

            echo '    
                <div id="doc_title">Усреднённый отчёт - Асмедика</div>';

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