<?php

//installment_add_f.php
//Функция добавления рассрочки (новое)

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            //include_once 'fl_DBWork.php';

            if (!isset($_POST['client_id']) || !isset($_POST['invoice_id']) || !isset($_POST['summ']) || !isset($_POST['month_start']) || !isset($_POST['year_start'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                //$msql_cnnct = ConnectToDB();
                include_once('DBWorkPDO.php');

                $db = new DB();

                //Добавляем данные по рассрочке
                $query = "INSERT INTO `journal_installments` (
                            `client_id`,
                            `invoice_id`,
                            `summ`,
                            `date_in`,
                            `create_person`, 
                            `create_time`,
                            `status`
                            )
                            VALUES (
                            :client_id,
                            :invoice_id,
                            :summ,
                            :date_in,
                            :create_person, 
                            :create_time,
                            :status
                            )";

                $args = [
                    'client_id' => $_POST['client_id'],
                    'invoice_id' => $_POST['invoice_id'],
                    'summ' => $_POST['summ'],
                    'date_in' => $_POST['year_start'].'-'.$_POST['month_start'].'-01',
                    'create_person' => $_SESSION['id'],
                    'create_time' => date('Y-m-d H:i:s', time()),
                    'status' => 1
                ];

                $db::sql($query, $args);


                //Обновляем статус рассрочки в карточке пациента
//                $args = [
//                    'client_id' => $_POST['client_id'],
//                    'status' => 1
//                ];
//
//                $query = "UPDATE `spr_clients` SET `installment`= :status WHERE `id`= :client_id";
//
//                $db::sql($query, $args);

                echo json_encode(array('result' => 'success', 'data' => ''));

                //А нет ли уже такого в базе?
//                $query = "SELECT * FROM `fl_spr_percents` WHERE `personal_id`='{$_POST['personal_id']}' AND `name`='{$_POST['cat_name']}'";
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Такая категория уже есть.</div>'));
//                } else {
//                    //Добавляем категорию процентов в базу
//                    $percent_cat_id = WritePercentCatToDB_Edit($_SESSION['id'], $_POST['cat_name'], (int)$_POST['work_percent'], (int)$_POST['material_percent'], (int)$_POST['summ_special'], $_POST['personal_id']);
//
//                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok"><a href="fl_percent_cat.php?id='.$percent_cat_id.'" class="ahref">Категория процентов</a> добавлена.</div>'));
//                }

            }
        }
    }
?>