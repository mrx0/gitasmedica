<?php

//get_zapis2.php
//Получение записи с сайта

    session_start();

	if ($_POST){
        if (isset($_POST['type'])){
            //$_POST['type'] = 5;

            $rezult = '';
            $rezult_arr = array();

            include_once 'DBWork.php';

            require 'config_zapis_online.php';

            $msql_cnnct = ConnectToDB();

            //if ($_POST['type'] == 5) {
                $URL = $URL_4zapis;
                $last_id_zapis_option = 'last_id_zapis_asstom';
            //}

            //if ($_POST['type'] == 6) {
                //$URL = 'https://www.asstom.ru/zapis_giveitotome.php?';
                //$last_id_zapis_option = 'last_id_zapis_ascosm';
            //}

            //if ($_POST['type'] == 10) {
                //$URL = 'https://www.asstom.ru/zapis_giveitotome.php?';
                //$last_id_zapis_option = 'last_id_zapis_assmed';
            //}

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

                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
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

            //Выборка
            if (empty($_SESSION['filial'])){
                $query = "SELECT COUNT(*) AS total FROM `zapis_online` WHERE `status` <> '7' AND `status` <> '6' ";
            }else{
                $query = "SELECT COUNT(*) AS total FROM `zapis_online` WHERE `status` <> '7' AND `status` <> '6' AND `place` = '".$_SESSION['filial']."'";
            }


            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $arr = mysqli_fetch_assoc($res);



            echo json_encode(array('result' => 'success', 'data' => $arr['total']));
        }
    }
?>
	