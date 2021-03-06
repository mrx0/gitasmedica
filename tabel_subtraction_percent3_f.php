<?php
	
//tabel_subtraction_percent3_f.php
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
        //Переменная для "отсыпания" денег по филиалам и позициям
        $iWantMyMoney_temp = $iWantMyMoney;
        //Массив, чтоб размазать по филиалам желаемую сумму
        $iWantMyMoneyPercentFilials = array();
        //Сумма которая в табеле
        $paidout_summ_tabel = $_POST['paidout_summ_tabel'];

        $tabel_j = array();

        $filials_j = getAllFilials(false, false, false);
        //var_dump($filials_j);

        $msql_cnnct = ConnectToDB ();

        //Наряды с данными
        $invoices_j = array();
        //Позиции из РЛов, которые в табеле
        $calculates_j_ex = array();
        //Позиции в нарядах
        $invoices_j_ex = array();
        //ID нарядов
        $invoices_ids_arr = array();
        //ID позиций в нарядах
        $pos_ids_arr = array();
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
        $pos_substraction_prev = array();
        //Сумма ЗП, которую мы могли бы выдать сейчас всю, как будто еще ничего не выплачивали
        $summ4ZP_All = 0;
        //Массив, где ключ - это ID филиала, а значение - это сколько с какого филиала ПРЕДЛАГАЕТСЯ вычесть сумму на выдачу ЗП
        $filial_subtraction = array();
        //Массив, где ключ - это ID позиции РЛ, а содержаться тут будет то, с каких филиалов кула чего выдать
        $pos_subtraction = array();
        //Общие суммы по филиалам, которые будем выдавать, опираясь на суммы позиций РЛ
        $pos_subtraction_summ_filials = array();
        //Временный массив, содержащий суммы, которые можно и нужно будет вычесть
        //после того, как распределим желаемую сумму $iWantMyMoney (если она отличается от фактической)
        $pos_subtraction_temp = array();


        //Получаем табель
        //!!!по сути нам это надо только для того, чтоб получить id worker'a
        //!!!в будущем надо убрать и получать id через POST, как и id табеля
        $query = "SELECT `type`, `month`, `year`, `worker_id`, `summ_calc`, `surcharge` FROM `fl_journal_tabels` WHERE `id` = '{$tabel_id}' LIMIT 1";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            $tabel_j = mysqli_fetch_assoc($res);

            $worker_id = $tabel_j['worker_id'];
            //Обозначим общую сумму к выплате. Собираем её из всех начислений (сумма РЛ, отпуск, больничный, премия...)
            //Если доктор (стом)
            //if ($tabel_j['type'] == 5) {
                //$summ4ZP_All = intval($tabel_j['summ_calc'] + $tabel_j['surcharge']);
                $summ4ZP_All = $tabel_j['summ_calc'] + $tabel_j['surcharge'];
            //}

        }
        //var_dump($tabel_j);
        //var_dump($summ4ZP_All);

        //!!! временно, сумма позиций всех РЛ, которые во всех нарядах
//        $r = 0;

        if (!empty($tabel_j)) {
            //if ($tabel_j['type'] == 5) {

                //Все позиции в РЛах, которые в табеле на данный момент
                $query = "
                    SELECT `inv_pos_id`, `summ`
                    FROM `fl_journal_calculate_ex` 
                    WHERE `calculate_id` IN (
                      SELECT `calculate_id` 
                      FROM `fl_journal_tabels_ex` 
                      WHERE `tabel_id` = '{$tabel_id}'
                    )";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        if (!isset($calculates_j_ex[$arr['inv_pos_id']])){
                            $calculates_j_ex[$arr['inv_pos_id']] = 0;
                        }
                        $calculates_j_ex[$arr['inv_pos_id']] = $arr['summ'];
                        array_push($pos_ids_arr, "`inv_pos_id`='" . $arr['inv_pos_id'] . "'");
                    }
                }
//                var_dump($query);
//                var_dump($calculates_j_ex);
//                var_dump($pos_ids_arr);

                //Наряды с позициями в нарядах + статус (открыт/закрыт) наряда, + филиал + цены за каждую позицию в зп
//                $query = "
//                    SELECT ji_ex.invoice_id, ji.office_id AS filial_id, ji.status AS status, jcalc_ex.summ AS pos_sum
//                    FROM `fl_journal_calculate` jcalc
//                    LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '{$tabel_id }' AND jtabex.noch = '0'
//                    LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
//                    RIGHT JOIN `journal_invoice_ex` ji_ex ON ji_ex.invoice_id = ji.id
//                    LEFT JOIN `fl_journal_calculate_ex` jcalc_ex ON jcalc_ex.inv_pos_id = ji_ex.id
//                    WHERE jtabex.calculate_id = jcalc.id
//                    ORDER BY `ji_ex`.`invoice_id` ASC";

                //.... без позиций РЛ и их цен
                $query = "
                    SELECT ji_ex.invoice_id, ji.office_id AS filial_id, ji.status AS status
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
//                        $r += $arr['pos_sum'];
                        array_push($invoices_ids_arr, "`invoice_id`='" . $arr['invoice_id'] . "'");
                    }
                }

//                var_dump($r);
//                var_dump($invoices_j_ex);
                //var_dump($invoices_ids_arr);

                //Оставим только уникальные ID нарядов
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
//                var_dump($itog_filials_percents);
                //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
                //var_dump(array_sum($itog_filials_percents));

                //"Размажем" желаеммую сумму по филиалам сначала (и позициям сразу?)
                //2019-07-16 начал и тут же отказался от затеи этой
//                foreach ($itog_filials_percents as $filial_id => $percent) {
//                    //$iWantMyMoneyPercentFilials
//                }



                //Посчитаем по сколько могли бы выдать с каждого филиала на текущий момент, если бы выдавали со всей суммы
                //пропорционально полученным деньгам
                //!!!!! НЕ ПРАВИЛЬНО !!! РАСЧЕТ ТОЛЬКО В ПРЕДЕЛАХ ОДНОГО ТАБЕЛЯ!!!
                //2019-07-15 выключено, так как переходим на расчет по позициям РЛ
//                foreach ($itog_filials_percents as $filial_id => $percent) {
//                    $summ4ZP[$filial_id] = $summ4ZP_All / 100 * $percent;
//                }
                //echo '<span style="font-size: 85%;"><b>Ключевое1 !</b> Сколько ВСЕГО БУДЕТ в итоге выдано с каждого филиала из общего объема денег</span>';
                //var_dump($summ4ZP);
                //Просто для самоконтроля
                //var_dump(array_sum($summ4ZP));

                //Посчитаем, сколько откуда реально выдадим с учетом суммы,
                //которую реально хотим выдать $iWantMyMoney
                //!!!!! НЕ ПРАВИЛЬНО !!! РАСЧЕТ ТОЛЬКО В ПРЕДЕЛАХ ОДНОГО ТАБЕЛЯ!!!
                //2019-07-15 выключено, так как переходим на расчет по позициям РЛ
//                foreach ($itog_filials_percents as $filial_id => $percent) {
//                    if (!isset($filial_subtraction[$filial_id])) {
//                        $filial_subtraction[$filial_id] = 0;
//                    }
//                    $filial_subtraction[$filial_id] = $summ4ZP[$filial_id] / 100 * ($iWantMyMoney * 100 / array_sum($summ4ZP));
//                }
                //echo '<span style="font-size: 85%;"><b>Ключевое3 !</b> Сколько ПРЕДЛАГАЕТСЯ ВСЕГО выдать с какого филиала в ЭТОТ раз</span>';
                //var_dump($filial_subtraction);

                //Просто для самоконтроля, должна получиться общая сумма выдаче
                //var_dump(array_sum($filial_subtraction));
                //var_dump(array_sum($filial_subtraction) + array_sum($pos_substraction_prev));


                //!! ПО КАЖДОЙ ПОЗИЦИИ КАЖДОГО РЛ
                //Посчитаем, сколько откуда можем выдать с учетом общей суммы,
                //Которая на данный момент стоит в табеле
                foreach ($itog_filials_percents as $filial_id => $percent) {
                    foreach ($calculates_j_ex as $inv_pos_id => $summ){
                        if (!isset($pos_subtraction[$inv_pos_id])) {
                            $pos_subtraction[$inv_pos_id] = array();
                        }
                        if (!isset($pos_subtraction[$inv_pos_id][$filial_id])) {
                            $pos_subtraction[$inv_pos_id][$filial_id] = 0;
                        }
                        $pos_subtraction[$inv_pos_id][$filial_id] = round(($summ / 100 * $percent), 7);
                    }
                }
//                var_dump('$pos_subtraction_1');
//                var_dump($pos_subtraction);


                //Получаем данные из БД о выдачах по этим нарядам, будто мы уже выдавали аванс
                //Строчка для следующего запроса
                $pos_ids_str = implode(' OR ', $pos_ids_arr);

                //Полные суммы по филиалам и позициям, которые уже выдали
                $pos_substraction_filials_prev = array();

                $query = "
                      SELECT `inv_pos_id`, `filial_id`, `summ`
                      FROM `fl_journal_pos_filials_subtractions` 
                      WHERE ({$pos_ids_str})";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //Формируем массив
                        if (!isset($pos_substraction_prev[$arr['inv_pos_id']])) {
                            $pos_substraction_prev[$arr['inv_pos_id']] = array();
                        }
                        if (!isset($pos_substraction_prev[$arr['inv_pos_id']][$arr['filial_id']])) {
                            $pos_substraction_prev[$arr['inv_pos_id']][$arr['filial_id']] = 0;
                        }
                        $pos_substraction_prev[$arr['inv_pos_id']][$arr['filial_id']] += $arr['summ'];
                        //array_push($pos_substraction_prev_test, $arr);

                        //Суммы по филиалам
                        //if (!isset($pos_substraction_filials_prev[$arr['inv_pos_id']][$arr['filial_id']])) {
                        //}



                    }
                }
//                var_dump($query);
//                var_dump('$pos_substraction_prev');
//                var_dump($pos_substraction_prev);



                //!!! Временное значение, для теста
//                $pos_substraction_prev = array(
//                    277464 => array(
//                        19 => 269.3865815,
//                        16 => 11.6734185
//                    ),
//                    277465 => array(
//                        19 => 4144.4089457,
//                        16 => 179.5910543
//                    ),
//                    277466 => array(
//                        19 => 394.94,
//                        16 => 0
//                    ),
//                    277468 => array(
//                        19 => 0,
//                        16 => 0
//                    ),
//                    277469 => array(
//                        19 => 0,
//                        16 => 0
//                    ),
//                    277467 => array(
//                        19 => 0,
//                        16 => 0
//                    )
//                );


                //Если ранее были выплаты по этому табелю, то вычтем эти суммы из итоговых остатков,
                //доступных к выдаче денег
                if (!empty($pos_substraction_prev)) {
                    foreach ($pos_subtraction as $inv_pos_id => $filials) {
                        foreach ($filials as $filial_id => $summ){
                            if (isset($pos_substraction_prev[$inv_pos_id])){
                                if (isset($pos_substraction_prev[$inv_pos_id][$filial_id])){
                                    if ($pos_substraction_prev[$inv_pos_id][$filial_id] > 0){
//                                        var_dump($inv_pos_id);
//                                        var_dump($filial_id);
//                                        var_dump(number_format($pos_subtraction[$inv_pos_id][$filial_id], 7));
//                                        var_dump(number_format($pos_substraction_prev[$inv_pos_id][$filial_id], 7));
//                                        var_dump(number_format(number_format($pos_subtraction[$inv_pos_id][$filial_id], 7) - number_format($pos_substraction_prev[$inv_pos_id][$filial_id], 7));

                                        if (($pos_subtraction[$inv_pos_id][$filial_id] - $pos_substraction_prev[$inv_pos_id][$filial_id]) < 0){
                                            var_dump($inv_pos_id);
                                            var_dump($pos_subtraction[$inv_pos_id][$filial_id] - $pos_substraction_prev[$inv_pos_id][$filial_id]);
                                        }else {
                                            $pos_subtraction[$inv_pos_id][$filial_id] = $pos_subtraction[$inv_pos_id][$filial_id] - $pos_substraction_prev[$inv_pos_id][$filial_id];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //echo '<span style="font-size: 85%;"><b>Ключевое2 !</b> Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег. ПОСЛЕ вычета того, что уже с этих филиалов вычли</span>';
//                var_dump('$pos_subtraction_2');
//                var_dump($pos_subtraction);

                //!!! Временная переменная
                //$sss = 0;

                //Посчитаем, сколько откуда можем выдать с учетом суммы,
                //которую реально хотим выдать $iWantMyMoney
                foreach ($pos_subtraction as $inv_pos_id => $filials) {
                    foreach ($filials as $filial_id => $summ){
//                        if (!isset($pos_subtraction_temp[$inv_pos_id])) {
//                            $pos_subtraction_temp[$inv_pos_id] = array();
//                        }
//                        if (!isset($pos_subtraction_temp[$inv_pos_id][$filial_id])) {
//                            $pos_subtraction_temp[$inv_pos_id][$filial_id] = 0;
//                        }
//                        //Тут мы вычисляем какой процент на данный момент составляет
//                        //вот эта вот цена позиции в филиале
//                        //и по этому проценту берем часть от той суммы, которую хотим выдать $iWantMyMoney
//                        //...
//                        //var_dump(round(($iWantMyMoney / 100 * ($summ * 100 / $summ4ZP_All)), 7));
//                        $pos_subtraction_temp[$inv_pos_id][$filial_id] = round(($iWantMyMoney / 100 * ($summ * 100 / $summ4ZP_All)), 7);

                        //!!! Просто для проверки
                        //$sss += round($iWantMyMoney / 100 * ($summ * 100 / $summ4ZP_All), 7);

                        //2019-07-16 было решение в лоб, оно ниже, решил сделать плавно, оно выше
                        //будем "сразу" размазывать всю сумму по всем позициям и всем филиалам... наверное
                        //2019-07-19 вернул как было, буду тестить дальше
                        if (!isset($pos_subtraction_temp[$inv_pos_id])) {
                            $pos_subtraction_temp[$inv_pos_id] = array();
                        }
                        if (!isset($pos_subtraction_temp[$inv_pos_id][$filial_id])) {
                            $pos_subtraction_temp[$inv_pos_id][$filial_id] = 0;
                        }
                        if ($iWantMyMoney_temp > 0) {
                            if ($iWantMyMoney_temp > $summ) {
                                $pos_subtraction_temp[$inv_pos_id][$filial_id] = $summ;

                                $iWantMyMoney_temp = $iWantMyMoney_temp - $summ;
                            }else{
                                $pos_subtraction_temp[$inv_pos_id][$filial_id] = $iWantMyMoney_temp;

                                $iWantMyMoney_temp = 0;

                                //break;
                            }
                        }
                    }
                }
//                var_dump('$pos_subtraction_temp');
//                var_dump($pos_subtraction_temp);

//                //!!! Просто для проверки
//                var_dump('$sss');
//                var_dump($sss);


                //Посчитаем общие суммы по филиалам, откуда сколько будем выдавать в итоге
                foreach ($pos_subtraction_temp as $inv_pos_id => $filials){
                    foreach ($filials as $filial_id => $summ){
                        if (!isset($pos_subtraction_summ_filials[$filial_id])) {
                            $pos_subtraction_summ_filials[$filial_id] = 0;
                        }
                        $pos_subtraction_summ_filials[$filial_id] += $summ;
                    }
                }
//                var_dump('$pos_subtraction_summ_filials');
//                var_dump($pos_subtraction_summ_filials);
//                //!! проверка самого себя сумма общая, которую выдадим со всех филиалов
//                var_dump(intval(array_sum($pos_subtraction_summ_filials)));


                //Сохраним данные в сессии для дальнейшего использования
                if (!isset($_SESSION['subtraction_data'])){
                    $_SESSION['subtraction_data'] = array();
                }

                //Сколько всего надо вычесть по каждой позиции со всех филиалов
                //$_SESSION['subtraction_data'][$tabel_id]['pos_subtraction'] = $pos_subtraction;
                //Сколько надо вычесть сейчас по каждой позиции с каждого филиала
                $_SESSION['subtraction_data'][$tabel_id]['pos_subtraction_temp'] = $pos_subtraction_temp;
                //Всего вычтем с филиалов
                //$_SESSION['subtraction_data'][$tabel_id]['pos_subtraction_summ_filials'] = $pos_subtraction_summ_filials;
                //var_dump($_SESSION['subtraction_data']);

                echo '
                    <table>';

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
                        if (isset($pos_subtraction_summ_filials[$f_id])) {
                            $value = round($pos_subtraction_summ_filials[$f_id]);
                            $fontcolor_str = ' background: #FFF; color: rgba(3, 14, 79, 0.96); font-weight: bold;';
                            $placeholder = '';
                        }
                        echo '<td><div class="button_tiny" style="width: 100px; font-size: 75%; cursor: pointer; ' . $fontcolor_str . '" onclick="allSubtractionInHere(' . $f_id . ', ' . intval(array_sum($pos_subtraction_summ_filials)) . ');">' . $filials_j_data['name2'] . '<i class="fa fa-chevron-right" style="color: green; float: right;" aria-hidden="true"></i></div></td>';
                        echo '<td><input type="text" size="10" class="filial_subtraction fil_sub_' . $f_id . '" filial_id="' . $f_id . '" name="" placeholder="0" autocomplete="off" value="' . $value . '" disabled></td>';
                        echo '<tr>';
                    }
                } else {
                    echo '
                        <tr>
                            <td>
                                <span style="font-size: 85%; color: red;">Ошибка #76. Невозможно рассчитать.</span>
                            </td>
                        </tr>';
                }


                echo '
                    </table>
                    <div class="button_tiny" style="width: 100px; font-size: 75%; cursor: pointer;" onclick="tabelSubtractionPercent(' . $tabel_id . ', ' . $tabel_j['type'] . ', '.$paidout_type.', ' . $iWantMyMoney . ', '.$paidout_summ_tabel.');">По умолчанию</div>
                    <div style="width: 250px; background-color: #EEE; border: 1px dotted #CCC; margin: 10px; padding: 5px; font-size: 85%;">
                        <div>Всего: <span id="fil_sub_sum" style="font-weight: bold;">' . array_sum($pos_subtraction_summ_filials) . '</span></div>
                        <div><span id="fil_sub_msg"></span></div>
                    </div>
                    <input type="hidden" id="iWantMyMoney" value="' . $iWantMyMoney . '">';
            //}
        }
    }
}

?>