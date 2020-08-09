<?php 

//prihod_close_f.php
//Провести приходную накладную, поставить статус 7

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){
			if (!isset($_POST['prihod_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                //include_once 'DBWork.php';
                /*!!!Тест PDO*/
                include_once('DBWorkPDO.php');

                //$date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in'].""));
                $date_in = date('Y-m-d H:i:s', time());

                $db = new DB();

                $query = "UPDATE `sclad_prihod` SET `status` = '7' WHERE `id`= :id";

                $args = [
                    'id' => $_POST['prihod_id']
                ];

                $db::sql($query, $args);

                //!!! Добавить сюда обновление количества на складе!!!
                //Нужно выбрать сначала некоторые данные из приходной накладной, а потом обновлять




				echo json_encode(array('result' => 'success', 'data' => 'Ok'));
			}
		}
	}

?>