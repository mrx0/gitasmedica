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

			if (!isset($_POST['worker_id']) || !isset($_POST['congr_id']) || !isset($_POST['congr_status']) || !isset($_POST['year'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                include_once('DBWorkPDO.php');

                //$time = date('Y', time());

                $db = new DB();

                if ($_POST['congr_status'] == 0) {
                    $query = "DELETE FROM `journal_bd_congr` WHERE `id` = '".$_POST['congr_id']."'";
                }else{
                    $query = "INSERT INTO `journal_bd_congr` (
                        `worker_id`,
                        `year`,
                        `status`
                        )
                        VALUES (
                        '{$_POST['worker_id']}',
                        '{$_POST['year']}',
                        1
                        )";
                }

                $db::sql($query, []);

                if ($_POST['congr_status'] == 1) {
                    $insert_id = $db->lastInsertId();
                }else{
                    $insert_id = 0;
                }

                echo json_encode(array('result' => 'success', 'data' => $insert_id ));

			}
		}
	}
?>