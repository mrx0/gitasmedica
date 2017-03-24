<?php 

//order_add_f.php
//Функция добавления ордера в базу

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['client_id']) || !isset($_POST['summ']) || !isset($_POST['invoice_id']) || !isset($_POST['date_in'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                $time = date('Y-m-d H:i:s', time());
                $date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in']." 09:00:00"));

                $comment = addslashes($_POST['comment']);

                //Проверки, проверочки
                include_once 'DBWork.php';
                //Ищем наряд
                $invoice_j = SelDataFromDB('journal_invoice', $_POST['invoice_id'], 'id');

                if ($invoice_j != 0){
                    //Ищем пациента
                    $client_j = SelDataFromDB('spr_clients', $invoice_j[0]['client_id'], 'user');

                    if ($client_j != 0){
                        //Если это был наряд того пациента
                        if ($client_j[0]['id'] == $invoice_j[0]['client_id']){
                            //Если наряд оплачен
                            if ($invoice_j[0]['summ'] == $invoice_j[0]['paid']) {
                                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Наряд уже оплачен</div>'));
                            //Если сумма наряда меньше, чем уже оплачено
                            }elseif($invoice_j[0]['summ'] < $invoice_j[0]['paid']){
                                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Сумма наряда не может быть меньше общей внесённой суммы.</div>'));
                            //Если сумма наряда больше, чем уже оплачено, он по факту не оплачен и вроде можно двигаться дальше
                            }elseif($invoice_j[0]['summ'] > $invoice_j[0]['paid']){
                                //Если стоит метка, что наряд оплачен, надо разбираться
                                if ($invoice_j[0]['status'] == 5) {
                                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка! У наряда стоит статус <оплачен>.</div>'));
                                }elseif($invoice_j[0]['status'] == 9){
                                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Наряд закрыт/заблокирован. Операции с ним запрещены</div>'));
                                }else{

                                    echo json_encode(array('result' => 'success', 'data' => 'Пока не работает. но скоро...'));
                                }
                            }else {
                                //echo json_encode(array('result' => 'success', 'data' => $invoice_j[0]['status'].'-'.$invoice_j[0]['summ']));
                            }
                        }
                    }

                }
/*

                require 'config.php';
                mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
                mysql_select_db($dbName) or die(mysql_error());
                mysql_query("SET NAMES 'utf8'");

                //Добавляем в базу
                $query = "INSERT INTO `journal_order` (`client_id`, `office_id`, `summ`, `summ_type`, `date_in`, `comment`, `create_person`, `create_time`) 
						VALUES (
						'{$_POST['client_id']}', '{$_POST['office_id']}', '{$_POST['summ']}', '{$_POST['summtype']}', '{$date_in}', '{$comment}', '{$_SESSION['id']}', '{$time}')";

                mysql_query($query) or die(mysql_error().' -> '.$query);

                //ID новой позиции
                $mysql_insert_id = mysql_insert_id();

                //!!! @@@ Пересчет баланса
                include_once 'ffun.php';
                calculateBalance ($_POST['client_id']);*/

                //echo json_encode(array('result' => 'success', 'data' => $invoice_j[0]['status']));

			}
		}
	}
?>