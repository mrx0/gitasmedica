<?php 

//add_percent_cat_id_in_invoice_f.php
//Добавление категории в сессионную переменную наряда

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            $temp_arr = array();

            if (!isset($_POST['ind']) || !isset($_POST['key']) || !isset($_POST['percent_cats']) || !isset($_POST['invoice_type']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{
                //var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);

                if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
                    if ($_POST['invoice_type'] == 5){
                        if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']])){
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['percent_cats'] = (int)$_POST['percent_cats'];
                        }
                    }
                    if ($_POST['invoice_type'] == 6){
                        if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']])){
                            $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['percent_cats'] = (int)$_POST['percent_cats'];
                        }
                    }
                }

                echo json_encode(array('result' => 'success', 'data' => 'Ok', 'data2' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data']));
            }
        }
    }

?>