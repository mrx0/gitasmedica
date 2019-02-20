<?php

//fl_editSchedulerReport.php
//Редактировать рабочие часы сотрудникам на филиале

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
            if ($_GET) {
                if (isset($_GET['report_id'])){

                    include_once 'DBWork.php';
                    include_once 'functions.php';

                    //!!! @@@
                    include_once 'ffun.php';

                    $filials_j = getAllFilials(false, false);
                    //var_dump($filials_j);

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

                    //Смотрим не было ли уже отчета на этом филиале за этот день
                    $dailyReports_j = array();

                    $msql_cnnct = ConnectToDB();

                    //Соберем строку для запроса
                    $dop_query = implode(' OR ', $report_ids_arr);
                    var_dump($dop_query);

//                    foreach ($report_ids_arr as $report_id){
//                        $dop_query .= '';
//                    }

                    $query = "SELECT * FROM `fl_journal_scheduler_report` WHERE `id`='{$_GET['report_id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($dailyReports_j, $arr);
                        }
                    }
                    //var_dump($query);
                    //var_dump($dailyReports_j);

                    CloseDB($msql_cnnct);

                    $report_date = $dailyReports_j[0]['day'].'.'.$dailyReports_j[0]['month'].'.'.$dailyReports_j[0]['year'];

                    $datastart = date('Y-m-d', strtotime($report_date.' 00:00:00'));
                    $dataend = date('Y-m-d', strtotime($report_date.' 23:59:59'));

                    $filial_id = $dailyReports_j[0]['filial_id'];

                    echo '
                        <div id="status">
                            <header>
                                <div class="nav">
                                    <a href="stat_cashbox.php" class="b">Касса</a>
                                    <a href="fl_consolidated_report_admin.php" class="b">Сводный отчёт по филиалу</a>
                                </div>
                                <h2>Редактировать рабочие часы</h2>
                            </header>';

                    if (!empty($dailyReports_j)){

                        echo '
                             <div id="data">';
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
                                                Дата отчёта
                                            </div>
                                            <div class="cellRight">
                                                ' . $report_date . '            
                                            </div>
                                        </div>';

                        echo '				
                                        <div class="cellsBlock400px">
                                            <div class="cellLeft" style="font-size: 90%;">
                                                Филиал
                                            </div>
                                            <div class="cellRight">';

                        echo $filials_j[$dailyReports_j[0]['filial_id']]['name'].'<input type="hidden" id="SelectFilial" name="SelectFilial" value="' . $dailyReports_j[0]['filial_id'] . '">';

                        echo '
                                            </div>
                                        </div>';


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
                            <input type="button" class="b" value="Применить" onclick="fl_editSchedulerReport_add('.$_GET['report_id'].');">';

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
            echo '
                </div>
                <div id="doc_title">Ежедневный отчёт - Асмедика</div>';


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
						
                        //Живой поиск
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