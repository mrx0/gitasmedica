<?php

//test_api.php
//Тест API DentalPro

    session_start();

	//if ($_POST){
//        if (isset($_POST['type'])){
            //$_POST['type'] = 5;

            $rezult = '';
            $rezult_arr = array();

            require 'config_dentalpro_api.php';

            include_once('DBWorkPDO.php');

            $db = new DB();

//            $zapis_id = $task[0]['zapis_id'];
//
//            $args = [
//                'zapis_id' => $zapis_id
//            ];
//
//            //Получаем данные по записи
//            //Получаем данные по записи
//            $query = "SELECT * FROM `zapis` WHERE `id`=:zapis_id LIMIT 1";
//
//            //Выбрать все
//            $sheduler_zapis = $db::getRows($query, $args);
//            //var_dump($sheduler_zapis);

//            $arr = array();
//
//            $query = "SELECT `value` FROM `settings` WHERE `option`='".$last_id_zapis_option."'";
//
//            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//            $number = mysqli_num_rows($res);
//            if ($number != 0){
//                $arr = mysqli_fetch_assoc($res);
//
//                $last = $arr['value'];
//                //var_dump($last);
//
//                $token = $token_4zapis;
//                //var_dump($token);

                $dopURL_api = 'lm/appointments';

                $date_from = date('Y-m-d', time());
                $date_to = date('Y-m-d', time());



                $query_req_str = 'date_from='.$date_from.'&date_to='.$date_to;


                $query = $URL_api . $dopURL_api . '?token=' . $token_api . '&' . 'secret=' . $secret_api . '&' . $query_req_str;
                var_dump($query);

                $ch = curl_init();
                //var_dump($ch);

                curl_setopt($ch, CURLOPT_URL, $query);

                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);

                $rezult = curl_exec($ch);
                //echo 'Ошибка curl: ' . curl_error($ch);
                //var_dump($rezult);

                $rezult_arr = json_decode($rezult, true);
                var_dump($rezult_arr);

                curl_close($ch);

//                if (!empty($rezult_arr)) {
//                    foreach ($rezult_arr as $zapis_val){
//                        //var_dump($zapis_val['id']);
//
//                        if ($zapis_val['id'] > $last){
//                            $last = $zapis_val['id'];
//                        }
//
//                        $query = "INSERT INTO `zapis_online` (`id_own`, `datetime`, `name`, `email`, `phone`, `time`, `place`, `type`, `comments`)
//                        VALUES (
//                        '{$zapis_val['id']}', '{$zapis_val['datetime']}', '{$zapis_val['name']}', '{$zapis_val['email']}', '{$zapis_val['phone']}', '{$zapis_val['time']}', '{$zapis_val['place']}', '{$zapis_val['type']}', '{$zapis_val['comments']}')";
//
//                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//                    }
//
//                    $query = "UPDATE `settings` SET `value`='{$last}' WHERE `option`='".$last_id_zapis_option."'";
//
//                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//                    //echo json_encode(array('result' => 'success', 'data' => $query));
//                }else{
//                    //echo json_encode(array('result' => 'success', 'data' => 0));
//                }
//
//            }
//
//            //Выборка
////            if (empty($_SESSION['filial'])){
////                $query = "SELECT COUNT(*) AS total FROM `zapis_online` WHERE `status` <> '7' AND `status` <> '6' ";
////            }else{
////                $query = "SELECT COUNT(*) AS total FROM `zapis_online` WHERE `status` <> '7' AND `status` <> '6' AND `place` = '".$_SESSION['filial']."'";
////            }
//
//            $dop = '';
//            if (isset($_SESSION['filial'])){
//                $dop = "WHERE `place`='".$_SESSION['filial']."'";
//
//                //Если энгельса, то плюсуем пустые (без филиала)
//                if ($_SESSION['filial'] == 15){
//                    $dop = "WHERE (`place`='".$_SESSION['filial']."' OR `place`='')";
//                }
//                //Если 72, то плюсуем 54
//                if ($_SESSION['filial'] == 19){
//                    $dop = "WHERE (`place`='".$_SESSION['filial']."' OR `place`='13')";
//                }
//                //Если ком, то плюсуем авиа
//                if ($_SESSION['filial'] == 14){
//                    $dop = "WHERE (`place`='".$_SESSION['filial']."' OR `place`='12')";
//                }
//            }
//            $query = "SELECT COUNT(*) AS total FROM `zapis_online`  ".$dop."";
//
//            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//            $arr = mysqli_fetch_assoc($res);



//            echo json_encode(array('result' => 'success', 'data' => $arr['total']));
//        }
//    }
?>
	