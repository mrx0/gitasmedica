<?php 

//order_add_f.php
//Функция добавления ордера в базу

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
			
			if (!isset($_POST['client_id']) || !isset($_POST['summ']) || !isset($_POST['summtype']) || !isset($_POST['date_in']) || !isset($_POST['office_id']) || !isset($_POST['org_pay'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                $msql_cnnct = ConnectToDB();

                $time = date('Y-m-d H:i:s', time());
                $date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in']." 09:00:00"));

                //Если заднее число записи
                if (
                    ((date("Y", strtotime($_POST['date_in']." 09:00:00")) < date("Y")) ||
                        ((date("Y", strtotime($_POST['date_in']." 09:00:00")) == date("Y")) && (date("m", strtotime($_POST['date_in']." 09:00:00")) < date("m"))) ||
                        ((date("m", strtotime($_POST['date_in']." 09:00:00")) == date("m")) && (date("d", strtotime($_POST['date_in']." 09:00:00")) < date("d")))) &&
                    !(($finances['see_all'] == 1) || $god_mode)
                ) {

                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Нельзя добавлять ордеры задним числом</div>'));
                }else{


                    $comment = addslashes($_POST['comment']);

                    //Добавляем в базу
                    $query = "INSERT INTO `journal_order` (`client_id`, `office_id`, `summ`, `summ_type`, `org_pay`, `date_in`, `comment`, `create_person`, `create_time`) 
                            VALUES (
                            '{$_POST['client_id']}', '{$_POST['office_id']}', '{$_POST['summ']}', '{$_POST['summtype']}', '{$_POST['org_pay']}', '{$date_in}', '{$comment}', '{$_SESSION['id']}', '{$time}')";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    //ID новой позиции
                    $mysql_insert_id = mysqli_insert_id($msql_cnnct);

                    //!!! @@@ Пересчет баланса
                    include_once 'ffun.php';
                    calculateBalance ($_POST['client_id']);

                    echo json_encode(array('result' => 'success', 'data' => $mysql_insert_id));
                }
			}
		}
	}
?>