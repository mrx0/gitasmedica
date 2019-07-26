<?php 

//fl_addInBank_f.php
//Функция для добавления вычета денег в банк

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
            if (isset($_POST['filial_id']) && isset($_POST['date']) && isset($_POST['summ']) && isset($_POST['comment'])){

                include_once 'DBWork.php';

                $time = time();

                $data_temp_arr = explode(".", $_POST['date']);

                $d = $data_temp_arr[0];
                $m = $data_temp_arr[1];
                $y = $data_temp_arr[2];

                $msql_cnnct = ConnectToDB();

                $create_time = date('Y-m-d H:i:s', time());

                $query = "INSERT INTO `fl_journal_in_bank`
                    (`filial_id`, `day`, `month`, `year`, `summ`, `comment`, `create_time`, `create_person`)
                    VALUES ('{$_POST['filial_id']}', '{$d}', '{$m}', '{$y}', '{$_POST['summ']}', '{$_POST['comment']}', '{$create_time}', '{$_SESSION['id']}');";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //логирование
                //AddLog ();

                echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Добавлено</div>'));

                //CloseDB($msql_cnnct);

			}else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}
		}
	}
?>