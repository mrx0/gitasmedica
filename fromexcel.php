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

	if (!file_exists("excel/new_prise_som_2022.03.08.xlsx")) {
		exit("На данный момент данных нет. Попробуйте позже.\n");
	}

	$objPHPExcel = PHPExcel_IOFactory::load("excel/new_prise_som_2022.03.08.xlsx");

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
                echo "<td colspan='3' style='border: 1px solid #BFBCB5; padding: 3px;'><b>$group_id</b></td>";

                continue;
            }
//            var_dump($group_id);

            $code_nom = $sheet->getCellByColumnAndRow(1, $i)->getValue();
            if ($code_nom == null) $code_nom = '';
            $name = $sheet->getCellByColumnAndRow(2, $i)->getValue();
            $price = $sheet->getCellByColumnAndRow(3, $i)->getValue();
            $price2 = $sheet->getCellByColumnAndRow(4, $i)->getValue();
            $price3 = $sheet->getCellByColumnAndRow(5, $i)->getValue();

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

            $db::sql($query, $args);

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

            $db::sql($query, $args);

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

            $db::sql($query, $args);



            echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>$code_u</td>";
            echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>$code_nom</td>";
            echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'><b>$group_id</b> / $name</td>";
            echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>".(int)$price."</td>";
            echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>".(int)$price2."</td>";
            echo "<td style='border: 1px solid #BFBCB5; padding: 3px;'>".(int)$price3."</td>";

		echo '</tr>';

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