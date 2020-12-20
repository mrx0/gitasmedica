<?php 

//cert_name_cell_f.php
//выдача именного сертификата

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['cert_id']) || !isset($_POST['client_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{
                include_once 'DBWork.php';

                $cert_j = SelDataFromDB('journal_cert_name', $_POST['cert_id'], 'id');
                $client_j = SelDataFromDB('spr_clients', $_POST['client_id'], 'id');

                if (($cert_j != 0) && ($client_j != 0)) {

                    $msql_cnnct = ConnectToDB();

                    $time = date('Y-m-d H:i:s', time());

                    //$cell_time = date_format(date_create($_POST['cell_date'].' '.date('H:i:s', time())), 'Y-m-d  H:i:s');
                    $cell_time = $time;

//                    $expires_time = date_create($cell_time);
//
//                    if ($_POST['expirationDate'] == 3) {
//                        date_modify($expires_time, '+3 month');
//                    }
//                    if ($_POST['expirationDate'] == 6) {
//                        date_modify($expires_time, '+6 month');
//                    }
//                    if ($_POST['expirationDate'] == 12) {
//                        date_modify($expires_time, '+12 month');
//                    }
//
//                    //date_modify($expires_time, '+3 month');
//                    $expires_time = date_format($expires_time, 'Y-m-d');

                    //Обновляем
                    $query = "UPDATE `journal_cert_name` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `client_id`='{$_POST['client_id']}', `cell_time`='{$cell_time}', `filial_id`='{$_SESSION['filial']}', `status`='7' WHERE `id`='{$_POST['cert_id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    //логирование
                    AddLog (GetRealIp(), $_SESSION['id'], '', 'Выдан именной сертификат ['.$_POST['cert_id'].']. ['.$time.'].');

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok"><a href="certificate_name.php?id=' . $_POST['cert_id'] . '" class="ahref">Сертификат</a> обновлён.</div>'));

                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
            }
        }
    }
?>