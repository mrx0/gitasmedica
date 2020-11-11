<?php 

//installment_status_change_f.php
//Функция для изменения статуса рассрочки

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		include_once 'functions.php';
		if ($_POST){
		    if (isset($_POST['client_id'])/* && isset($_POST['invoice_id'])*/ && isset($_POST['installment_status_now'])) {

                //$msql_cnnct = ConnectToDB();
                /*!!!Тест PDO*/
                include_once('DBWorkPDO.php');

                $db = new DB();

                //$date = date('Y-m-d', time());

                //Если ничего нет и не было, ставим, что теперь есть
                if ($_POST['installment_status_now'] == 0){
                    $status = 1;
                }
                //Если была рассрочка, ставим, что теперь закрыта
                if ($_POST['installment_status_now'] == 1){
                    $status = 7;
                }
                //Если была рассрочка, но была закрыта, то ставим, что теперь снова есть
                if ($_POST['installment_status_now'] == 7){
                    $status = 1;
                }

                $args = [
                    'client_id' => $_POST['client_id'],
                    'status' => $status
                ];

                $query = "UPDATE `spr_clients` SET `installment`= :status WHERE `id`= :client_id";

                $db::sql($query, $args);

                //Создаем рассрочку
                if ($status == 1) {
                    //Вставить рассрочке в БД
                    $query = "INSERT INTO `journal_installments` (
                            `client_id`,
                            `summ`,
                            `date_in`,
                            `create_person`, 
                            `create_time`,
                            `status`
                            )
                            VALUES (
                            :client_id,
                            :summ,
                            :date_in,
                            :create_person, 
                            :create_time,
                            :status
                            )";

                    $args = [
                        'client_id' => $_POST['client_id'],
                        'summ' => 0,
                        'date_in' => date('Y-m-d', time()),
                        'create_person' => $_SESSION['id'],
                        'create_time' => date('Y-m-d H:i:s', time()),
                        'status' => $status
                    ];

                }else{
                    //Удаляем рассрочку

                    $query = "DELETE FROM `journal_installments` WHERE `client_id`= :client_id";

                    $args = [
                        'client_id' => $_POST['client_id']
                    ];
                }

                $db::sql($query, $args);

                echo json_encode(array('result' => 'success', 'data' => ''));
            }

		}

	}
	
?>