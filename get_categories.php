<?php

//get_categories.php
//Получаем  категории по типу

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
                $query = "SELECT * FROM `spr_categories` WHERE `permission`='{$_POST['permission']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($rez, $arr);
                    }
                }

                if (!empty($rez)){

                    $rezult .= '<select name="SelectCategory" id="SelectCategory">';
                    $rezult .= "<option value=''>Нажми и выбери</option>";
                    foreach ($rez as $item) {

                        $rezult .= "<option value='".$item['id']."'>".$item['name']."</option>";
                    }
                    $rezult .= '</select>';
                }

                echo json_encode(array('result' => 'success', 'data' => $rezult));
            }
        }
    }
?>
	