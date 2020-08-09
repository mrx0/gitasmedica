<?php 

//prihod_open_f.php
//Снять отметку о проведении приходной накладной, поставить статус 0

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){
			if (!isset($_POST['prihod_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
//                include_once 'DBWork.php';
//
//                $msql_cnnct = ConnectToDB ();
//
//                $query = "UPDATE `sclad_prihod` SET `status` = '0', `closed_time`='0' WHERE `id`='{$_POST['prihod_id']}'";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                CloseDB ($msql_cnnct);

                /*!!!Тест PDO*/
                include_once('DBWorkPDO.php');

                //$date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in'].""));
                $date_in = date('Y-m-d H:i:s', time());



                //!!! сначала надо проверить можно ли распроводить
                //собрать данные по позиции, прикинуть, не уйдёт ли кол-во в минус и так далее


                $db = new DB();

                $query = "UPDATE `sclad_prihod` SET `status` = '0' WHERE `id`= :id";

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