<?php 

//fl_calculate_reset_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['invoice_type']) || !isset($_POST['summ']) || !isset($_POST['summins']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
					
				if (isset($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
					if (!empty($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
						/*$data = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'];

                        //!!! @@@
                        include_once 'ffun.php';

                        $msql_cnnct = ConnectToDB2 ();

						$time = date('Y-m-d H:i:s', time());

                        //if ($_POST['invoice_type'] == 5) {
                            //$discount = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['discount'];
                       // }
                        //if ($_POST['invoice_type'] == 6){
                            //$discount = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['discount'];
                        //}
						//Добавляем в базу
						$query = "INSERT INTO `fl_journal_calculate` (`zapis_id`, `office_id`, `client_id`, `worker_id`, `type`, `summ`, `discount`, `summins`, `create_person`, `create_time`) 
						VALUES (
						'{$_POST['zapis_id']}', '{$_POST['filial']}', '{$_POST['client']}', '{$_POST['worker']}', '{$_POST['invoice_type']}', '{$_POST['summ']}', '{$discount}', '{$_POST['summins']}', '{$_SESSION['id']}', '{$time}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
						
						//ID новой позиции
						$mysql_insert_id = mysqli_insert_id($msql_cnnct);

						foreach ($data as $ind => $calculate_data){

							if (!empty($invoice_data)){
								if ($_POST['calculate_data'] == 5){
									foreach ($invoice_data as $key => $items){
										
										$price_id = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['id'];
										$quantity = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['quantity'];
										$insure = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['insure'];
										$insure_approve = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['insure_approve'];
										$price = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['price'];
										$guarantee = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['guarantee'];
										$spec_koeff = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['spec_koeff'];
										$discount = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['discount'];

                                        $percent_cats = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['percent_cats'];
                                        $work_percent = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['work_percent'];
                                        $material_percent = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['material_percent'];
										
										//Добавляем в базу
										$query = "INSERT INTO `fl_journal_calculate_ex` (`invoice_id`, `ind`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `spec_koeff`, `discount`, `percent_cats`, `work_percent`, `material_percent`) 
										VALUES (
										'{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$price}', '{$guarantee}', '{$spec_koeff}', '{$discount}', '{$percent_cats}', '{$work_percent}', '{$material_percent}')";

                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
									}

									/*if (isset($_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind])){
										$mkb_data = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind];
										foreach ($mkb_data as $mkb_id){
											//Добавляем в базу МКБ
											$query = "INSERT INTO `journal_invoice_ex_mkb` (`invoice_id`, `ind`, `mkb_id`)
											VALUES (
											'{$mysql_insert_id}', '{$ind}', '{$mkb_id}')";
										
											mysql_query($query) or die(mysql_error().' -> '.$query);
										}
									}*/
									
						/*		}
								
								if ($_POST['invoice_type'] == 6){
									
									$price_id = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['id'];
									$quantity = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['quantity'];
									$insure = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['insure'];
									$insure_approve = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['insure_approve'];
									$price = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['price'];
									$guarantee = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['guarantee'];
									$spec_koeff = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['spec_koeff'];
									$discount = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['discount'];

                                    $percent_cats = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['percent_cats'];
                                    $work_percent = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['work_percent'];
                                    $material_percent = $_SESSION['calculate_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['material_percent'];

									//Добавляем в базу
									$query = "INSERT INTO `fl_journal_calculate_ex` (`invoice_id`, `ind`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `spec_koeff`, `discount`) 
									VALUES (
									'{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$price}', '{$guarantee}', '{$spec_koeff}', '{$discount}')";

                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
									
								}
								//unset($_SESSION['calculate_data']);
							}
						}*/
						unset($_SESSION['calculate_data']);

                        //!!! @@@ Пересчет долга
                        //include_once 'ffun.php';
                        //calculateDebt ($_POST['client']);

						echo json_encode(array('result' => 'success', 'data' => ''));
					}
				}
			}
		}
	}
?>