<?php 

//finance_dp_edit_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){

			$old = '';

            $msql_cnnct = ConnectToDB ();
			
			$date_expires = strtotime($_POST['date_expires'].' 00:00:00');
			
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
			$time = time();

			$query = "UPDATE `journal_debts_prepayments` SET 
			`summ`='{$_POST['summ']}', `comment`='{$_POST['comment']}', `date_expires`='{$date_expires}', `last_edit_person`='{$_SESSION['id']}', `last_edit_time`='{$time}' WHERE `id`='{$_POST['id']}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            CloseDB ($msql_cnnct);
			
			//логирование
			AddLog ('0', $_SESSION['id'], $old, 'Аванс или долг ['.$_POST['id'].']. ['.date('d.m.y H:i', $time).']. Сумма ['.$_POST['summ'].']. Срок истечения ['.$_POST['date_expires'].']. Комментарий ['.$_POST['comment'].'].');
			
			echo '
				<div class="query_ok">
					Отредактировано	<a href="finance_dp.php?id='.$_POST['id'].'">#'.$_POST['id'].'</a>
				</div>';			
		}

	}
	
?>