<?php 

//edit_zapis_change_client_f.php
//Функция для смены пациента в записи и всё, что ему создали

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			if (isset($_POST['zapis_id']) && isset($_POST['client_id']) && isset($_POST['new_client']) && ($_POST['new_client'] != '')){
				//Ищем Пациента
				$clients_j = SelDataFromDB ('spr_clients', $_POST['new_client'], 'client_full_name');
				//var_dump($clients);
				if ($clients_j != 0){
					if ($clients_j[0]['id'] != $_POST['client_id']){

                        require 'config.php';
                        mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
                        mysql_select_db($dbName) or die(mysql_error());
                        mysql_query("SET NAMES 'utf8'");
                        $time = time();

                        //Запись
                        $query = "UPDATE `zapis` SET 
						`patient`='{$clients_j[0]['id']}' 
						WHERE 
						`id`='{$_POST['zapis_id']}'";

                        mysql_query($query) or die(mysql_error().' -> '.$query);

						//Косметология
						$query = "UPDATE `journal_cosmet1` SET 
						`client`='{$clients_j[0]['id']}'
						WHERE 
						`zapis_id`='{$_POST['zapis_id']}'";

						mysql_query($query) or die(mysql_error().' -> '.$query);
						
						//Этапы
						/*$query = "UPDATE `journal_etaps` SET
						`client_id`='{$clients_j[0]['id']}'
						WHERE 
						`client_id`='{$_POST['id']}'";

						mysql_query($query) or die(mysql_error().' -> '.$query);*/

						//Наряды
						$query = "UPDATE `journal_invoice` SET
						`client_id`='{$clients_j[0]['id']}' 
						WHERE 
						`zapis_id`='{$_POST['zapis_id']}'";

						mysql_query($query) or die(mysql_error().' -> '.$query);

						//Стоматология
						$query = "UPDATE `journal_tooth_status` SET 
						`client`='{$clients_j[0]['id']}' 
						WHERE 
						`zapis_id`='{$_POST['zapis_id']}'";

						mysql_query($query) or die(mysql_error().' -> '.$query);
						
						//Ротовые снимки
						/*$query = "UPDATE `journal_zub_img` SET
						`client`='{$clients_j[0]['id']}'
						WHERE 
						`client`='{$_POST['id']}'";

						mysql_query($query) or die(mysql_error().' -> '.$query);*/
						
						//Заметки
						/*$query = "UPDATE `notes` SET
						`client`='{$clients_j[0]['id']}'
						WHERE 
						`client`='{$_POST['id']}'";

						mysql_query($query) or die(mysql_error().' -> '.$query);*/
						
						//Направления !!! не исправляется пока. Так что пациента в посещении будет наверняка другой
						/*$query = "UPDATE `removes` SET
						`client`='{$clients_j[0]['id']}'
						WHERE 
						`client`='{$_POST['id']}'";

						mysql_query($query) or die(mysql_error().' -> '.$query);*/
						
						//Снимки КД
						/*$query = "UPDATE `spr_kd_img` SET
						`client`='{$clients_j[0]['id']}'
						WHERE 
						`client`='{$_POST['id']}'";

						mysql_query($query) or die(mysql_error().' -> '.$query);*/


						
						//Авансы долги
						/*$query = "UPDATE `journal_debts_prepayments` SET
						`client`='{$clients_j[0]['id']}'
						WHERE 
						`client`='{$_POST['id']}'";

						mysql_query($query) or die(mysql_error().' -> '.$query);*/
						
						

						mysql_close();	
					}else{
						echo '
							<div class="query_neok">
								Нельзя переносить самому себе<br><br>
							</div>';
					}
				}else{
					echo '
						<div class="query_neok">
							В нашей базе нет такого пациента<br><br>
						</div>';
				}
			}else{
				echo '
					<div class="query_neok">
						Не указали пациента<br><br>
					</div>';
			}
		}
	}
	
?>