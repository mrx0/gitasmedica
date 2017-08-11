<?php 

//lab_order_add_f.php
//Функция добавления заказа в лабораторию в базу

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';

            //разбираемся с правами
            $god_mode = FALSE;

            require_once 'permissions.php';

			$temp_arr = array();

			if (!isset($_POST['client_id']) || !isset($_POST['worker']) || !isset($_POST['lab']) || !isset($_POST['descr']) || !isset($_POST['comment'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так1</div>'));
			}else{

                //Филиал
                if (isset($_SESSION['filial'])){

                    $time = date('Y-m-d H:i:s', time());
                    //$date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in']." 09:00:00"));

                    $worker_j = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');

                    if ($worker_j != 0){

                        $worker_id = $worker_j[0]['id'];

                        $msql_cnnct = ConnectToDB ();

                        $descr = addslashes($_POST['descr']);
                        $comment = addslashes($_POST['comment']);

                        //Добавляем в базу
                        $query = "INSERT INTO `journal_laborder` (`client_id`, `office_id`, `worker_id`, `labor_id`, `descr`, `comment`, `create_person`, `create_time`) 
                            VALUES (
                            '{$_POST['client_id']}', '{$_SESSION['filial']}', '{$worker_id}', '{$_POST['lab']}', '{$descr}', '{$comment}', '{$_SESSION['id']}', '{$time}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        //ID новой позиции
                        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

                        echo json_encode(array('result' => 'success', 'data' => $mysql_insert_id));

                    }else{
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">В нашей базе нет такого врача</div>'));
                    }
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">У вас не определён филиал</div>'));
                }
			}
		}else{
            //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так2</div>'));
        }
	}
?>