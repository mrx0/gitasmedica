<?php 

//change_pnone_call_mark_f.php
//Непосредственное добавление нового звонка в БД

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
//		var_dump ($_POST);
		
		if ($_POST){

			$temp_arr = array();

			if (!isset($_POST['client_id']) || !isset($_POST['status']) || !isset($_POST['call_time']) || !isset($_POST['comment'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                //include_once 'DBWork.php';

                /*!!!Тест PDO*/
                include_once('DBWorkPDO.php');

                $time = date('Y-m-d H:i:s', time());

                $comment = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['comment'])))));

                $db = new DB();

                //Вставить запись прихода в БД:
                $query = "INSERT INTO `journal_phone_calling` (
                `client_id`,
                `status`,
                `call_time`,
                `comment`,
                `create_person`,
                `create_time`
                )
                VALUES (
                :client_id,
                :status,
                :call_time,
                :comment,
                :create_person,
                :create_time
                )";

                $args = [
                    'client_id' => $_POST['client_id'],
                    'status' => $_POST['status'],
                    'call_time' => date('Y-m-d', strtotime($_POST['call_time'].'')),
                    'comment' => $comment,
                    'create_person' => $_SESSION['id'],
                    'create_time' => $time
                ];

                $db::sql($query, $args);

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));

			}
		}
	}
?>