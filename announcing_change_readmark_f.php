<?php

//announcing_change_readmark_f
//Отметка ясно (прочитано)

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);

    if ($_POST){

        if (!isset($_POST['announcingID'])){
            //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
        }else{

            $time = date('Y-m-d H:i:s', time());

            include_once 'DBWork.php';

            $msql_cnnct = ConnectToDB ();

            $query = "SELECT `id` FROM `journal_announcing` WHERE `id` IN (SELECT `announcing_id` FROM `journal_announcing_readmark` WHERE `announcing_id`='{$_POST['announcingID']}' AND `create_person`='{$_SESSION['id']}' AND `status`='1')";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {

            } else {

                $query = "INSERT INTO `journal_announcing_readmark` (`announcing_id`, `create_time`, `create_person`, `status`)
				VALUES (
				'{$_POST['announcingID']}', '{$time}', '{$_SESSION['id']}', '1')";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                //ID новой позиции
                //$mysql_insert_id = mysqli_insert_id($msql_cnnct);

                echo json_encode(array('result' => 'success', 'data' => 'Статус изменён на "Прочитано"'));
            }
        }
    }
}
?>