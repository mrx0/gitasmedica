<?php 

//delete_announce_f.php
//Функция для Удаление(блокирование) 

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
        include_once('DBWorkPDO.php');
		include_once 'functions.php';

		if ($_POST){

            if (!isset($_POST['ann_id']) || !isset($_POST['status'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

//                if ($order_j != 0) {

                    $db = new DB();

                    $time = date('Y-m-d H:i:s', time());

                    $args = [
                        'id' => $_POST['ann_id'],
                        'status' => $_POST['status'],
                        'last_edit_person' => $_SESSION['id'],
                        'time' => $time
                    ];

                    $query = "UPDATE `journal_announcing` SET `status`=:status, `last_edit_time`=:time, `last_edit_person`=:last_edit_person WHERE `id`=:id;";

                    $db::sql($query, $args);

                    echo json_encode(array('result' => 'success', 'data' => 'Готово'));

//                }else{
//                    //echo json_encode(array('result' => 'success', 'data' => 'Чёт ошибка какая-то'));
//                }
            }
		}
	}
	
?>