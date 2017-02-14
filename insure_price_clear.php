<?php

//insure_price_clear.php
//Очистить

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['close'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$insure_j = SelDataFromDB('spr_insure', $_GET['id'], 'id');
				//var_dump($insure_j);
				
				if ($insure_j !=0){
					
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					
					$arr = array();
					$rez = array();
					
					echo '
						<div id="status">
							<header>
								<h2>Очистить прайс <a href="insure.php?id='.$_GET['id'].'" class="ahref" style="color: green; font-size: 90%; font-weight: bold;">'.$insure_j[0]['name'].'</a></h2>
							</header>
							<a href="insure_price.php?id='.$_GET['id'].'" class="b">В прайс компании</a><br>';

					echo '
							<div id="data">';
					echo '
							<div id="errrror"></div>';

					echo '
								<div style="font-size: 85%; color: #FF0202; margin: 15px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i>
									Прайс для этой страховой<br>
									будет полностью стёрт.
									</span>
								</div>';
					echo '				
								<div id="errror"></div>
								<input type="button" class="b" value="Очистить" onclick="Ajax_insure_price_clear('.$_GET['id'].')">';

					echo '
						</div>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	} else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>