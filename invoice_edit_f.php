<?php 

//invoice_edit_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			
			if (!isset($_POST['invoice_id']) || !isset($_POST['invoice_type']) || !isset($_POST['summ']) || !isset($_POST['summins']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				include_once 'DBWork.php';
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
				
				$invoice_j = SelDataFromDB('journal_invoice', $_POST['invoice_id'], 'id');

				if ($invoice_j != 0){
					if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
						if (!empty($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
							$data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'];

                            $msql_cnnct = ConnectToDB ();

							$time = date('Y-m-d H:i:s', time());

                            $discount = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['discount'];

							//Обновляем в базу
							/*$query = "INSERT INTO `journal_invoice` (`zapis_id`, `office_id`, `client_id`, `worker_id`, `type`, `summ`, `summins`, `create_person`, `create_time`)
							VALUES (
							'{$_POST['zapis_id']}', '{$_POST['filial']}', '{$_POST['client']}', '{$_POST['worker']}', '{$_POST['invoice_type']}', '{$_POST['summ']}', '{$_POST['summins']}', '{$_SESSION['id']}', '{$time}')";
							*/
							$query = "UPDATE `journal_invoice` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `summ`='{$_POST['summ']}', `discount`='{$discount}', `summins`='{$_POST['summins']}' WHERE `id`='{$_POST['invoice_id']}'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

							//ID старой позиции
							$mysql_insert_id = $_POST['invoice_id'];

							//Удаляем старое
							$query = "DELETE FROM `journal_invoice_ex` WHERE `invoice_id` = '{$mysql_insert_id}'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

							$query = "DELETE FROM `journal_invoice_ex_mkb` WHERE `invoice_id` = '{$mysql_insert_id}'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            foreach ($data as $ind => $invoice_data){

                                if (!empty($invoice_data)){
                                    if ($_POST['invoice_type'] == 5){
                                        foreach ($invoice_data as $key => $items){

                                            $price_id = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['id'];
                                            $quantity = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['quantity'];
                                            $insure = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['insure'];
                                            $insure_approve = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['insure_approve'];
                                            $price = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['price'];
                                            $guarantee = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['guarantee'];
                                            $gift = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['gift'];
                                            $spec_koeff = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['spec_koeff'];
                                            $discount = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['discount'];
                                            $percent_cat = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['percent_cats'];
                                            $manual_price = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['manual_price'];
                                            $itog_price = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['itog_price'];

                                            //Добавляем в базу
                                            $query = "INSERT INTO `journal_invoice_ex` (`invoice_id`, `ind`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `gift`, `spec_koeff`, `discount`, `percent_cats`, `manual_price`, `itog_price`) 
										VALUES (
										'{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$price}', '{$guarantee}', '{$gift}', '{$spec_koeff}', '{$discount}', '{$percent_cat}', '{$manual_price}', '{$itog_price}')";

                                            mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                        }

                                        if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind])){
                                            $mkb_data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind];
                                            foreach ($mkb_data as $mkb_id){
                                                //Добавляем в базу МКБ
                                                $query = "INSERT INTO `journal_invoice_ex_mkb` (`invoice_id`, `ind`, `mkb_id`) 
											VALUES (
											'{$mysql_insert_id}', '{$ind}', '{$mkb_id}')";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                            }
                                        }

                                    }

                                    if ($_POST['invoice_type'] == 6){

                                        $price_id = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['id'];
                                        $quantity = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['quantity'];
                                        $insure = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['insure'];
                                        $insure_approve = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['insure_approve'];
                                        $price = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['price'];
                                        $guarantee = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['guarantee'];
                                        $gift = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['gift'];
                                        $spec_koeff = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['spec_koeff'];
                                        $discount = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['discount'];
                                        $percent_cat = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['percent_cats'];
                                        $manual_price = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['manual_price'];
                                        $itog_price = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind]['itog_price'];

                                        //Добавляем в базу
                                        $query = "INSERT INTO `journal_invoice_ex` (`invoice_id`, `ind`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `gift`, `spec_koeff`, `discount`, `percent_cats`, `manual_price`, `itog_price`) 
									VALUES (
									'{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$price}', '{$guarantee}', '{$gift}', '{$spec_koeff}', '{$discount}', '{$percent_cat}', '{$manual_price}', '{$itog_price}')";

                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                    }
                                    //unset($_SESSION['invoice_data']);
                                }
                            }
                            unset($_SESSION['invoice_data']);

                            //!!! @@@ Пересчет долга
                            include_once 'ffun.php';
                            calculateDebt ($_POST['client']);

							echo json_encode(array('result' => 'success', 'data' => $mysql_insert_id));
						}
					}
				}else{
					echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
				}
			}
		}
	}
?>