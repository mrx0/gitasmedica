<?php

//dp_api_check_client_data_f.php
//Тест API DentalPro функция проверки есть ли такой пациент

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
//        var_dump ($_POST);
        if ($_POST){
            $rezult = '';
            $rezult_arr = array();

            $rezult_client_id = 0;

            //ID клиентов и врачей
            $client_ids_arr = array();
            $doc_ids_arr = array();

            include_once('DBWorkPDO.php');
            include_once 'functions.php';

            if (!isset($_POST['fio']) || !isset($_POST['birth_phone'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $fio_post = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['fio']))));
                $temp_data_fio = explode(' ', $fio_post);
                $f = $temp_data_fio[0];
                $i = $temp_data_fio[1];
                $o = $temp_data_fio[2];

                //Составляем ФИО заново для запроса в БД
                $fio_str = $f.' '.$i;

                if (mb_strlen($o) == 1){
                    //Проверка строки на только русские буквы
                    if (preg_match("/[^а-яА-ЯЁё\s]+/msi", $o)) {
                        //Нет отчества
                        $o = '*';
                    }else{
                        $fio_str .= ' '.$o;
                    }
                }else{
                    $fio_str .= ' '.$o;
                }
//                var_dump($fio_str);

                $temp_data_bp_post = explode(';', $_POST['birth_phone']);
                $birthday = $temp_data_bp_post[0];
//                var_dump($birthday);
                $phone = $temp_data_bp_post[1];

                $str_tel = preg_replace("/[^0-9]/", '', $phone);
                //костыль
                if (mb_strlen($str_tel) == 0){
                    $str_tel = '_x_';
                }
//                var_dump($str_tel);

                //Основные данные
                $args = [
                ];
//                var_dump ($args);

                $db = new DB();

                $query = "SELECT * FROM `spr_clients` WHERE (
                  LOWER(`full_name`) RLIKE LOWER('^{$fio_str}')
                  ) LIMIT 10";

                $res = $db::getRows($query, $args);

//                var_dump($query);
//                var_dump($res);
//                var_dump($birthday);

                if (!empty($res)) {
                    //ФИО найдены
                    $rezult .= '<span style="color: #36ff00; font-size: 90%; font-weight: normal;">ФИО найдены. </span>';

                    //Если их больше чем 1
                    if (count($res) > 1){
                        $rezult .= '<span style="color: #8800ff; font-size: 90%; font-weight: normal;">Найдено больше одного пациента с такими ФИО. </span>';
                        //Проверка всех поочереди на дату рождения и телефон
                        foreach ($res as $item) {
                            if ($item['birthday2'] == $birthday){
                                $rezult .= '<span style="color: #36ff00; font-size: 90%; font-weight: normal;">ДР у <a href="client.php?id='.$item['id'].'" class="ahref" style="text-align: center; color: #2ebdbd; text-decoration: underline;" target="_blank" rel="nofollow noopener">одного</a> совпадает. Можно не добавлять. </span>';

                                $rezult_client_id = $item['id'];
                            }
                        }
                    }else{
                        //Проверка на дату рождения и телефон
                        if ($res[0]['birthday2'] == $birthday){
                            $rezult .= '<span style="color: #36ff00; font-size: 90%; font-weight: normal;">ДР <a href="client.php?id='.$res[0]['id'].'" class="ahref" style="text-align: center; color: #2ebdbd; text-decoration: underline;" target="_blank" rel="nofollow noopener">пациента</a> совпадает. Можно не добавлять. </span>';

                            $rezult_client_id = $res[0]['id'];
                        }else{
                            $rezult .= '<span style="color: #ff0077; font-size: 90%; font-weight: normal;">ДР не совпадает. Требуется уточнение. </span>';
                        }
                    }

                    echo json_encode(array('result' => 'success', 'data' => '<div>'.$rezult.'</div>', 'client_acc_id' => $rezult_client_id));

                }else{
                    $rezult .= '<span style="color: #ff0000; font-weight: normal;">ФИО не найдены. Необходимо добавить. </span>';
                    echo json_encode(array('result' => 'success', 'data' => '<div>'.$rezult.'</div>'));
                }
                //var_dump($rezult);

                //echo json_encode(array('result' => 'error', 'data' => $rezult));


//                $query = "SELECT * FROM `spr_clients` WHERE (
//                  LOWER(`full_name`) RLIKE LOWER('^$search_data')
//                  OR
//                  UPPER(`card`) LIKE UPPER('%$search_data%')
//                  OR
//                  (REPLACE(REPLACE(REPLACE(`telephone`, '-', '' ), '(', ''), ')', '') LIKE '%$str_tel%' AND LENGTH(`telephone`) > 4)
//                  OR
//                  (REPLACE(REPLACE(REPLACE(`htelephone`, '-', '' ), '(', ''), ')', '') LIKE '%$str_tel%' AND LENGTH(`htelephone`) > 4)
//                  OR
//                  (REPLACE(REPLACE(REPLACE(`telephoneo`, '-', '' ), '(', ''), ')', '') LIKE '%$str_tel%' AND LENGTH(`telephoneo`) > 4)
//                  OR
//                  (REPLACE(REPLACE(REPLACE(`htelephoneo`, '-', '' ), '(', ''), ')', '') LIKE '%$str_tel%' AND LENGTH(`htelephoneo`) > 4)
//                  OR
//                  `passport` RLIKE '^$search_data'
//                  OR
//                  `id` RLIKE '^$search_data'
//                  ) AND `status`<> 9 ORDER BY `full_name` ASC LIMIT 10";

                //Если есть данные - работаем
//                if (!empty($rezult_arr)) {
//                    //Если запись, надо проверить, а все ли мы  выгрузили. Если нет, грузим все остальное
//                    if ($dopURL_api == 'lm/appointments'){
////                        var_dump('total');
////                        var_dump($rezult_arr['total']);
////                        var_dump('limit');
////                        var_dump($rezult_arr['limit']);
////                        var_dump('page');
////                        var_dump($rezult_arr['page']);
////                        var_dump($rezult_arr['data']);
//                        if ($rezult_arr['total'] > $rezult_arr['limit']){
////                            var_dump('> TRUE');
//
//                            //Даты от - до
////                            $date_from = '2023-03-22';
////                            $date_to = '2023-03-22';
//                            $date_from = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
//                            $date_to = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
//
//                            $query_req_str = 'date_from=' . $date_from . '&date_to=' . $date_to;
//
//                            $pages_count = (int)ceil($rezult_arr['total']/$rezult_arr['limit']);
//                            //var_dump($pages_count);
//
//                            for($i = 1; $i < $pages_count; $i++){
//                                $query_req_str_f = $query_req_str.'&page=' . $i;
//                                //var_dump($query_req_str_f);
//
//                                //Строка запроса
//                                $query = $URL_api . $dopURL_api . '?token=' . $token_api . '&' . 'secret=' . $secret_api . '&' . $query_req_str_f;
////                                var_dump($query);
//
//                                $rezult_arr_dop = getDataFromAPI_DP($query);
////                                var_dump($rezult_arr_dop['data']);
//
//                                if (!empty($rezult_arr_dop)){
//                                    if (!empty($rezult_arr_dop['data'])){
//                                        $rezult_arr['data'] = array_merge($rezult_arr['data'],$rezult_arr_dop['data']);
//                                    }
//                                }
//                            }
//                        }
//
//                        foreach ($rezult_arr['data'] as $zapis_data){
//                            array_push($client_ids_arr, $zapis_data['client_id']);
//                            array_push($doc_ids_arr, $zapis_data['doctor_id']);
//                        }
//
//                    }
//
////                    var_dump($rezult_arr['data']);
//                    echo json_encode(array('result' => 'success', 'data' => $rezult_arr,  'query' => $query, 'client_ids' => $client_ids_arr, 'doc_ids' => $doc_ids_arr));
//
//                }else{
//                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка! Данных нет.</div>'));
//                }


            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>'));
        }
    }

?>
	