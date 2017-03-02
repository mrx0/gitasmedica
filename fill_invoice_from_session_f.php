<?php 

//fill_invoice_from_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		$request = '
						<div class="cellsBlock">
							<div class="cellCosmAct" style="font-size: 80%; text-align: center;">
								<i><b>Зуб</b></i>
							</div>
							<!--<div class="cellCosmAct" style="font-size: 70%; text-align: center;">
								<i><b>МКБ</b></i>
							</div>-->
							<div class="cellText2" style="font-size: 100%; text-align: center;">
								**
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
								<i><b>Цена, руб.</b></i>
							</div>
							<div class="cellCosmAct" style="font-size: 70%; text-align: center;">
								<i><b>-</b></i>
							</div>
						</div>
		';
		
		if ($_POST){
			if (!isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				$client = $_POST['client'];
				$zapis_id = $_POST['zapis_id'];
				$filial = $_POST['filial'];
				$worker = $_POST['worker'];
				
				if (!isset($_SESSION['invoice_data'][$client][$zapis_id]['data'])){
					echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
				}else{
					//берем из сесии данные
					$data = $_SESSION['invoice_data'][$client][$zapis_id]['data'];
					
					ksort($data);
					
					$t_number_active = $_SESSION['invoice_data'][$client][$zapis_id]['t_number_active'];
					
					foreach ($data as $zub => $invoice_data){
						if ($t_number_active == $zub){
							$bg_col = 'background: rgba(131, 219, 83, 0.5) none repeat scroll 0% 0%;';
						}else{
							$bg_col = '';
						}
						$request .= '
							<div class="cellsBlock">
								<div class="cellCosmAct toothInInvoice" style="'.$bg_col.'">
									'.$zub.'
								</div>';
						if (!empty($invoice_data)){
							foreach ($invoice_data as $key => $items){
								$request .= '
								<div class="cellsBlock" style="font-size: 100%;">
								<!--<div class="cellCosmAct" style=" '.$bg_col.'">
									-
								</div>-->
								<div class="cellText2" style=" '.$bg_col.'">';
								
								//Хочу имя позиции в прайсе
								$arr = array();
								$rez = array();
					
								require 'config.php';
								mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
								mysql_select_db($dbName) or die(mysql_error()); 
								mysql_query("SET NAMES 'utf8'");

								$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` = '{$items}'";			
					
								$res = mysql_query($query) or die(mysql_error().' -> '.$query);
								$number = mysql_num_rows($res);
								if ($number != 0){
									while ($arr = mysql_fetch_assoc($res)){
										array_push($rez, $arr);
									}
									$rezult2 = $rez;
								}else{
									$rezult2 = 0;
								}
								
								if ($rezult2 != 0){
									$request .= $rezult2[0]['name'];
									
									//Узнать цену
									$arr = array();
									$rez = array();
									$price = 0;
									
									$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='{$items}' ORDER BY `create_time` DESC LIMIT 1";
									
									$res = mysql_query($query) or die(mysql_error().' -> '.$query);
									$number = mysql_num_rows($res);
									if ($number != 0){
										$arr = mysql_fetch_assoc($res);
										$price = $arr['price'];
									}else{
										$price = 0;
									}
									
								}else{
									$request .= '?';
								}
								
								$request .= '
								</div>
								<div class="cellCosmAct invoiceItemPrice" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px; '.$bg_col.'">
									'.$price.'
								</div>
								<div invoiceitemid="'.$key.'" class="cellCosmAct info" style="font-size: 100%; text-align: center; '.$bg_col.'" onclick="deleteInvoiceItem('.$zub.', this);">
									<i class="fa fa-trash-o" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
								</div>
							</div>';
							}
						}else{
							$request .= '
								<!--<div class="cellCosmAct" style="text-align: center; '.$bg_col.'">
									-
								</div>-->
								<div class="cellText2" style="text-align: center; '.$bg_col.'">
									не заполнено
								</div>
								<!--<div class="cellCosmAct" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px; '.$bg_col.'">
									0
								</div>-->
								<div class="cellCosmAct info" style="font-size: 100%; text-align: center; '.$bg_col.'" onclick="deleteInvoiceItem('.$zub.', this);">
									<i class="fa fa-trash-o" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
								</div>';
						}
							$request .= '
							</div>';
					}
					
					echo json_encode(array('result' => 'success', 'data' => $request));
				}
				
				
				/*include_once 'DBWork.php';
				include_once 'functions.php';
				
				require 'config.php';
				//Вставка

				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				$query = "INSERT INTO `journal_etaps` (
					`name`, `client_id`)
					VALUES (
					'{$_POST['name']}', '{$_POST['client']}') ";
				//mysql_query($query) or die(mysql_error());
				
				$mysql_insert_id = mysql_insert_id();
				
				mysql_close();
				
				//логирование
				AddLog (GetRealIp(), $_POST['session_id'], '', 'Добавлено исследование. ['.date('d.m.y H:i', $time).']. Пациент ['.$_POST['client'].'] ID ['.$mysql_insert_id.']');

				echo '
							<h1>Добавлено исследование</h1>
							<a href="etap.php?id='.$mysql_insert_id.'" class="b">Перейти</a>
							';*/
			}
		}
	}
?>