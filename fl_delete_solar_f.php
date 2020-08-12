<?php 

//fl_delete_solar_f.php
//Функция для Удаление(блокирование) 

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		include_once 'functions.php';

		if ($_POST){

            if (!isset($_POST['id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {
                //Ищем
                $solar_j = SelDataFromDB('journal_solar', $_POST['id'], 'id');

                if ($solar_j != 0) {

                    $msql_cnnct = ConnectToDB();

                    $time = date('Y-m-d H:i:s', time());

                    //если было по абонементу, надо вернуть туда минуты
                    if ($solar_j[0]['abon_id'] != 0){
                        $query = "SELECT `debited_min` FROM `journal_abonement_solar` WHERE `id`='{$solar_j[0]['abon_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){
                            $arr = mysqli_fetch_assoc($res);
                            $debited_min = $arr['debited_min'];

                            $query = "UPDATE `journal_abonement_solar` SET `debited_min`='".($debited_min - $solar_j[0]['min_count'])."' WHERE `id`='{$solar_j[0]['abon_id']}';";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        }
                    }

                    //$query = "UPDATE `journal_solar` SET `status`='9', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$_POST['id']}';";
                    $query = "DELETE FROM `journal_solar` WHERE `id`='{$_POST['id']}';";
                    
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    CloseDB ($msql_cnnct);

                    echo json_encode(array('result' => 'success', 'data' => 'Документ удалён'));

                }else{
                    //echo json_encode(array('result' => 'success', 'data' => 'Чёт ошибка какая-то'));
                }
            }
		}
	}
	
?>