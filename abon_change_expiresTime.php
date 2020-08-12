<?php 

//abon_change_expiresTime.php
//Функция редактирования даты истечения срока абонемента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			if (!isset($_POST['id']) || !isset($_POST['dataCertEnd'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                include_once 'DBWork.php';

                $abon_j = SelDataFromDB('journal_abonement_solar', $_POST['id'], 'id');

                if ($abon_j != 0) {

                    $msql_cnnct = ConnectToDB();

                    $time = date('Y-m-d H:i:s', time());

                    //Обновляем
                    $query = "UPDATE `journal_abonement_solar` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `expires_time`='{$_POST['dataCertEnd']}' WHERE `id`='{$_POST['id']}'";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    //логирование
                    //AddLog (GetRealIp(), $_SESSION['id'], '', 'Изменён сертификат ['.$_POST['id'].']. ['.$time.']. Номер ['.$_POST['num'].']. Номинал: ['.$_POST['nominal'].'].');

                    echo json_encode(array('result' => 'success', 'data' => 'OK'));

                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
			}
		}
	}
?>