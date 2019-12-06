<?php 

//fl_add_new_salary_f.php
//Функция добавления нового оклада сотрудника

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';

			if (!isset($_POST['worker_id']) || !isset($_POST['summ']) || !isset($_POST['date_from'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                $time = date('Y-m-d H:i:s', time());
                $date_from = date('Y-m-01 H:i:s', strtotime($_POST['date_from']." 00:00:00"));

                $msql_cnnct = ConnectToDB ();

                //Вставим новую запись
                $query = "INSERT INTO `fl_spr_salaries` (
                  `worker_id`, `summ`, `date_from`, `category`, `create_time`, `create_person`)
                VALUES (
                  '{$_POST['worker_id']}', '{$_POST['summ']}', '{$date_from}', '{$_POST['category_id']}', '{$time}', '{$_SESSION['id']}')";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                echo json_encode(array('result' => 'success', 'data' => $_POST['summ']));

			}
		}
	}
?>