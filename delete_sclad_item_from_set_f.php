<?php 

//delete_sclad_item_from_set_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['item_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                if (isset($_SESSION['sclad'])) {
                    if (!empty($_SESSION['sclad']['items_data'])) {

                        if ($_POST['item_id'] == 0){
                            unset($_SESSION['sclad']['items_data']);
                        }else{
                            //Элемент есть в массиве
                            if (in_array($_POST['item_id'], $_SESSION['sclad']['items_data'])) {
                                ///Удаляем
                                unset($_SESSION['sclad']['items_data'][array_search($_POST['item_id'], $_SESSION['sclad']['items_data'])]);
                            }
                        }
                    }
                }

				echo json_encode(array('result' => 'success'));
			}
		}
	}
?>