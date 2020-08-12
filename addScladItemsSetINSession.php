<?php 

//addScladItemsSetINSession.php
//Добавление в сессию данных по складским позициям, с которыми будем работать (ID)

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

        if (isset($_POST['item_id']) && isset($_POST['status'])){

            //if (!empty($_POST['sclad'])) {

            if (!isset($_SESSION['sclad'])) {
                $_SESSION['sclad'] = array();
            }

            if (!isset($_SESSION['sclad']['items_data'])) {
                $_SESSION['sclad']['items_data'] = array();
            }

            //Если хотим добавить
            if ($_POST['status'] == 'true') {
                //И элемента еще нет
                if (!in_array($_POST['item_id'], $_SESSION['sclad']['items_data'])) {
                    //Добавляем
                    array_push($_SESSION['sclad']['items_data'], $_POST['item_id']);
                }
            //Если хотим удалить
            }else{
                //И он уже есть в массиве
                if (in_array($_POST['item_id'], $_SESSION['sclad']['items_data'])) {
                    ///Удаляем
                    //unset($_SESSION['sclad']['items_data'][$_POST['item_id']]);
                    unset($_SESSION['sclad']['items_data'][array_search($_POST['item_id'], $_SESSION['sclad']['items_data'])]);
                }
            }

            echo json_encode(array('result' => 'success', 'count' => count($_SESSION['sclad']['items_data']), 'data' => $_SESSION['sclad']));

        }else{
            echo json_encode(array('result' => 'error', 'count' => 0, 'data' => $_SESSION['sclad']));
        }
	}
?>