<?php 

//cert_name_cell_dell_f.php
//Функция отмены выдачи именного сертификата

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

                $cert_j = SelDataFromDB('journal_cert_name', $_POST['cert_id'], 'id');
                //var_dump($cert_j);

                if ($cert_j != 0) {

                    //Если не закрыт
                    if ($cert_j[0]['status'] == 5){
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Сертификат использован и закрыт.</div>'));
                    }else {

                        $msql_cnnct = ConnectToDB();

                        $time = date('Y-m-d H:i:s', time());

                        //Обновляем
                        $query = "UPDATE `journal_cert_name` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `client_id`='0', `cell_time`='0000-00-00 00:00:00', `filial_id`='0', `summ_type`='0', `expires_time`='0000-00-00', `status`='0' WHERE `id`='{$_POST['cert_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //логирование
                        AddLog(GetRealIp(), $_SESSION['id'], '', 'Отменена выдачи именного сертификата [' . $_POST['cert_id'] . ']. [' . $time . '].');

                        echo json_encode(array('result' => 'success', 'data' => $query));
                    }
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
			}
		}
	}
?>