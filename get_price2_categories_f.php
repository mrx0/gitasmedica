<?php

//get_price2_categories_f.php
//получаем категории прайса

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){
            //if (isset($_POST['cat_id']) && isset($_POST['start']) && isset($_POST['limit']) && isset($_POST['free'])){

                $rezult = '';
                //$rezult_arr = array();

                include_once('DBWorkPDO.php');
                include_once 'functions.php';

                $db = new DB();

                $rezult = showTreePrice2(NUll, '', 'list', 0, TRUE, 0, FALSE, 'spr_price2_category', 0, 0, $db);

                echo json_encode(array('result' => 'success', 'data' => $rezult));

            //}
        }
    }


?>
	