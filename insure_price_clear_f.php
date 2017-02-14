<?php 

//insure_price_clear_f.php
//Функция для очистки прайса

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		
		if ($_POST){
			if (isset($_POST['id'])){
					
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				
				$query = "DELETE FROM `spr_pricelists_insure` WHERE `insure`='{$_POST['id']}'";
				mysql_query($query) or die(mysql_error().' -> '.$query);
				
				$query = "DELETE FROM `spr_priceprices_insure` WHERE `insure`='{$_POST['id']}'";
				//mysql_query($query) or die(mysql_error().' -> '.$query);
				

				echo '
						<div class="query_ok">
							Прайс очищен<br><br>
						</div>';
			}
				
		}else{
			echo '
				<div class="query_neok">
					Что-то пошло не так.<br><br>
				</div>';
		}
	}

?>