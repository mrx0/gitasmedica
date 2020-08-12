<?php 

//abonement_cell_dell_f.php
//Функция отмены продажи абонемента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			if (!isset($_POST['abonement_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                include_once 'DBWork.php';

                $abonement_j = SelDataFromDB('journal_abonement_solar', $_POST['abonement_id'], 'id');
                //var_dump($abonement_j);

                if ($abonement_j != 0) {

                    if ($abonement_j[0]['debited_min'] != 0){
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">С абонемента уже списывались минуты.</div>'));
                    }else {

                        $msql_cnnct = ConnectToDB();

                        $time = date('Y-m-d H:i:s', time());

                        //Обновляем
                        $query = "UPDATE `journal_abonement_solar` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `cell_price`='0', `cell_time`='0000-00-00 00:00:00', `filial_id`='0', `summ_type`='0', `expires_time`='0000-00-00', `status`='0' WHERE `id`='{$_POST['abonement_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //логирование
                        AddLog(GetRealIp(), $_SESSION['id'], '', 'Отменена продажа абонемента [' . $_POST['abonement_id'] . ']. [' . $time . '].');

                        echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Продажа отменена.</div>'));
                    }
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
			}
		}
	}
?>