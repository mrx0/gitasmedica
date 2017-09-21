<?php 

//changeOnlineZapisStatus_f.php
//Функция изменения статуса онлайн записи

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

			if (!isset($_POST['online_zapis_id']) || !isset($_POST['status'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                $time = date('Y-m-d H:i:s', time());

                $msql_cnnct = ConnectToDB();

                $dop = '';

                if ($_POST['status'] == 7){
                    $dop = ", `closed_time`='".$time."', `last_edit_person`='".$_SESSION['id']."'";
                }
                if ($_POST['status'] == 0){
                    $dop = ", `closed_time`='".$time."', `last_edit_person`='".$_SESSION['id']."'";
                }

                //Обновляем
                $query = "UPDATE `zapis_online` SET `status`='{$_POST['status']}'".$dop." WHERE `id`='{$_POST['online_zapis_id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                echo json_encode(array('result' => 'success', 'data' => ''));

			}
		}
	}
?>