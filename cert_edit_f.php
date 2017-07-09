<?php 

//cert_edit_f.php
//Функция редактирования сертификата

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			if (!isset($_POST['cert_id']) || !isset($_POST['num']) || !isset($_POST['nominal'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                include_once 'DBWork.php';

                $cert_j = SelDataFromDB('journal_cert', $_POST['cert_id'], 'id');

                if ($cert_j != 0) {

                    $old = 'Сертификат. Номер ['.$cert_j[0]['num'].']. Номинал: ['.$cert_j[0]['nominal'].'].';

                    $msql_cnnct = ConnectToDB();

                    $time = date('Y-m-d H:i:s', time());

                    //Обновляем
                    $query = "UPDATE `journal_cert` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `num`='{$_POST['num']}', `nominal`='{$_POST['nominal']}' WHERE `id`='{$_POST['cert_id']}'";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    //логирование
                    AddLog (GetRealIp(), $_SESSION['id'], $old, 'Изменён сертификат ['.$_POST['cert_id'].']. ['.$time.']. Номер ['.$_POST['num'].']. Номинал: ['.$_POST['nominal'].'].');

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok"><a href="certificate.php?id=' . $_POST['cert_id'] . '" class="ahref">Сертификат</a> обновлён.</div>'));

                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
			}
		}
	}
?>