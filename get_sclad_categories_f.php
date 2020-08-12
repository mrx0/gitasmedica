<?php

//get_sclad_categories_f.php
//получаем категории склада

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){
            //if (isset($_POST['cat_id']) && isset($_POST['start']) && isset($_POST['limit']) && isset($_POST['free'])){

                $rezult = '';
                //$rezult_arr = array();

                include_once 'DBWork.php';
                include_once 'functions.php';

                $msql_cnnct = ConnectToDB();

                $rezult = showTreeSclad2(NUll, '', 'list', 0, TRUE, 0, FALSE, 'spr_sclad_category', 0, 0, $msql_cnnct);

                CloseDB ($msql_cnnct);

                echo json_encode(array('result' => 'success', 'data' => $rezult));

            //}
        }
    }


?>
	