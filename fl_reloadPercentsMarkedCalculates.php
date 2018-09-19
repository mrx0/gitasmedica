<?php 

//fl_reloadPercentsMarkedCalculates.php
//Пересчет РЛ

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['calcArr'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{
                if (!empty($_POST['calcArr']['main_data'])){
                    if (isset($_POST['calcArr']['data']) && isset($_POST['calcArr']['main_data'])){
                        if (!empty($_POST['calcArr']['main_data'])){

                            $data = array();

                            include_once 'DBWork.php';
                            include_once 'ffun.php';

                            $msql_cnnct = ConnectToDB ();

                            //массив id расчетных листов, которые надо перерассчитать
                            $calcsArr = $_POST['calcArr']['main_data'];

                            $temp_arr = explode("_", $_POST['calcArr']['data']);

                            $invoice_type = $temp_arr[1];
                            //$worker_id = $temp_arr[2];
                            //$filial_id = $temp_arr[3];

                            //Эта переменная объявляется ниже, а тут я решил от греха подальше в 0 её обозначит
                            //А то вдруг undef будет
                            $newCalcID = 0;

                            //Переменная маркер, если будет true, значит
                            //Кто-то захотел применить % вручную, не глядя на справочник
                            //Например в случае Приказа №8
                            $handMadePercent = false;

                            //ID табеля, в котором могли находиться эти РЛ
                            $tabel_id = 0;
                            //Если сюда передали таки ID табеля, зафиксируем его
                            if (isset($_POST['calcArr']['tabel_id'])){
                                $tabel_id = $_POST['calcArr']['tabel_id'];
                            }

                            //Если сюда передали таки новый процент к категорям (которые тоже передали),
                            //то, зафиксируем их и будем дальше с ними наверное работать
                            if (isset($_POST['calcArr']['newPercent']) && isset($_POST['calcArr']['controlCategories'])){
                                $controlCategories_arr = $_POST['calcArr']['controlCategories'];
                                $newPercent = $_POST['calcArr']['newPercent'];

                                $handMadePercent = true;
                            }

                            //Для каждого ID РЛ
                            foreach ($calcsArr as $calc_id){
                                //получим РЛ по id
                                $calculate_j = SelDataFromDB('fl_journal_calculate', $calc_id, 'id');

                                if ($calculate_j != 0){

                                    $zapis_id = $calculate_j[0]['zapis_id'];
                                    $invoice_id = $calculate_j[0]['invoice_id'];
                                    $filial_id = $calculate_j[0]['office_id'];
                                    $client_id = $calculate_j[0]['client_id'];
                                    $worker_id = $calculate_j[0]['worker_id'];

                                    $summ = $calculate_j[0]['summ'];
                                    $discount = $calculate_j[0]['discount'];
                                    //$_SESSION['id']

                                    //получим подробные данные РЛ по позициям
                                    $calculate_ex_j = array();

                                    $query = "SELECT * FROM `fl_journal_calculate_ex` WHERE `calculate_id`='".$calc_id."';";
                                    //var_dump($query);

                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                    $number = mysqli_num_rows($res);
                                    if ($number != 0){
                                        //для теста
                                        $test_arr = array();

                                        while ($arr = mysqli_fetch_assoc($res)){

                                            //получим процентовки по всем позициям для данного врача !!! каждый раз ???
                                            $percents_j = getPercents($worker_id, $arr['percent_cats']);

                                            //для теста
                                            $test_arr[$arr['percent_cats']] = $percents_j;

                                            if ($arr['percent_cats'] != 0) {

                                                $work_percent = (int)$percents_j[$arr['percent_cats']]['work_percent'];
                                                $material_percent = (int)$percents_j[$arr['percent_cats']]['material_percent'];

                                                //Поменяем % в массиве, так как мы тут вообще-то вручнукю хотим % поставить
                                                if ($handMadePercent){
                                                    //Если конечно такое надо менять
                                                    if (in_array($arr['percent_cats'], $controlCategories_arr)){
                                                        //Меняем только % за работу
                                                        $work_percent = $newPercent;
                                                    }
                                                }

                                                //Если стоматологи
                                                if ($invoice_type == 5) {
                                                    if (!isset($calculate_ex_j[$arr['ind']])) {
                                                        $calculate_ex_j[$arr['ind']] = array();
                                                    }
                                                    array_push($calculate_ex_j[$arr['ind']], $arr);
                                                    //и бахаем новые проценты
                                                    //сначала узнаем индекс
                                                    end($calculate_ex_j[$arr['ind']]);
                                                    $last_id = key($calculate_ex_j[$arr['ind']]);
                                                    //и бахаем
                                                    $calculate_ex_j[$arr['ind']][$last_id]['material_percent'] = $material_percent;
                                                    $calculate_ex_j[$arr['ind']][$last_id]['work_percent'] = $work_percent;
                                                }
                                                //Если косметологи
                                                if ($invoice_type == 6) {
                                                    /*if (!isset($calculate_ex_j[$arr['ind']])) {
                                                        $calculate_ex_j[$arr['ind']] = array();
                                                    }*/
                                                    //array_push($calculate_ex_j[$arr['ind']], $arr);

                                                   $calculate_ex_j[$arr['ind']] = $arr;

                                                    //и бахаем новые проценты
                                                    //сначала узнаем индекс
                                                    //end($calculate_ex_j[$arr['ind']]);
                                                    //$last_id = key($calculate_ex_j[$arr['ind']]);
                                                    //и бахаем
                                                    $calculate_ex_j[$arr['ind']]['material_percent'] = $material_percent;
                                                    $calculate_ex_j[$arr['ind']]['work_percent'] = $work_percent;
                                                }
                                            }
                                        }

                                        if (!empty($calculate_ex_j)) {

                                            //Лишняя операция вообще-то
                                            $data = $calculate_ex_j;

                                            //Отправляем на перерасчет, заодно там создасться новый РЛ с этими данными
                                            $calculateSaveResult = calculateCalculateSave($data, $zapis_id, $invoice_id, $filial_id, $client_id, $worker_id, $invoice_type, $summ, $discount, $_SESSION['id']);
                                            //ID нового РЛ
                                            $newCalcID = $calculateSaveResult['data'];

                                            //Удаляем старый РЛ
                                            //Удаляем из БД
                                            $query = "DELETE FROM `fl_journal_calculate` WHERE `id`='{$calc_id}'";
                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                            $query = "DELETE FROM `fl_journal_calculate_ex` WHERE `calculate_id`='{$calc_id}'";
                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                            //А если у нас вдруг сюда был передан tabel_id
                                            //То мы предполагаем, что РЛ были в каком-то табеле
                                            //И надо 1. удалить привязку 2. добавить новые РЛ туда же 3. Пересчитать табель с этим ID
                                            if ($tabel_id > 0){

                                                //1. Удаляем привязку
                                                $query = "DELETE FROM `fl_journal_tabels_ex` WHERE `tabel_id` = '{$tabel_id}' AND `calculate_id` = '{$calc_id}' ;";
                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                                //2. добавить новый РЛ туда же
                                                $query = "INSERT IGNORE INTO `fl_journal_tabels_ex` (`tabel_id`, `calculate_id`) VALUES ('{$tabel_id}', '{$newCalcID}');";
                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                                //3. Пересчитать табель с этим ID
                                                //Это мы вынесем за этот цикл и выполним после всех этих "волшебных преобразований"

                                            }

                                        }
                                    }
                                }
                            }

                            CloseDB ($msql_cnnct);

                            echo json_encode(array('result' => 'success', 'data' => $data, 'newCalcID' => $newCalcID));
                        }
                    }
                }
            }
        }
    }

?>