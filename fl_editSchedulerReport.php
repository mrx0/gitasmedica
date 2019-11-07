<?php

//fl_editSchedulerReport.php
//Редактировать рабочие часы сотрудникам на филиале

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';

        if (($scheduler['see_all'] == 1) || ($scheduler['add_own'] == 1) || $god_mode){
            if ($_GET) {
                if (isset($_GET['report_id'])){

                    include_once 'DBWork.php';
                    include_once 'functions.php';

                    //!!! @@@
                    include_once 'ffun.php';

                    $filials_j = getAllFilials(false, false, false);
                    //var_dump($filials_j);

                    //Массив типов сотрудников для этого конкретного отчёта
                    $workers_target_arr = [1, 9, 12, 777];

                    //Сегодняшняя дата
                    /*$d = date('d', time());
                    $m = date('n', time());
                    $y = date('Y', time());*/
                    //$filial_id = $_GET['filial_id'];

                    //Разберем $_GET['report_id'] в массив id-шников
                    $report_ids_arr = explode(',', $_GET['report_id']);
                    //var_dump($report_ids_arr);

                    //Уберем пустые значения
                    $report_ids_arr = array_diff($report_ids_arr, array(''));
                    //var_dump($report_ids_arr);

                    //Для отчета на этом филиале за этот день
                    $dailyReports_j = array();

                    $msql_cnnct = ConnectToDB();

                    //Соберем строку для запроса
                    $dop_query = implode(' OR sch.id = ', $report_ids_arr);

                    $dop_query = 'sch.id = '.$dop_query;
                    //var_dump($dop_query);

//                    foreach ($report_ids_arr as $report_id){
//                        $dop_query .= '';
//                    }

                    $type = 0;

                    $type_str = '';
                    if (isset($_GET['type'])){
                        $type_str = '&type='.$_GET['type'];
                        $type = $_GET['type'];
                    }

                    //Смотрим не было ли уже отчета на этом филиале за этот день
                    $query = "SELECT sch.*, s_w.full_name AS full_name, s_p.name AS type_name  FROM `fl_journal_scheduler_report` sch
                    LEFT JOIN `spr_workers` s_w
                      ON sch.worker_id = s_w.id
                    LEFT JOIN `spr_permissions` s_p
                      ON s_w.permissions = s_p.id
                    WHERE ".$dop_query."
                    ORDER BY sch.type, s_w.full_name ASC";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($dailyReports_j, $arr);
                        }
                    }
                    //var_dump($query);
//                    var_dump($dailyReports_j);

                    echo '
                        <div id="status">
                            <header>
                                <div class="nav">
                                    <a href="stat_cashbox.php" class="b">Касса</a>
                                    <a href="fl_consolidated_report_admin.php" class="b">Сводный отчёт по филиалу</a>
                                    <a href="scheduler3.php" class="b">График</a>
                                </div>
                                <!--<span style="color: red;">Тестовый режим</span>-->
                                <h2>Редактировать рабочие часы</h2>
                            </header>';

                    if (!empty($dailyReports_j)){

                        $report_date = $dailyReports_j[0]['day'].'.'.$dailyReports_j[0]['month'].'.'.$dailyReports_j[0]['year'];
                        //var_dump($report_date);

//                    $datastart = date('Y-m-d', strtotime($report_date.' 00:00:00'));
//                    $dataend = date('Y-m-d', strtotime($report_date.' 23:59:59'));

                        $filial_id = $dailyReports_j[0]['filial_id'];



                        //Все сотрудники, которые есть в графике тут в эту дату
                        $scheduler_j = array();

                        $workers_target_str = implode(',', $workers_target_arr);

                        $dop_query = " AND (sch.type='4' OR sch.type='7' OR sch.type='11' OR sch.type='13' OR sch.type='14' OR sch.type='15' OR sch.type IN ($workers_target_str)) ";

                        $query = "SELECT sch.*, s_w.full_name AS full_name, s_p.name AS type_name FROM `scheduler` sch 
                          LEFT JOIN `spr_workers` s_w
                          ON sch.worker = s_w.id
                          LEFT JOIN `spr_permissions` s_p
                          ON sch.type = s_p.id
                          WHERE sch.filial='{$filial_id}' AND sch.day='{$dailyReports_j[0]['day']}' AND  sch.month='{$dailyReports_j[0]['month']}' AND  sch.year='{$dailyReports_j[0]['year']}'".$dop_query." 
                          ORDER BY sch.type, s_w.full_name ASC";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0) {
                            while ($arr = mysqli_fetch_assoc($res)) {
                                $scheduler_j[$arr['worker']] = $arr;
                            }
                        }
                        //var_dump($scheduler_j);



                        echo '
                             <div id="data">';

                        if (($scheduler['see_all'] == 1) || (($scheduler['see_all'] != 1) && ($report_date == date('d.m.Y', time()))) || $god_mode) {
                            echo '				
                                    <div id="errrror"></div>';

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
                                                    ' . $report_date . '<input type="hidden" id="iWantThisDate2" name="iWantThisDate2" value="' . $report_date . '">
                                                </div>
                                            </div>';

                            echo '				
                                            <div class="cellsBlock400px">
                                                <div class="cellLeft" style="font-size: 90%;">
                                                    <b>Филиал</b>
                                                </div>
                                                <div class="cellRight">';

                            echo $filials_j[$dailyReports_j[0]['filial_id']]['name'].'<input type="hidden" id="SelectFilial" name="SelectFilial" value="' . $dailyReports_j[0]['filial_id'] . '">';

                            echo '
                                                </div>
                                            </div>';


                            foreach ($dailyReports_j as $sch_item){
                                //var_dump($sch_item);

                                $border_color = '';
                                $norma_hours = $sch_item['hours'];
                                if ($norma_hours == 0) {
                                    $norma_hours = getNormaHours($sch_item['worker_id']);
                                    $border_color = 'border: 1px solid rgb(255, 0, 0)';
                                }

                                $display_style = '';

                                if (($sch_item['type'] == 1) || ($sch_item['type'] == 9) || ($sch_item['type'] == 11) || ($sch_item['type'] == 12) || ($sch_item['type'] == 777)) {
                                    if (($finances['see_all'] == 1) || $god_mode) {
                                        //--
                                    }else{
                                        $display_style = 'display: none;';
                                    }
                                }

                                echo '
                                <div class="cellsBlock400px" style="position: relative; '.$display_style.'">
                                    <div class="cellLeft" style="font-size: 90%;">
                                        '.$sch_item['full_name'].'<br>
                                        <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'.$sch_item['type_name'].'</span>
                                    </div>
                                    <div class="cellRight" style="font-size: 13px;">
                                        <input type="text" size="1" class="workerHoursValue" style="'.$border_color.'" worker_id="'.$sch_item['worker_id'].'" worker_type="'.$sch_item['type'].'" value="'.$norma_hours.'" autocomplete="off"> часов
                                        <label id="hours_'.$sch_item['worker_id'].'_num_error" class="error"></label>
                                    </div>';
                                if (($scheduler['see_all'] == 1) || $god_mode || (($scheduler['add_own'] == 1) && ($_SESSION['filial'] == $sch_item['filial_id']) && ($report_date == date('d.m.Y', time())))) {
                                    echo '
                                        <i class="fa fa-times" aria-hidden="true" style="position: absolute; top: 10px; right: 10px; cursor: pointer; color:red;" title="Удалить" onclick="fl_deleteSchedulerReportItem(' . $sch_item['id'] . ');"></i>';
                                }
                                echo '
                                </div>';

                                //Удаляем сотрудника из массива тех, кто сегодня тут в графике
                                unset($scheduler_j[$sch_item['worker_id']]);
                            }
                            //var_dump($scheduler_j);

                            //Если остались сотрудники, которые тут работают в эту дату, но у них еще нет часов в базе
                            if (!empty($scheduler_j)){

                                foreach ($scheduler_j as $sch_item){
                                    //var_dump($sch_item);

                                    $border_color = '';
//                                    $norma_hours = $sch_item['hours'];
//                                    if ($norma_hours == 0) {
                                        $norma_hours = getNormaHours($sch_item['worker']);
                                        $border_color = 'border: 1px solid rgb(255, 0, 0)';
//                                    }

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
                                                '.$sch_item['full_name'].'<br>
                                                <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'.$sch_item['type_name'].'</span>
                                            </div>
                                            <div class="cellRight" style="font-size: 13px;">
                                                <input type="text" size="1" class="workerHoursValue" style="'.$border_color.'" worker_id="'.$sch_item['worker'].'" worker_type="'.$sch_item['type'].'" value="'.$norma_hours.'" autocomplete="off"> часов
                                                <label id="hours_'.$sch_item['worker'].'_num_error" class="error"></label>
                                            </div>
                                        </div>';

                                }
                            }

                            //конец левого блока
                            echo '
                                        </div>';

                            //начало правого блока
    //                        echo '
    //                                    <div style="display: inline-block; vertical-align: top; /*border: 2px dotted rgb(201, 206, 206);*/">';
    //
    //                        echo '
    //                                        <div class="cellsBlock400px" style="font-size: 90%;">
    //                                            <div class="cellLeft">
    //                                            </div>
    //                                            <div class="cellRight" style="color: red;">
    //                                            </div>
    //                                        </div>';
    //
    //                        echo '
    //                                    </div>';
                            //конец правого блока

                            echo '
                            </div>';

                            echo '
                                <input type="button" id="fl_editSchedulerReport_add" class="b" value="Применить" onclick="fl_editSchedulerReport_add(\''.$_GET['report_id'].'\', '.$type.'); $(this).attr(\'disabled\', \'disabled\');">';

                        }else{
                            echo '<span style="color: red;">Ничего не найдено</span>';
                        }

                        echo '
                            </div>';

                    }else{
                        echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                    }
                }else{
                    echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                }
            }else{
                echo '<h1>Редактировать можно только текущее число</h1>';
            }
            echo '
                </div>
                <div id="doc_title">Редактировать рабочие часы - Асмедика</div>';


            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';

            echo '
					<script>
					
						$(function() {
							$("#SelectFilial").change(function(){
							    
							    blockWhileWaiting (true);
							    
							    var get_data_str = "";
							    
                                //!!!Получение данных из GET тест
                                /*var params = window
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
                                    );*/
                                
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
						
						$(document).ready(function(){
						    
						    calculateDailyReportSumm();
						    
            	            //$(document).attr("title", $("#doc_title").html());
            	            //console.log($("#doc_title").html());
                        });
						
						$("#arendaNal, #ortoSummNal, #ortoSummBeznal, #specialistSummNal, #specialistSummBeznal, #analizSummNal, #analizSummBeznal, #summMinusNal, #summMinusBeznal, #solarSummNal, #solarSummBeznal").blur(function() {
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
                            
                        });
						
                        //
                        $("#arendaNal, #ortoSummNal, #ortoSummBeznal, #specialistSummNal, #specialistSummBeznal, #analizSummNal, #analizSummBeznal, #summMinusNal, #summMinusBeznal, #solarSummNal, #solarSummBeznal").bind("change keyup input click", function() {
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
                        })
						
					</script>';

        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>