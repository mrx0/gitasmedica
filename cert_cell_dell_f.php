<?php 

//cert_cell_dell_f.php
//Функция отмены продажи сертификата

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			if (!isset($_POST['cert_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                include_once 'DBWork.php';

                $cert_j = SelDataFromDB('journal_cert', $_POST['cert_id'], 'id');
                //var_dump($cert_j);

                if ($cert_j != 0) {

                    if ($cert_j[0]['debited'] != 0){
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">С сертификата уже списывались средства.</div>'));
                    }else {

                        $msql_cnnct = ConnectToDB();

                        $time = date('Y-m-d H:i:s', time());

                        //Обновляем
                        $query = "UPDATE `journal_cert` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `cell_price`='0', `cell_time`='0000-00-00 00:00:00', `office_id`='0', `summ_type`='0', `expires_time`='0000-00-00', `status`='0' WHERE `id`='{$_POST['cert_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //логирование
                        AddLog(GetRealIp(), $_SESSION['id'], '', 'Отменена продажа сертификата [' . $_POST['cert_id'] . ']. [' . $time . '].');

                        echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Продажа отменена.</div>'));
                    }
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
			}
		}
	}
?>