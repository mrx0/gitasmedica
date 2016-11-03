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
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			
			//$date_expires = strtotime($_POST['date_expires'].' 00:00:00');
			
			//Для лога соберем сначала то, что было в записи.
			$query = "SELECT * FROM `journal_debts_prepayments` WHERE `id`='{$_POST['id']}'";
			$res = mysql_query($query) or die(mysql_error());
			$number = mysql_num_rows($res);
			if ($number != 0){
				$arr = mysql_fetch_assoc($res);
				$old = 'Пациент ['.$arr['client'].']. Сумма ['.$arr['summ'].']. Тип ['.$arr['type'].']. Статус ['.$arr['status'].']. Комментарий ['.$arr['comment'].'].';
			}else{
				$old = 'Не нашли старую запись.';
			}
			$time = time();
			$query = "UPDATE `journal_debts_prepayments` SET 
			`summ`='{$_POST['summ']}', `comment`='{$_POST['comment']}', `last_edit_person`='{$_SESSION['id']}', `last_edit_time`='{$time}' WHERE `id`='{$_POST['id']}'";
			mysql_query($query) or die(mysql_error());
			mysql_close();
			
			//логирование
			AddLog ('0', $_SESSION['id'], $old, 'Погашение ОТРЕДАКТИРОВАНО ['.$_POST['id'].']. ['.date('d.m.y H:i', $time).']. Сумма ['.$_POST['summ'].']. Комментарий ['.$_POST['comment'].'].');
			
			echo '
				<div class="query_ok">
					Отредактировано	<a href="finance_dp_repayment.php?id='.$_POST['id'].'">#'.$_POST['id'].'</a>
				</div>';			
		}

	}
	
?>