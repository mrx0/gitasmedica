<?php 

//finance_dp_repayment_add_f.php
//Функция для добавления погашения

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['id']) && isset($_POST['summ']) && isset($_POST['comment'])){
				include_once 'DBWork.php';
				
				$time = time();
				
				$old = '';

                $msql_cnnct = ConnectToDB ();
				
				$date_expires = 0;
				
				//Для лога соберем сначала то, что было в записи.
				$query = "SELECT * FROM `journal_debts_prepayments` WHERE `id`='{$_POST['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

				$number = mysqli_num_rows($res);

				if ($number != 0){
					$arr = mysqli_fetch_assoc($res);
					$old = 'Пациент ['.$arr['client'].']. Сумма ['.$arr['summ'].']. Тип ['.$arr['type'].']. Срок истечения ['.$arr['date_expires'].']. Статус ['.$arr['status'].']. Комментарий ['.$arr['comment'].'].';
				}else{
					$old = 'Не нашли старую запись.';
				}
				
				if ($number != 0){
					
					$query = "INSERT INTO `journal_debts_prepayments` (
							`create_time`, `client`, `date_expires`, `summ`, `type`, `create_person`, `comment`, `parent`) 
							VALUES (
							'{$time}', '{$arr['client']}', '{$date_expires}',
							'{$_POST['summ']}', '8', '{$_SESSION['id']}', '{$_POST['comment']}', '{$_POST['id']}') ";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $mysql_insert_id = mysqli_insert_id($msql_cnnct);

                    CloseDB ($msql_cnnct);

                    //логирование
                    AddLog ('0', $_SESSION['id'], '', 'Добавлено погашение #'.$mysql_insert_id.' к Документу #'.$_POST['id'].'. Пациент ['.$arr['client'].']. Сумма ['.$_POST['summ'].']. Срок истечения ['.$arr['date_expires'].']. Тип ['.$arr['type'].']. Комментарий ['.$_POST['comment'].'].');

                    echo '
							<div class="query_ok">
								ПОгашение <a href="finance_dp_repayment.php?id='.$mysql_insert_id.'">#'.$mysql_insert_id.'</a> добавлено.
								<br><br>
								<a href="client.php?id='.$arr['client'].'" class="b">Карточка пациента</a>
								<a href="client_finance.php?client='.$arr['client'].'" class="b">Счёт <i class="fa fa-rub"></i></a>
							</div>';
				}else{
					echo '
						<div class="query_neok">
							Не нашли запись.
						</div>';
				}
			}else{
				echo '
					<div class="query_neok">
						Что-то пошло не так
					</div>';
			}
		}
	}
?>