<?php 

//salary_del_f.php
//Функция для Удаление(блокирование) 

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		include_once 'functions.php';
		if ($_POST){

            if (!isset($_POST['id']) || !isset($_POST['type'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $msql_cnnct = ConnectToDB ();

                //Удаляем оплату из БД
                if ($_POST['type'] == 'worker') {
                    $query = "DELETE FROM `fl_spr_salaries` WHERE `id`='{$_POST['id']}'";
                }

                if ($_POST['type'] == 'category') {
                    $query = "DELETE FROM `fl_spr_salaries_category` WHERE `id`='{$_POST['id']}'";
                }

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                echo json_encode(array('result' => 'success', 'data' => ''));

            }
		}
	}
	
?>