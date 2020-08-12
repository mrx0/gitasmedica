<?php 

//add_new_sclad_item_from_prihod_data_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['item_id']) || !isset($_POST['prihod_id']) || !isset($_POST['edit'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                if (isset($_SESSION['sclad'])) {

                    //Элемент
                    $data4copy = array(
                        'quantity' => 0,
                        'exp_garant_type' => 0,
                        'exp_garant_date' => '',
                        'price' => 0,
                        'summ' => 0
                    );

                    //Если редактирование
                    if ($_POST['edit'] == 'true'){
                        if (!empty($_SESSION['sclad']['items_prihod_data_edit'])) {

                            //Элемента нет в массиве
                            if (!empty($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']])) {
                                //$_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']] = array();

                                //Элемента нет в массиве
                                if (!isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']])) {
                                    $_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']] = array();
                                    //Элемент есть в массиве
                                } else {
                                    //--
                                }
                            }

                            array_push($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']], $data4copy);

                        }
                    }else {
                        if (!empty($_SESSION['sclad']['items_prihod_data'])) {

                            //Элемента нет в массиве
                            if (!isset($_SESSION['sclad']['items_prihod_data'][$_POST['item_id']])) {
                                $_SESSION['sclad']['items_prihod_data'][$_POST['item_id']] = array();
                                //Элемент есть в массиве
                            } else {
                                //--
                            }

                            array_push($_SESSION['sclad']['items_prihod_data'][$_POST['item_id']], $data4copy);

                        }
                    }
                }

				echo json_encode(array('result' => 'success'/*, 'data' => $_POST['item_id']*/));
			}
		}
	}
?>