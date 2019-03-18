<?php 

//fl_refreshTabelForWorkerFromSchedulerReport_f.php
//Обновлние табеля админов и ассистов / пересчет данных

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){
            if (!isset($_POST['tabel_id']) || !isset($_POST['worker_id']) || !isset($_POST['oklad']) || !isset($_POST['w_percenthours']) ||
                !isset($_POST['worker_revenue_percent']) || !isset($_POST['filialmoney']) || !isset($_POST['worker_category_id']) ||
                !isset($_POST['w_hours']) || !isset($_POST['summ'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{

                include_once 'DBWork.php';
                include_once 'ffun.php';

                $time = date('Y-m-d H:i:s', time());

                $msql_cnnct = ConnectToDB ();

                $percent_summ = 0;

                $percent_summ = ($_POST['filialmoney'] * $_POST['worker_revenue_percent']/ 100) * $_POST['w_percenthours']/ 100;

                $query = "UPDATE `fl_journal_tabels` SET `salary` = '{$_POST['oklad']}', `hours_percent` = '{$_POST['w_percenthours']}', 
                  `revenue_percent` = '{$_POST['worker_revenue_percent']}', `filial_summ` = '{$_POST['filialmoney']}',
                   `hours_count` = '{$_POST['w_hours']}', `percent_summ` = '{$percent_summ}',
                   `summ` = '{$_POST['summ']}',
                   `category` = '{$_POST['worker_category_id']}',
                  `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' 
                  WHERE `id`='{$_POST['tabel_id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                CloseDB ($msql_cnnct);

                echo json_encode(array('result' => 'success', 'data' => $_POST['summ']));

            }
        }
    }

?>