<?php 

//fl_reloadPercentsMarkedCalculates.php
//

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

                            foreach ($calcsArr as $calc_id){
                                //получим РЛ по id
                                $calculate_j = SelDataFromDB('fl_journal_calculate', $calc_id, 'id');

                                if ($calculate_j != 0){

                                    $zapis_id = $calculate_j[0]['zapis_id'];
                                    $invoice_id = $calculate_j[0]['invoice_id'];
                                    $filial_id = $calculate_j[0]['office_id'];
                                    $client_id = $calculate_j[0]['client_id'];
                                    $worker_id = $calculate_j[0]['worker_id'];
                                    //$invoice_type
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
                                        while ($arr = mysqli_fetch_assoc($res)){

                                            //получим процентовки по всем позициям для данного врача !!! каждый раз ???
                                            $percents_j = getPercents($worker_id, $arr['percent_cats']);

                                            $work_percent = (int)$percents_j[$arr['percent_cats']]['work_percent'];
                                            $material_percent = (int)$percents_j[$arr['percent_cats']]['material_percent'];

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
                                                array_push($calculate_ex_j[$arr['ind']], $arr);
                                                //и бахаем новые проценты
                                                //сначала узнаем индекс
                                                end($calculate_ex_j[$arr['ind']]);
                                                $last_id = key($calculate_ex_j[$arr['ind']]);
                                                //и бахаем
                                                $calculate_ex_j[$arr['ind']][$last_id]['material_percent'] = $material_percent;
                                                $calculate_ex_j[$arr['ind']][$last_id]['work_percent'] = $work_percent;
                                            }
                                        }

                                        $data = $calculate_ex_j;

                                        //Удаляем старый РЛ
                                        //Удаляем из БД
                                        $query = "DELETE FROM `fl_journal_calculate` WHERE `id`='{$calc_id}'";
                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                        $query = "DELETE FROM `fl_journal_calculate_ex` WHERE `calculate_id`='{$calc_id}'";
                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                        //Отправляем на перерасчет
                                        calculateCalculateSave ($data, $zapis_id, $invoice_id, $filial_id, $client_id, $worker_id, $invoice_type, $summ, $discount, $_SESSION['id']);
                                    }

                                }
                            }

                            echo json_encode(array('result' => 'success', 'data' => $data, 'calcsArr' => $calcsArr));
                        }
                    }
                }
            }
        }
    }

?>