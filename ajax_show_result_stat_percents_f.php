<?php

//ajax_show_result_stat_percents_f.php
//

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){

        include_once 'DBWork.php';
        include_once 'functions.php';

        //разбираемся с правами
        $god_mode = FALSE;

        require_once 'permissions.php';

        $creatorExist = false;
        $workerExist = false;
        $clientExist = false;
        $queryDopExist = false;
        $queryDopExExist = false;
        $queryDopEx2Exist = false;
        $queryDopClientExist = false;
        $query = '';
        $queryDop = '';
        $queryDopEx = '';
        $queryDopEx2 = '';
        $queryDopClient = '';

        $dop = array();

        $edit_options = false;
        $upr_edit = false;
        $admin_edit = false;
        $stom_edit = false;
        $cosm_edit = false;
        $finance_edit = false;

        $datastart_temp_arr = array();

        //Дополнительные настройки, чтобы передать их дальше
        $dop['zapis']['fullAll'] = $_POST['fullAll'];
        $dop['zapis']['fullWOInvoice'] = $_POST['fullWOInvoice'];
        $dop['zapis']['fullWOTask'] = $_POST['fullWOTask'];
        $dop['zapis']['fullOk'] = $_POST['fullOk'];

        $dop['invoice']['invoiceAll'] = $_POST['invoiceAll'];
        $dop['invoice']['invoicePaid'] = $_POST['invoicePaid'];
        $dop['invoice']['invoiceNotPaid'] = $_POST['invoiceNotPaid'];
        $dop['invoice']['invoiceInsure'] = $_POST['invoiceInsure'];

        $dop['patientUnic'] = $_POST['patientUnic'];

        //Финальный массив для результатов...
        $rezFinal_arr = array();

        //Кто создал запись
        if ($_POST['creator'] != ''){
            include_once 'DBWork.php';
            $creatorSearch = SelDataFromDB ('spr_workers', $_POST['creator'], 'worker_full_name');

            if ($creatorSearch == 0){
                $creatorExist = false;
            }else{
                $creatorExist = true;
                $creator = $creatorSearch[0]['id'];
            }
        }else{
            $creatorExist = true;
            $creator = 0;
        }

        //К кому запись
        if ($_POST['worker'] != ''){
            include_once 'DBWork.php';
            $workerSearch = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');

            if ($workerSearch == 0){
                $workerExist = false;
            }else{
                $workerExist = true;
                $worker = $workerSearch[0]['id'];
            }
        }else{
            $workerExist = true;
            $worker = 0;
        }

        //Клиент
        if ($_POST['client'] != ''){
            include_once 'DBWork.php';
            $clientSearch = SelDataFromDB ('spr_clients', $_POST['client'], 'client_full_name');

            if ($clientSearch == 0){
                $clientExist = false;
            }else{
                $clientExist = true;
                $client = $clientSearch[0]['id'];
            }
        }else{
            $clientExist = true;
            $client = 0;
        }

        if ($creatorExist && $workerExist) {
            if ($clientExist) {
                $query .= "SELECT * FROM `zapis` z";

                $data_temp_arr = explode(".", $_POST['datastart']);
                $_POST['datastart'] = $data_temp_arr[2].'-'.$data_temp_arr[1].'-'.$data_temp_arr[0];

                $data_temp_arr = explode(".", $_POST['dataend']);
                $_POST['dataend'] = $data_temp_arr[2].'-'.$data_temp_arr[1].'-'.$data_temp_arr[0];

                //Дата/время
//                if ($_POST['all_time'] != 1) {
//                    //$queryDop .= "`create_time` BETWEEN '" . strtotime($_POST['datastart']) . "' AND '" . strtotime($_POST['dataend'] . " 23:59:59") . "'";
//
//                    $queryDop .= "CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) BETWEEN '{$_POST['datastart']}' AND '{$_POST['dataend']}'";
//                    $queryDopExist = true;
//                }

                //Кто создал запись
//                if ($creator != 0) {
//                    if ($queryDopExist) {
//                        $queryDop .= ' AND';
//                    }
//                    $queryDop .= " z.create_person = '" . $creator . "'";
//                    $queryDopExist = true;
//                }

                //К кому запись
                if ($worker != 0) {
//                    if ($queryDopExist) {
//                        $queryDop .= ' AND';
//                    }
//                    $queryDop .= " z.worker = '" . $worker . "'";
                    $queryDopExist = true;
                }

                //Клиент
//                if ($client != 0) {
//                    if ($queryDopExist) {
//                        $queryDop .= ' AND';
//                    }
//                    $queryDop .= " z.patient = '" . $client . "'";
//                    $queryDopExist = true;
//                }

                //Филиал
                if ($_POST['filial'] != 99) {
//                    if ($queryDopExist) {
//                        $queryDop .= ' AND';
//                    }
//                    $queryDop .= " z.office = '" . $_POST['filial'] . "'";
                    $queryDopExist = true;
                }

                //Все записи
                if ($_POST['zapisAll'] != 0) {
                    //ничего
                } else {
                    //Пришёл
                    if ($_POST['zapisArrive'] != 0) {
                        if ($queryDopExExist) {
                            $queryDopEx .= ' OR';
                        }
                        if ($_POST['zapisArrive'] == 1) {
                            $queryDopEx .= "z.enter = '1'";
                            $queryDopExExist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Не пришёл
                    if ($_POST['zapisNotArrive'] != 0) {
                        if ($queryDopExExist) {
                            $queryDopEx .= ' OR';
                        }
                        if ($_POST['zapisNotArrive'] == 1) {
                            $queryDopEx .= " z.enter = '9'";
                            $queryDopExExist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Не отмеченные
                    if ($_POST['zapisNull'] != 0) {
                        if ($queryDopExExist) {
                            $queryDopEx .= ' OR';
                        }
                        if ($_POST['zapisNull'] == 1) {
                            $queryDopEx .= " z.enter = '0'";
                            $queryDopExExist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Ошибочные
                    if ($_POST['zapisError'] != 0) {
                        if ($queryDopExExist) {
                            $queryDopEx .= ' OR';
                        }
                        if ($_POST['zapisError'] == 1) {
                            $queryDopEx .= " z.enter = '8'";
                            $queryDopExExist = true;
                        }
                        //$queryDopExExist = true;
                    }
                }

                //Тип
                if ($_POST['typeW'] != 0) {
//                    if ($queryDopExist) {
//                        $queryDop .= ' AND';
//                    }
//                    $queryDop .= " z.type = '" . $_POST['typeW'] . "'";
                    $queryDopExist = true;
                }


                //Первичный ночной страховой
//                if ($_POST['statusAll'] != 0) {
//                    //ничего
//                } else {
//                    //Первичные
//                    if ($_POST['statusPervich'] != 0) {
//                        if ($queryDopEx2Exist) {
//                            $queryDopEx2 .= ' OR';
//                        }
//                        if ($_POST['statusPervich'] == 1) {
//                            $queryDopEx2 .= " z.pervich = '1' OR z.pervich = '2'";
//                            $queryDopEx2Exist = true;
//                        }
//                        //$queryDopExExist = true;
//                    }
//
//                    //Страховые
//                    if ($_POST['statusInsure'] != 0) {
//                        if ($queryDopEx2Exist) {
//                            $queryDopEx2 .= ' OR';
//                        }
//                        if ($_POST['statusInsure'] == 1) {
//                            $queryDopEx2 .= " z.insured = '1'";
//                            $queryDopEx2Exist = true;
//                        }
//                        //$queryDopExExist = true;
//                    }
//
//                    //Ночные
//                    if ($_POST['statusNight'] != 0) {
//                        if ($queryDopEx2Exist) {
//                            $queryDopEx2 .= ' OR';
//                        }
//                        if ($_POST['statusNight'] == 1) {
//                            $queryDopEx2 .= " z.noch = '1'";
//                            $queryDopEx2Exist = true;
//                        }
//                        //$queryDopExExist = true;
//                    }
//
//                    //Все остальные
//                    if ($_POST['statusAnother'] != 0) {
//                        if ($queryDopEx2Exist) {
//                            $queryDopEx2 .= ' OR';
//                        }
//                        if ($_POST['statusAnother'] == 1) {
//                            $queryDopEx2 .= " (z.pervich = '0' OR z.pervich = '3' OR z.pervich = '4') AND z.insured = '0' AND z.noch = '0'";
//                            $queryDopEx2Exist = true;
//                        }
//                        //$queryDopExExist = true;
//                    }
//                }


                if ($queryDopExist) {
                    $query .= ' WHERE ' . $queryDop;

                    if ($queryDopExExist) {
                        $query .= ' AND (' . $queryDopEx . ')';
                    }

                    if ($queryDopEx2Exist) {
                        $query .= ' AND (' . $queryDopEx2 . ')';
                    }

                    /*if ($queryDopClientExist){
                        $queryDopClient = "SELECT `id` FROM `spr_clients` WHERE ".$queryDopClient;
                        if ($queryDopExist){
                            $query .= ' AND';
                        }
                        $query .= "`client` IN (".$queryDopClient.")";
                    }*/

                    $query = $query . " ORDER BY CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) DESC";

                    //var_dump($query);

                    $msql_cnnct = ConnectToDB();

                    $arr = array();
                    $journal = array();

                    $query = "
                        SELECT ji_ex.*, ji.zapis_id, ji.office_id, ji.worker_id, ji.client_id
                        FROM `journal_invoice` ji
                        INNER JOIN `zapis` z 
                          ON ji.zapis_id = z.id
                        LEFT JOIN `journal_invoice_ex` ji_ex
                          ON ji.id = ji_ex.invoice_id
                            WHERE ji.summ <> '0' AND ji.summins = '0'
                            AND CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) BETWEEN '{$_POST['datastart']}' AND '{$_POST['dataend']}' 
                            AND z.enter = '1' AND z.insured = '0'";

                    //Филиал
                    if ($_POST['filial'] != 99) {
                        $query .= "AND z.office = '" . $_POST['filial'] . "'";
                    }

                    //К кому запись
                    if ($worker != 0) {
                        $query .= "AND z.worker = '" . $worker . "'";
                    }

                    //Тип
                    if ($_POST['typeW'] != 0) {
                        $query .= "AND z.type = '" . $_POST['typeW'] . "'";
                    }



                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            if (!isset($journal[$arr['office_id']])){
                                $journal[$arr['office_id']] = array();
                            }
                            if (!isset($journal[$arr['office_id']][$arr['worker_id']])){
                                $journal[$arr['office_id']][$arr['worker_id']] = array();
                            }
                            if (!isset($journal[$arr['office_id']][$arr['worker_id']])){
                                $journal[$arr['office_id']][$arr['worker_id']] = array();
                            }
                            if (!isset($journal[$arr['office_id']][$arr['worker_id']][$arr['zapis_id']])){
                                $journal[$arr['office_id']][$arr['worker_id']][$arr['zapis_id']] = array();
                            }
                            if (!isset($journal[$arr['office_id']][$arr['worker_id']][$arr['zapis_id']][$arr['invoice_id']])){
                                $journal[$arr['office_id']][$arr['worker_id']][$arr['zapis_id']][$arr['invoice_id']] = array();
                            }

                            array_push($journal[$arr['office_id']][$arr['worker_id']][$arr['zapis_id']][$arr['invoice_id']], $arr);

                            if (!isset($rezFinal_arr[$arr['office_id']])){
                                $rezFinal_arr[$arr['office_id']] = array();
                            }
                            if (!isset($rezFinal_arr[$arr['office_id']][$arr['worker_id']])){
                                $rezFinal_arr[$arr['office_id']][$arr['worker_id']] = array();
                            }
                            if (!isset($rezFinal_arr[$arr['office_id']][$arr['worker_id']])){
                                $rezFinal_arr[$arr['office_id']][$arr['worker_id']] = array();
                            }

                        }
                    }
//                    var_dump($journal);
//                    var_dump($rezFinal_arr);

                    //Обработка результата
                    if (!empty($journal)) {
                        //var_dump($journal);

                        $filials_j = getAllFilials(true, true, true);
                        //var_dump($filials_j);

                        echo '<table style="border: 1px solid #CCC;">';

                        echo '
                            <tr>
                                <td colspan="3">Необработанные наряды (обработанные скрыты <div id="" style="font-size: 80%; color: #601ba5; cursor: pointer; display: inline;" onclick="toggleSomething (\'.hiddenTRs\');">показать/скрыть</div>)</td>
                            </tr>';

                        //Для каждого филиала
                        foreach ($journal as $filial_id => $filial_arr){

                            echo '<tr>';

                            echo '<td rowspan="'.(count($filial_arr)+1).'" style="border: 1px solid #CCC; vertical-align: top; padding: 2px; font-size: 80%">'.$filials_j[$filial_id]['name'].'</td>';

//                            echo '<td style="border: 1px solid #CCC;">';

                            //echo '<table style="border: 1px solid #CCC;">';

                            echo '<td style="display: none;"></td>';

                            echo '</tr>';

                            //Для каждого сотрудника
                            foreach ($filial_arr as $worker_id => $worker_arr){

                                echo '<tr>';


                                echo '<td style="border: 1px solid #CCC; vertical-align: top; padding: 2px; font-size: 80%">'.WriteSearchUser('spr_workers', $worker_id, 'user', false).'</td>';

                                echo '</td>';

                                echo '<td style="border: 1px solid #CCC; vertical-align: top; padding: 2px;">';

                                echo '<table border="0">';

                                //Для каждой записи
                                foreach ($worker_arr as $zapis_id => $zapis_arr){
                                    //Для каждого наряда
                                    foreach ($zapis_arr as $invoice_id => $invoice_arr){

                                        //Наценка:
                                        $spec_koeff_arr = array();
                                        //Скидка:
                                        $discount_arr = array();

                                        //Для каждой позиции наряда
                                        foreach ($invoice_arr as $invoice_item){
                                            //var_dump($invoice_item);

                                            if ($invoice_item['spec_koeff'] == 'k1') $invoice_item['spec_koeff'] = 10;
                                            if ($invoice_item['spec_koeff'] == 'k2') $invoice_item['spec_koeff'] = 20;

                                            //Наценка:
                                            $spec_koeff = $invoice_item['spec_koeff'];
                                            //Скидка:
                                            $discount = $invoice_item['discount'];

                                            //Добавим скидку/наценку в массивы
                                            if (!in_array($spec_koeff, $spec_koeff_arr)){
                                                array_push($spec_koeff_arr, $spec_koeff);
                                            }
                                            if (!in_array($discount, $discount_arr)){
                                                array_push($discount_arr, $discount);
                                            }
                                        }

                                        if (count ($spec_koeff_arr) == 1){
                                            if (count ($discount_arr) == 1){
                                                if(!isset($rezFinal_arr[$filial_id][$worker_id][$spec_koeff_arr[0]])){
                                                    $rezFinal_arr[$filial_id][$worker_id][$spec_koeff_arr[0]] = array();
                                                }
                                                if(!isset($rezFinal_arr[$filial_id][$worker_id][$spec_koeff_arr[0]][$discount_arr[0]])){
                                                    $rezFinal_arr[$filial_id][$worker_id][$spec_koeff_arr[0]][$discount_arr[0]] = 0;
                                                }
                                                $rezFinal_arr[$filial_id][$worker_id][$spec_koeff_arr[0]][$discount_arr[0]] ++;
                                            }
                                        }

                                        if ((count ($spec_koeff_arr) > 1) || (count ($discount_arr) > 1)) {
                                            echo '<tr>';
                                        }else{
                                            echo '<tr class="hiddenTRs" style="display: none;">';
                                        }

                                        echo '<td style="border: 1px solid limegreen; vertical-align: top; padding: 2px;">';
                                        echo '<a href="invoice.php?id=' . $invoice_id . '" class="ahref" target="_blank" rel="nofollow noopener">' . $invoice_id . '</a><br>';

                                        echo 'Наценка: ';
                                        if (count($spec_koeff_arr) > 1) {
                                            echo '<i class="fa fa-warning" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(111, 111, 111, 0.8);"></i>';
                                        }
                                        foreach ($spec_koeff_arr as $item) {
                                            echo $item . '/';
                                        }

                                        echo '<br>';

                                        echo 'Скидка: ';
                                        if (count($discount_arr) > 1) {
                                            echo '<i class="fa fa-warning" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(111, 111, 111, 0.8);"></i>';
                                        }
                                        foreach ($discount_arr as $item) {
                                            echo $item . '/';
                                        }

                                        echo '<br>';

                                        if ((count ($spec_koeff_arr) > 1) || (count ($discount_arr) > 1)) {
                                            echo '</td>';

                                            echo '</tr>';
                                        }

                                    }
                                }

                                echo '</table>';


                                echo '</td>';

                                echo '</tr>';

                            }

                        }

                        echo '</table>';

//                        var_dump($rezFinal_arr[19]);

                        echo '<table style="border: 1px solid #CCC; margin-top: 20px;">';

                        echo '
                            <tr>
                                <td colspan="3">Итоговая таблица</td>
                            </tr>';

                        //Выводим итог
                        //Для каждого филиала
                        foreach ($rezFinal_arr as $filial_id => $filial_arr) {

                            echo '<tr>';

                            echo '<td rowspan="'.(count($filial_arr)+1).'" style="border: 1px solid #CCC; vertical-align: top; padding: 2px; font-size: 80%">'.$filials_j[$filial_id]['name'].'</td>';

                            echo '<td style="display: none;"></td>';

                            echo '</tr>';

                            //Для каждого сотрудника
                            foreach ($filial_arr as $worker_id => $worker_arr) {
                                //Сортируем по ключу
                                ksort($worker_arr);
                                //var_dump($worker_arr);

                                echo '<tr>';


                                echo '<td style="border: 1px solid #CCC; vertical-align: top; padding: 2px; font-size: 80%">'.WriteSearchUser('spr_workers', $worker_id, 'user', false).'</td>';

                                echo '</td>';

                                echo '<td style="border: 1px solid #CCC; vertical-align: top; padding: 2px;">';

                                //echo '<table border="0">';

                                //var_dump($worker_arr);
                                foreach ($worker_arr as $spec_koeff_ind => $discount_ind_arr) {
                                    //Сортируем по ключу
                                    ksort($discount_ind_arr);
//                                    var_dump($discount_ind_arr);

                                    foreach($discount_ind_arr as $discount_ind => $count){
                                        echo '+'.$spec_koeff_ind.'%  -'.$discount_ind.'% => '.$count.'<br>';
                                    }
                                }

                                //echo '</table>';


                                echo '</td>';

                                echo '</tr>';
                            }

                        }

                        echo '</table>';




//                        echo '
//                                    <li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
//                                        Всего : ' . count($journal) . '<br>
//                                    </li>
//
//                                    <li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
//                                        '.$query.'
//                                    </li>';

                        echo '
                                        </ul>
                                    </div>';
                    } else {
                        echo '<span style="color: red;">Ничего не найдено</span>';
                    }

                } else {
                    echo '<span style="color: red;">Ожидается слишком большой результат выборки. Уточните запрос.</span>';
                }

            }else {
                echo '<span style="color: red;">Не найден пациент.</span>';
            }
        }else{
            echo '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>';
        }
    }
}
?>