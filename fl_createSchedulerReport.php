<?php

//fl_createSchedulerReport.php
//Добавить рабочие часы сотрудникам на филиале

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';

        if (($scheduler['see_all'] == 1) || ($scheduler['add_own'] == 1) || $god_mode){

            include_once 'DBWork.php';
            include_once 'functions.php';

            //!!! @@@
            include_once 'ffun.php';

            //Определён ли филиал
            $have_target_filial = true;

            $filials_j = getAllFilials(false, false, false);
            //var_dump($filials_j);

            //Сегодняшняя дата
            $d = date('d', time());
            $m = date('m', time());
            $y = date('Y', time());
            //var_dump($m);

            if (isset($_GET['d'])) {
                $d = $_GET['d'];
            }
            if (isset($_GET['m'])) {
                $m = $_GET['m'];
            }
            if (isset($_GET['y'])) {
                $y = $_GET['y'];
            }

            if (isset($_GET['filial_id'])) {
                $filial_id = $_GET['filial_id'];
            }else{
                if (isset($_SESSION['filial'])) {
                    $filial_id = $_SESSION['filial'];
                }else{
                    $have_target_filial = false;
                }
            }

            $type = 0;

            $type_str = '';
            if (isset($_GET['type'])){
                $type_str = 'type='.$_GET['type'];
                $type = $_GET['type'];
            }

            $report_date = $d.'.'.$m.'.'.$y;

//            $datastart = date('Y-m-d', strtotime($report_date.' 00:00:00'));
//            $dataend = date('Y-m-d', strtotime($report_date.' 23:59:59'));

            //!!! тип (стоматолог...
            //$type = 5;

            echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <a href="stat_cashbox.php" class="b">Касса</a>
                            <a href="fl_consolidated_report_admin.php" class="b">Сводный отчёт по филиалу</a>';
            if ($have_target_filial) {
                echo '
                            <a href="scheduler3.php?filial=' . $filial_id . '&who=4" class="b">График</a>';
            }else{
                echo '
                            <a href="scheduler3.php?who=4" class="b">График</a>';
            }
            echo '
                        </div>
                        <!--<span style="color: red;">Тестовый режим</span>-->
                        <h2>Добавить рабочие часы</h2>
                        <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Проставьте данные и нажмите "Добавить"</span><br>
                    </header>';

            echo '
                    <div id="data">';

            if (($scheduler['see_all'] == 1) || (($scheduler['see_all'] != 1) && ($d == date('d', time())) && ($m == date('m', time())) && ($y == date('Y', time()))) || $god_mode) {

                echo '				
                            <div id="errrror"></div>';

                if ($have_target_filial) {

                    //Смотрим не было ли уже отчета на этом филиале за этот день
                    $dailyReports_j = array();

                    $msql_cnnct = ConnectToDB();

                    $query = "SELECT * FROM `fl_journal_scheduler_report` WHERE `filial_id`='{$filial_id}' AND `day`='{$d}' AND  `month`='$m' AND  `year`='$y'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($dailyReports_j, $arr);
                        }
                    }
                    //var_dump($query);
                    //var_dump($dailyReports_j);

                    //CloseDB ($msql_cnnct);

                    //Если нет отчета в этом филиале за этот день
                    //Или отчёт есть, но мы имеем право смотреть тут
                    //if ((empty($dailyReports_j)) || (!empty($dailyReports_j) && (($scheduler['add_new'] == 1) || $god_mode))) {
                    if (empty($dailyReports_j)) {

                        /*if (!empty($dailyReports_j)){
                            echo '
                                <style="color: red;"><a href="fl_editDailyReport.php?report_id='.$dailyReports_j[0]['id'].'" class="">Отчёт</a> за указаную дату для этого филиала уже был сформирован.</span><br><br>';
                        }*/

                        echo '
                            <div style="">';

                        //начало левого блока
                        echo '
                                <div style="display: inline-block; vertical-align: top; border: 2px dotted rgb(201, 206, 206);">';

                        echo '
                                    <div class="cellsBlock400px">
                                        <div class="cellLeft" style="font-size: 90%;">
                                            <b>Дата отчёта</b>
                                        </div>
                                        <div class="cellRight">
                                            <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" value="' . $report_date . '" onfocus="this.select();_Calendar.lcs(this)"
                                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off">
                                            <span class="button_tiny" style="font-size: 80%; cursor: pointer" onclick="iWantThisDate2(\'fl_createSchedulerReport.php?filial_id=' . $filial_id . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>            
                                        </div>
                                    </div>';

                        echo '				
                                    <div class="cellsBlock400px">
                                        <div class="cellLeft" style="font-size: 90%;">
                                            <b>Филиал</b>
                                        </div>
                                        <div class="cellRight">';

                        if (($scheduler['see_all'] == 1) || $god_mode) {

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
                                        </div>
                                    </div>';

                        //                    if (($scheduler['see_all'] == 1) || $god_mode) {
                        //                        echo '
                        //                                <div class="cellsBlock400px">
                        //                                    <div class="cellLeft" style="font-size: 90%; border: 1px solid rgb(2, 108, 33);">Итоговая сумма</div>
                        //                                    <div class="cellRight calculateOrder" style="border: 1px solid rgb(2, 108, 33);">
                        //                                        <span id="itogSummShow">0</span> руб. <!--<i class="fa fa-refresh" aria-hidden="true" title="Обновить" style="color: red;" onclick="calculateDailyReportSumm();"></i>-->
                        //                                    </div>
                        //                                </div>';
                        //                    }

                        //Получим сотрудников, кто в графике
                        $scheduler_j = array();

                        $msql_cnnct = ConnectToDB();

                        $dop_query = " AND (sch.type='4' OR sch.type='7' OR sch.type='11' OR sch.type='13' OR sch.type='14' OR sch.type='15') ";

                        $query = "SELECT sch.*, s_w.full_name AS full_name, s_p.name AS type_name FROM `scheduler` sch 
                          LEFT JOIN `spr_workers` s_w
                          ON sch.worker = s_w.id
                          LEFT JOIN `spr_permissions` s_p
                          ON sch.type = s_p.id
                          WHERE sch.filial='{$filial_id}' AND sch.day='{$d}' AND  sch.month='{$m}' AND  sch.year='{$y}'" . $dop_query . " 
                          ORDER BY sch.type, s_w.full_name ASC";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0) {
                            while ($arr = mysqli_fetch_assoc($res)) {
                                array_push($scheduler_j, $arr);
                            }
                        }
                        //                    var_dump($query);
                        //                    var_dump($scheduler_j);

                        if (!empty($scheduler_j)) {
                            foreach ($scheduler_j as $sch_item) {
                                $norma_hours = getNormaHours($sch_item['worker']);
                                //var_dump($norma_hours);

                                $display_style = '';

                                if ($sch_item['type'] == 11) {
                                    if (($finances['see_all'] == 1) || $god_mode) {
                                        //--
                                    }else{
                                        $display_style = 'display: none;';
                                    }
                                }

                                echo '
                                    <div class="cellsBlock400px" style="'.$display_style.'">
                                        <div class="cellLeft" style="font-size: 90%;">
                                            ' . $sch_item['full_name'] . '<br>
                                            <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">' . $sch_item['type_name'] . '</span>
                                        </div>
                                        <div class="cellRight" style="font-size: 13px;">
                                            <input type="text" size="1" class="workerHoursValue" worker_id="' . $sch_item['worker'] . '" worker_type="' . $sch_item['type'] . '" value="'.$norma_hours.'" autocomplete="off"> часов
                                            <label id="hours_' . $sch_item['worker'] . '_num_error" class="error"></label>
                                        </div>
                                    </div>';
                            }
                        } else {
                            echo '<span style="color: red;">В этот день на филиале никого нет в графике.</span>';
                        }


                        //конец левого блока
                        echo '
                                </div>';

                        //начало правого блока
                        //                    echo '
                        //                            <div style="display: inline-block; vertical-align: top; /*border: 2px dotted rgb(201, 206, 206);*/">';
                        //
                        //                    echo '
                        //                                <div class="cellsBlock400px" style="font-size: 90%; ">
                        //                                    <div class="cellLeft">
                        //                                    </div>
                        //                                    <div class="cellRight" style="">
                        //                                    </div>
                        //                                </div>';
                        //                    echo '
                        //                            </div>';
                        //конец правого блока

                        echo '
                            </div>';

                        echo '
                            <input type="button" id="fl_createSchedulerReport_add" class="b" value="Добавить" onclick="fl_createSchedulerReport_add('.$type.'); $(this).attr(\'disabled\', \'disabled\');">';

                        echo '
                        </div>';
                    } else {
                        //var_dump($dailyReports_j);
                        $get_str = '';

                        foreach ($dailyReports_j as $item) {
                            $get_str .= $item['id'] . ',';
                        }

                        //                    if (($scheduler['see_all'] == 1) || (($scheduler['see_all'] != 1) && ($d == date('d', time())) && ($m == date('m', time())) && ($y == date('Y', time()))) || $god_mode){
                        echo '
                                <span style="color: red;">Отчёт за указаную дату для этого филиала уже был сформирован.</span><br>
                                <span style="color: red;">Вы можете его <a href="fl_editSchedulerReport.php?'.$type_str.'&report_id=' . $get_str . '" class="">отредактировать</a></span>';
                        //                    }else{
                        //                        echo '
                        //                            <span style="color: red;">Отчёт за указаную дату для этого филиала уже был сформирован.</span>';
                        //                    }
                    }
                } else {
                    echo '
                             <span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
                }
            }else{
                echo '<h1>Редактировать можно только текущее число</h1>';
            }
            echo '
                </div>
                <div id="doc_title">Добавить рабочие часы - Асмедика</div>';


            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';

            echo '
					<script>
					
					    //Приведем часы в нормальный вид
                        $(".workerHoursValue").keyup(function(){
						    //console.log($(this).val());
                        
                            $(this).val($(this).val().replace(",", "."));

                            hideAllErrors();
						});
					
						$(function() {
							$("#SelectFilial").change(function(){
							    
							    blockWhileWaiting (true);
							    
							    var get_data_str = "";
							    
                                //!!!Получение данных из GET тест
                                //console.log(params["data"]);
                                //выведет в консоль значение  GET-параметра data
                                //console.log(params);
                                
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
						
							
						/*$(document).ready(function(){
						    
						    calculateDailyReportSumm();
						    
            	            //$(document).attr("title", $("#doc_title").html());
            	            //console.log($("#doc_title").html());
                        });*/
						
						/*$("#arendaNal, #ortoSummNal, #ortoSummBeznal, #specialistSummNal, #specialistSummBeznal, #analizSummNal, #analizSummBeznal, /!*#summMinusNal, #summMinusBeznal,*!/ #solarSummNal, #solarSummBeznal").blur(function() {
                            //console.log($(this).val());
                            
                            var value = $(this).val();
                            //Если не число
                            if (isNaN(value)){
                                $(this).val(0);
                            }else{
                                if (value < 0){
                                    $(this).val(value * -1);
                                }else{
                                    if (value == ""){
                                        $(this).val(0);
                                    }else{
                                        if (value === undefined){
                                            $(this).val(0);
                                        }else{
                                            //Всё норм с типами данных
                                            //console.log("Всё норм с типами данных")
                                            
                                            calculateDailyReportSumm();
                                        }
                                    }
                                }
                            }
                            
                            //calculateDailyReportSumm();
                            
                        });*/
						
                        //Живой поиск
                        /*$("#arendaNal, #ortoSummNal, #ortoSummBeznal, #specialistSummNal, #specialistSummBeznal, #analizSummNal, #analizSummBeznal, /!*#summMinusNal, #summMinusBeznal,*!/ #solarSummNal, #solarSummBeznal").bind("change keyup input click", function() {
                            if($(this).val().length > 0){
                                //console.log($(this).val().length);
                                
                                //меняем запятую на точку (разделитель)
                                $(this).val($(this).val().replace(\',\', \'.\'));
                                
                                if ($(this).val() == 0){
                                    $(this).val("")
                                }
                            }
                            if (!isNaN($(this).val())){
                                calculateDailyReportSumm();
                            }
                        })*/
						
					</script>';

        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>