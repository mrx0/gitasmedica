<?php
	
//tabel_subtraction_percent2_f.php
//

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){

        include_once 'DBWork.php';
        include_once 'functions.php';

        require 'variables.php';

        //ID табеля
        $tabel_id = $_POST['tabel_id'];
        //Тип оплаты
        $paidout_type = $_POST['paidout_type'];
        //Сумма, которую хотим выдать сейчас (аванс, зп... не важно)
        $iWantMyMoney = $_POST['summ'];
        //Сумма которая в табеле
        $paidout_summ_tabel = $_POST['paidout_summ_tabel'];
        //Ночной ли табель
        $noch = false;

        $tabel_j = array();

        $filials_j = getAllFilials(false, false, false);
        //var_dump($filials_j);

        $msql_cnnct = ConnectToDB ();

        //Наряды с данными
        $invoices_j = array();
        //Позиции в нарядах
        $invoices_j_ex = array();
        //ID нарядов
        $invoices_ids_arr = array();
        //Оплаты
        $payments_j = array();
        //Итоговый массив, куда соберем общие суммы по филиалам
        $itog_filials_summ = array();

        //Позиции, которые прошли как подарки пациентам,
        //но нам же надо дать ЗП за них
        //НЕ разбиваем по филиалам
        //Филиал мы присвоим от филиала наряда, в котором был подарок
        $gift_invoice_ex = array();
        //Сумма позиций с подарками, НЕ разбитые по филиалам, на которых был сделан наряд
        //просто чтоб отследить отдельно эту сумму
        $gift_invoice_summ = 0;
        //Страховые позиции в наряде
        //возьмём всю сумму и привяжем её к филиалу, где был сделан наряд
        $filial_insure_invoice_ex = array();
        //Итоговый массив, куда соберем суммы по филиалам по тем работам,
        // которые мы хотим и должны оплатить другому человеку
        $itog_filials_summ_not4tou = array();
        //Сумма со всех филиалов
        $itog_all_filial_summ = 0;
        //Массив с процентами по филиалам
        $itog_filials_percents = array();
        //Массив, где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
        $summ4ZP = array();
        //Массив, где ключ - это ID филиала, а значение - это сколько всего УЖЕ было выдано денег с каких филиалов
        $summ4ZP_prev = array();
        //Сумма ЗП, которую мы могли бы выдать сейчас всю, как будто еще ничего не выплачивали
        $summ4ZP_All = 0;
        //Массив, где ключ - это ID филиала, а значение - это сколько с какого филиала ПРЕДЛАГАЕТСЯ вычесть сумму на выдачу ЗП
        $filial_subtraction = array();


        //Получаем табель
        //!!!по сути нам это надо только для того, чтоб получить id worker'a
        //!!!в будущем надо убрать и получать id через POST, как и id табеля
        $query = "SELECT * FROM `fl_journal_tabels` WHERE `id` = '{$tabel_id}' LIMIT 1";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            $tabel_j = mysqli_fetch_assoc($res);
            //var_dump($tabel_j);

            $worker_id = $tabel_j['worker_id'];
            //var_dump($worker_id);
            //Обозначим общую сумму к выплате. Собираем её из всех начислений (сумма РЛ, отпуск, больничный, премия...)
            //Если доктор (стом, косм,...)
//            if (($tabel_j['type'] == 5) || ($tabel_j['type'] == 6) || ($tabel_j['type'] == 10)) {
//                //$summ4ZP_All = intval($tabel_j['summ_calc'] + $tabel_j['surcharge']);
//                $summ4ZP_All = $tabel_j['summ_calc'] + $tabel_j['surcharge'];
////                var_dump('1');
////                var_dump($tabel_j['summ_calc']);
////                var_dump('2');
////                var_dump($tabel_j['surcharge']);
//            }else{
//                $summ4ZP_All = $tabel_j['summ'];
////                var_dump('3');
////                var_dump($tabel_j['surcharge']);
//            }
            //Я хз, че я пытался сделать выше, но теперь так будет
            //Хотя я вообще не понимаю, что я пытаюсь тут сделать...
            $summ4ZP_All = $tabel_j['summ'] + $tabel_j['surcharge'];
            if ($tabel_j['type'] == 7){
                $summ4ZP_All += $tabel_j['summ_calc'];
            }

        }
        //var_dump($tabel_j);
        //var_dump($summ4ZP_All);


        if (!empty($tabel_j)) {
            //Если стоматологи/косметологи
            if (($tabel_j['type'] == 5) || ($tabel_j['type'] == 6)) {
                //Если больничный или отпускной !!! или на карту ?!!!, то с одного филиала предложим
                if (($paidout_type == 2) || ($paidout_type == 3) || ($paidout_type == 4)){
                    $itog_filials_percents[$tabel_j['office_id']] = 100;
                    $summ4ZP[$tabel_j['office_id']] = $summ4ZP_All;
                }else{
                    //Наряды с позициями в нарядах + статус (открыт/закрыт) наряда, + филиал
                    $query = "
                        SELECT ji_ex.*, ji.office_id AS filial_id, ji.status AS status
                        FROM `fl_journal_calculate` jcalc
                        LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '{$tabel_id }' AND jtabex.noch = '0'
                        LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
                        RIGHT JOIN `journal_invoice_ex` ji_ex ON ji_ex.invoice_id = ji.id  
                        WHERE jtabex.calculate_id = jcalc.id
                        ORDER BY `ji_ex`.`invoice_id` ASC";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($invoices_j_ex, $arr);

                            array_push($invoices_ids_arr, "`invoice_id`='" . $arr['invoice_id'] . "'");
                        }
                    }
                    //var_dump($invoices_j_ex);.
                    //var_dump($invoices_ids_arr);

                    //Оставим только уникальные ID
                    $invoices_ids_arr = array_unique($invoices_ids_arr);
                    //var_dump($invoices_ids_arr);

                    //Строчка для следующего запроса
                    $invoices_ids_str = implode(' OR ', $invoices_ids_arr);

                    //Получаем все оплаты по всем нарядам
                    //С ограничениями по датам внесения
                    //                $query = "
                    //                    SELECT jp.*
                    //                    FROM `journal_payment` jp
                    //                    WHERE MONTH(jp.date_in) = '{$tabel_j['month']}' AND YEAR(jp.date_in) = '{$tabel_j['year']}' AND ({$invoices_ids_str})";
                    //Без ограничений
                    $query = "
                        SELECT jp.*
                        FROM `journal_payment` jp
                        WHERE ({$invoices_ids_str})";
                    //var_dump($query);

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($payments_j, $arr);

                            //Раскидаем суммы оплат сразу по филиалам
                            //предварительно добавив в массив элемент с ID филиала, если его не было
                            if (!isset($itog_filials_summ[$arr['filial_id']])) {
                                $itog_filials_summ[$arr['filial_id']] = 0;
                            }
                            $itog_filials_summ[$arr['filial_id']] += $arr['summ'];

                        }
                    }

                    //var_dump($payments_j);
                    //echo '<span style="font-size: 85%;">Сколько куда принесли денег</span>';
                    //var_dump($itog_filials_summ);

                    //    var_dump($invoices_ids_str);
                    //    var_dump(str_replace("`invoice_id`", "ji_ex.invoice_id", $invoices_ids_str));

                    //Получаем позиции, которые прошли как подарки пациентам
                    $query = "
                        SELECT ji_ex.*, ji.office_id
                        FROM `journal_invoice_ex` ji_ex
                        LEFT JOIN `journal_invoice` ji ON ji.id = ji_ex.invoice_id
                        WHERE (" . str_replace("`invoice_id`", "ji_ex.invoice_id", $invoices_ids_str) . ")
                        AND ji_ex.gift = '1'";
                    //echo ($query);

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            //var_dump($arr);

                            //Сразу добавляем в итоговый массив,
                            //предварительно добавив в массив элемент с ID филиала, если его не было
                            if (!isset($itog_filials_summ[$arr['office_id']])) {
                                $itog_filials_summ[$arr['office_id']] = 0;
                            }
                            $itog_filials_summ[$arr['office_id']] += $arr['itog_price'];

                            //Просто чтоб отследить отдельно эту сумму
                            $gift_invoice_summ += $arr['itog_price'];
                        }
                    }
                    //var_dump($itog_filials_summ);


                    //Посмотрим, а не страховой ли наряд (сделать мы это можем только пройдясь по всем позициям из наряда)
                    //Если да, возьмём всю сумму и привяжем её к филиалу, где был сделан наряд
                    $query = "
                        SELECT ji_ex.*, ji.office_id
                        FROM `journal_invoice_ex` ji_ex
                        LEFT JOIN `journal_invoice` ji ON ji.id = ji_ex.invoice_id
                        WHERE (" . str_replace("`invoice_id`", "ji_ex.invoice_id", $invoices_ids_str) . ")
                        AND ji_ex.insure <> '0' AND ji_ex.insure_approve = '1'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    //Если что-то нашли страхового
                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {

                            //Сразу добавляем в итоговый массив,
                            //предварительно добавив в массив элемент с ID филиала, если его не было
                            if (!isset($itog_filials_summ[$arr['office_id']])) {
                                $itog_filials_summ[$$arr['office_id']] = 0;
                            }
                            $itog_filials_summ[$arr['office_id']] += $arr['itog_price'];

                        }
                    }
                    //var_dump($itog_filials_summ);

                    //А теперь выберем РЛ, которые были не этому исполнителю.
                    //Ибо может быть так, что наряд один, а исполнителей больше.
                    //Выберем их и вычтем суммы из общих
                    //... а может потом и не придется, заставим админов делать отдельные наряды
                    $query = "
                      SELECT * 
                      FROM `fl_journal_calculate` 
                      WHERE ({$invoices_ids_str}) 
                      AND `worker_id` <> '{$worker_id}'";
                    //echo($query);

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    //Если нашли РЛ, которые не принадлежат указанному в табеле исполнителю
                    //... и тут на самом деле жопа, потому что денег могли принести в одном, а работу сделают на другом
                    //... и ппц
                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {

                            //Сразу добавляем в итоговый массив,
                            //предварительно добавив в массив элемент с ID филиала, если его не было
                            //... Тут у нас опять филиал берётся по наряду, что наверное не есть хорошо,
                            //... но пока так
                            if (!isset($itog_filials_summ_not4tou[$arr['office_id']])) {
                                $itog_filials_summ_not4tou[$arr['office_id']] = 0;
                            }
                            $itog_filials_summ_not4tou[$arr['office_id']] += $arr['summ_inv'];
                        }
                    }
                    //var_dump($itog_filials_summ_not4tou);

                    //Вычтем с филиалов суммы, которые уйдут в зп другому человеку (ассистент например)
                    //!!!перенести это потом в цикл выше, ибо зачем лишнее вот это вот всё
                    foreach ($itog_filials_summ_not4tou as $filial_id => $summ) {
                        if (isset($itog_filials_summ[$filial_id])) {
                            $itog_filials_summ[$filial_id] -= $summ;
                        }
                    }
                    //echo '<span style="font-size: 85%;">Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)</span>';
                    //var_dump($itog_filials_summ);

                    //Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)
                    $itog_all_filial_summ = array_sum($itog_filials_summ);


                    //Вычислим процентное соотношение
                    foreach ($itog_filials_summ as $filial_id => $summ) {

                        $percent_value = 0;

                        //предварительно добавляем в массив элемент с ID филиала, если его не было
                        //!!! потом сделать это выше, когда суммы собираем
                        if (!isset($itog_filials_percents[$filial_id])) {
                            $itog_filials_percents[$filial_id] = 0;
                        }

                        $percent_value = (100 * $summ) / $itog_all_filial_summ;

                        $itog_filials_percents[$filial_id] = $percent_value;
                    }
                    //var_dump($itog_filials_percents);
                    //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
                    //var_dump(array_sum($itog_filials_percents));
                }
            }else{
                $itog_filials_percents[$tabel_j['office_id']] = 100;
                $summ4ZP[$tabel_j['office_id']] = $summ4ZP_All;
            }
            //var_dump($itog_filials_percents);
            //var_dump($summ4ZP);

            //Посчитаем по сколько могли бы выдать с каждого филиала на текущий момент, если бы выдавали со всей суммы
            //пропорционально полученным деньгам
            foreach ($itog_filials_percents as $filial_id => $percent) {
                $summ4ZP[$filial_id] = $summ4ZP_All / 100 * $percent;
            }
            //echo '<span style="font-size: 85%;"><b>Ключевое1 !</b> Сколько ВСЕГО БУДЕТ в итоге выдано с каждого филиала из общего объема денег</span>';
            //var_dump($summ4ZP);
            //Просто для самоконтроля
            //var_dump(array_sum($summ4ZP));

            if (($paidout_type == 1) || ($paidout_type == 7)){
                $paidout_type_str = "AND ((`type` = '1') OR (`type` = '7'))";
            }else{
                $paidout_type_str = "AND `type` = '{$paidout_type}'";
            }


            //Получаем данные из БД о выдачах по этим нарядам, будто мы уже выдавали аванс
            //!!!!! НЕ ПРАВИЛЬНО !!! РАСЧЕТ % ПРОИСХОДИТ ТОЛЬКО В ПРЕДЕЛАХ ОДНОГО ТАБЕЛЯ!!!

            if (!$noch) {
                $query = "
                      SELECT `filial_id`, `summ`
                      FROM `fl_journal_filial_subtractions` 
                      WHERE `tabel_id` = '{$tabel_id}' ".$paidout_type_str;
            }else{
                $query = "
                      SELECT `filial_id`, `summ`
                      FROM `fl_journal_filial_subtractions` 
                      WHERE `tabel_noch_id` = '{$tabel_id}' ".$paidout_type_str;
            }

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {
                    if (!isset($summ4ZP_prev[$arr['filial_id']])){
                        $summ4ZP_prev[$arr['filial_id']] = 0;
                    }
                    $summ4ZP_prev[$arr['filial_id']] += $arr['summ'];
                }
            }

            //fl_journal_filial_subtractions
            //        $summ4ZP_prev = array(
            //            19 => 9091,
            //            16 => 909
            //        );

            //var_dump($summ4ZP_prev);

            //Если ранее были выплаты по этому табелю, то вычтем эти суммы из итоговых остатков,
            //доступных к выдаче денег
            if (!empty($summ4ZP_prev)) {
                foreach ($summ4ZP_prev as $filial_id => $summ) {
                    if (isset($summ4ZP[$filial_id])) {
                        $summ4ZP[$filial_id] -= $summ;
                    }
                }
            }
            //echo '<span style="font-size: 85%;"><b>Ключевое2 !</b> Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег. ПОСЛЕ вычета того, что уже с этих филиалов вычли</span>';
            //var_dump($summ4ZP);
            //Просто для самоконтроля
            //var_dump(array_sum($summ4ZP));


            //Избегаем деления на 0
            if (array_sum($summ4ZP) > 0) {

                //Посчитаем, сколько откуда реально выдадим с учетом суммы,
                //которую реально хотим выдать $iWantMyMoney
                foreach ($itog_filials_percents as $filial_id => $percent) {
                    if (!isset($filial_subtraction[$filial_id])) {
                        $filial_subtraction[$filial_id] = 0;
                    }
                    $filial_subtraction[$filial_id] = $summ4ZP[$filial_id] / 100 * ($iWantMyMoney * 100 / array_sum($summ4ZP));
                }
                //echo '<span style="font-size: 85%;"><b>Ключевое3 !</b> Сколько ПРЕДЛАГАЕТСЯ ВСЕГО выдать с какого филиала в ЭТОТ раз</span>';
                //var_dump($filial_subtraction);

                //Просто для самоконтроля, должна получиться общая сумма выдаче
                //var_dump(array_sum($filial_subtraction));
                //var_dump(array_sum($filial_subtraction) + array_sum($summ4ZP_prev));

                //Чистим данные в сессии
                //unset($_SESSION['subtraction_data']);

                //Сохраним данные в сессии для дальнейшего использования
                if (!isset($_SESSION['subtraction_data'])) {
                    $_SESSION['subtraction_data'] = array();
                }

                //Сколько всего надо вычесть по каждой позиции со всех филиалов
                //$_SESSION['subtraction_data'][$tabel_id]['pos_subtraction'] = $pos_subtraction;
                //Сколько надо вычесть сейчас по каждой позиции с каждого филиала
                $_SESSION['subtraction_data'][$tabel_id]['subtraction_temp'] = $filial_subtraction;
                //Всего вычтем с филиалов
                //$_SESSION['subtraction_data'][$tabel_id]['pos_subtraction_summ_filials'] = $pos_subtraction_summ_filials;
                //var_dump($_SESSION['subtraction_data']);

            }

            echo '
                <table style="display: none;">';

            echo '
                    <tr>
                        <td colspan="2">
                            <span style="font-size: 95%;">Будет вычтено с филиалов:</span>
                        </td>
                    </tr>';

            if (!empty($filials_j)) {
                foreach ($filials_j as $f_id => $filials_j_data) {
                    echo '<tr>';
                    //Значение, сколько с какого филиала будем выдавать
                    $value = '';
                    $fontcolor_str = '';

                    //Если мы посчитали, что с этого филиала будем выдавать столько, то пожалуйста
                    if (isset($filial_subtraction[$f_id])) {
                        $value = round($filial_subtraction[$f_id]);
                        $fontcolor_str = ' background: #FFF; color: rgba(3, 14, 79, 0.96); font-weight: bold;';
                        $placeholder = '';
                    }
                    echo '<td><div class="button_tiny" style="width: 100px; font-size: 75%; cursor: pointer; ' . $fontcolor_str . '" onclick="allSubtractionInHere(' . $f_id . ', ' . array_sum($filial_subtraction) . ');">' . $filials_j_data['name2'] . '<i class="fa fa-chevron-right" style="color: green; float: right; margin-top: 3px;" aria-hidden="true"></i></div></td>';
                    echo '<td><input type="text" size="10" class="filial_subtraction fil_sub_' . $f_id . '" filial_id="' . $f_id . '" name="" placeholder="0" autocomplete="off" value="' . $value . '"></td>';
                    echo '<tr>';
                }
            } else {
                echo '
                    <tr>
                        <td>
                            <span style="font-size: 85%; color: red;">Ошибка #55. Невозможно рассчитать.</span>
                        </td>
                    </tr>';
            }


            echo '
                </table>
                <div class="button_tiny" style="display: none; width: 100px; font-size: 75%; cursor: pointer;" onclick="tabelSubtractionPercent(' . $tabel_id . ', ' . $tabel_j['type'] . ', '.$paidout_type.', ' . $iWantMyMoney . ', '.$paidout_summ_tabel.');">По умолчанию</div>
                <div style="display: none; width: 250px; background-color: #EEE; border: 1px dotted #CCC; margin: 10px; padding: 5px; font-size: 85%;">
                    <div>Всего: <span id="fil_sub_sum" style="font-weight: bold;">' . array_sum($filial_subtraction) . '</span></div>
                    <div><span id="fil_sub_msg"></span></div>
                </div>
                <input type="hidden" id="iWantMyMoney" value="' . $iWantMyMoney . '">';

        }
    }
}

?>