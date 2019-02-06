<?php

//get_specializations.php
//Получаем  специализации по типу

    session_start();

	if ($_POST){
        if (isset($_POST['permission'])){

            if (isset($_SESSION['id'])) {
                $rezult = '';

                include_once 'DBWork.php';
                include_once 'functions.php';

                $msql_cnnct = ConnectToDB();

                $arr = array();
                $rez = array();

                //Выбираем количество непрочитанных сообщений
                $query = "SELECT * FROM `spr_specialization` WHERE `permission`='{$_POST['permission']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($rez, $arr);
                    }
                }

                if (!empty($rez)){

                    foreach ($rez as $item) {

                        $rezult .= '<input type="checkbox" name="specializations[]" value="' . $item['id'] . '" > ' . $item['name'] . '<br>';
                    }
                }

                echo json_encode(array('result' => 'success', 'data' => $rezult));
            }
        }
    }
?>
	