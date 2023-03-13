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

            require 'config_dentalpro_api.php';
            include_once('DBWorkPDO.php');

            if (!isset($_POST['method'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $db = new DB();

                //URI он же метод
                $dopURL_api = $_POST['method'];

                //Уточняющая строка запроса
                //Если запрошена запись
                if ($dopURL_api == 'lm/appointments') {
                    //Даты от - до
                    $date_from = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
                    $date_to = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];

                    $query_req_str = 'date_from=' . $date_from . '&date_to=' . $date_to;
                }
                //Если запрошен пациент
                if (($dopURL_api == 'i/client') || ($dopURL_api == 'lm/doctors')) {
                    $query_req_str = 'id=' . $_POST['id'];
                }

                //Строка запроса
                $query = $URL_api . $dopURL_api . '?token=' . $token_api . '&' . 'secret=' . $secret_api . '&' . $query_req_str;

                //Запрашиваем и получаем данные по API DP
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

                curl_close($ch);

                //Если есть данные - работаем
                if (!empty($rezult_arr)) {

                    //var_dump($rezult_arr);
                    echo json_encode(array('result' => 'success', 'data' => $rezult_arr));

                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка! Данных нет.</div>'));
                }


            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>'));
        }
    }

?>
	