<?php 

//fl_add_percent_cat_in_item_invoice_f.php
//

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){

			$temp_arr = array();

			if (!isset($_POST['ind']) || !isset($_POST['key']) || !isset($_POST['percent_cats']) || !isset($_POST['invoice_type']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				echo json_encode(array('result' => 'error', 'data' => 'Ошибка #4'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]);

				if (isset($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'])){

                    include_once 'DBWork.php';
                    include_once 'ffun.php';

                    //Категории процентов
                    //$percents_j = SelDataFromDB('fl_spr_percents', $_POST['percent_cats'], 'id');

                    $percents_j = getPercents($_POST['worker'], $_POST['percent_cats']);

                    $work_percent = (int)$percents_j[$_POST['percent_cats']]['work_percent'];
                    $material_percent = (int)$percents_j[$_POST['percent_cats']]['material_percent'];

                    //var_dump($work_percent);

					if ($_POST['invoice_type'] == 5){
						if (isset($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']])){
							$_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['percent_cats'] = (int)$_POST['percent_cats'];
							$_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['work_percent'] = $work_percent;
							$_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']][$_POST['key']]['material_percent'] = $material_percent;
						}
					}
					if ($_POST['invoice_type'] == 6){
						if (isset($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']])){
							$_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['percent_cats'] = (int)$_POST['percent_cats'];
							$_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['work_percent'] = $work_percent;
							$_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['ind']]['material_percent'] = $material_percent;
						}
					}

                    echo json_encode(array('result' => 'success', 'data' => 'Ok #1'));
				}else{
                    echo json_encode(array('result' => 'error', 'data' => 'Ошибка #5'));
                }

				//echo json_encode(array('result' => 'success', 'data' => $_POST['key']));
			}
		}else{
            echo json_encode(array('result' => 'error', 'data' => 'Ошибка #6'));
        }
        //var_dump($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data']);
	}
?>