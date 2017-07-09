<?php

//cert_add_f.php
//Функция добавления сертификата непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';

            if (!isset($_POST['num']) || !isset($_POST['nominal'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                $msql_cnnct = ConnectToDB();

                //А нет ли уже такого номера в базе?
                $query = "SELECT * FROM `journal_cert` WHERE `num`='{$_POST['num']}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Сертификат с таким номером уже присутствует в базе.</div>'));
                } else {
                    //Добавляем сертификат в базу
                    $cert_id = WriteCertToDB_Edit($_SESSION['id'], $_POST['num'], $_POST['nominal']);

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok"><a href="certificate.php?id=' . $cert_id . '" class="ahref">Сертификат</a> добавлен.</div>'));
                }
            }
        }
    }
?>