<?php

//insure_price_fill.php
//Заполнить

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['add_new'] == 1) || $god_mode){
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
							<h2>Заполнение прайса <a href="insure.php?id='.$_GET['id'].'" class="ahref" style="color: green; font-size: 90%; font-weight: bold;">'.$insure_j[0]['name'].'</a></h2>
						</header>
						<a href="insure_price.php?id='.$_GET['id'].'" class="b">В прайс компании</a><br>';

				echo '
						<div id="data">';
				echo '
						<div id="errrror"></div>';

				echo '
							<div style="font-size: 85%; color: #FF0202; margin: 15px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i>
								При заполнении, существующий прайс этой компании, <br>
								если он есть, будет стёрт.<br>
								Будьте внимательны.<br>
								Прайс полностью копируется из основного.<br><br>
								<span style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">
									Выберите раздел/группу,<br>
									которую хотите скопировать в прайс компании.
								</span>
							</div>
				
							<form action="insure_price_fill_f.php">';
							
				$space = '';			
									
				$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='0' ORDER BY `name`";
				
				$res = mysql_query($query) or die($query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($rez, $arr);
					}
					$rezult = $rez;
				}else{
					$rezult = 0;
				}
				
				if ($rezult != 0){
					echo '
								<select name="group" id="group" size="6" style="width: 250px;">
									<option value="0" selected>*</option>';			
					foreach ($rezult as $key => $value){

						echo '<option value="'.$value['id'].'" '.$selected.'>'.$space.$value['name'].'</option>';
					}
					echo '	
								</select>';
				}
				echo '				
								<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
								<div id="errror"></div>
								<input type="button" class="b" value="Заполнить" onclick="Ajax_insure_price_fill('.$_GET['id'].')">';
				}


				echo '				
							</form>';	
				echo '
						</div>
					</div>';

			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>