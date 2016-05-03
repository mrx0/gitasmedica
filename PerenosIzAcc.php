<?php

//price.php
//

/*
11 	ЦО
12 	Авиаконструторов 10
13 	Просвещения 54
14 	Комендантский 17
15 	Энгельса 139
16 	Гражданский 114
17 	Чернышевского 17
18 	Некрасова 58
19 	Просвещения 72
20 	Литейный 59
*/


	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);

	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

	/** Include PHPExcel_IOFactory */
	require_once dirname(__FILE__) . '/acc/Classes/PHPExcel/IOFactory.php';

	/*if (!file_exists("acc/price/denisovaENG.xls")) {
		exit("На данный момент прайса нет. Попробуйте позже.\n");
	}*/

	$objPHPExcel = PHPExcel_IOFactory::load("acc/price/yakovlevaGR114.xls");

	// Устанавливаем индекс активного листа
	$objPHPExcel->setActiveSheetIndex(0);
	// Получаем активный лист
	$sheet = $objPHPExcel->getActiveSheet();
	
	//$DOCTOR = 'Вайлова Юлия Александровна';
	//$DOCTOR = 'Яблочко Анастасия Андреевна';
	//$DOCTOR = 'Яковлева Татьяна Сергеевна';
	//$DOCTOR = 'Денисова Светлана Игоревна';
	//$DOCTOR = 'Григорьева Юлия Ростиславовна';
	//$DOCTOR = 'Пинигина Светлана Викторовна';
	//$DOCTOR = 'Пешина Таисия Олеговна';
	//$DOCTOR = 'Калабина Елена Валерьевна';
	//$DOCTOR = 'Плотникова Юлия Михайловна';
	$DOCTOR = 'Татаринцева Анна Сергеевна';
	//$DOCTOR = 'Смольянинова Галина Петровна';
	//$DOCTOR = 'Борисенко Марина Николаевна';
	$FILIAL = 16;
	$POSESCHENIE = FALSE;
	$comment = '';
	$day = 1;
	
	include_once 'DBWork.php';
	include_once 'functions.php';
	
	//Отслеживание смены месяца
	//$m_count_sw = TRUE;
	//дата
	$date = '';
	$date4DB = '';
	//начальный год
	$year_start = 2015;
	//месяцы
	$months = array(1 => '01',2 => '02',3 => '03',4 => '04',5 => '05',6 => '06',7 => '07',8 => '08',9 => '09',10 => '10',11 => '11',12 => '12');
	//var_dump ($months);
	//Счетчик месяцев
	$m = 1; //индекс месяца
	$m_count = 21; //периодичность месяца (каждые +N? ячеек )
	//Счетчик процедур
	//$p_count = $m_count;
	//массивы для процедур
	$epil = array();
	$sosud = array();
	$akne = array();
	$qsw = array();
	$hir = array();
	$micro = array();
	$erbiy = array();
	$omoloj = array();
	$uhod = array();
	$azot = array();
	$rf = array();
	$inj = array();
	$perv = array();
	$rezerv = array();
	$r1 = array();
	$r2 = array();
	$compd = array();
	$proch = array();
	
	$client = '';
	$client_id = 0;

	
								$therapists = SelDataFromDB ('spr_workers', $DOCTOR, 'worker_full_name');
								if ($therapists != 0){
									$therapist = $therapists[0]['id'];
									
								}else{
									$therapist = 0;
									
								}
	
	
	
	echo '<table border=1 width=auto>';
	
	$year = $year_start;
	
	for ($i = 6; $i <= $sheet->getHighestRow(); $i++) {  
		echo '<tr>';
		$nColumn = PHPExcel_Cell::columnIndexFromString(
			$sheet->getHighestColumn());
			
		$arr = array(
		1 => '0',
		2 => '0',
		3 => '0',
		4 => '0',
		5 => '0',
		6 => '0',
		7 => '0',
		8 => '0',
		9 => '0',
		10 => '0',
		11 => '0',
		12 => '0',
		13 => '0',
		14 => '0',
		15 => '0',
		16 => '0',
		17 => '0',
		18 => '0',
		19 => '0',
		20 => '0',
		21 => '0',
		200 => '0',
		201 => '0');

		for ($j = 4; $j < $nColumn; $j++) {
			
			//$POSESCHENIE = FALSE;
			
			//не учитываем цветастенькую градацию статистики
			if ($j < 5 || $j>20){
				$value = $sheet->getCellByColumnAndRow($j, $i)->getValue();
				//Составим массивы для процедур
				if ($i == 8){
					$order   = array("\r\n", "\n", "\r", ' ');
					$replace = '';
					$value = str_replace($order, $replace, $value);
					
					
					//$value = str_replace(chr(13),'',$value);
					//$value = str_replace(chr(10),'',$value);
					if ($value == 'эпиляция'){
						array_push($epil, $j);
					}
					if ($value == 'сосуды'){
						array_push($sosud, $j);
					}
					if ($value == 'акне'){
						array_push($akne, $j);
					}
					if ($value == 'Qswitch'){
						array_push($qsw, $j);
					}
					if ($value == 'хирургия'){
						array_push($hir, $j);
					}
					if ($value == 'микротоки'){
						array_push($micro, $j);
					}
					if ($value == 'эрбий'){
						array_push($erbiy, $j);
					}
					if ($value == 'омоложение'){
						array_push($omoloj, $j);
					}
					if ($value == 'уходпроц'){
						array_push($uhod, $j);
					}
					if ($value == 'азот'){
						array_push($azot, $j);
					}
					if ($value == 'RF'){
						array_push($rf, $j);
					}
					if ($value == 'инъекции'){
						array_push($inj, $j);
					}
					if ($value == 'первичконсулт'){
						array_push($perv, $j);
					}
					if ($value == 'резерв'){
						array_push($rezerv, $j);
					}
					if ($value == 'r1'){
						array_push($r1, $j);
					}
					if ($value == 'r2'){
						array_push($r2, $j);
					}
					if ($value == 'компдиагност'){
						array_push($compd, $j);
					}
					if ($value == 'прочее'){
						array_push($proch, $j);
					}
				}

					if ($j == $m_count){
						$date = '01.'.$months[$m].'.'.$year;
						$date4DB = $months[$m].'.'.$year;
						$m_count = $m_count+19;
						$m++;
						$POSESCHENIE = FALSE;
						if ($m > 12){
							$year++;
							$m = 1;
						}
						//$m_count_sw = FALSE;
					}
					//Если Пациент
					if (($j == 4) && ($i > 8)){
						//Если не пустое значение пациента
						if (($value != '') && ($value != '.') && ($value != 'ошибочно')){
							$client = 'Пациент: '.$value;
							//Разложим на массив через пробел
							$client_arr = explode (' ', trim($value));
							//var_dump ($client_arr);
							if (!isset($client_arr[1]) && !isset($client_arr[2]) && !isset($client_arr[3]) && !isset($client_arr[4])){
								$client_arr[1] = '*';
								$client_arr[2] = '*';
								$client_arr[3] = '000';
								
							}
							if (!isset($client_arr[2])){
								$client_arr[2] = '*';
							}
							if (!isset($client_arr[3])){
								$client_arr[3] = '000';
							}
							///!!! Костыль. для Лелы
							if (strstr($client_arr[2], '8') != FALSE){
								$client_arr[3] = $client_arr[2];
								$client_arr[2] = $client_arr[1];
							}
							if (isset($client_arr[4])){
								$client_arr[2] = $client_arr[2].' '.$client_arr[3];
								$client_arr[3] = $client_arr[4];
							}
							
							
							$full_name = CreateFullName(trim($client_arr[0]), trim($client_arr[1]), trim($client_arr[2]));
							//Проверяем есть ли такой пациент
							if (isSameFullName('spr_clients', $full_name)){
								$warning = '';
								$clients = SelDataFromDB ('spr_clients', $full_name, 'client_full_name');
								//var_dump($clients);
								if ($clients != 0){
									$client_id = $clients[0]["id"];
								}else{
									$warning = 'Ошибка! Не нашли существующего клиента.';
								}
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$client<br />Такой пациент уже есть. {$full_name} - {$warning}<br /></div></td>";
							}else{
								$warning = '';
								//Лечащий врач

								//Короткое имя
								$name = CreateName(trim($client_arr[0]), trim($client_arr[1]), trim($client_arr[2]));
								$contacts = $client_arr[3];
								//Добавим пациента в базу
//								WriteClientToDB_Edit ($name, $full_name, $contacts, $therapist, 0, 0);
								
								$clients = SelDataFromDB ('spr_clients', $full_name, 'client_full_name');
								if ($clients != 0){
									$client_id = $clients[0]["id"];
								}else{
									$warning = 'Ошибка! Не нашли существующего клиента.';
								}
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$client = $name {$warning}</div></td>";
							}
							
						}else{
							break;
						}
					}
					if ($i > 9){
						//Если эпиляция
						if (in_array($j, $epil)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[1] = 1;
								$procedure = 'Процедура: эпиляция'.$arr[1];
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[1] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";

								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если сосуды
						elseif (in_array($j, $sosud)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[2] = 1;
								$procedure = 'Процедура: сосуды';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[2] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если акне
						elseif (in_array($j, $akne)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[3] = 1;
								$procedure = 'Процедура: акне';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[3] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";

								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если qswitch
						elseif (in_array($j, $qsw)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[4] = 1;
								$procedure = 'Процедура: qswitch';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[4] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";

								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если хирургия
						elseif (in_array($j, $hir)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[5] = 1;
								$procedure = 'Процедура: хирургия';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[5] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";

								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если микротоки
						elseif (in_array($j, $micro)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[6] = 1;
								$procedure = 'Процедура: микротоки';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[6] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";

								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если эрбий
						elseif (in_array($j, $erbiy)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[7] = 1;
								$procedure = 'Процедура: эрбий';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[7] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";

								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если омоложение
						elseif (in_array($j, $omoloj)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[8] = 1;
								$procedure = 'Процедура: омоложение'.$arr[8];;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[8] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";

								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если уходпроц
						elseif (in_array($j, $uhod)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[9] = 1;
								$procedure = 'Процедура: уходпроц';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[9] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если азот
						elseif (in_array($j, $azot)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[10] = 1;
								$procedure = 'Процедура: азот';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[10] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если RF
						elseif (in_array($j, $rf)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[11] = 1;
								$procedure = 'Процедура: RF';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[11] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если инъекции
						elseif (in_array($j, $inj)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[14] = 1;
								$procedure = 'Процедура: инъекции';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[14] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если первичконсулт
						elseif (in_array($j, $perv)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[13] = 1;
								$procedure = 'Процедура: первичконсулт';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[13] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если резерв
						elseif (in_array($j, $rezerv)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[201] = 1;
								$procedure = 'Процедура: резерв';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[201] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если r1
						elseif (in_array($j, $r1)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$procedure = 'Процедура: r1';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
							}else{
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если r2
						elseif (in_array($j, $r2)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$procedure = 'Процедура: r2';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
							}else{
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если компдиагност
						elseif (in_array($j, $compd)){
							if (($value != '') && (mb_strlen($value) > 0)){
								$arr[12] = 1;
								$procedure = 'Процедура: компдиагност';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								echo "</td>";
								
							}else{
								$arr[12] = 0;
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
						}
						//Если прочее
						elseif (in_array($j, $proch)){
							if (($value != '') && (mb_strlen($value) > 0)){	
								$comment = $value;
								$procedure = 'Процедура: прочее';
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$procedure:$value</div>";
								$POSESCHENIE = TRUE;
								var_dump ($POSESCHENIE);
								
								echo "</td>";
								
							}else{
								echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">*</div>";
								var_dump ($POSESCHENIE);
								echo "</td>";
							}
							
			
							echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">";
							
		if ($POSESCHENIE){
			//var_dump ($arr);
			$rezult = json_encode($arr);
			
			var_dump ($POSESCHENIE);
			echo $FILIAL.'<br />';
			echo $date.'<br />';
			echo $day.'.'.$date4DB.'<br />';
			echo $client_id.'<br />';
			echo $rezult.'<br />';
			echo $comment.'<br />';
			echo $therapist.'<br />-------<br />';
			
//			WriteToDB_EditCosmet ($FILIAL, $client_id, $rezult, strtotime($day.'.'.$date4DB), $therapist, time(), $therapist, $therapist, '');
			$day++;
			if ($day > 28) $day = 1;
		}				
							
							
							echo "</div></td>";
							
						
							
		
		$comment = '';					
							
						}
					}
					/*else{
						echo "<td width='200px'><div style=\"overflow: hidden; width: auto; white-space: nowrap;\">$i:$j=$date///$value</div></td>";
					}*/
			}
			
			/*if ($j == 21){
				$m_count = $m_count+19;
				$m++;
				if ($m >= 12){
					$year++;
					$m = 1;
				}
			}*/
		}


		echo '</tr>';
		$m_count = 21;
		$m = 1;	
		$year = $year_start;
		$client = '';
	}
	
	echo '</table>';
	/*var_dump ($epil);
	var_dump ($sosud);
	var_dump ($akne);
	var_dump ($qsw);
	var_dump ($hir);
	var_dump ($micro);
	var_dump ($erbiy);
	var_dump ($omoloj);
	var_dump ($uhod);
	var_dump ($azot);
	var_dump ($rf);
	var_dump ($inj);
	var_dump ($perv);
	var_dump ($rezerv);
	var_dump ($r1);
	var_dump ($r2);
	var_dump ($compd);
	var_dump ($proch);*/
	


?>