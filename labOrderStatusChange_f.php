<?php 

//labOrderStatusChange_f.php
//Функция изменения статуса заказа в лабораторию

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';

            //разбираемся с правами
            //$god_mode = FALSE;

			if (!isset($_POST['lab_order_id']) || !isset($_POST['status'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{


                $time = date('Y-m-d H:i:s', time());

                require 'config.php';
                mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
                mysql_select_db($dbName) or die(mysql_error());
                mysql_query("SET NAMES 'utf8'");

                $query = "INSERT INTO `journal_laborder_ex` (`laborder_id`, `create_person`, `create_time`, `status`)
                    VALUES (
                    '{$_POST['lab_order_id']}', '{$_SESSION['id']}', '{$time}', '{$_POST['status']}')";

                mysql_query($query) or die(mysql_error().' -> '.$query);

                //Обновляем
                $query = "UPDATE `journal_laborder` SET `status`='{$_POST['status']}' WHERE `id`='{$_POST['lab_order_id']}'";

                mysql_query($query) or die(mysql_error().' -> '.$query);

                mysql_close();

                echo json_encode(array('result' => 'success', 'data' => ''));

			}
		}
	}
?>