<?php 

//finance_debt_add_f.php
//Функция для добавления долга

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['client']) && isset($_POST['summ']) && isset($_POST['type']) && isset($_POST['date_expires'])){
				include_once 'DBWork.php';
				
				$time = time();

                $msql_cnnct = ConnectToDB2 ();
				
				$date_expires = strtotime($_POST['date_expires'].' 00:00:00');
				
				$query = "INSERT INTO `journal_debts_prepayments` (
						`create_time`, `client`, `date_expires`, `summ`, `type`, `create_person`, `comment`) 
						VALUES (
						'{$time}', '{$_POST['client']}', '{$date_expires}',
						'{$_POST['summ']}', '{$_POST['type']}', '{$_SESSION['id']}', '{$_POST['comment']}') ";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $mysql_insert_id = mysqli_insert_id($msql_cnnct);

                CloseDB ($msql_cnnct);
					
                //логирование
                AddLog ('0', $_SESSION['id'], '', 'Добавлен долг #'.$mysql_insert_id.'. Пациент ['.$_POST['client'].']. Сумма ['.$_POST['summ'].']. Срок истечения ['.$_POST['date_expires'].']. Тип ['.$_POST['type'].']. Комментарий ['.$_POST['comment'].'].');
				
                if ($_POST['type'] == 3){
                    $descr = 'Аванс';
                }
                if ($_POST['type'] == 4){
                    $descr = 'Долг';
                }

                echo '
						<div class="query_ok">
							'.$descr.' <a href="finance_dp.php?id='.$mysql_insert_id.'">#'.$mysql_insert_id.'</a> добавлен.
							<br><br>
							<a href="client.php?id='.$_POST['client'].'" class="b">Карточка пациента</a>
							<a href="client_finance.php?client='.$_POST['client'].'" class="b">Счёт <i class="fa fa-rub"></i></a>
						</div>';
			}else{
				echo '
					<div class="query_neok">
						Что-то пошло не так
					</div>';
			}
		}
	}
?>