<?php 

//invoice_add_f.php
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
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);

                include_once('DBWorkPDO.php');
                //include_once 'DBWork.php';

                $dbase = 'journal_invoice';
                $dbase_ex = 'journal_invoice_ex';
                $dbase_ex_mkb = 'journal_invoice_ex_mkb';

                if ($_POST['adv'] == 'true'){
                    $dbase = 'journal_advanaced_invoice';
                    $dbase_ex = 'journal_advanaced_invoice_ex';
                    $dbase_ex_mkb = 'journal_advanaced_invoice_ex_mkb';
                }

				if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
					if (!empty($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
						$data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'];

                        //$msql_cnnct = ConnectToDB ();
                        $db = new DB();

						$time = date('Y-m-d H:i:s', time());

                        //if ($_POST['invoice_type'] == 5) {
                            //$discount = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['discount'];
                       // }
                        //if ($_POST['invoice_type'] == 6){
                            $discount = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['discount'];
                        //}

                        $zapis_id =  $_POST['zapis_id'];

                        if (isset($_POST['zapis_id_new'])){
                            if ($_POST['zapis_id_new'] > 0) {
                                $zapis_id = $_POST['zapis_id_new'];
                            }
                        }

						//Добавляем в базу
						$query = "INSERT INTO `$dbase` (
                        `zapis_id`, 
                        `office_id`, `client_id`, `worker_id`, `type`, `summ`, `discount`, `summins`, `comment`, `create_person`, `create_time`) 
						VALUES (
						:zapis_id, 
						'{$_POST['filial']}', '{$_POST['client']}', '{$_POST['worker']}', '{$_POST['invoice_type']}', '{$_POST['summ']}', '{$discount}', '{$_POST['summins']}', '{$_POST['comment']}', '{$_SESSION['id']}', 
						:time
						)";

                        $args = [
                            'zapis_id' => $zapis_id,
                            'time' => $time
                        ];

                        //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                        $db::sql($query, $args);

						//ID новой позиции
                        //$mysql_insert_id = mysqli_insert_id($msql_cnnct);

                        // Получаем id вставленной записи
                        $insert_id = $db->lastInsertId();

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
										$jaw_select = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$ind][$key]['jaw_select'];

										//Добавляем в базу
										$query = "INSERT INTO `$dbase_ex` (
                                        `invoice_id`, `ind`, `jaw_select`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `gift`, `spec_koeff`, `discount`, `percent_cats`, `manual_price`, `itog_price`) 
										VALUES (
										:invoice_id,
										'{$ind}', '{$jaw_select}', '{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$price}', '{$guarantee}', '{$gift}', '{$spec_koeff}', '{$discount}', '{$percent_cat}', '{$manual_price}', 
										:itog_price
										)";

                                        $args = [
                                            'invoice_id' => $insert_id,
                                            'itog_price' => $itog_price
                                        ];

                                        //mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                        $db::sql($query, $args);
									}

									if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind])){

									    $mkb_data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind];

									    foreach ($mkb_data as $mkb_id){
											//Добавляем в базу МКБ
											$query = "INSERT INTO `$dbase_ex_mkb` (
                                            `invoice_id`, 
                                            `ind`, 
                                            `mkb_id`
                                            ) 
											VALUES (
											:invoice_id, 
											:ind, 
											:mkb_id
											)";

                                            $args = [
                                                'invoice_id' => $insert_id,
                                                'ind' => $ind,
                                                'mkb_id' => $mkb_id
                                            ];

                                            //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                            $db::sql($query, $args);
										}
									}

								}

								if (($_POST['invoice_type'] == 6) || ($_POST['invoice_type'] == 10) || ($_POST['invoice_type'] == 7)){

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
									$query = "INSERT INTO `$dbase_ex` (
                                    `invoice_id`, 
                                    `ind`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `gift`, `spec_koeff`, `discount`, `percent_cats`, `manual_price`, `itog_price`) 
									VALUES (
									:invoice_id, 
									:ind, 
									'{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$price}', '{$guarantee}', '{$gift}', '{$spec_koeff}', '{$discount}', '{$percent_cat}', '{$manual_price}', 
									:itog_price
									)";

                                    $args = [
                                        'invoice_id' => $insert_id,
                                        'ind' => $ind,
                                        'itog_price' => $itog_price
                                    ];

                                    //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                    $db::sql($query, $args);

								}
								//unset($_SESSION['invoice_data']);
							}
						}
						unset($_SESSION['invoice_data']);

                        //!!! @@@ Пересчет долга
                        include_once 'ffun.php';
                        calculateDebt ($_POST['client']);

                        //Если использован именной сертификат
                        if ($_POST['cert_name_id'] > 0){
                            //
                            $query = "UPDATE `journal_cert_name` SET `invoice_id`= :invoice_id, `closed_time`= :closed_time, `status` = :status WHERE `id`= :cert_name_id";

                            $args = [
                                'invoice_id' => $insert_id,
                                'closed_time' => $time,
                                'status' => 5,
                                'cert_name_id' => $_POST['cert_name_id']
                            ];

                            //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                            $db::sql($query, $args);
                        }

						echo json_encode(array('result' => 'success', 'data' => $insert_id, 'data2' => $itog_price));
					}
				}
			}
		}
	}
?>