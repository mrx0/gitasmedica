<?php 

//add_price_sclad_item_prihod_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['item_id']) || !isset($_POST['ind']) || !isset($_POST['price']) || !isset($_POST['edit']) || !isset($_POST['prihod_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);

                //Если редактирование
                if ($_POST['edit'] == 'true'){

                    if (isset($_SESSION['sclad']['items_prihod_data_edit'])) {
                        if (isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']])) {
                            if (isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']])) {
                                if (isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']][$_POST['ind']])) {
                                    $_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']][$_POST['ind']]['price'] = $_POST['price'] * 100;
                                }
                            }
                        }
                    }

                    echo json_encode(array('result' => 'success', 'data' => $_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']][$_POST['ind']]));
                }else {

                    if (isset($_SESSION['sclad']['items_prihod_data'])) {
                        if (isset($_SESSION['sclad']['items_prihod_data'][$_POST['item_id']])){
                            if (isset($_SESSION['sclad']['items_prihod_data'][$_POST['item_id']][$_POST['ind']])) {
                                $_SESSION['sclad']['items_prihod_data'][$_POST['item_id']][$_POST['ind']]['price'] = $_POST['price'] * 100;
    //                        $_SESSION['sclad']['items_prihod_data'][$_POST['item_id']][$_POST['ind']]['price'] = number_format(floatval(str_replace(',','.',$_POST['price'])), 2, '.', '');
    //                        $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['item_id']]['manual_itog_price'] = (int)$_POST['quantity'] * $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['price'];
                            }
                        }
                    }

                    echo json_encode(array('result' => 'success', 'data' => $_SESSION['sclad']['items_prihod_data'][$_POST['item_id']][$_POST['ind']]));
                    //echo json_encode(array('result' => 'success', 't_number_active' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active']));
                }
			}
		}
	}
?>