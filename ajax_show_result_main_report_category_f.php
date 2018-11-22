<?php

//ajax_show_result_main_report_category_f.php
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
                //$query .= "SELECT `id`, `year`, `month`, `day`, `office`,`worker`, `create_person`, `patient`, `type`, `pervich`, `insured`, `noch`, `enter` FROM `zapis` z";
                $query .= "
                    SELECT jcalcex.* FROM `zapis` z 
                    INNER JOIN `fl_journal_calculate` jcalc ON z.id = jcalc.zapis_id
                    LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalc.id = jcalcex.calculate_id";

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
                if ($_POST['all_time'] != 1) {
                    //$queryDop .= "`create_time` BETWEEN '" . strtotime($_POST['datastart']) . "' AND '" . strtotime($_POST['dataend'] . " 23:59:59") . "'";

                    $queryDop .= "CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) BETWEEN '{$_POST['datastart']}' AND '{$_POST['dataend']}'";
                    $queryDopExist = true;
                }
                //var_dump($queryDop);

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
                if ($client != 0) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= " z.patient = '" . $client . "'";
                    $queryDopExist = true;
                }

                //Филиал
                if ($_POST['filial'] != 99) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= " z.office = '" . $_POST['filial'] . "'";
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
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= " z.type = '" . $_POST['typeW'] . "'";
                    $queryDopExist = true;
                }


                //Первичный ночной страховой
                if ($_POST['statusAll'] != 0) {
                    //ничего
                } else {
                    //Первичные
                    if ($_POST['statusPervich'] != 0) {
                        if ($queryDopEx2Exist) {
                            $queryDopEx2 .= ' OR';
                        }
                        if ($_POST['statusPervich'] == 1) {
                            $queryDopEx2 .= " z.pervich = '1'";
                            $queryDopEx2Exist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Страховые
                    if ($_POST['statusInsure'] != 0) {
                        if ($queryDopEx2Exist) {
                            $queryDopEx2 .= ' OR';
                        }
                        if ($_POST['statusInsure'] == 1) {
                            $queryDopEx2 .= " z.insured = '1'";
                            $queryDopEx2Exist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Ночные
                    if ($_POST['statusNight'] != 0) {
                        if ($queryDopEx2Exist) {
                            $queryDopEx2 .= ' OR';
                        }
                        if ($_POST['statusNight'] == 1) {
                            $queryDopEx2 .= " z.noch = '1'";
                            $queryDopEx2Exist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Все остальные
                    if ($_POST['statusAnother'] != 0) {
                        if ($queryDopEx2Exist) {
                            $queryDopEx2 .= ' OR';
                        }
                        if ($_POST['statusAnother'] == 1) {
                            $queryDopEx2 .= " z.pervich = '0' AND z.insured = '0' AND z.noch = '0'";
                            $queryDopEx2Exist = true;
                        }
                        //$queryDopExExist = true;
                    }
                }

                $journal = array();


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

                    $query = $query . "AND jcalc.status <>  '9' ORDER BY CONCAT_WS('-', z.year, LPAD(z.month, 2, '0'), LPAD(z.day, 2, '0')) ASC";
                    //var_dump($query);

                    $msql_cnnct = ConnectToDB();

                    $arr = array();
                    $rez = array();

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            //array_push($journal, $arr);
                            $journal[$arr['id']] = $arr;
                        }
                    }
                    //var_dump($journal);

                    //echo json_encode(array('result' => 'success', 'data' => $journal, 'query' => $query));

                    $temp_cat_array = array();
                    $all_summ = 0;

                    //Выводим результат (нет)
                    //Делаем рассчеты
                    if (!empty($journal)) {
                        //include_once 'functions.php';

                        //Категории процентов
                        $percent_cats_j = array();
                        //Для сортировки по названию
                        $percent_cats_j_names = array();
                        //$percent_cats_j = SelDataFromDB('fl_spr_percents', '', '');
                        $query = "SELECT `id`, `name` FROM `fl_spr_percents`";
                        //var_dump( $percent_cats_j);

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                $percent_cats_j[$arr['id']] = $arr['name'];
                                //array_push($percent_cats_j_names, $arr['name']);
                            }
                        }

                        //var_dump($temp_cat_array);

                        foreach ($journal as $item){
                            //var_dump($item);
                            //var_dump($item['id']);
                            //var_dump($item['price']);
                            //echo $item['id'].'<br>';

                            //if ($item['price'] != 0) {
                            if ($item['id'] != NULL) {
                                if (!isset($temp_cat_array[$item['percent_cats']])) {
                                    $temp_cat_array[$item['percent_cats']] = $item['price'];
                                    //var_dump($item['percent_cats']);
                                    //var_dump($temp_cat_array);

                                } else {
                                    $temp_cat_array[$item['percent_cats']] += $item['price'];
                                }
                            }
                            //}
                            $all_summ += $item['price'];

                        }
                        //var_dump($temp_cat_array);

                        //var_dump($temp_cat_array);
                        //Сортируем по значению
                        arsort($temp_cat_array);

                        echo '
                                <div style="padding: 10px 4px;">
                                    Общая сумма по выполненным (закрытым) работам: <b>' . number_format($all_summ, 0, ',', ' ') . ' руб.</b>
                                </div>
                                <div>
                                    <div style="display: inline-block; vertical-align: top;">';

                        if (!empty($temp_cat_array)){
                            foreach ($temp_cat_array as $item_id => $item_summ) {

                                $percent_from_all_summ = $item_summ * 100 / $all_summ;
                                $percent_from_all_summ = number_format($percent_from_all_summ, 2, ',', '');

                                if ($item_id != 0) {
                                    echo '<li><div class="cellOrder">'.$percent_cats_j[$item_id].'</div><div class="cellName" style="text-align: right;">' . number_format($item_summ, 0, ',', ' ') . ' руб.</b></div><div class="cellName categoryItem" percentCat="'.$percent_from_all_summ.'" nameCat="'.$percent_cats_j[$item_id].'"  style="text-align: right;">' . $percent_from_all_summ . ' %</div></li>';
                                }else{
                                    echo '<li><div class="cellOrder" style="color: red;">'.'Не указана категория ' . '</div><div class="cellName" style="text-align: right; color: red;">' . number_format($item_summ, 0, ',', ' ')   . ' руб.</b></div><div class="cellName" style="text-align: right; color: red;">' . $percent_from_all_summ . ' %</div></li>';
                                }

                            }
                        }

                        echo '
                                    </div>';

                        echo '
                                    <!--<div id="canvas-holder" style="display: inline-block; vertical-align: top; /*border: 1px dotted #CCC;*/ width: 450px;">
                                        <canvas id="chart-area"></canvas>
                                    </div>-->
                                </div>';



                    } else {
                        echo '<span style="color: red;">Ничего не найдено</span>';
                    }

                /*} else {
                    echo '<span style="color: red;">Ожидается слишком большой результат выборки. Уточните запрос.</span>';
                }*/

                //var_dump($query);
                //var_dump($queryDopEx);
                //var_dump($queryDopClient);

                //mysql_close();
            }else {
                echo json_encode(array('result' => 'error', 'data' => '<span style="color: red;">Не найден пациент.</span>'));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>'));
        }
    }
}
?>