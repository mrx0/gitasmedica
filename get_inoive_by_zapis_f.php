<?php

//get_inoive_by_zapis_f.php
//получаем наряды по id записям

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){
            if (isset($_POST['zapis_id'])){

                include_once('DBWorkPDO.php');
                include_once 'functions.php';

                require 'variables.php';

                $db = new DB();

                $args = [
                    'zapis_id' => $_POST['zapis_id']
                ];

//                $query = "SELECT `id` FROM `journal_invoice` WHERE `zapis_id`={$_POST['zapis_id']}";
//
//                $invoice_j = $db::getRows($query, $args);

                $query = "SELECT * FROM `zapis` WHERE `id`=:zapis_id LIMIT 1";

                $sheduler_zapis = $db::getRow($query, $args);

                $rezult = '';

                include_once 'showZapisRezult.php';

                $rezult .= showZapisRezult(array($sheduler_zapis), false, false, false, false, false, false, 0, true, false);;

//                if (!empty($invoice_j)){
//                    include_once 'showZapisRezult.php';
//
//                    foreach ($invoice_j as $data) {
//                        //$rezult .= '<li><a href="invoice.php?id=' . $data['id'] . '" class="ahref button_tiny" style="margin: 0 3px; font-size: 90%;" target="_blank" rel="nofollow noopener">' . $data['id'] . '</a></li>';
//                    }
//                }

                echo json_encode(array('result' => 'success', 'data' => $rezult));
                //var_dump($sheduler_zapis);

            }
        }
    }


?>
	