<?php

//fl_main_report2.php
//Финальный отчет v2.0

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		//if (($finances['see_all'] == 1) || $god_mode){
        //if (($_SESSION['id'] == 270) || ($god_mode)){
        /*!!!Тест PDO*/
        include_once('DBWorkPDO.php');

        include_once 'DBWork.php';
        include_once 'functions.php';
        include_once 'widget_calendar.php';
        include_once 'ffun.php';
        include_once 'fl_main_report2_f.php';
        require 'variables.php';

        //Опция доступа к филиалам конкретных сотрудников
        $optionsWF = getOptionsWorkerFilial($_SESSION['id']);
        //var_dump($optionsWF);

        if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){


            //$permissions_sort_method = [5,6,10,7,4,13,14,15,9,12,11,777];

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
                $filial_id = 15;
            }

            if (!$god_mode) {
                if (!in_array($filial_id, $optionsWF[$_SESSION['id']])) {
                    $filial_id = $optionsWF[$_SESSION['id']][0];
                }
            }


            //Пробуем получить данные (тест)
            $datas = fl_main_report2_f($month, $year, $filial_id);

            $rezult_arr = $datas['rezult_arr'];
            $cashbox_nal = $datas['cashbox_nal'];
            $arenda = $datas['arenda'];
            $money_from_outside = $datas['money_from_outside'];
            $beznal = $datas['beznal'];
            $giveoutcash_summ = $datas['giveoutcash_summ'];
            $subtractions_j = $datas['subtractions_j'];
            $subtractions_j_beznal = $datas['subtractions_j_beznal'];
//            $subtractions_j_all = $datas['subtractions_j_all'];
            $subtractions_summ = $datas['subtractions_summ'];
            $subtractions_summ_beznal = $datas['subtractions_summ_beznal'];
//            $subtractions_summ_all = $datas['subtractions_summ_all'];
            $paidouts_temp_j = $datas['paidouts_temp_j'];
            $paidouts_temp_summ = $datas['paidouts_temp_summ'];
            $giveoutcash_ex_j = $datas['giveoutcash_ex_j'];
            $bank_summ = $datas['bank_summ'];
            $director_summ = $datas['director_summ'];
            $temp_solar_beznal = $datas['temp_solar_beznal'];
            $temp_solar_nal = $datas['temp_solar_nal'];
            $percents_j = $datas['percents_j'];
            $giveoutcash_j = $datas['giveoutcash_j'];
            $give_out_cash_types_j = $datas['give_out_cash_types_j'];
            $prev_month_filial_summ_arr = $datas['prev_month_filial_summ_arr'];
            $zapis_j = $datas['zapis_j'];
            $pervich_summ_arr_new = $datas['pervich_summ_arr_new'];
//            var_dump($subtractions_summ_beznal);

            //var_dump($datas['error_invoices']);

            if (!empty($datas['error_invoices'])){
                echo '<div class="query_neok" style="padding: 10px 4px 2px; margin-left: 10px; width: 50vw;">Наряды с ошибками в категориях:';

                for($i=0; $i < count($datas['error_invoices']); $i++){
                    echo '<a href="invoice.php?id='.$datas['error_invoices'][$i].'" class="ahref button_tiny" style="margin: 0 3px;" target="_blank" rel="nofollow noopener">#'.$datas['error_invoices'][$i].'</a>';
                }
                echo '</div>';
            }


            //Расходы на материалы внесённые вручную
            $db = new DB();

            //Выбрать все категории
            $query = "
            SELECT j_mc.*
            FROM `journal_material_costs_test` j_mc
            WHERE j_mc.filial_id = :filial_id AND j_mc.month = :month AND j_mc.year = :year
            ORDER BY j_mc.create_time";

            $args = [
                'month' => $month,
                'year' => $year,
                'filial_id' => $filial_id
            ];

            $material_costs_j = $db::getRows($query, $args);
            //var_dump($material_costs_j);

            $material_costs = array();

            //Пересоберём массив
            if (!empty($material_costs_j)){
                foreach ($material_costs_j as $data){
                    if (!isset($material_costs[$data['category_id']])){
                        $material_costs[$data['category_id']] = array();
                        $material_costs[$data['category_id']]['data'] = array();
                        $material_costs[$data['category_id']]['summ'] = 0;
                    }
                    array_push($material_costs[$data['category_id']]['data'], $data);
                    $material_costs[$data['category_id']]['summ'] += $data['summ'];
                }
            }
            //var_dump($material_costs);

            $dop = 'filial_id='.$filial_id;

            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav never_print_it">
                            <a href="fl_consolidated_report_admin.php?filial_id='.$filial_id.'&m='.$month.'&y='.$year.'" class="b">Сводный отчёт по филиалу</a>
                            <a href="fl_money_from_outside_add.php" class="b">Добавить приход вручную</a>
                        </div>
                        <h2 style="padding: 0;">Отчёт '.$filials_j[$filial_id]['name2'].' / '.$monthsName[$month].' '.$year.'</h2>
                    </header>';



            echo '
                    <div id="data">';

//            var_dump(strtotime('-2 month', gmmktime(0, 0, 0, 1, date('m', time()), date('Y', time()))));
//            var_dump(strtotime('-2 month', gmmktime(0, 0, 0, 1, $month, $year)));
//            var_dump(time());

            if (TRUE) {
                echo '				
                            <div id="errrror"></div>';

                echo '<div class="no_print">';
                echo widget_calendar ($month, $year, 'fl_main_report2.php', $dop);
                echo '</div><br>';

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
                echo '
                                <input type="checkbox" name="material_costs_show" id="material_costs_show" value="1"> <span style="font-size:80%;">Показать расходы на материалы</span>
                            </div>';

                //Пробуем вывести то, что получили

                echo '
                        <div id="report" class="report" style="margin-top: 10px;">';


                echo '<div style="display: inline-block; vertical-align: top;">';
                //
                //            echo '
                //                    <li class="filterBlock">
                //                        <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                //                           наличные касса
                //                        </div>
                //                        <div class="cellRight" style="width: 200px; min-width: 200px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';
                //
                //            echo number_format($cashbox_nal, 0, '.', ' ');
                //
                //                echo '
                //                        </div>
                //                    </li>';

                //Сумма по страховым
                $insure_summ = 0;

                if (isset($rezult_arr[5])) {
                    if (isset($rezult_arr[5]['insure_data'])) {
                        $insure_summ = array_sum($rezult_arr[5]['insure_data']);
                    }
                }

                echo '
                        <div style="border: 1px solid #CCC;">';
                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Приход</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(236, 247, 95, 0.52); text-align: right;">
                                    <b id="allSumm">' . number_format($cashbox_nal + $beznal + $insure_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Безнал</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(236, 247, 95, 0.52);">
                                    <div style="float:left;">' . number_format($beznal, 0, '.', ' ') . '</div>
                                </div>
                            </li>';

                if (isset($rezult_arr[5])) {
                    if (isset($rezult_arr[5]['insure_data'])) {
                        echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Страховые</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(236, 247, 95, 0.52);">
                                    <div style="float:left;">' . number_format(array_sum($rezult_arr[5]['insure_data']), 0, '.', ' ') . '</div>
                                </div>
                            </li>';
                    }
                }
                //border: 1px solid rgba(255, 107, 0, 0.52);
                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Нал касса</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(236, 247, 95, 0.52);">
                                    <div style="float:left;">' . number_format($cashbox_nal, 0, '.', ' ') . '</div>
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(95, 247, 145, 0.3);">
                                   <b>Аренда</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(95, 247, 145, 0.3);">
                                    <div style="float:left;">' . number_format($arenda, 0, '.', ' ') . '</div>
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Приход Общий</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(236, 247, 95, 0.52); text-align: right;">
                                    <b id="allSumm">' . number_format($cashbox_nal + $arenda + $beznal + $insure_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                if ($money_from_outside > 0) {
                    echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(247,109,2,0.3);">
                                   <b>Приход извне</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(247,109,2,0.3);">
                                    <div style="float:left;">' . number_format($money_from_outside, 0, '.', ' ') . '</div>';

                    if (($_SESSION['permissions'] == 3) || ($god_mode)) {
                        echo '                
                                    <span class="never_print_it" style="font-size: 120%; float: right;">
                                        <i class="fa fa-times" aria-hidden="true" style="color: red; cursor: pointer;" title="Удалить все приходы" onclick="Ajax_MoneyFromOutsideDelete('.$filial_id.', '.$year.', \''.$month.'\');"></i>
                                    </span>';
                    }

                    echo '
                                </div>
                            </li>';
                }

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 110%; font-weight: bold; background-color: rgba(236, 247, 95, 0.52);">
                                   <b>Всего нал:</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(236, 247, 95, 0.52); text-align: right;">
                                    <b>' . number_format($cashbox_nal + $arenda, 0, '.', ' ') . '</b>                                
                                </div>
                            </li>';


                echo '
                        </div>';


                //!!! Суммы долгов/авансов за прошлые месяцы (пока ручной ввод)
                //                11 => ЦО,
                //                12 => Авиаконструкторов 10,
                //                13 => Просвещения 54,
                //                14 => Комендантский 17,
                //                15 => Энгельса 139,
                //                16 => Гражданский 114,
                //                17 => Чернышевского 17,
                //                18 => Некрасова 58,
                //                19 => Просвещения 72,
                //                20 => Литейный 59,
                //                21 => Бассейная 45

                //            $prev_month_filial_summ_arr = array(
                //                11 => 0,
                //                12 => -151929,
                //                13 => -169961,
                //                14 => -232,
                //                15 => -411380,
                //                16 => -684164,
                //                17 => -533,
                //                18 => -16780,
                //                19 => -218297,
                //                20 => -323,
                //                21 => 0
                //            );

                $prev_month_filial_summ = 0;

                if (isset($prev_month_filial_summ_arr[$filial_id])) {
                    $prev_month_filial_summ = $prev_month_filial_summ_arr[$filial_id];
                }

                $bg_color = 'rgba(219, 215, 214, 0.44)';

                //var_dump($prev_month_filial_summ_arr[$filial_id]);

                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 310px; min-width: 310px; font-size: 100%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                    <b>Дефицит предыдущего м-ца</b>';

                if (($_SESSION['permissions'] == 3) || ($god_mode)) {
                    echo '                
                                    <span class="never_print_it" style="font-size: 120%; float: right;">
                                        <i class="fa fa-times" aria-hidden="true" style="color: red; cursor: pointer;" title="Удалить" onclick="Ajax_PrevMonthDeficitDelete('.$filial_id.', '.$year.', \''.$month.'\');"></i>
                                    </span>';
                }

                echo '
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: ' . $bg_color . ';">
                                    
                                </div>
                                <div class="cellRight" style=" font-size: 120%; width: 180px; min-width: 180px; background-color: ' . $bg_color . '; text-align: right;">
                                    <b>' . number_format($prev_month_filial_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                echo '
                        </div>';


                //Расходы
                //var_dump($giveoutcash_j);
                //var_dump($give_out_cash_types_j);
                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgba(191, 191, 191, 0.38);">
                                   <b>Расходы</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(191, 191, 191, 0.38);">
    
                                </div>
                            </li>';

                $giveout_wo_type_summ = 0;
                $giveout_all_summ = 0;

                if (!empty($giveoutcash_j)) {
                    foreach ($giveoutcash_j as $type => $summ) {

                        $giveout_all_summ += $summ;

                        if ($type != 0) {
                            echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(191, 191, 191, 0.38);">
                                   <b>' . $give_out_cash_types_j[$type] . '</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(191, 191, 191, 0.38);">
                                    <div style="float:left;">' . number_format($summ, 2, '.', ' ') . '</div>
                                </div>
                            </li>';
                        } else {
                            $giveout_wo_type_summ += $summ;
                        }
                    }
                    echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(191, 191, 191, 0.38);">
                                   <b>Прочее</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(191, 191, 191, 0.38);">
                                    <div style="float:left;">' . number_format($giveout_wo_type_summ, 2, '.', ' ') . '</div>
                                </div>
                            </li>';
                }

                //Всего
                //var_dump($giveout_all_summ);
                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 110%; font-weight: bold; background-color: rgba(191, 191, 191, 0.38);">
                                   <b>Всего:</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(191, 191, 191, 0.38); text-align: right;">
                                    <b>' . number_format($giveoutcash_summ, 0, '.', ' ') . '</b>                                
                                </div>
                            </li>';

                echo '
                        </div>';

                //ЗП выданные

                //Для создания разных цветов полей
                $bg_color = 'rgba(219, 214, 214, 0.25)';

                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                   <b>Заработная плата</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(219, 215, 214, 0.44); text-align: right;">
                                    <b>На руки:</b><br>
                                    <b>' . number_format(($subtractions_summ-$subtractions_summ_beznal), 0, '.', ' ') . '</b><br>  
                                </div>
                            </li>';

                //таблица с ЗП по каждому сотруднику
                $personal_zp_str = '
                        
                        <table width="" style="border: 1px solid #CCC;">';

                //Загловок
                //            $personal_zp_str .= '
                //                        <tr>
                //                            <td colspan="6" style="font-size:80%;"><b>Выдачи сотрудникам:</b></td>
                //                        </tr>
                //                        <tr style="background-color: rgba(252, 237, 199, 0.77);">
                //                            <td style="width: 149px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">аванс</i></td>
                //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">зп</i></td>
                //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">отпускной</i></td>
                //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">больничный</i></td>
                //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><i style="color: orangered;">на карту</i></td>
                //                        </tr>';

                $personal_zp_str .= '
                            <tr>
                                <td colspan="2" style="font-size:80%;"><b>Выдачи сотрудникам:</b></td>
                            </tr>
                            <tr style="background-color: rgba(252, 237, 199, 0.77);">
                                <td style="width: 149px; outline: 1px solid rgb(233, 233, 233); text-align: center;"><!--Всего--></td>
                                <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;">
                                    <i style="color: orangered;">на руки</i>
                                </td>
                                <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;">
                                    <i style="color: orangered;">на карту</i>
                                </td>
                            </tr>';

                //Пошли по типам/должностям
//                var_dump($subtractions_j);
//                var_dump($subtractions_j_beznal);

                foreach ($subtractions_j as $permissions => $subtractions_data) {
                    //var_dump($subtractions_data);
                    //var_dump($permissions);

                    //Для создания разных цветов полей
                    //var_dump($bg_color);

                    if ($bg_color == 'rgba(219, 214, 214, 0.25)') {
                        $bg_color = 'rgba(219, 215, 214, 0.44)';
                    } else {
                        $bg_color = 'rgba(219, 214, 214, 0.25)';
                    }

                    //Сумма выданных денег по должностям
                    $permission_summ = 0;

                    echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: ' . $bg_color . ';">
                                   <!--<b style="color: orangered;">' . $permissions_j[$permissions]['name'] . '</b>-->
                                   <b style="color: rgb(0, 36, 255);">' . $permissions_j[$permissions]['name'] . '</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: ' . $bg_color . ';">
    
                                </div>
                            </li>';

                    //                $personal_zp_str .= '
                    //                        <tr style="background-color: rgba(225, 248, 220, 0.77);">
                    //                            <td style="width: 149px; outline: 1px solid rgb(233, 233, 233); text-align: left;"><b style="color: rgb(0, 36, 255); font-size: 90%;">'.$permissions_j[$permissions]['name'].'</b></td>
                    //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                    //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                    //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                    //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                    //                            <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                    //                        </tr>';

                    $personal_zp_str .= '
                            <tr style="background-color: rgba(225, 248, 220, 0.77);">
                                <td style="width: 149px; outline: 1px solid rgb(233, 233, 233); text-align: left;"><b style="color: rgb(0, 36, 255); font-size: 90%;">' . $permissions_j[$permissions]['name'] . '</b></td>
                                <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                                <td style="width: 89px; outline: 1px solid rgb(233, 233, 233); text-align: center;"></td>
                            </tr>';

                    //foreach ($subtractions_data as $type => $type_data){
                    //var_dump($typ);
                    //var_dump($type_data);

                    //Пошли по сотрудникам
                    foreach ($subtractions_data as $worker_id => $worker_data) {
                        //var_dump($worker_data);

                        $w_name = $worker_data['name'];
                        //var_dump($w_name);
                        //Выдано всего сотруднику
                        $fin_summ_w = 0;

                        $personal_zp_str .= '
                            <tr>
                                <td style="outline: 1px solid rgb(233, 233, 233); text-align: left;">' . $w_name . '</td>';

                        //Массив для распределения сумм по типам
                        $temp_summ_arr = array(1 => 0, 7 => 0, 2 => 0, 3 => 0, 4 => 0);
                        $temp_summ_arr_karta = array(1 => 0, 7 => 0, 2 => 0, 3 => 0, 4 => 0);

                        foreach ($worker_data['data'] as $type => $type_data) {
                            //                        var_dump($type);

                            //                        if ($type == 1) {
                            //                            $type_name = ' аванс ';
                            //                        } elseif ($type == 2) {
                            //                            $type_name = ' отпускной ';
                            //                        } elseif ($type == 3) {
                            //                            $type_name = ' больничный ';
                            //                        } elseif ($type == 4) {
                            //                            $type_name = ' на карту ';
                            //                        } elseif ($type == 7) {
                            //                            $type_name = ' зп ';
                            //                        } elseif ($type == 5) {
                            //                            $type_name = ' ночь ';
                            //                        } else {
                            //                            $type_name = ' !!!ошибка данных ';
                            //                        }

                            foreach ($type_data as $data) {
//                                var_dump($data);

                                //$w_name = $data['name'];
                                $fin_summ_w += $data['summ'];
                                $permission_summ += $data['summ'];

                                //на руки
                                if ($type != 4) {
                                    $temp_summ_arr[$type] += $data['summ'];
                                //на карту
                                }else{
                                    $temp_summ_arr_karta[$type] += $data['summ'];
                                }
                            }
                        }
                        //var_dump($temp_summ_arr);

                        //                    foreach ($temp_summ_arr as $temp_summ){
                        //
                        //                        $personal_zp_str .= '
                        //                            <td style="outline: 1px solid rgb(233, 233, 233); text-align: right;">
                        //                                '.$temp_summ.'
                        //                            </td>
                        //                            ';
                        //                    }
                        $personal_zp_str .= '
                                <td style="outline: 1px solid rgb(233, 233, 233); text-align: right;">
                                    ' . array_sum($temp_summ_arr) . '
                                </td>
                                <td style="outline: 1px solid rgb(233, 233, 233); text-align: right;">
                                    ' . array_sum($temp_summ_arr_karta) . '
                                </td>
                                ';


                        $personal_zp_str .= '
                            </tr>';
                    }

                    //Процент зп от выручки
                    $zp_percent = 0;

                    if (($cashbox_nal + $beznal + $insure_summ) > 0) {
                        $zp_percent = number_format(($permission_summ * 100 / ($cashbox_nal + $beznal + $insure_summ)), 2, '.', ' ');
                    }

                    echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: ' . $bg_color . ';">
                                   <!--<b>всего:</b>-->
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: ' . $bg_color . ';">
                                    <div id="permission_summ'.$permissions.'" permission_id="'.$permissions.'" style="float:left;">' . number_format($permission_summ, 0, '.', ' ') . '</div>
                                    <div class="percent'.$permissions.'" style="float:right;">' . $zp_percent . '%</div>
                                </div>
                            </li>';
                }
                //var_dump($permission_summ);

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 110%; font-weight: bold; background-color: rgba(219, 214, 214, 0.25);">
                                   <b>Всего:</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(219, 214, 214, 0.25); text-align: right;">
                                    <b>' . number_format($subtractions_summ, 0, '.', ' ') . '</b>                                
                                </div>
                            </li>';


                echo '
                        </div>';


                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 100%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                   <b>Прочие выдачи/расходы</b>
                                </div>
                                <div class="cellRight never_print_it" style="width: 180px; min-width: 180px; background-color: rgba(219, 215, 214, 0.44);">
                                    <a href="fl_paidout_another_test_in_tabel_add.php?filial_id=' . $filial_id . '" class="ahref b2 never_print_it"  target="_blank" rel="nofollow noopener">Добавить</a>
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(219, 215, 214, 0.44);">
                                    
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(219, 215, 214, 0.44); text-align: right;">
                                    <b>' . number_format($paidouts_temp_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                echo '
                        </div>';

                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 100%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                   <b>Банк</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(219, 215, 214, 0.44); text-align: right;">
    
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(219, 215, 214, 0.44);">
                                    
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(219, 215, 214, 0.44); text-align: right;">
                                    <b>' . number_format($bank_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                echo '
                        </div>';

                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 100%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                   <b>АН</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; background-color: rgba(219, 215, 214, 0.44);">
    
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(219, 215, 214, 0.44);">
                                    
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(219, 215, 214, 0.44); text-align: right;">
                                    <b>' . number_format($director_summ, 0, '.', ' ') . '</b>
                                </div>
                            </li>';

                echo '
                        </div>';


                //            //Выручка без страховых
                //            var_dump($cashbox_nal+$beznal+$arenda);
                //            //Расходы
                //            var_dump($giveoutcash_summ);
                //            //Выплаты персоналу
                //            var_dump($subtractions_summ);
                //            //Выплаты персоналу, которые не обрабатываются пока в программе
                //            var_dump($paidouts_temp_summ);
                //            //Банк
                //            var_dump($bank_summ);
                //            //АН
                //            var_dump($director_summ);


                //Остаток
                //var_dump(
                //$cashbox_nal
                // + $arenda
                // - $giveoutcash_summ
                // - $subtractions_summ
                // - $paidouts_temp_summ
                // - $bank_summ
                // - $director_summ
                // + $prev_month_filial_summ
                // + $subtractions_summ_beznal
                //);
                //var_dump($subtractions_summ_beznal);

                $ostatok = $cashbox_nal + $arenda - $giveoutcash_summ - $subtractions_summ + $subtractions_summ_beznal - $paidouts_temp_summ - $bank_summ - $director_summ + $prev_month_filial_summ + $money_from_outside;

                $ostatok = number_format($ostatok, 0, '.', ' ');


                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 310px; min-width: 310px; font-size: 120%; font-weight: bold; background-color: rgba(219, 215, 214, 0.44);">
                                   <b>Дефицит текущего месяца</b> 
                                </div>
                            </li>';

                echo '
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; background-color: rgba(219, 215, 214, 0.44);;">
                                    <button class="ahref b2 never_print_it" onclick="showPrevMonthDeficitAdd(' . $filial_id . ');">Сохранить</button>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 120%; background-color: rgba(219, 215, 214, 0.44);; text-align: right;">
                                    <div id="ostatokDeficit" style="font-weight: bold;">' . $ostatok . '</div>
                                </div>
                            </li>';
                echo '
                        </div>';


                echo '
                        <div style="border: 1px solid #CCC;">
                            <li class="filterBlock">
                                <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 140%; font-weight: bold; background-color: rgba(219, 214, 214, 0.25);">
                                   <b>Итог</b>
                                </div>
                                <div class="cellRight" style="width: 180px; min-width: 180px; font-size: 140%; background-color: rgba(219, 214, 214, 0.25); text-align: right;">
                                    <b>0</b>
                                </div>
                            </li>';

                echo '
                        </div>';


                echo '
                    </div>';


                //Процентное соотношение работ начатых в текущем месяце, опираясь на запись

                echo '<div style="display: inline-block; vertical-align: top; margin-left: 10px;">';


                //Сумма за детскую стоматологию
                $child_stom_summ = 0;

                //Если за детскую стоматологию есть сумма
                if (isset($rezult_arr[5])) {
                    if (isset($rezult_arr[5]['child_stom_summ'])) {
                        $child_stom_summ = $rezult_arr[5]['child_stom_summ'];
                    }
                }
                //var_dump($child_stom_summ);

                //Стоматология
                if (isset($rezult_arr[5])) {

                    //!!! Костыль.
                    // Предположительная сумма выручки по стоматологии
                    $stom_summ_temp = $cashbox_nal + $beznal + $insure_summ - ($temp_solar_nal + $temp_solar_beznal);

                    //Сумма по стоматологии будет разницей:
                    //Вся выручка минус косметология, специалисты, орто/кт, солярий
                    if (!empty($rezult_arr[7])) {
                        if (!empty($rezult_arr[7]['data'])) {
                            $stom_summ_temp -= array_sum($rezult_arr[7]['data']);
                        }
                    }
                    if (!empty($rezult_arr[6])) {
                        if (!empty($rezult_arr[6]['data'])) {
                            $stom_summ_temp -= array_sum($rezult_arr[6]['data']);
                        }
                    }
                    if (!empty($rezult_arr[10])) {
                        if (!empty($rezult_arr[10]['data'])) {
                            $stom_summ_temp -= array_sum($rezult_arr[10]['data']);
                        }
                    }

                    echo '
                        <li class="filterBlock">
                            <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                               Стоматология
                            </div>
                            <div class="cellRight" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';


                    if (!empty($rezult_arr[5])) {
                        if (!empty($rezult_arr[5]['data'])) {
                            //arsort($rezult_arr[5]['data']);

                            if ($stom_summ_temp < 0) $stom_summ_temp = 0;

                            //                        echo number_format(array_sum($rezult_arr[5]['data']) + $child_stom_summ, 0, '.', ' ').' ';
                            echo '<div id="summ5">'.number_format($stom_summ_temp, 0, '.', ' ').'</div>';

                            $average_check_summ = number_format($stom_summ_temp / ($pervich_summ_arr_new[5][1] + $pervich_summ_arr_new[5][2] + $pervich_summ_arr_new[5][3] + $pervich_summ_arr_new[5][4]), 0, '.', ' ');
//                            var_dump($stom_summ_temp);
//                            var_dump($pervich_summ_arr_new[5][1] + $pervich_summ_arr_new[5][2] + $pervich_summ_arr_new[5][3] + $pervich_summ_arr_new[5][4]);
//                            var_dump($average_check_summ);

                            //Средний чек
                            echo '<div id="average_check_5" style="/*display: none;*/ font-style: normal; font-size: 70%;">Ср. чек: <span id="average_check_summ_5" style="">'.$average_check_summ .'</span></div>';

                            //                        //!!! Костыль для %
                            //                        echo number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[5], 0, '.', ' ');
                            //                        var_dump(number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[5], 0, '.', ' '));
                            //                        var_dump($cashbox_nal + $beznal + $insure_summ);



                        } else {
                            echo 'нет данных';
                        }
                    } else {
                        echo 'нет данных';
                    }

                    echo '
                            </div>
                            <div class="cellRight material_costs" id="material_costs_summ_5" style="display: none !important">
                            </div>
                        </li>';
                }

                $material_costs_summ_5 = 0;
                $material_costs_summ_6 = 0;
                $material_costs_summ_10 = 0;

                $material_costs_summ_p_5 = 0;
                $material_costs_summ_p_6 = 0;
                $material_costs_summ_p_10 = 0;


                if (isset($rezult_arr[5])) {
                    if (!empty($rezult_arr[5])) {
                        if (!empty($rezult_arr[5]['data'])) {
                            arsort($rezult_arr[5]['data']);
                            //var_dump($rezult_arr[5]['data']);

                            foreach ($rezult_arr[5]['data'] as $percent_cat_id => $value) {

                                //$pervent_value = ;
                                //var_dump($percent_cat_id);

                                //Если все хорошо в массиве с данными
                                if ((strlen($percent_cat_id) > 0) && ($value != 0)) {
                                    //%
                                    $cat_prcnt_temp = $value * 100 / (array_sum($rezult_arr[5]['data']) + $child_stom_summ);
                                    //                                var_dump($cat_prcnt_temp);
                                    //                                var_dump($stom_summ_temp);
                                    //                                var_dump($stom_summ_temp / 100 * $cat_prcnt_temp);

                                    echo '
                                    <li class="filterBlock">
                                        <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                           <b>' . $percents_j[5][$percent_cat_id]['name'] . '</b>
                                        </div>
                                        <div class="cellRight" style="width: 120px; min-width: 120px;">
                                            <div style="float:left;">' . number_format($stom_summ_temp / 100 * $cat_prcnt_temp, 0, '.', ' ') . '</div> 
                                            <div style="float:right;">' . number_format($cat_prcnt_temp, 2, '.', '') . '%</div>
                                        </div>';
                                    echo '
                                        <div class="cellRight material_costs" style="display: none !important">';
                                    if (isset($material_costs[$percent_cat_id])){
                                        echo $material_costs[$percent_cat_id]['summ'].' / '.number_format(($material_costs[$percent_cat_id]['summ'] * 100 / $stom_summ_temp), 2, '.', ' ').'%';
                                        $material_costs_summ_5 += $material_costs[$percent_cat_id]['summ'];
                                        $material_costs_summ_p_5 += $material_costs[$percent_cat_id]['summ'] * 100 / $stom_summ_temp;
                                    }else{
                                        echo '0 / 0%';
                                    }
                                    echo '
                                        </div>';
                                    echo '
                                    </li>';
                                } else {

                                }
                            }
                            //Детство отдельно

                            if (array_sum($rezult_arr[5]['data']) + $child_stom_summ > 0) {
                                //%
                                $cat_prcnt_temp = $child_stom_summ * 100 / (array_sum($rezult_arr[5]['data']) + $child_stom_summ);
                            } else {
                                $cat_prcnt_temp = 0;
                            }


                            echo '
                                <li class="filterBlock">
                                    <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                       <b>Детство</b>
                                    </div>
                                    <div class="cellRight" style="width: 120px; min-width: 120px;">
                                        <div style="float:left;">' . number_format($stom_summ_temp / 100 * $cat_prcnt_temp, 0, '.', ' ') . '</div> <div style="float:right;">' . number_format($cat_prcnt_temp, 2, '.', '') . '%</div>
                                    </div>
                                </li>';
                        }
                    }
                    if (isset($zapis_j[5])) {
                        if (!empty($zapis_j[5])) {
                            //var_dump($zapis_j[5]);
                            echo '
                                <li class="filterBlock" style="text-align: center;">
                                    <table style="width: 250px;">
                                        <tr>
                                            <td>
                                                I
                                            </td>
                                            <td>
                                                I(ост)
                                            </td>
                                            <!--<td>
                                                II(6)
                                            </td>-->
                                            <td>
                                                II
                                            </td>
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <!--' . ($zapis_j[5]['pervich_summ_arr'][1] + $zapis_j[5]['pervich_summ_arr'][2]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[5][1] + $pervich_summ_arr_new[5][2]) . '</span>
                                            </td>
                                            <td>
                                                <!--' . $zapis_j[5]['pervich_summ_arr'][2] . '<br>-->
                                                <span style="/*color: red;*/">' . $pervich_summ_arr_new[5][2] . '</span>
                                            </td>
                                            <td>
                                                <!--' . ($zapis_j[5]['pervich_summ_arr'][3] + $zapis_j[5]['pervich_summ_arr'][4]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[5][3] + $pervich_summ_arr_new[5][4]) . '</span>
                                            </td>
                                            <!--<td>
                                                ' . $zapis_j[5]['pervich_summ_arr'][4] . '
                                            </td>-->
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                    </table>
                                </li>';
                        }
                    }
                }

                //Ассистенты
                if (isset($rezult_arr[7])) {
                    echo '
                        <li class="filterBlock">
                            <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                               Ассистенты
                            </div>
                            <div class="cellRight" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';


                    if (!empty($rezult_arr[7])) {
                        if (!empty($rezult_arr[7]['data'])) {
                            //arsort($rezult_arr[10]['data']);

                            echo '<span id="summ7">'.number_format(array_sum($rezult_arr[7]['data']), 0, '.', ' ') . '</span>';

                            //                        //!!! Костыль для %
                            //                        echo number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[7], 0, '.', ' ');
                            //                        var_dump(number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[7], 0, '.', ' '));

                            //Средний чек
                            //echo '<div id="average_check_7" style="/*display: none;*/ font-style: normal; font-size: 70%;">Ср. чек: <span id="average_check_summ_7" style="">0</span></div>';

                        } else {
                            echo 'нет данных';
                        }
                    } else {
                        echo 'нет данных';
                    }
                    echo '
                            </div>
                        </li>';
                }


                if (isset($rezult_arr[7])) {
                    if (!empty($rezult_arr[7])) {
                        if (!empty($rezult_arr[7]['data'])) {
                            arsort($rezult_arr[7]['data']);

//                            var_dump($rezult_arr[7]['data']);
//                            var_dump($percents_j);

                            foreach ($rezult_arr[7]['data'] as $percent_cat_id => $value) {

                                if (isset($percents_j[7][$percent_cat_id])) {
                                    $percent_cat_name = $percents_j[7][$percent_cat_id]['name'];
                                } else {
                                    $percent_cat_name = $percents_j[$percent_cat_id]['name'] . '<i class="fa fa-warning" aria-hidden="true"></i>';
                                }

                                echo '
                                <li class="filterBlock">
                                    <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                       <b>' . $percent_cat_name . '</b>
                                    </div>
                                    <div class="cellRight" style="width: 120px; min-width: 120px;">
                                        <div style="float:left;">' . number_format($value, 0, '.', ' ') . '</div> 
                                        <div style="float:right;">' . number_format((($value * 100) / array_sum($rezult_arr[7]['data'])), 2, '.', '') . '%</div>
                                    </div>';
//                                echo '
//                                    <div class="cellRight material_costs" style="display: none !important">';
//                                    if (isset($material_costs[$percent_cat_id])){
//                                        echo $material_costs[$percent_cat_id]['summ'].' / '.number_format(($material_costs[$percent_cat_id]['summ'] * 100 / $stom_summ_temp), 2, '.', ' ').'%';
//                                    }else{
//                                        echo '0 / 0%';
//                                    }
//                                    echo '
//                                </div>';
                                    echo '
                                    </li>';
                            }
                        }
                    }
                }


                //Косметология
                if (isset($rezult_arr[6])) {
                    echo '
                        <li class="filterBlock">
                            <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                               Косметология
                            </div>
                            <div class="cellRight" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';

                    if (!empty($rezult_arr[6])) {
                        if (!empty($rezult_arr[6]['data'])) {
//                            var_dump($rezult_arr[6]);
//                            var_dump(array_sum($rezult_arr[6]['data']));
                            //arsort($rezult_arr[5]['data']);

                            echo '<div id="summ6">'.number_format(array_sum($rezult_arr[6]['data']), 0, '.', ' ') . '</div>';

                            //Средний чек
                            $average_check_summ = number_format(array_sum($rezult_arr[6]['data']) / ($pervich_summ_arr_new[6][1] + $pervich_summ_arr_new[6][2] + $pervich_summ_arr_new[6][3] + $pervich_summ_arr_new[6][4]), 0, '.', ' ');
//                            var_dump($stom_summ_temp);
//                            var_dump($pervich_summ_arr_new[5][1] + $pervich_summ_arr_new[5][2] + $pervich_summ_arr_new[5][3] + $pervich_summ_arr_new[5][4]);
//                            var_dump($average_check_summ);

                            echo '<div id="average_check_6" style="/*display: none;*/ font-style: normal; font-size: 70%;">Ср. чек: <span id="average_check_summ_6" style="">'.$average_check_summ .'</span></div>';



                            //                        //!!! Костыль для %
                            //                        echo number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[6], 0, '.', ' ');
                            //                        var_dump(number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[6], 0, '.', ' '));

                        } else {
                            echo 'нет данных';
                        }
                    } else {
                        echo 'нет данных';
                    }
                    echo '
                            </div>
                            <div class="cellRight material_costs" id="material_costs_summ_6" style="display: none !important">
                            </div>
                        </li>';
                }


                if (isset($rezult_arr[6])) {
                    if (!empty($rezult_arr[6])) {
                        if (!empty($rezult_arr[6]['data'])) {
                            arsort($rezult_arr[6]['data']);

                            foreach ($rezult_arr[6]['data'] as $percent_cat_id => $value) {

                                //$pervent_value = ;


                                echo '
                                <li class="filterBlock">
                                    <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                       <b>' . $percents_j[6][$percent_cat_id]['name'] . '</b>
                                    </div>
                                    <div class="cellRight" style="width: 120px; min-width: 120px;">
                                        <div style="float:left;">' . number_format($value, 0, '.', ' ') . '</div> 
                                        <div style="float:right;">' . number_format((($value * 100) / array_sum($rezult_arr[6]['data'])), 2, '.', '') . '%</div>
                                    </div>';
                                echo '
                                    <div class="cellRight material_costs" style="display: none !important">';
                                    if (isset($material_costs[$percent_cat_id])){
                                        echo $material_costs[$percent_cat_id]['summ'].' / '.number_format(($material_costs[$percent_cat_id]['summ'] * 100 / array_sum($rezult_arr[6]['data'])), 2, '.', ' ').'%';
                                        $material_costs_summ_6 += $material_costs[$percent_cat_id]['summ'];
                                        $material_costs_summ_p_6 += $material_costs[$percent_cat_id]['summ'] * 100 / array_sum($rezult_arr[6]['data']);
                                    }else{
                                        echo '0 / 0%';
                                    }
                                    echo '
                                </div>';
                                echo '
                                    </li>';
                            }
                        }
                    }
                    if (isset($zapis_j[6])) {
                        if (!empty($zapis_j[6])) {
                            //var_dump($zapis_j[6]);
                            echo '
                                <li class="filterBlock" style="text-align: center;">
                                    <table style="width: 250px;">
                                        <tr>
                                            <td>
                                                I
                                            </td>
                                            <td>
                                                I(ост)
                                            </td>
                                            <td>
                                                II
                                            </td>
                                            <!--<td>
                                                II(6)
                                            </td>-->
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <!--' . ($zapis_j[6]['pervich_summ_arr'][1] + $zapis_j[6]['pervich_summ_arr'][2]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[6][1] + $pervich_summ_arr_new[6][2]) . '</span>
                                            </td>
                                            <td>
                                                <!--' . $zapis_j[6]['pervich_summ_arr'][2] . '<br>-->
                                                <span style="/*color: red;*/">' . $pervich_summ_arr_new[6][2] . '</span>
                                            </td>
                                            <td>
                                                <!--' . ($zapis_j[6]['pervich_summ_arr'][3] + $zapis_j[6]['pervich_summ_arr'][4]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[6][3] + $pervich_summ_arr_new[6][4]) . '</span>
                                            </td>
                                            <!--<td>
                                                ' . $zapis_j[6]['pervich_summ_arr'][4] . '
                                            </td>-->
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                    </table>
                                </li>';
                        }
                    }
                }

//                var_dump($temp_solar_nal);
//                var_dump($temp_solar_beznal);
                if ($temp_solar_nal + $temp_solar_beznal != 0) {
                    //Солярий
                    echo '
                        <li class="filterBlock">
                            <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                               Солярий
                            </div>
                            <div class="cellRight" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';

                    echo '<div id="summSol">'.number_format(($temp_solar_nal + $temp_solar_beznal), 0, '.', ' ') . '</div>';

                    //                //!!! Костыль для %
                    //                echo number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt['sol'], 0, '.', ' ');
                    //                var_dump(number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt['sol'], 0, '.', ' '));

                    //Средний чек
                    //echo '<div id="average_check_Sol" style="/*display: none;*/ font-style: normal; font-size: 70%;">Ср. чек: <span id="average_check_summ_Sol" style="">0</span></div>';


                    echo '
                            </div>
                        </li>';
                }

                //Специалисты
                if (isset($rezult_arr[10])) {
                    echo '
                        <li class="filterBlock">
                            <div class="cellLeft" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">
                               Специалисты
                            </div>
                            <div class="cellRight" style="width: 120px; min-width: 120px; font-size: 120%; font-weight: bold; background-color: rgb(199, 234, 234);">';


                    if (!empty($rezult_arr[10])) {
                        if (!empty($rezult_arr[10]['data'])) {
                            //arsort($rezult_arr[10]['data']);

                            echo '<span id="summ10">'.number_format(array_sum($rezult_arr[10]['data']), 0, '.', ' ') . '</span>';

                            //                        //!!! Костыль для %
                            //                        echo number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[10], 0, '.', ' ');
                            //                        var_dump(number_format(($cashbox_nal + $beznal + $insure_summ) / 100 * $rezult_arr_prcnt[10], 0, '.', ' '));

                            //Средний чек
                            $average_check_summ = number_format(array_sum($rezult_arr[10]['data']) / ($pervich_summ_arr_new[10][1] + $pervich_summ_arr_new[10][2] + $pervich_summ_arr_new[10][3] + $pervich_summ_arr_new[10][4]), 0, '.', ' ');
//                            var_dump($stom_summ_temp);
//                            var_dump($pervich_summ_arr_new[5][1] + $pervich_summ_arr_new[5][2] + $pervich_summ_arr_new[5][3] + $pervich_summ_arr_new[5][4]);
//                            var_dump($average_check_summ);

                            echo '<div id="average_check_10" style="/*display: none;*/ font-style: normal; font-size: 70%;">Ср. чек: <span id="average_check_summ_10" style="">'.$average_check_summ .'</span></div>';

                        } else {
                            echo 'нет данных';
                        }
                    } else {
                        echo 'нет данных';
                    }
                    echo '
                            </div>
                            <div class="cellRight material_costs" id="material_costs_summ_10" style="display: none !important">
                            </div>
                        </li>';
                }


                if (isset($rezult_arr[10])) {
                    if (!empty($rezult_arr[10])) {
                        if (!empty($rezult_arr[10]['data'])) {
                            arsort($rezult_arr[10]['data']);

                            foreach ($rezult_arr[10]['data'] as $percent_cat_id => $value) {

                                //$pervent_value = ;

//                                if (!isset($percents_j[10][$percent_cat_id])){
//                                    var_dump('!!!');
//                                }

                                echo '
                                <li class="filterBlock">
                                    <div class="cellLeft" style="width: 120px; min-width: 120px;">
                                       <b>' . $percents_j[10][$percent_cat_id]['name'] . '</b>
                                    </div>
                                    <div class="cellRight" style="width: 120px; min-width: 120px;">
                                        <div style="float:left;">' . number_format($value, 0, '.', ' ') . '</div> <div style="float:right;">' . number_format((($value * 100) / array_sum($rezult_arr[10]['data'])), 2, '.', '') . '%</div>
                                    </div>';
                                echo '
                                    <div class="cellRight material_costs" style="display: none !important">';
                                    if (isset($material_costs[$percent_cat_id])){
                                        echo $material_costs[$percent_cat_id]['summ'].' / '.number_format(($material_costs[$percent_cat_id]['summ'] * 100 / array_sum($rezult_arr[10]['data'])), 2, '.', ' ').'%';
                                        $material_costs_summ_10 += $material_costs[$percent_cat_id]['summ'];
                                        $material_costs_summ_p_10 += $material_costs[$percent_cat_id]['summ'] * 100 / array_sum($rezult_arr[10]['data']);
                                    }else{
                                        echo '0 / 0%';
                                    }
                                    echo '
                                </div>';
                                echo '
                                    </li>';
                            }
                        }
                    }
                    if (isset($zapis_j[10])) {
                        if (!empty($zapis_j[10])) {
                            //var_dump($zapis_j[10]);
                            echo '
                                <li class="filterBlock" style="text-align: center;">
                                    <table style="width: 250px;">
                                        <tr>
                                            <td>
                                                I
                                            </td>
                                            <td>
                                                I(ост)
                                            </td>
                                            <td>
                                                II
                                            </td>
                                            <!--<td>
                                                II(6)
                                            </td>-->
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <!--' . ($zapis_j[10]['pervich_summ_arr'][1] + $zapis_j[10]['pervich_summ_arr'][2]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[10][1] + $pervich_summ_arr_new[10][2]) . '</span>
                                            </td>
                                            <td>
                                                <!--' . $zapis_j[10]['pervich_summ_arr'][2] . '<br>-->
                                                <span style="/*color: red;*/">' . $pervich_summ_arr_new[10][2] . '</span>
                                            </td>
                                            <td>
                                                <!--' . ($zapis_j[10]['pervich_summ_arr'][3] + $zapis_j[10]['pervich_summ_arr'][4]) . '<br>-->
                                                <span style="/*color: red;*/">' . ($pervich_summ_arr_new[10][3] + $pervich_summ_arr_new[10][4]) . '</span>
                                            </td>
                                            <!--<td>
                                                ' . $zapis_j[10]['pervich_summ_arr'][4] . '
                                            </td>-->
                                            <td>
                                                
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                    </table>
                                </li>';
                        }
                    }
                }

                $personal_zp_str .= '</table>';


                //            echo '
                //                    <li class="filterBlock">
                //                        <div class="cellLeft" style="width: 120px; min-width: 120px;">
                //                           <b>Продано сертификатов</b>
                //                        </div>
                //                        <div class="cellRight" style="width: 180px; min-width: 180px;">
                //                            <div style="float:left;">'.count($certificates_j).' шт. на сумму: '.number_format($certificates_summSell, 0, '.', ' ').'</div>
                //                        </div>
                //                    </li>';
                //
                //            echo '
                //                    <li class="filterBlock">
                //                        <div class="cellLeft" style="width: 120px; min-width: 120px;">
                //                           <b>Оплаченно ранее проданными сертификатами</b>
                //                        </div>
                //                        <div class="cellRight" style="width: 180px; min-width: 180px;">
                //                            <div style="float:left;">на сумму: '.number_format($certificate_payments_summ, 0, '.', ' ').'</div>
                //                        </div>
                //                    </li>';


                echo '
                            <!--</ul>-->
                                    </div>
                                </div>
                            </div>';


                //ЗП по каждому сотруднику
                echo '
                            <div class="rezult_item3print" style="display: block; vertical-align: top; margin: 10px;">';

                echo '
                                <div style="vertical-align: top;">';

                echo $personal_zp_str;

                echo '
                                </div>
                                <div style="vertical-align: top; margin-top: 10px;">';

                //Прочие расходы подробно
                if (!empty($paidouts_temp_j)) {
                    //var_dump($paidouts_temp_j);

                    echo '
                                    <div class="" style="">
                                        <ul style="/*margin-left: 6px;*/ margin-bottom: 10px; font-size: 14px;">
                                            <li class="cellsBlock" style="width: auto; font-size: 80%;">
                                                <div class="cellOrder" style="width: 622px; text-align: left;">
                                                    <b>Прочие выдачи/расходы подробно:</b>
                                                </div>
                                            </li>';
                    echo '
                                            <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                    echo '
                                                <div class="cellOrder" style="text-align: center; border-right: none;">
                                                    <b>Кому</b>
                                                </div>
                                                <div class="cellName" style="text-align: center; border-right: none;">
                                                    <b>Тип</b>           
                                                </div>
                                                <div class="cellName" style="text-align: center; border-right: none;">
                                                    <b>Сумма</b>
                                                 </div>
                                                <div class="cellName" style="text-align: center;">
                                                    <b>Описание</b>
                                                </div>
                                                <div class="cellCosmAct info" style="text-align: center;">
                                                    -
                                                </div>';
                    echo '
                                            </li>';

                    foreach ($paidouts_temp_j as $paidouts_item) {

                        if ($paidouts_item['type'] == 1) {
                            $type_name = ' аванс ';
                        } elseif ($paidouts_item['type'] == 2) {
                            $type_name = ' отпускной ';
                        } elseif ($paidouts_item['type'] == 3) {
                            $type_name = ' больничный ';
                        } elseif ($paidouts_item['type'] == 4) {
                            $type_name = ' на карту ';
                        } elseif ($paidouts_item['type'] == 7) {
                            $type_name = ' зп ';
                        } elseif ($paidouts_item['type'] == 5) {
                            $type_name = ' ночь ';
                        } else {
                            $type_name = ' !!!ошибка данных ';
                        }

                        echo '
                                            <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                        echo '
                                                <div class="cellOrder" style="text-align: left; border-right: none;">
                                                    ' . WriteSearchUser('spr_workers', $paidouts_item['worker_id'], 'user', false) . '
                                                </div>
                                                <div class="cellName" style="text-align: center; border-right: none;">
                                                      ' . $type_name . '
                                                </div>
                                                <div class="cellName" style="text-align: right; border-right: none;">
                                                    ' . $paidouts_item['summ'] . '
                                                 </div>
                                                <div class="cellName" style="text-align: center;">
                                                    ' . $paidouts_item['descr'] . '
                                                </div>
                                                <div class="cellCosmAct info" style="font-size: 100%; text-align: center; " onclick="deletePaidoutsTempItem('. $paidouts_item['id'].', this);">
                                                    <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                                </div>';
                        echo '
                                            </li>';
                    }

                    echo '
                                        </ul>
                                    </div>';
                }

                echo '
                                </div>';
                echo '
                            </div>';

                //Расходы из кассы подробно
                if (!empty($giveoutcash_ex_j)) {

                    echo '
                            <div class="rezult_item3print" style="display: block; vertical-align: top; margin: 10px;">';

                    echo '
                                    <li class="cellsBlock" style="width: auto; ">
                                        <div class="cellOrder" style="width: 510px; text-align: left;">
                                            <b>Все расходы из кассы за месяц подробно:</b>
                                        </div>
                                    </li>';


                    foreach ($giveoutcash_ex_j as $item) {
                        //var_dump($item);

                        $bgColor = '';

                        echo '
                                    <li class="cellsBlock" style="width: auto; ' . $bgColor . '">';
                        echo '
                                        <div class="cellOrder" style="width: 120px; min-width: 120px; position: relative; border-right: none; border-top: none; font-size: 90%;">
                                            
                                            <a href="giveout_cash_all.php?filial_id=' . $item['office_id'] . '&d=' . date("d", strtotime($item['date_in'])) . '&m=' . date("m", strtotime($item['date_in'])) . '&y=' . date("Y", strtotime($item['date_in'])) . '" class="ahref" target="_blank" rel="nofollow noopener"><b>Расх. ордер #' . $item['id'] . '</b></a>
    
                                        </div>
                                        <div class="cellName" style="border-right: none; border-top: none;">';
                        if ($item['type'] != 0) {
                            echo $give_out_cash_types_j[$item['type']];
                        } else {
                            echo 'Прочее';

                            if ($item['additional_info'] != '') {
                                echo ':<br><i>' . $item['additional_info'] . '</i>';
                            }
                            //var_dump($item);
                        }

                        echo '                              
                                        </div>
                                        <div class="cellName" style="width: 90px; min-width: 90px; border-right: none; border-top: none;">
                                            <div style="text-align: right;">
                                                <span class="calculateInvoice" style="font-size: 90%; font-style: normal; color: #333;">' . $item['summ'] . '</span>
                                            </div>
                                        </div>
                                        <div class="cellName" style="border-right: none; border-top: none;">
                                            <div style="margin: 1px 0; padding: 1px 3px;">
                                                <span class="" style="font-size: 11px">' . $item['comment'] . '</span>
                                            </div>
                                        </div>';

                        echo '
                                        <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;">
                                            
                                        </div>';


                        echo '
                                    </li>';

                    }
                    echo '
                        </div>';
                }
            }else{
                echo '<span class="query_neok">Нет данных</span>';
            }

            echo '
                    </div>
                </div>';

            echo '
                            <div class="no_print" style="position: fixed; top: 45px; right: 10px; border: 1px solid #0C0C0C; border-radius: 5px; padding: 5px 5px; background-color: #FFFFFF">
                                <div class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;"
                                onclick="window.print(); ">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                </div>
                            </div>';

            echo '    
                <div id="doc_title">Отчёт - Асмедика</div>';

            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';

//            var_dump($material_costs_summ_5);
//            var_dump($material_costs_summ_6);
//            var_dump($material_costs_summ_10);

//            var_dump($material_costs_summ_p_5);
//            var_dump($material_costs_summ_p_6);
//            var_dump($material_costs_summ_p_10);

			echo '


				<script type="text/javascript">
				
				    //Изменили тут стили основные, чтоб умещалось
                    $(document).ready(function() {
                        $("#main").css({margin: \'0\', padding: \'10px 0 20px\'});                        
                        $("#header").css({"padding-left": \'10px\'});                        
                        $("#data").css({margin: \'10px \'});                        
                        $("#livefilter-list").css({width: \'min-content\'}); 
                                               
                                               
                       //$(".permission_summ").each(function(){
                           
                            var allSumm = 0;
                            var stomSumm = 0;
                            var cosmSumm = 0;
                            var assSumm = 0;
                            var specSumm = 0;
                            var solSumm = 0;
                            
                            if ($("#allSumm").html() !== undefined){
                                //allSumm = Number($("#allSumm").html().replace(/\s+/g, \'\'));
                                allSumm = Number(('.$cashbox_nal.' + '.$beznal.' + '.$insure_summ.'));
                            }
                            if ($("#summ5").html() !== undefined){
                                stomSumm = Number($("#summ5").html().replace(/\s+/g, \'\'));
                            }
                            if ($("#summ6").html() !== undefined){
                                cosmSumm = Number($("#summ6").html().replace(/\s+/g, \'\'));
                            }
                            if ($("#summ7").html() !== undefined){
                                assSumm = Number($("#summ7").html().replace(/\s+/g, \'\'));
                            }
                            if ($("#summ10").html() !== undefined){
                                specSumm = Number($("#summ10").html().replace(/\s+/g, \'\'));
                            }
                            if ($("#summSol").html() !== undefined){
                                solSumm = Number($("#summSol").html().replace(/\s+/g, \'\'));
                            }

                            var id = $(this).attr("permission_id");
                            //console.log(id);
//                            console.log(assSumm);
//                            console.log(allSumm);
                            
                            //if (($cashbox_nal + $beznal + $insure_summ) > 0) {
//                                number_format(($permission_summ * 100 / ($cashbox_nal + $beznal + $insure_summ)), 2, \'.\', \' \');
                            //}
                            
                            if (allSumm > 0){
                                
                                if ($(".percent5").html() !== undefined){
                                    $(".percent5").html(
                                        number_format((Number($("#permission_summ5").html().replace(/\s+/g, \'\')) * 100 / stomSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent5").html(0);
                                }
                                $(".percent5").append(" %");
                                
                                if ($(".percent6").html() !== undefined){
                                    $(".percent6").html(
                                        number_format((Number($("#permission_summ6").html().replace(/\s+/g, \'\')) * 100 / cosmSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent6").html(0);
                                }
                                $(".percent6").append(" %");
                                
                                if ($(".percent7").html() !== undefined){
                                    $(".percent7").html(
                                        number_format((Number($("#permission_summ7").html().replace(/\s+/g, \'\')) * 100 / (stomSumm + assSumm)), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent7").html(0);
                                }
                                $(".percent7").append(" %");
                                
                                if ($(".percent10").html() !== undefined){
                                    $(".percent10").html(
                                        number_format((Number($("#permission_summ10").html().replace(/\s+/g, \'\')) * 100 / specSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent10").html(0);
                                }
                                $(".percent10").append(" %");
                                
                                //адм
                                if ($(".percent4").html() !== undefined){
                                    $(".percent4").html(
                                        number_format((Number($("#permission_summ4").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent4").html(0);
                                }
                                $(".percent4").append(" %");
                                
                                if ($(".percent9").html() !== undefined){
                                    $(".percent9").html(
                                        number_format((Number($("#permission_summ9").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent9").html(0);
                                }
                                $(".percent9").append(" %");
                                
                                if ($(".percent11").html() !== undefined){
                                    $(".percent11").html(
                                        number_format((Number($("#permission_summ11").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent11").html(0);
                                }
                                $(".percent11").append(" %");
                                
                                if ($(".percent13").html() !== undefined){
                                    $(".percent13").html(
                                        number_format((Number($("#permission_summ13").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent13").html(0);
                                }
                                $(".percent13").append(" %");
                                
                                if ($(".percent14").html() !== undefined){
                                    $(".percent14").html(
                                        number_format((Number($("#permission_summ14").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent14").html(0);
                                }
                                $(".percent14").append(" %");
                                
                                if ($(".percent15").html() !== undefined){
                                    $(".percent15").html(
                                        number_format((Number($("#permission_summ15").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent15").html(0);
                                }
                                $(".percent15").append(" %");
                                    
                                if ($(".percent777").html() !== undefined){
                                    $(".percent777").html(
                                        number_format((Number($("#permission_summ777").html().replace(/\s+/g, \'\')) * 100 / allSumm), 2, \'.\', \' \')
                                    );
                                }else{
                                    $(".percent777").html(0);
                                }
                                $(".percent777").append(" %");
                            }else{
                            }
                       //});
                                               
                                               
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
                    
                    
                    $("#material_costs_show").click(function() {
                        
					    let checked_status = $(this).is(":checked");
					    
					    $("#material_costs_summ_5").html('.$material_costs_summ_5.' + " / " + '.number_format($material_costs_summ_p_5, 2, '.', "").' + "%");
					    $("#material_costs_summ_6").html('.$material_costs_summ_6.' + " / " + '.number_format($material_costs_summ_p_6, 2, '.', "").' + "%");
					    $("#material_costs_summ_10").html('.$material_costs_summ_10.' + " / " + '.number_format($material_costs_summ_p_10, 2, '.', "").' + "%");

					    
					    if (checked_status){
					        //console.log(checked_status);
					        
					        $(".material_costs").each(function(){
					            $(this).attr(\'style\',\'display: table-cell !important;\');
					        })
					    }else{
                            $(".material_costs").each(function(){
					            $(this).attr(\'style\',\'display:none !important\');
					        })
					    }
                    });
                    
                    $(document).ready(function(){

                        var elems = $(".blockControl"), count = elems.length;

                        elems.each( function() {
                            
                            //fl_getDailyReports($(this));
                            
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