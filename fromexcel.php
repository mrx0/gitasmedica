<?php

//fromexcel.php
//

    require_once 'header.php';

    include_once('DBWorkPDO.php');
    $db = new DB();
	
	echo '
		<section>
			<section>';
			

	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);

	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

	/** Include PHPExcel_IOFactory */
	require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel/IOFactory.php';

	if (!file_exists("excel/new_prise_s.xlsx")) {
		exit("На данный момент данных нет. Попробуйте позже.\n");
	}

	$objPHPExcel = PHPExcel_IOFactory::load("excel/new_prise_s.xlsx");

	// Устанавливаем индекс активного листа
	$objPHPExcel->setActiveSheetIndex(0);
	// Получаем активный лист
	$sheet = $objPHPExcel->getActiveSheet();
	
	echo '
			<table>';

	for ($i = 1; $i <= $sheet->getHighestRow(); $i++) {  
	
		
		echo '<tr>';
		//echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>строка $i</td>";

		$nColumn = PHPExcel_Cell::columnIndexFromString(
			$sheet->getHighestColumn());
		
//		for ($j = 0; $j < $nColumn; $j++) {

//		    $value = $sheet->getCellByColumnAndRow($j, $i)->getValue();

            $code_u = $sheet->getCellByColumnAndRow(0, $i)->getValue();

            //Если группа/подгруппа
            if ($code_u === 'gr'){

//                $value = '<b>'.$value.'</b>';

                $group_id = $sheet->getCellByColumnAndRow(1, $i)->getValue();
                //$group_id = $code_u;
//                var_dump($group_id);

                continue;
            }
//            var_dump($group_id);

            $code_nom = $sheet->getCellByColumnAndRow(1, $i)->getValue();
            if ($code_nom == null) $code_nom = '';
            $name = $sheet->getCellByColumnAndRow(2, $i)->getValue();
            $price = $sheet->getCellByColumnAndRow(3, $i)->getValue();

            //Вставляем позицию прайса
            $args = [
                'name' => $name,
                'code_u' => $code_u,
                'code_nom' => $code_nom,
                'new' => 1
            ];

            $query = "INSERT INTO `spr_pricelist_template`
                            (`name`, `code_u`, `code_nom`, `new`)
                            VALUES (:name, :code_u, :code_nom, :new);";

            //$db::sql($query, $args);

            //Получаем id вставленной записи
            $insert_id = $db->lastInsertId();

            //Вставляем в какой она группе
            $args = [
                'item' => $insert_id,
                'group' => $group_id
            ];

            $query = "INSERT INTO `spr_itemsingroup`
                            (`item`, `group`)
                            VALUES (:item, :group);";

            //$db::sql($query, $args);

             //Вставляем цену
            $args = [
                'item' => $insert_id,
                'date_from' => 1622538000,
                'price' => (int)$price,
                'price2' => (int)$price,
                'price3' => (int)$price
            ];

            $query = "INSERT INTO `spr_priceprices`
                                (`item`, `date_from`, `price`, `price2`, `price3`)
                                VALUES (:item, :date_from, :price, :price2, :price3);";

            //$db::sql($query, $args);



            echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>$code_u</td>";
            echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>$code_nom</td>";
            echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>$name</td>";
            echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>".(int)$price."</td>";




//			echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>$j $i - $value</td>";
			//Если ячейка с именем
//			if ($j == 0){
//				$full_name = $value;
//				if (isSameFullName('spr_workers', $full_name)){
//					$add = FALSE;
//					$request1 = '<td style="border: 1px solid #CF0737; padding: 3px; color:#AD0015;">существует, пропустили</td>';
//				}else{
//					$add = TRUE;
//					$login = '';
//					$name_arr = array();
//					$name_arr = explode(' ', $full_name);
//					$login = CreateLogin(trim($name_arr[0]), trim($name_arr[1]), trim($name_arr[2]));
//					//Если такой логин уже есть, добавляем символ 2 в конце или 3 или 4 ..
//					$login = isSameLogin ($login);
//					$name = CreateName(trim($name_arr[0]), trim($name_arr[1]), trim($name_arr[2]));
//					$request1 = '<td style="border: 1px solid #37DF3F; padding: 3px; color:#03AB0B;">OK</td>';
//				}
//			}
//			//Если ячейка с должностью
//			if ($j == 1){
//				$permissions = SearchInArray($arr_permissions, $value, 'id');
//				if ($permissions != 0){
//					$request2 = '<td style="border: 1px solid #37DF3F; padding: 3px; color:#03AB0B;">OK</td>';
//				}else{
//					$request2 = '<td style="border: 1px solid #CF0737; padding: 3px; color:#AD0015;">нет такой должности в спр.</td>';
//				}
//			}
//			//Если ячейка с конторой
//			if ($j == 2){
//				$org = SearchInArray($arr_orgs, $value, 'id');
//				if ($org != 0){
//					$request3 = '<td style="border: 1px solid #37DF3F; padding: 3px; color:#03AB0B;">OK</td>';
//				}else{
//					$request3 = '<td style="border: 1px solid #CF0737; padding: 3px; color:#AD0015;">нет такой конторы в спр.</td>';
//				}
//			}
//		}
		
		//echo $request1.$request2.$request3;
		
		echo '</tr>';
//		if ($add){
//			$password = PassGen();
//			WriteWorkerToDB_Edit ($login, $name, $full_name, $password, '', $permissions, $org);
//		}
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