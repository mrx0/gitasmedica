<?php

//zapis_giveitotome_server.php
//Получение записи с сайта через промежуточный сервер
//Распологается на промежуточном сервере

	if (isset($_GET['last']) && isset($_GET['token'])){

        $zapis_arr = array();

        //Пока проверка такая, потом при желании можно токен каждый раз менять
        if ($_GET['token'] == 'ec3d3704abf1bb0430cd82e66fefdce7'){

            $rezult = '';
            $rezult_arr = array();

            require 'config_zapis_online.php';

            $URL = $URL_4zapis;
            $last = $_GET['last'];
            $token = $_GET['token'];

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

        }

        echo json_encode($rezult_arr);
    }
?>
	