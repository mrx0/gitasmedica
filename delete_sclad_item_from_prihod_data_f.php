<?php 

//delete_sclad_item_from_prihod_data_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['item_id']) || (!isset($_POST['ind']))){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                if (isset($_SESSION['sclad'])) {

                    if (($_POST['edit'] == 'true') && ($_POST['prihod_id'] != 0)){

                        if (!empty($_SESSION['sclad']['items_prihod_data_edit'])) {

                            if ($_POST['item_id'] == 0) {
                                unset($_SESSION['sclad']['items_prihod_data_edit']);
                            } else {
                                //Элемент есть в массиве
                                if (isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']])) {
                                    if (isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']])) {
                                        if (isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']][$_POST['ind']])) {
                                            ///Удаляем
                                            unset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']][$_POST['ind']]);
                                        }

                                        if (empty($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']])) {
                                            unset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']]);
                                        }
                                    }
                                }

                                if (empty($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']])) {
                                    unset($_SESSION['sclad']['items_prihod_data_edit']);
                                }
                            }

                            //Элемент есть в массиве
                            if (isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']])) {
                                if (isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']])) {
                                    if (isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']][$_POST['ind']])) {
                                        $data4copy = $_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']][$_POST['ind']];

                                        array_push($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']][$_POST['item_id']], $data4copy);
                                    }
                                }
                            }

                        }
                    }else {


                        if (!empty($_SESSION['sclad']['items_prihod_data'])) {

                            if ($_POST['item_id'] == 0) {
                                unset($_SESSION['sclad']['items_prihod_data']);
                            } else {
                                //Элемент есть в массиве
                                if (isset($_SESSION['sclad']['items_prihod_data'][$_POST['item_id']])) {
                                    if (isset($_SESSION['sclad']['items_prihod_data'][$_POST['item_id']][$_POST['ind']])) {
                                        ///Удаляем
                                        unset($_SESSION['sclad']['items_prihod_data'][$_POST['item_id']][$_POST['ind']]);
                                    }

                                    if (empty($_SESSION['sclad']['items_prihod_data'][$_POST['item_id']])) {
                                        unset($_SESSION['sclad']['items_prihod_data'][$_POST['item_id']]);
                                    }
                                }

                                if (empty($_SESSION['sclad']['items_prihod_data'])) {
                                    unset($_SESSION['sclad']['items_prihod_data']);
                                }
                            }
                        }
                    }
                }

				echo json_encode(array('result' => 'success'));
			}
		}
	}
?>