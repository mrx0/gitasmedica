<?php

//fl_createDailyReport.php
//Добавить ежедневный отчёт администратор

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){

            include_once 'DBWork.php';
            include_once 'functions.php';

            //!!! @@@
            include_once 'ffun.php';

            $have_target_filial = true;

            $filials_j = getAllFilials(false, false);
            //var_dump($filials_j);

            $d = date('d', time());
            $m = date('n', time());
            $y = date('Y', time());
            //$filial_id = $_GET['filial_id'];

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

            $report_date = $d.'.'.$m.'.'.$y;

            $datastart = date('Y-m-d', strtotime($report_date.' 00:00:00'));
            $dataend = date('Y-m-d', strtotime($report_date.' 23:59:59'));

            //!!! тип (стоматолог...
            //$type = 5;

            echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <a href="stat_cashbox.php" class="b">Касса</a>
                        </div>
                        <h2>Добавить ежедневный отчёт</h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';

            if ($have_target_filial) {

                //Смотрим не было ли уже отчета на этом филиале за этот день
                $dailyReports_j = array();

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT * FROM `fl_journal_daily_report` WHERE `filial_id`='{$filial_id}' AND `day`='{$d}' AND  `month`='$m' AND  `year`='$y'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($dailyReports_j, $arr);
                    }
                }
                //var_dump($query);
                //var_dump($dailyReports_j);

                CloseDB ($msql_cnnct);

                //Если нет отчета в этом филиале за этот день
                //Или отчёт есть, но мы имеем право смотреть тут
                if ((empty($dailyReports_j)) || (!empty($dailyReports_j) && (($finances['add_new'] == 1) || $god_mode))) {

                    if (!empty($dailyReports_j)){
                        echo '
                         <span style="color: red;">Отчёт за указаную дату для этого филиала уже был сформирован.</span><br><br>';
                    }


                    echo '
                    
                        <div class="cellsBlock2">
                            <div class="cellLeft" style="font-size: 90%;">
                                Дата отчёта
                            </div>
                            <div class="cellRight">
                                <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" value="' . $report_date . '" onfocus="this.select();_Calendar.lcs(this)"
                                            onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
                                <span class="button_tiny" style="font-size: 80%; cursor: pointer" onclick="iWantThisDate2(\'fl_createDailyReport.php?filial_id=' . $filial_id . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>            
                            </div>
                        </div>';

                    echo '				
                        <div class="cellsBlock2">
                            <div class="cellLeft" style="font-size: 90%;">
                                Филиал
                            </div>
                            <div class="cellRight">';

                    if (($finances['see_all'] == 1) || $god_mode) {

                        echo '
                                <select name="SelectFilial" id="SelectFilial">';

                        foreach ($filials_j as $filial_item) {

                            $selected = '';

                            if ($filial_id == $filial_item['id']) {
                                $selected = 'selected';
                            }

                            echo '<option value="' . $filial_item['id'] . '" ' . $selected . '>' . $filial_item['name'] . '</option>';
                        }

                        echo '
                                </select>';
                    } else {

                        echo $filials_j[$_SESSION['filial']]['name'] . '<input type="hidden" id="SelectFilial" name="SelectFilial" value="' . $_SESSION['filial'] . '">';

                    }

                    echo '
                            </div>
                        </div>';

                    echo '
                        <div class="cellsBlock2">
                            <div class="cellLeft" style="font-size: 90%;">Z-отчёт, руб.</div>
                            <div class="cellRight">
                                <input type="text" name="zreport" id="zreport" value="" style="font-size: 12px;" disabled>
                            </div>
                        </div>';

                    echo '
                        <div class="cellsBlock2">
                            <div class="cellLeft" style="font-size: 90%;">Общая сумма</div>
                            <div class="cellRight calculateOrder">
                                <span id="allsumm">0</span> руб. <!--<i class="fa fa-refresh" aria-hidden="true" title="Обновить" style="color: red;" onclick="calculateDailyReportSumm();"></i>-->
                            </div>
                        </div>';


                    //Получаем данные по отчету касса
                    //var_dump(ajaxShowResultCashbox($datastart, $dataend, $filial_id, 0, 1));

                    $SummNal = 0;
                    $SummBeznal = 0;
                    $SummCertNal = 0;
                    $SummCertBeznal = 0;
                    $CertCount = 0;

                    $result = ajaxShowResultCashbox($datastart, $dataend, $filial_id, 0, 1);

                    if (!empty($result)) {
                        if (!empty($result['rezult'])) {
                            foreach ($result['rezult'] as $item) {
                                if ($item['summ_type'] == 1) {
                                    $SummNal += $item['summ'];
                                }
                                if ($item['summ_type'] == 2) {
                                    $SummBeznal += $item['summ'];
                                }
                            }
                        }
                        if (!empty($result['rezult_cert'])) {

                            $CertCount = count($result['rezult_cert']);

                            foreach ($result['rezult_cert'] as $item) {
                                if ($item['summ_type'] == 1) {
                                    $SummCertNal += $item['cell_price'];
                                }
                                if ($item['summ_type'] == 2) {
                                    $SummCertBeznal += $item['cell_price'];
                                }
                            }
                        }
                    }

                    /*var_dump($SummNal);
                    var_dump($SummBeznal);
                    var_dump($CertCount);
                    var_dump($SummCertNal);
                    var_dump($SummCertBeznal);*/

                    echo '
                        <div class="cellsBlock2" style="font-size: 90%;">
                            <div class="cellLeft">
                                Приход из отчёта "Касса"<br>
                                <span style="font-size:80%; color: #999; ">всё, что добавляется через программу</span>
                            </div>
                            <div class="cellRight" id="general">
                                <div style="margin: 2px 0; ">Наличная оплата: <b><i id="SummNal" class="allSumm">' . $SummNal . '</i></b> руб.</div>
                                <div style="margin: 2px 0; ">Безналичная оплата: <b><i id="SummBeznal" class="allSumm">' . $SummBeznal . '</i></b> руб.</div>
                                <div style="margin: 6px 0 2px; ">Продано сертификатов: <b><i id="CertCount" class="">' . $CertCount . '</i></b> руб.</div>
                                <div style="margin: 2px 0; ">- наличная оплата: <b><i id="SummCertNal" class="allSumm">' . $SummCertNal . '</i></b> руб.</div>
                                <div style="margin: 2px 0; ">- безналичная оплата: <b><i id="SummCertBeznal" class="allSumm">' . $SummCertBeznal . '</i></b> руб.</div>
                            </div>
                        </div>';

                    echo '
                        <div class="cellsBlock2" style="font-size: 90%;">
                            <div class="cellLeft">
                                Ортопантомограмма + КТ
                                <span style="font-size:80%; color: #999; "></span>
                            </div>
                            <div class="cellRight">
                                <span style="font-size:90%; color: #5f5f5f; ">Нал. </span><br><input type="text" id="ortoSummNal" class="allSummInput" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span><br>
                                <span style="font-size:90%; color: #5f5f5f; ">Безнал. </span><br><input type="text" id="ortoSummBeznal" class="allSummInput" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span>
                            </div>
                        </div>';

                    echo '
                        <div class="cellsBlock2" style="font-size: 90%;">
                            <div class="cellLeft">
                                Специалисты<br>
                                <span style="font-size:80%; color: #999; ">для ПР72</span>
                            </div>
                            <div class="cellRight">
                                <span style="font-size:90%; color: #5f5f5f; ">Нал. </span><br><input type="text" id="specialistSummNal" class="allSummInput" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span><br>
                                <span style="font-size:90%; color: #5f5f5f; ">Безнал. </span><br><input type="text" id="specialistSummBeznal" class="allSummInput" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span>
                            </div>
                        </div>';

                    echo '
                        <div class="cellsBlock2" style="font-size: 90%;">
                            <div class="cellLeft">
                                Анализы<br>
                                <span style="font-size:80%; color: #999; ">для ПР72</span>
                            </div>
                            <div class="cellRight">
                                <span style="font-size:90%; color: #5f5f5f; ">Нал. </span><br><input type="text" id="analizSummNal" class="allSummInput" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span><br>
                                <span style="font-size:90%; color: #5f5f5f; ">Безнал. </span><br><input type="text" id="analizSummBeznal" class="allSummInput" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span>
                            </div>
                        </div>';

                    echo '
                        <div class="cellsBlock2" style="font-size: 90%;">
                            <div class="cellLeft">
                                Солярий<br>
                                <span style="font-size:80%; color: #999; "></span>
                            </div>
                            <div class="cellRight">
                                <span style="font-size:90%; color: #5f5f5f; ">Нал. </span><br><input type="text" id="solarSummNal" class="allSummInput" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span><br>
                                <span style="font-size:90%; color: #5f5f5f; ">Безнал. </span><br><input type="text" id="solarSummBeznal" class="allSummInput" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span>
                            </div>
                        </div>';

                    echo '
                        <div class="cellsBlock2" style="font-size: 90%;">
                            <div class="cellLeft">
                                Расход<br>
                                <span style="font-size:80%; color: #999; ">Выдано из кассы</span>
                            </div>
                            <div class="cellRight" style="color: red;">
                                <input type="text" id="summMinusNal" class="summMinus" style="font-size: 12px; color: red; " value="0"><span  style="font-size: 90%;"> руб.</span>
                            </div>
                        </div>';

                    echo '
                        <input type="button" class="b" value="Добавить" onclick="fl_createDailyReport_add();">';

                    echo '
                    </div>';
                }else{
                    echo '
                         <span style="color: red;">Отчёт за указаную дату для этого филиала уже был сформирован.</span>';
                }
            }else{
                echo '
                         <span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
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
						
						$("#ortoSummNal, #ortoSummBeznal, #specialistSummNal, specialistSummBeznal, #analizSummNal, #analizSummBeznal, #summMinusNal, #summMinusBeznal, #solarSummNal, #solarSummBeznal").blur(function() {
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
                                        }
                                    }
                                }
                            }
                            
                            calculateDailyReportSumm();
                            
                        });
						
                        //Живой поиск
                        $("#ortoSummNal, #ortoSummBeznal, #specialistSummNal, specialistSummBeznal, #analizSummNal, #analizSummBeznal, #summMinusNal, #summMinusBeznal, #solarSummNal, #solarSummBeznal").bind("change keyup input click", function() {
                            if($(this).val().length > 0){
                                //console.log($(this).val().length);
                                
                                if ($(this).val() == 0){
                                    $(this).val("")
                                }
                            }
                            calculateDailyReportSumm();
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