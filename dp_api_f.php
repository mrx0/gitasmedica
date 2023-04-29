<?php

//dp_api_f.php
//Тест API DentalPro функция

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            $rezult = '';
            $rezult_arr = array();

            //ID клиентов и врачей + id записей
            $client_ids_arr = array();
            $doc_ids_arr = array();
            $zapis_ids_arr = array();

            require 'config_dentalpro_api.php';
            include_once('DBWorkPDO.php');
            include_once 'functions.php';

            if (!isset($_POST['method'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $db = new DB();

                //URI он же метод
                $dopURL_api = $_POST['method'];

                $page = 0;

                //Уточняющая строка запроса
                //Если запрошена запись
                if ($dopURL_api == 'lm/appointments') {
                    //Даты от - до
//                    $date_from = '2023-03-22';
//                    $date_to = '2023-03-22';
                    $date_from = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
                    $date_to = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];

                    $query_req_str = 'date_from=' . $date_from . '&date_to=' . $date_to . '&page=' . $page;

                }
                //Если запрошен пациент
                if (($dopURL_api == 'i/client') || ($dopURL_api == 'lm/doctors')) {
                    $query_req_str = 'id=' . $_POST['id'];
                }

                //Строка запроса
                $query = $URL_api . $dopURL_api . '?token=' . $token_api . '&' . 'secret=' . $secret_api . '&' . $query_req_str . '&limit=200';

//                //Запрашиваем и получаем данные по API DP
//                $ch = curl_init();
//                //var_dump($ch);
//
//                curl_setopt($ch, CURLOPT_URL, $query);
//
//                curl_setopt($ch, CURLOPT_HEADER, FALSE);
//                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//
//                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
//                curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
//
//                $rezult = curl_exec($ch);
//                //echo 'Ошибка curl: ' . curl_error($ch);
//                //var_dump($rezult);
//
//                $rezult_arr = json_decode($rezult, true);
//                var_dump(gettype($rezult_arr));
//                curl_close($ch);

                $rezult_arr = getDataFromAPI_DP($query);
                //var_dump($rezult_arr);

                //Если есть данные - работаем
                if (!empty($rezult_arr)) {
                    //Если запись, надо проверить, а все ли мы  выгрузили. Если нет, грузим все остальное
                    if ($dopURL_api == 'lm/appointments'){
//                        var_dump('total');
//                        var_dump($rezult_arr['total']);
//                        var_dump('limit');
//                        var_dump($rezult_arr['limit']);
//                        var_dump('page');Показания счётчика солярия/лазеров
//                        var_dump($rezult_arr['page']);
//                        var_dump($rezult_arr['data']);
                        if ($rezult_arr['total'] > $rezult_arr['limit']){
//                            var_dump('> TRUE');

                            //Даты от - до
//                            $date_from = '2023-03-22';
//                            $date_to = '2023-03-22';
                            $date_from = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
                            $date_to = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];

                            $query_req_str = 'date_from=' . $date_from . '&date_to=' . $date_to;

                            $pages_count = (int)ceil($rezult_arr['total']/$rezult_arr['limit']);
                            //var_dump($pages_count);

                            for($i = 1; $i < $pages_count; $i++){
                                $query_req_str_f = $query_req_str.'&page=' . $i;
                                //var_dump($query_req_str_f);

                                //Строка запроса
                                $query = $URL_api . $dopURL_api . '?token=' . $token_api . '&' . 'secret=' . $secret_api . '&' . $query_req_str_f;
//                                var_dump($query);

                                $rezult_arr_dop = getDataFromAPI_DP($query);
//                                var_dump($rezult_arr_dop['data']);

                                if (!empty($rezult_arr_dop)){
                                    if (!empty($rezult_arr_dop['data'])){
                                        $rezult_arr['data'] = array_merge($rezult_arr['data'],$rezult_arr_dop['data']);
                                    }
                                }
                            }
                        }

                        foreach ($rezult_arr['data'] as $zapis_data){
                            array_push($client_ids_arr, $zapis_data['client_id']);
                            array_push($doc_ids_arr, $zapis_data['doctor_id']);
                            array_push($zapis_ids_arr, $zapis_data['id']);
                        }

                    }

//                    var_dump($rezult_arr['data']);
                    echo json_encode(array('result' => 'success', 'data' => $rezult_arr,  'query' => $query, 'client_ids' => $client_ids_arr, 'doc_ids' => $doc_ids_arr, 'zapis_ids' => $zapis_ids_arr));

                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка! Данных нет.</div>'));
                }


            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>'));
        }
    }

?>
	