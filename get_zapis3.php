<?php

//get_zapis3.php
//Получение записи с сайта через промежуточный сервер

    session_start();

	if ($_POST){
        if (isset($_POST['type'])){

            $rezult = '';
            $rezult_arr = array();

            include_once 'DBWork.php';

            require 'config_zapis_online.php';

            $msql_cnnct = ConnectToDB();

            $URL = $URL_server_4zapis;
            $last_id_zapis_option = 'last_id_zapis_asstom';

            $arr = array();

            $query = "SELECT `value` FROM `settings` WHERE `option`='".$last_id_zapis_option."'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                $arr = mysqli_fetch_assoc($res);

                $last = $arr['value'];
                //var_dump($last);

                $token = $token_4zapis;
                //var_dump($token);

                $query = $URL . 'last=' . $last . '&' . 'token=' . $token;
                //var_dump($query);

                $ch = curl_init();
                //var_dump($ch);

                curl_setopt($ch, CURLOPT_URL, $query);

                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

                curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);

                $rezult = curl_exec($ch);
                //echo 'Ошибка curl: ' . curl_error($ch);
                //var_dump($rezult);

                $rezult_arr = json_decode($rezult, true);
                //var_dump($rezult_arr);

                curl_close($ch);

                if (!empty($rezult_arr)) {
                    foreach ($rezult_arr as $zapis_val){
                        //var_dump($zapis_val['id']);

                        if ($zapis_val['id'] > $last){
                            $last = $zapis_val['id'];
                        }

                        $query = "INSERT INTO `zapis_online` (`id_own`, `datetime`, `name`, `email`, `phone`, `time`, `place`, `type`, `comments`)
                        VALUES (
                        '{$zapis_val['id']}', '{$zapis_val['datetime']}', '{$zapis_val['name']}', '{$zapis_val['email']}', '{$zapis_val['phone']}', '{$zapis_val['time']}', '{$zapis_val['place']}', '{$zapis_val['type']}', '{$zapis_val['comments']}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                    }

                    $query = "UPDATE `settings` SET `value`='{$last}' WHERE `option`='".$last_id_zapis_option."'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                    //echo json_encode(array('result' => 'success', 'data' => $query));
                }else{
                    //echo json_encode(array('result' => 'success', 'data' => 0));
                }

            }

            $dop = '';
            if (isset($_SESSION['filial'])){
                $dop = "AND `place`='".$_SESSION['filial']."'";

                //Если энгельса, то плюсуем пустые (без филиала)
                if ($_SESSION['filial'] == 15){
                    $dop = "AND (`place`='".$_SESSION['filial']."' OR `place`='')";
                }
                //Если 72, то плюсуем 54
                if ($_SESSION['filial'] == 19){
                    $dop = "AND (`place`='".$_SESSION['filial']."' OR `place`='13')";
                }
                //Если ком, то плюсуем авиа
                if ($_SESSION['filial'] == 14){
                    $dop = "AND (`place`='".$_SESSION['filial']."' OR `place`='12')";
                }
            }
            $query = "SELECT COUNT(*) AS total FROM `zapis_online` WHERE `status` <> '7' AND `status` <> '6' ".$dop."";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $arr = mysqli_fetch_assoc($res);


            //когда была последняя запись
            $query = "SELECT `datetime` FROM `zapis_online` WHERE `id` IN (SELECT MAX(`id`) FROM `zapis_online`) LIMIT 1";
//            var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                $arr1 = mysqli_fetch_assoc($res);

                $datetime = $arr1['datetime'];
            }

            $today3daysplus = date('Y-m-d', strtotime(date('Y-m-d', strtotime($datetime )).' +3 days'));

            if (date('Y-m-d', time()) >= $today3daysplus){
                $arr['total']++;
            }



            echo json_encode(array('result' => 'success', 'data' => $arr['total']));
        }
    }
?>
	