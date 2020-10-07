<?php

//ajax_show_result_stat_lost_pervich_f.php
//Функция для Пропавшая первичка v2.0. Поиск тех, у кого была консультация и они больше не пришли

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){

        //include_once 'DBWork.php';

        include_once('DBWorkPDO.php');

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
//        $dop['zapis']['fullAll'] = $_POST['fullAll'];
//        $dop['zapis']['fullWOInvoice'] = $_POST['fullWOInvoice'];
//        $dop['zapis']['fullWOTask'] = $_POST['fullWOTask'];
//        $dop['zapis']['fullOk'] = $_POST['fullOk'];
//
//        $dop['invoice']['invoiceAll'] = $_POST['invoiceAll'];
//        $dop['invoice']['invoicePaid'] = $_POST['invoicePaid'];
//        $dop['invoice']['invoiceNotPaid'] = $_POST['invoiceNotPaid'];
//        $dop['invoice']['invoiceInsure'] = $_POST['invoiceInsure'];

        $dop['patientUnic'] = $_POST['patientUnic'];

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
//        if ($_POST['client'] != ''){
//            include_once 'DBWork.php';
//            $clientSearch = SelDataFromDB ('spr_clients', $_POST['client'], 'client_full_name');
//
//            if ($clientSearch == 0){
//                $clientExist = false;
//            }else{
//                $clientExist = true;
//                $client = $clientSearch[0]['id'];
//            }
//        }else{
//            $clientExist = true;
//            $client = 0;
//        }

        if ($creatorExist && $workerExist) {
            //if ($clientExist) {
                $query .= "SELECT * FROM `zapis` z";

                /*require 'config.php';
                mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
                mysql_select_db($dbName) or die(mysql_error());
                mysql_query("SET NAMES 'utf8'");*/
                //$time = time();

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
                if ($creator != 0) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= " z.create_person = '" . $creator . "'";
                    $queryDopExist = true;
                }

                //К кому запись
                if ($worker != 0) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= " z.worker = '" . $worker . "'";
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

                //Все записи
//                if ($_POST['zapisAll'] != 0) {
//                    //ничего
//                } else {
//                    //Пришёл
//                    if ($_POST['zapisArrive'] != 0) {
//                        if ($queryDopExExist) {
//                            $queryDopEx .= ' OR';
//                        }
//                        if ($_POST['zapisArrive'] == 1) {
//                            $queryDopEx .= "z.enter = '1'";
//                            $queryDopExExist = true;
//                        }
//                        //$queryDopExExist = true;
//                    }
//
//                    //Не пришёл
//                    if ($_POST['zapisNotArrive'] != 0) {
//                        if ($queryDopExExist) {
//                            $queryDopEx .= ' OR';
//                        }
//                        if ($_POST['zapisNotArrive'] == 1) {
//                            $queryDopEx .= " z.enter = '9'";
//                            $queryDopExExist = true;
//                        }
//                        //$queryDopExExist = true;
//                    }
//
//                    //Не отмеченные
//                    if ($_POST['zapisNull'] != 0) {
//                        if ($queryDopExExist) {
//                            $queryDopEx .= ' OR';
//                        }
//                        if ($_POST['zapisNull'] == 1) {
//                            $queryDopEx .= " z.enter = '0'";
//                            $queryDopExExist = true;
//                        }
//                        //$queryDopExExist = true;
//                    }
//
//                    //Ошибочные
//                    if ($_POST['zapisError'] != 0) {
//                        if ($queryDopExExist) {
//                            $queryDopEx .= ' OR';
//                        }
//                        if ($_POST['zapisError'] == 1) {
//                            $queryDopEx .= " z.enter = '8'";
//                            $queryDopExExist = true;
//                        }
//                        //$queryDopExExist = true;
//                    }
//                }

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


                //if ($queryDopExist) {
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

//                    "
//                    'SELECT * FROM `zapis` z
//                    WHERE CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) BETWEEN '2020-09-01' AND '2020-09-30'
//                    ORDER BY CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) DESC'
//                    "

                    $worker_str2 = '';

                    $db = new DB();

                    $arr = array();
                    $rez = array();

                    $args = [
                        'start_time' => $_POST['datastart'],
                        'end_time' => $_POST['dataend'],
                    ];

                    //Тип
                    if ($_POST['typeW'] != 0) {
                        $args['type'] = $_POST['typeW'];
                        $type_str = ' AND z.type = :type';
                    }else{
                        $type_str = '';
                    }

                    //Филиал
                    if ($_POST['filial'] != 99) {
                        $args['filial_id'] = $_POST['filial'];
                        $filial_str = ' AND z.office = :filial_id';
                    }else{
                        $filial_str = '';
                    }


                    $query = "
                        SELECT jiex.*, ji.summ AS invoice_summ, ji.summins AS invoice_summins, ji.status AS invoice_status, ji.type AS type, ji.zapis_id AS zapis_id, z.enter AS enter, z.pervich AS pervich, sc.birthday2 AS birthday
                        FROM `zapis` z
                        INNER JOIN `journal_invoice` ji ON z.id = ji.zapis_id 
                        LEFT JOIN `journal_invoice_ex` jiex ON ji.id = jiex.invoice_id
                        LEFT JOIN `spr_clients` sc ON ji.client_id = sc.id 
                        WHERE ji.status <> '9'
                        AND 
                        CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) BETWEEN :start_time AND :end_time
                        AND (z.enter = '1' OR z.enter = '6') 
                        {$filial_str}
                        {$type_str}";

                    //var_dump($query);

                    $journal_j = $db::getRows($query, $args);

                    //var_dump($journal_j);


                    //Типы посещений - первичка/нет (количество) (pervich)
                    //Памятка
                    //1 - Посещение для пациента первое без работы
                    //2 - Посещение для пациента первое с работой
                    //3 - Посещение для пациента не первое
                    //4 - Посещение для пациента не первое, но был более полугода назад
                    //--
                    //5 - Продолжение работы
                    //6 - Без записи (enter)
                    $pervich_summ_arr = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);

                    //Массив, где будем хранить суммы нарядов, чтобы потом определять первичное посещение или нет по сумме
                    $zapis_summ = array();

                    //Выводим результат
                    if (!empty($journal_j)) {

                        foreach ($journal_j as $arr) {

                            //Теперь суммы нарядов
                            //Пришел/не пришел/с улицы
                            if (!isset($zapis_summ[$arr['type']])) {
                                $zapis_summ[$arr['type']] = array();

                            }
                            //тип стом, косм, ...
                            if (!isset($zapis_summ[$arr['type']][$arr['pervich']])) {
                                $zapis_summ[$arr['type']][$arr['pervich']] = array();
                                $zapis_summ[$arr['type']][$arr['pervich']]['data'] = array();
                                $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'] = array();
                            }
                            //Если страховой
                            if ($arr['insure'] == 1) {
                                if (!isset($zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']])) {
                                    $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']] = 0;
                                }
                                $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']] += (int)$arr['itog_price'];
                            } else {
                                if (!isset($zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']])) {
                                    $zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']] = 0;
                                }
                                $zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']] += (int)$arr['itog_price'];
                            }

                        }
                        //var_dump($zapis_summ);
                        //var_dump($zapis_summ[5]);
                        //var_dump($zapis_summ[5]);

                        //сортируем по основным ключам
                        ksort($zapis_summ);
                        //!!! тестовая проверка нового определения первичек
                        $pervich_summ_arr_new = array();

                        foreach ($zapis_summ as $type => $pervich_data) {
                            foreach ($pervich_data as $pervich => $zapis_data) {
                                if (!isset($pervich_summ_arr_new[$type])) {
                                    $pervich_summ_arr_new[$type] = $pervich_summ_arr;
                                }

                                if (isset($zapis_data['data'])) {
                                    if (!empty($zapis_data['data'])) {
                                        foreach ($zapis_data['data'] as $z_id => $i_summ) {
                                            if ($pervich == 1 || $pervich == 2) {
                                                //Стоматология
                                                if ($type == 5) {
                                                    if ($i_summ >= 0) {
                                                        if ($i_summ < 1100) {
                                                            //нас интересует сейчас это условие, первичка без работы
                                                            $pervich_summ_arr_new[$type][1]++;
                                                            //var_dump($i_id);
                                                            echo '<input type="hidden" id="zapis_'.$z_id.'">';
                                                        }/* else {
                                                            $pervich_summ_arr_new[$type][2]++;
                                                        }*/
                                                    }
                                                }
                                                //Косметология
                                                //!!! Доделать
                                                if ($type == 6) {
                                                    if ($i_summ >= 0) {
                                                        if ($i_summ < 550) {
                                                            //нас интересует сейчас это условие, первичка без работы
                                                            $pervich_summ_arr_new[$type][1]++;
                                                            //var_dump($i_id);
                                                            //echo '<a href="invoice.php?id='.$z_id.'" class="ahref button_tiny" style="margin: 0 3px; font-size: 90%;" target="_blank" rel="nofollow noopener">'.$z_id.'</a><br>';
                                                            echo '<input type="hidden" id="zapis_'.$z_id.'">';
                                                        }/* else {
                                                            $pervich_summ_arr_new[$type][2]++;
                                                        }*/
                                                    }
                                                }
                                                //Соматика
                                                if ($type == 10) {
                                                    if ($i_summ >= 0) {
                                                        if ($i_summ < 990) {
                                                            //нас интересует сейчас это условие, первичка без работы
                                                            $pervich_summ_arr_new[$type][1]++;
                                                            //var_dump($i_id);
                                                            //echo '<a href="invoice.php?id='.$z_id.'" class="ahref button_tiny" style="margin: 0 3px; font-size: 90%;" target="_blank" rel="nofollow noopener">'.$z_id.'</a><br>';
                                                            echo '<input type="hidden" id="zapis_'.$z_id.'">';
                                                        }/* else {
                                                            $pervich_summ_arr_new[$type][2]++;
                                                        }*/
                                                    }
                                                }
                                            }
//                                            if ($pervich == 3 || $pervich == 4 || $pervich == 5) {
//                                                if ($i_summ > 0) {
//                                                    $pervich_summ_arr_new[$type][3]++;
//                                                }
//                                            }
                                        }
                                    }
                                }
                            }
                        }
                        var_dump($pervich_summ_arr_new);





                        include_once 'functions.php';

                        // !!! **** тест с записью
                        //include_once 'showZapisRezult2.php';
                        include_once 'showZapisRezult.php';

                        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode) {
                            $finance_edit = true;
                            $edit_options = true;
                        }

                        if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode) {
                            $stom_edit = true;
                            $edit_options = true;
                        }
                        if (($cosm['add_own'] == 1) || ($cosm['add_new'] == 1) || $god_mode) {
                            $cosm_edit = true;
                            $edit_options = true;
                        }

                        if (($zapis['add_own'] == 1) || ($zapis['add_new'] == 1) || $god_mode) {
                            $admin_edit = true;
                            $edit_options = true;
                        }

                        if (($scheduler['see_all'] == 1) || $god_mode) {
                            $upr_edit = true;
                            $edit_options = true;
                        }

                        //Если хотим видеть только уникальные пациенты
                        if ($_POST['patientUnic'] == 1){
                            //var_dump($journal);

                            $journal_temp = array();

                            foreach($journal as $journal_item){
                                //Нам нужны фио пациентов чтоб потом сортировать их по фио

                                $journal_temp[WriteSearchUser('spr_clients', $journal_item['patient'], 'user_full', false)] =  $journal_item;
                            }

                            ksort($journal_temp);
                            $journal = $journal_temp;
                            $journal = array_values($journal_temp);

                        }
                        //($journal);


                        echo showZapisRezult($journal, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, true, false, $dop);
                        //$ZapisHereQueryToday, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, $type, $format, $menu, $dop


                        echo '
                                    <li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
                                        Всего : ' . count($journal) . '<br>
                                    </li>
                                    
                                    <!--<li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
                                        '.$query.'
                                    </li>-->';

                        echo '
                                        </ul>
                                    </div>';
                    } else {
                        echo '<span style="color: red;">Ничего не найдено</span>';
                    }

//                } else {
//                    echo '<span style="color: red;">Ожидается слишком большой результат выборки. Уточните запрос.</span>';
//                }

                //var_dump($query);
                //var_dump($queryDopEx);
                //var_dump($queryDopClient);

                //mysql_close();
//            }else {
//                echo '<span style="color: red;">Не найден пациент.</span>';
//            }
        }else{
            echo '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>';
        }
    }
}
?>