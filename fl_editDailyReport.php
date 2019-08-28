<?php

//fl_editDailyReport.php
//Редактировать ежедневный отчёт администратор

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

                    $filials_j = getAllFilials(false, false, false);
                    //var_dump($filials_j);

                    //Сегодняшняя дата
                    /*$d = date('d', time());
                    $m = date('n', time());
                    $y = date('Y', time());*/
                    //$filial_id = $_GET['filial_id'];

                    //Смотрим не было ли уже отчета на этом филиале за этот день
                    $dailyReports_j = array();

                    $msql_cnnct = ConnectToDB();

                    $query = "SELECT * FROM `fl_journal_daily_report` WHERE `id`='{$_GET['report_id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

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
                                    <a href="fl_consolidated_report_admin.php?filial_id='.$filial_id.'" class="b">Сводный отчёт по филиалу</a>
                                    <a href="fl_createSchedulerReport.php?filial_id='.$filial_id.'" class="b">Добавить рабочие часы</a>
                                </div>
                                <h2>Редактировать ежедневный отчёт</h2>
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

                        echo $filials_j[$filial_id]['name'].'<input type="hidden" id="SelectFilial" name="SelectFilial" value="' . $filial_id . '">';

                        echo '
                                            </div>
                                        </div>';



                        if (($finances['see_all'] == 1) || $god_mode) {
                            echo '
                                        <div class="cellsBlock400px">
                                            <div class="cellLeft" style="font-size: 90%; border: 1px solid rgb(2, 108, 33);">
                                                Итоговая сумма<br>
                                                <i style="color: rgb(127, 17, 3);">наличные</i>
                                            </div>
                                            <div class="cellRight calculateOrder" style="border: 1px solid rgb(2, 108, 33);">
                                                <span id="itogSummShow">'.$dailyReports_j[0]['itogSumm'].'</span> руб. <!--<i class="fa fa-refresh" aria-hidden="true" title="Обновить" style="color: red;" onclick="calculateDailyReportSumm();"></i>--><br>
                                                <span id="itogSummNalShow" style="color: rgb(127, 17, 3);">0</span> руб.
                                            </div>
                                        </div>';
                        }

                        echo '
                                        <input type="hidden" id="itogSumm" value="'.$dailyReports_j[0]['itogSumm'].'">';

                        echo '
                                        <div class="cellsBlock400px">
                                            <div class="cellLeft" style="font-size: 90%;">
                                                Z-отчёт, руб.<br>
                                                <span style="font-size:80%; color: #999; ">Сумма всех Z-отчётов за смену</span>
                                            </div>
                                            <div class="cellRight">
                                                <input type="text" name="zreport" id="zreport" value="'.$dailyReports_j[0]['zreport'].'" style="font-size: 12px;">
                                            </div>
                                        </div>';

//                        echo '
//                                        <div class="cellsBlock400px">
//                                            <div class="cellLeft" style="font-size: 90%;">Общая сумма</div>
//                                            <div class="cellRight calculateOrder" style="font-size: 13px;">
//                                                <span id="allsumm">0</span> руб. <!--<i class="fa fa-refresh" aria-hidden="true" title="Обновить" style="color: red;" onclick="calculateDailyReportSumm();"></i>-->
//                                            </div>
//                                        </div>';

                        echo '
                                        <div class="cellsBlock400px">
                                            <div class="cellLeft" style="font-size: 90%;">
                                                Общая сумма<br>
                                                <i style="color: rgb(127, 17, 3);">по кассе <span style="font-size: 80%">(Z-отчет)</span></i>
                                            </div>
                                            <div class="cellRight calculateOrder" style="font-size: 13px;">
                                                <span id="allsumm">0</span> руб. <!--<i class="fa fa-refresh" aria-hidden="true" title="Обновить" style="color: red;" onclick="calculateDailyReportSumm();"></i>--><br>
                                                <span id="allsummKassa" style="font-weight: normal; color: rgb(127, 17, 3);">0</span> <span style="font-weight: normal; color: rgb(127, 17, 3);">руб.</span>
                                            </div>
                                        </div>';


                        $SummNal = 0;
                        $SummBeznal = 0;

                        echo '
                                <div class="cellsBlock400px">
                                    <div class="cellLeft" style="font-size: 90%;">
                                        Наличные<br>
                                        <i style="color: rgb(127, 17, 3);">остаток</i>
                                    </div>
                                    <div class="cellRight calculateOrder" style="font-size: 13px; font-weight: normal;">
                                        <span id="SummNal">0</span> руб. <!--<i class="fa fa-refresh" aria-hidden="true" title="Обновить" style="color: red;" onclick="calculateDailyReportSumm();"></i>--><br>
                                        <span id="SummNalOstatok" style="color: rgb(127, 17, 3);">0</span> руб.
                                    </div>
                                </div>';

                        echo '
                                <div class="cellsBlock400px">
                                    <div class="cellLeft" style="font-size: 90%;">Безнал.</div>
                                    <div class="cellRight calculateOrder" style="font-size: 13px; font-weight: normal;">
                                        <span id="SummBeznal">0</span> руб. <!--<i class="fa fa-refresh" aria-hidden="true" title="Обновить" style="color: red;" onclick="calculateDailyReportSumm();"></i>-->
                                    </div>
                                </div>';

                        //Получаем данные по отчету касса
                        //var_dump(ajaxShowResultCashbox($datastart, $dataend, $filial_id, 0, 1));

                        $SummNalStomCosm = 0;
                        $SummBeznalStomCosm = 0;

                        $SummCertNal = 0;
                        $SummCertBeznal = 0;
                        $CertCount = 0;

                        $SummGiveOutCash = 0;

                        $SummAbonNal = 0;
                        $SummAbonBeznal = 0;
                        $AbonCount = 0;

                        $SummSolarNal = 0;
                        $SummSolarBeznal = 0;
                        $SolarCount = 0;

                        $SummRealizNal = 0;
                        $SummRealizBeznal = 0;
                        $RealizCount = 0;

                        $result = ajaxShowResultCashbox($datastart, $dataend, $filial_id, 0, 1, false);

                        if (!empty($result)) {
                            if (!empty($result['rezult'])) {
                                foreach ($result['rezult'] as $item) {
                                    if ($item['summ_type'] == 1) {
                                        $SummNalStomCosm += $item['summ'];
                                    }
                                    if ($item['summ_type'] == 2) {
                                        $SummBeznalStomCosm += $item['summ'];
                                    }
                                }
                            }
                            //сертификаты
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
                            //абонементы
                            if (!empty($result['rezult_abon'])) {

                                $AbonCount = count($result['rezult_abon']);

                                foreach ($result['rezult_abon'] as $item) {
                                    if ($item['summ_type'] == 1) {
                                        $SummAbonNal += $item['cell_price'];
                                    }
                                    if ($item['summ_type'] == 2) {
                                        $SummAbonBeznal += $item['cell_price'];
                                    }
                                }
                            }
                            //солярий
                            if (!empty($result['rezult_solar'])) {

                                $SolarCount = count($result['rezult_solar']);

                                foreach ($result['rezult_solar'] as $item) {
                                    if ($item['summ_type'] == 1) {
                                        $SummSolarNal += $item['summ'];
                                    }
                                    if ($item['summ_type'] == 2) {
                                        $SummSolarBeznal += $item['summ'];
                                    }
                                }
                            }
                            //реализация
                            if (!empty($result['rezult_realiz'])) {

                                $AbonRealiz = count($result['rezult_realiz']);

                                foreach ($result['rezult_realiz'] as $item) {
                                    if ($item['summ_type'] == 1) {
                                        $SummRealizNal += $item['summ'];
                                    }
                                    if ($item['summ_type'] == 2) {
                                        $SummRealizBeznal += $item['summ'];
                                    }
                                }
                            }
                            //Расходы
                            if (!empty($result['rezult_give_out_cash'])){
                                foreach ($result['rezult_give_out_cash'] as $item) {
                                    $SummGiveOutCash += $item['summ'];
                                }
                            }
                        }

                        echo '
                                <div class="cellsBlock400px" style="font-size: 90%;">
                                    <div class="cellLeft">
                                        Средства из отчёта "Касса"<br>
                                        <span style="font-size:80%; color: #999; ">сумма из нарядов</span>
                                    </div>
                                    <div class="cellRight" id="general">
                                        <!--<div style="margin: 2px 0;">Наличная оплата: <b><i id="SummNalStomCosm" class="allSummNal">' . $SummNalStomCosm . '</i></b> руб.</div>
                                        <div style="margin: 2px 0;">Безналичная оплата: <b><i id="SummBeznalStomCosm" class="allSummBeznal">' . $SummBeznalStomCosm . '</i></b> руб.</div>
                                        <div style="margin: 6px 0 2px;">Продано сертификатов: <b><i id="CertCount" class="">' . $CertCount . '</i></b> шт.</div>
                                        <div style="margin: 2px 0;">- наличная оплата: <b><i id="SummCertNal" class="allSummNal">' . $SummCertNal . '</i></b> руб.</div>
                                        <div style="margin: 2px 0;">- безналичная оплата: <b><i id="SummCertBeznal" class="allSummBeznal">' . $SummCertBeznal . '</i></b> руб.</div>-->
                                        
                                                                                <div style="margin: 2px 0;">Наличная оплата: <b><i id="SummNalStomCosm" class="allSummNal">' . $SummNalStomCosm . '</i></b> руб.</div>
                                        <div style="margin: 2px 0;">Безналичная оплата: <b><i id="SummBeznalStomCosm" class="allSummBeznal">' . $SummBeznalStomCosm . '</i></b> руб.</div>
                                        
                                        <div style="margin: 6px 0 2px;">Продано сертификатов: <b><i id="CertCount" class="">' . $CertCount . '</i></b> шт.</div>
                                        <div style="margin: 2px 0;">- нал. оплата: <b><i id="SummCertNal" class="allSummNal">' . $SummCertNal . '</i></b> руб.</div>
                                        <div style="margin: 2px 0;">- безнал. оплата: <b><i id="SummCertBeznal" class="allSummBeznal">' . $SummCertBeznal . '</i></b> руб.</div>
                                        
                                        <div style="margin: 6px 0 2px;">Продано абонементов: <b><i id="AbonCount" class="">' . $AbonCount . '</i></b> шт.</div>
                                        <div style="margin: 2px 0;">- нал. оплата: <b><i id="SummAbonNal" class="allSummNal">' . $SummAbonNal . '</i></b> руб.</div>
                                        <div style="margin: 2px 0;">- безнал. оплата: <b><i id="SummAbonBeznal" class="allSummBeznal">' . $SummAbonBeznal . '</i></b> руб.</div>
                                        
                                        <div style="margin: 6px 0 2px;">Посещения солярия: <b><i id="SolarCount" class="">' . $SolarCount . '</i></b> шт.</div>
                                        <div style="margin: 2px 0;">- нал. оплата: <b><i id="SummSolarNal" class="allSummNal">' . $SummSolarNal . '</i></b> руб.</div>
                                        <div style="margin: 2px 0;">- безнал. оплата: <b><i id="SummSolarBeznal" class="allSummBeznal">' . $SummSolarBeznal . '</i></b> руб.</div>
                                        
                                        <div style="margin: 6px 0 2px;">Реализация: <b><i id="RealizCount" class="">' . $RealizCount . '</i></b> шт.</div>
                                        <div style="margin: 2px 0;">- нал. оплата: <b><i id="SummRealizNal" class="allSummNal">' . $SummRealizNal . '</i></b> руб.</div>
                                        <div style="margin: 2px 0;">- безнал. оплата: <b><i id="SummRealizBeznal" class="allSummBeznal">' . $SummRealizBeznal . '</i></b> руб.</div>
                                    </div>
                                </div>';


                        if (($finances['see_all'] == 1) || $god_mode) {
                            echo '
                                <div class="cellsBlock400px" style="font-size: 90%;">
                                    <div class="cellLeft">
                                        Аренда
                                        <span style="font-size:80%; color: #999; "></span>
                                    </div>
                                    <div class="cellRight">
                                        <input type="text" id="arendaNal" class="itogSummInputNal" style="font-size: 12px; color: rgb(206, 0, 255);" value="'.$dailyReports_j[0]['arenda'].'"><span  style="font-size: 90%;"> руб.</span>
                                    </div>
                                </div>';
                        }else {

                            echo '
                                <input type="hidden" id="arendaNal" class="itogSummInput" value="' . $dailyReports_j[0]['arenda'] . '">';
                        }

                        /*echo '
                                <div class="cellsBlock400px" style="font-size: 90%; display: none;">
                                    <div class="cellLeft">
                                        Ортопантомограмма + КТ
                                        <span style="font-size:80%; color: #999; "></span>
                                    </div>
                                    <div class="cellRight">
                                        <span style="font-size:90%; color: #5f5f5f; ">Нал. </span><br><input type="text" id="ortoSummNal" class="allSummInputNal" value="'.$dailyReports_j[0]['temp_orto_nal'].'" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span><br>
                                        <span style="font-size:90%; color: #5f5f5f; ">Безнал. </span><br><input type="text" id="ortoSummBeznal" class="allSummInputBeznal" value="'.$dailyReports_j[0]['temp_orto_beznal'].'" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span>
                                    </div>
                                </div>';

                        echo '
                                <div class="cellsBlock400px" style="font-size: 90%; display: none;">
                                    <div class="cellLeft">
                                        Специалисты<br>
                                        <span style="font-size:80%; color: #999; ">для ПР72</span>
                                    </div>
                                    <div class="cellRight">
                                        <span style="font-size:90%; color: #5f5f5f; ">Нал. </span><br><input type="text" id="specialistSummNal" class="allSummInputNal" value="'.$dailyReports_j[0]['temp_specialist_nal'].'" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span><br>
                                        <span style="font-size:90%; color: #5f5f5f; ">Безнал. </span><br><input type="text" id="specialistSummBeznal" class="allSummInputBeznal" value="'.$dailyReports_j[0]['temp_specialist_beznal'].'" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span>
                                    </div>
                                </div>';*/

                        echo '<input type="hidden" id="specialistSummNal" class="allSummInputNal" value="'.$dailyReports_j[0]['temp_specialist_nal'].'">';
                        echo '<input type="hidden" id="specialistSummBeznal" class="allSummInputBeznal" value="'.$dailyReports_j[0]['temp_specialist_beznal'].'">';

                        echo '<input type="hidden" id="ortoSummNal" class="allSummInputNal" value="'.$dailyReports_j[0]['temp_orto_nal'].'">';
                        echo '<input type="hidden" id="ortoSummBeznal" class="allSummInputBeznal" value="'.$dailyReports_j[0]['temp_orto_beznal'].'">';

/*                        echo '
                                <div class="cellsBlock400px" style="font-size: 90%;">
                                    <div class="cellLeft">
                                        Анализы<br>
                                        <span style="font-size:80%; color: #999; ">для ПР72</span>
                                    </div>
                                    <div class="cellRight">
                                        <span style="font-size:90%; color: #5f5f5f; ">Нал. </span><br><input type="text" id="analizSummNal" class="allSummInputNal" value="'.$dailyReports_j[0]['temp_analiz_nal'].'" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span><br>
                                        <span style="font-size:90%; color: #5f5f5f; ">Безнал. </span><br><input type="text" id="analizSummBeznal" class="allSummInputBeznal" value="'.$dailyReports_j[0]['temp_analiz_beznal'].'" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span>
                                    </div>
                                </div>';*/

                        echo '<input type="hidden" id="analizSummNal" class="allSummInputNal" value="'.$dailyReports_j[0]['temp_analiz_nal'].'">';
                        echo '<input type="hidden" id="analizSummBeznal" class="allSummInputBeznal" value="'.$dailyReports_j[0]['temp_analiz_beznal'].'">';

/*                        echo '
                                <div class="cellsBlock400px" style="font-size: 90%;">
                                    <div class="cellLeft">
                                        Солярий<br>
                                        <span style="font-size:80%; color: #999; "></span>
                                    </div>
                                    <div class="cellRight">
                                        <span style="font-size:90%; color: #5f5f5f; ">Нал. </span><br><input type="text" id="solarSummNal" class="allSummInputNal" value="'.$dailyReports_j[0]['temp_solar_nal'].'" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span><br>
                                        <span style="font-size:90%; color: #5f5f5f; ">Безнал. </span><br><input type="text" id="solarSummBeznal" class="allSummInputBeznal" value="'.$dailyReports_j[0]['temp_solar_beznal'].'" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span>
                                    </div>
                                </div>';*/

                        echo '<input type="hidden" id="solarSummNal" class="allSummInputNal" value="'.$dailyReports_j[0]['temp_solar_nal'].'">';
                        echo '<input type="hidden" id="solarSummBeznal" class="allSummInputBeznal" value="'.$dailyReports_j[0]['temp_solar_beznal'].'">';

                        //конец левого блока
                        echo '
                            </div>';

                        //начало правого блока
                        echo '
                            <div style="display: inline-block; vertical-align: top; /*border: 2px dotted rgb(201, 206, 206);*/">';

                        echo '
                                <div class="cellsBlock400px" style="font-size: 90%;">
                                    <div class="cellLeft">
                                        Расход <!--<a href="giveout_cash.php?filial_id='.$filial_id.'&d=01&m='.$dailyReports_j[0]['month'].'&y='.$dailyReports_j[0]['year'].'"><i class="fa fa-sign-in" aria-hidden="true" title="Перейти к расходам"></i></a>--><br>
                                        <span style="font-size:80%; color: #999; ">Выдано из кассы</span>
                                    </div>
                                    <div class="cellRight" style="color: red;">
                                        <!--<input type="text" id="summMinusNal" class="summMinus" style="font-size: 12px; color: red; " value="'.$dailyReports_j[0]['temp_giveoutcash'].'"><span  style="font-size: 90%;"> руб.</span>-->
                                        <!--<input type="text" id="summMinusNal" class="summMinus" style="font-size: 12px; color: red; " value="$SummGiveOutCashNal"><span  style="font-size: 90%;"> руб.</span>-->
                                        <span id="summMinusNal" class="summMinus" style="font-size: 12px; color: red; ">'.$SummGiveOutCash.'</span><span  style="font-size: 90%;"> руб.</span>
                                        <a href="giveout_cash.php?filial_id='.$filial_id.'&d='.$dailyReports_j[0]['day'].'&m='.$dailyReports_j[0]['month'].'&y='.$dailyReports_j[0]['year'].'" class="ahref button_tiny" style="font-size: 80%; cursor: pointer; float: right;">Подробно</a>
                                    </div>
                                </div>';

//                        if (($finances['see_all'] == 1) || $god_mode) {
//                            echo '
//                                <div class="cellsBlock400px" style="font-size: 90%;">
//                                    <div class="cellLeft">
//                                        Банк<br>
//                                        <span style="font-size:80%; color: #999; "></span>
//                                    </div>
//                                    <div class="cellRight">
//                                        <span style="font-size:90%; color: #5f5f5f; ">Нал. </span><br><input type="text" id="bankSummNal" class="summMinus" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span><br>
//                                        <!--<span style="font-size:90%; color: #5f5f5f; ">Безнал. </span><br><input type="text" id="solarSummBeznal" class="allSummInputBeznal" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span>-->
//                                    </div>
//                                </div>';
//
//                            echo '
//                                <div class="cellsBlock400px" style="font-size: 90%;">
//                                    <div class="cellLeft">
//                                        АН<br>
//                                        <span style="font-size:80%; color: #999; "></span>
//                                    </div>
//                                    <div class="cellRight">
//                                        <span style="font-size:90%; color: #5f5f5f; ">Нал. </span><br><input type="text" id="directorSummNal" class="summMinus" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span><br>
//                                        <!--<span style="font-size:90%; color: #5f5f5f; ">Безнал. </span><br><input type="text" id="solarSummBeznal" class="allSummInputBeznal" value="0" style="font-size: 12px;"><span  style="font-size: 90%;"> руб.</span>-->
//                                    </div>
//                                </div>';
//                        }else {
//                            echo '
//                                <input type="hidden" id="bankSummNal" value="' . $dailyReports_j[0]['bank_summ_nal'] . '">';
//                            echo '
//                                <input type="hidden" id="directorSummNal" value="' . $dailyReports_j[0]['director_summ_nal'] . '">';
//                        }

                        echo '
                            </div>';
                        //конец правого блока

                        echo '
                        </div>';

                        echo '
                            <input type="button" class="b" value="Применить" onclick="fl_editDailyReport_add('.$_GET['report_id'].');">';

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