<?php 

//pricelistitem_del_f.php
//Функция для Удаление(блокирование) 

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		include_once 'functions.php';
		if ($_POST){

			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			$time = time();
				
			$query = "DELETE FROM `spr_itemsingroup` WHERE `item` = '{$_POST['id']}'";
			mysql_query($query) or die(mysql_error().' -> '.$query);
			
			$query = "UPDATE `spr_pricelist_template` SET `status`='9' WHERE `id`='{$_POST['id']}'";
			mysql_query($query) or die(mysql_error().' -> '.$query);

			mysql_close();	
			
			echo '
				<div class="query_ok" style="padding-bottom: 10px;">
					<h3>Позиция удалена (заблокирована).</h3>
				</div>';	
		}

	}
	
?>