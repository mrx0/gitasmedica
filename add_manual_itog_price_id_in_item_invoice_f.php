<?php

//add_manual_itog_price_id_in_item_invoice_f.php
//

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);

    if ($_POST){

        $temp_arr = array();

        if (!isset($_POST['ind']) || !isset($_POST['key']) || !isset($_POST['price']) || !isset($_POST['invoice_type']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
            echo json_encode(array('result' => 'error', 'data' => $_POST));
        }else{
            //var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);

            if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
                if ($_POST['invoice_type'] == 5){
                    if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']])){
                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['manual_itog_price'] = (int)$_POST['manual_itog_price'];
                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['itog_price'] = (int)$_POST['price'];
                        //$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$t_number_active]
                    }
                }
                if (($_POST['invoice_type'] == 6) || ($_POST['invoice_type'] == 10) || ($_POST['invoice_type'] == 7)){
                    if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']])){
                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['manual_itog_price'] = (int)$_POST['manual_itog_price'];
                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['itog_price'] = (int)$_POST['price'];
                    }
                }
            }

            //echo json_encode(array('result' => 'success', 'data' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['itog_price']));
        }
    }
}
?>