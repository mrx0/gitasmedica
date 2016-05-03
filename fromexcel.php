<?php

//fromexcel.php
//

	require_once 'header.php';
	include_once 'DBWork.php';
	include_once 'functions.php';
	
	//$arr_offices = SelDataFromDB('spr_office', '', '');
	//var_dump ($offices);
	$arr_orgs = SelDataFromDB('spr_org', '', '');
	//var_dump ($arr_orgs);
	$arr_permissions = SelDataFromDB('spr_permissions', '', '');
	//var_dump ($arr_permissions);
	
	$arr_fio = array();
	$sovp_fio = 0;
	$sovp_rez = array();
	$add = FALSE;
	
	echo '
		<section>
			<section>';
			

	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);

	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

	/** Include PHPExcel_IOFactory */
	require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';

	if (!file_exists("price/price.xls")) {
		exit("На данный момент данных нет. Попробуйте позже.\n");
	}

	$objPHPExcel = PHPExcel_IOFactory::load("xls/file.xls");

	// Устанавливаем индекс активного листа
	$objPHPExcel->setActiveSheetIndex(0);
	// Получаем активный лист
	$sheet = $objPHPExcel->getActiveSheet();
	
	echo '
			<table>';

	for ($i = 1; $i <= $sheet->getHighestRow(); $i++) {  
	
		
		echo '<tr>';
		echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>".$i."</td>";
		$nColumn = PHPExcel_Cell::columnIndexFromString(
			$sheet->getHighestColumn());
		
		for ($j = 0; $j < $nColumn; $j++) {
			$value = $sheet->getCellByColumnAndRow($j, $i)->getValue();
			echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>$value</td>";
			//Если ячейка с именем
			if ($j == 0){
				$full_name = $value;
				if (isSameFullName('spr_workers', $full_name)){
					$add = FALSE;
					$request1 = '<td style="border: 1px solid #CF0737; padding: 3px; color:#AD0015;">существует, пропустили</td>';
				}else{
					$add = TRUE;
					$login = '';
					$name_arr = array();
					$name_arr = explode(' ', $full_name);
					$login = CreateLogin(trim($name_arr[0]), trim($name_arr[1]), trim($name_arr[2]));
					//Если такой логин уже есть, добавляем символ 2 в конце или 3 или 4 ..
					$login = isSameLogin ($login);
					$name = CreateName(trim($name_arr[0]), trim($name_arr[1]), trim($name_arr[2]));
					$request1 = '<td style="border: 1px solid #37DF3F; padding: 3px; color:#03AB0B;">OK</td>';
				}
			}
			//Если ячейка с должностью
			if ($j == 1){
				$permissions = SearchInArray($arr_permissions, $value, 'id');
				if ($permissions != 0){
					$request2 = '<td style="border: 1px solid #37DF3F; padding: 3px; color:#03AB0B;">OK</td>';
				}else{
					$request2 = '<td style="border: 1px solid #CF0737; padding: 3px; color:#AD0015;">нет такой должности в спр.</td>';
				}
			}
			//Если ячейка с конторой
			if ($j == 2){
				$org = SearchInArray($arr_orgs, $value, 'id');
				if ($org != 0){
					$request3 = '<td style="border: 1px solid #37DF3F; padding: 3px; color:#03AB0B;">OK</td>';
				}else{
					$request3 = '<td style="border: 1px solid #CF0737; padding: 3px; color:#AD0015;">нет такой конторы в спр.</td>';
				}
			}
		}
		
		echo $request1.$request2.$request3;
		
		echo '</tr>';
		if ($add){
			$password = PassGen();
			WriteWorkerToDB_Edit ($login, $name, $full_name, $password, '', $permissions, $org);
		}
	}

	echo '
			</table>
		</section>';

	echo '
		<aside>
			<ul>';


	echo '
			</ul>
		</aside>';


	echo '
		</section>';
		
	//var_dump($arr_permissions);	
	//var_dump($arr_permissions);	
	//echo 'Совпадений fio: '.$sovp_fio;
	//var_dump($sovp_rez);
	
	require_once 'footer.php';

?>