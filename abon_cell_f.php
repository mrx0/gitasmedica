<?php 

//abon_cell_f.php
//Функция продажи абонемента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			if (!isset($_POST['abonement_id']) || !isset($_POST['filial_id']) || !isset($_POST['cell_price']) || !isset($_POST['summ_type']) || !isset($_POST['cell_date'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                include_once 'DBWork.php';

                $cert_j = SelDataFromDB('journal_abonement_solar', $_POST['abonement_id'], 'id');

                if ($cert_j != 0) {

                    $msql_cnnct = ConnectToDB();

                    $time = date('Y-m-d H:i:s', time());

                    $cell_time = date_format(date_create($_POST['cell_date'].' '.date('H:i:s', time())), 'Y-m-d  H:i:s');

                    $expires_time = date_create($cell_time);

                    date_modify($expires_time, '+'.$cert_j[0]['exp_days'].' days');

                    //date_modify($expires_time, '+30 month');
                    $expires_time = date_format($expires_time, 'Y-m-d');

                    //Обновляем
                    $query = "UPDATE `journal_abonement_solar` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `cell_price`='{$_POST['cell_price']}', `cell_time`='{$cell_time}', `filial_id`='{$_POST['filial_id']}', `summ_type`='{$_POST['summ_type']}', `expires_time`='{$expires_time}', `status`='7' WHERE `id`='{$_POST['abonement_id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    //логирование
                    AddLog (GetRealIp(), $_SESSION['id'], '', 'Продан абонемент ['.$_POST['abonement_id'].']. ['.$time.'].');

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok"><a href="abonement.php?id=' . $_POST['abonement_id'] . '" class="ahref">Абонемент</a> обновлён.</div>'));

                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
			}
		}
	}
?>