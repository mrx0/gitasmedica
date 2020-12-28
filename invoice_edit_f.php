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
                include_once('DBWorkPDO.php');
				include_once 'DBWork.php';
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
                include_once 'functions.php';

                $dbase = 'journal_invoice';
                $dbase_ex = 'journal_invoice_ex';
                $dbase_ex_mkb = 'journal_invoice_ex_mkb';

                if ($_POST['adv'] == 'true'){
                    $dbase = 'journal_advanaced_invoice';
                    $dbase_ex = 'journal_advanaced_invoice_ex';
                    $dbase_ex_mkb = 'journal_advanaced_invoice_ex_mkb';
                }

                $db = new DB();

				$invoice_j = SelDataFromDB($dbase, $_POST['invoice_id'], 'id');

				if ($invoice_j != 0){
					if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
						if (!empty($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){

							$data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'];

                            $msql_cnnct = ConnectToDB ();

							$time = date('Y-m-d H:i:s', time());

                            $discount = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['discount'];

                            //Если использован именной сертификат
                            //Далее проверки, другой это или нет,
                            //Если удаляем старый, то проверить счет того, кому накидывали на счёт

                            //Будем добавлять сертификат
//                            if ($_POST['cert_name_id'] > 0){
//                                //Оставили тот сертификат, который был
//                                if ($_POST['cert_name_id'] == $_POST['cert_name_old_id']){
//                                    //--Ничего не меняем
//
//                                //Был сертификат, теперь другой ИЛИ не было, но добавили новый
//                                }else{
//                                    //Был другой
//                                    if ($_POST['cert_name_old_id'] > 0){
//                                        //--Проверяем, можно ли удалить старый
//                                        //--Если можно, удаляем старый (точнее убираем у него привязку от наряда), удаляем деньги со счета хозяина
//                                        //--Добавляем новый
//
//                                    //Не было
//                                    }else{
//                                        //--Добавляем новый
//                                    }
//                                }
//                            //Не будем добавлять сертификат
//                            }else{
//                                //Не было сертификата
//                                if ($_POST['cert_name_id'] == $_POST['cert_name_old_id']){
//                                    //--Ничего не меняем
//
//                                //Был сертификат
//                                }else{
//                                    //--Проверяем, можно ли удалить старый
//                                    //--Если можно, удаляем старый (точнее убираем у него привязку от наряда), удаляем деньги со счета хозяина
//                                }
//                            }


                            //Было или не было, если одинаковые, ничего делать не надо
                            if ($_POST['cert_name_id'] == $_POST['cert_name_old_id']){
                                //--Ничего не меняем
                            }else{
                                //Будем добавлять новый, но старый надо удалить
                                if ($_POST['cert_name_id'] > 0){
                                    //--Проверяем, можно ли удалить старый
                                    //-Если можно, удаляем старый (точнее убираем у него привязку от наряда), удаляем деньги со счета хозяина
                                    if (TRUE) {
                                        //Удаляем старый (точнее убираем у него привязку от наряда)
                                        $query = "UPDATE `journal_cert_name` SET `invoice_id`= :invoice_id, `closed_time`= :closed_time, `status` = :status WHERE `id`= :cert_name_id";

                                        $args = [
                                            'invoice_id' => 0,
                                            'closed_time' => '0000-00-00 00:00:00',
                                            'status' => 7,
                                            'cert_name_id' => $_POST['cert_name_old_id']
                                        ];

                                        $db::sql($query, $args);

                                        //Удаляем деньги со счета хозяина
                                        $query = "DELETE FROM `journal_order_nonclient` WHERE `cert_name_id`= '{$_POST['cert_name_old_id']}'";
                                        $db::sql($query, $args);

                                        //Сначала получим данные этого сертификата
                                        $query = "SELECT `client_id`, `nominal`, `status` FROM `journal_cert_name` WHERE `id`=:cert_name_id LIMIT 1";
                                        //var_dump($query);

                                        $args = [
                                            'cert_name_id' => $_POST['cert_name_id']
                                        ];

                                        $cert_name = $db::getRow($query, $args);
                                        //var_dump($cert_name);

                                        //Добавляем новый
                                        if (!empty($cert_name)) {
                                            if (($cert_name['status'] == 7) && ($cert_name['client_id'] != 0)) {
                                                //Обновим данные сертификата
                                                $query = "UPDATE `journal_cert_name` SET `invoice_id`= :invoice_id, `closed_time`= :closed_time, `status` = :status WHERE `id`= :cert_name_id";

                                                $args = [
                                                    'invoice_id' => $_POST['invoice_id'],
                                                    'closed_time' => $time,
                                                    'status' => 5,
                                                    'cert_name_id' => $_POST['cert_name_id']
                                                ];

                                                //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                                $db::sql($query, $args);

                                                //добавим системный ордер хозяину именного сертификата
                                                orderNonClient_add($cert_name['client_id'], $cert_name['nominal'], $time, 0, 0, $_POST['cert_name_id']);
                                            }
                                        }



                                        //Добавляем новый
//                                        $query = "UPDATE `journal_cert_name` SET `invoice_id`= :invoice_id, `closed_time`= :closed_time, `status` = :status WHERE `id`= :cert_name_id";
//
//                                        $args = [
//                                            'invoice_id' => $_POST['invoice_id'],
//                                            'closed_time' => $time,
//                                            'status' => 5,
//                                            'cert_name_id' => $_POST['cert_name_id']
//                                        ];
//
//                                        $db::sql($query, $args);
                                    }
                                }else{
                                    //--Проверяем, можно ли удалить старый
                                    //-Если можно, удаляем старый (точнее убираем у него привязку от наряда), удаляем деньги со счета хозяина
                                    if (TRUE) {
                                        //Удаляем старый (точнее убираем у него привязку от наряда)
                                        $query = "UPDATE `journal_cert_name` SET `invoice_id`= :invoice_id, `closed_time`= :closed_time, `status` = :status WHERE `id`= :cert_name_id";

                                        $args = [
                                            'invoice_id' => 0,
                                            'closed_time' => '0000-00-00 00:00:00',
                                            'status' => 7,
                                            'cert_name_id' => $_POST['cert_name_old_id']
                                        ];

                                        $db::sql($query, $args);

                                        //Удаляем деньги со счета хозяина
                                        $query = "DELETE FROM `journal_order_nonclient` WHERE `cert_name_id`= '{$_POST['cert_name_old_id']}'";
                                        $db::sql($query, $args);
                                    }
                                }

                            }







							//Обновляем в базу
							/*$query = "INSERT INTO `journal_invoice` (`zapis_id`, `office_id`, `client_id`, `worker_id`, `type`, `summ`, `summins`, `create_person`, `create_time`)
							VALUES (
							'{$_POST['zapis_id']}', '{$_POST['filial']}', '{$_POST['client']}', '{$_POST['worker']}', '{$_POST['invoice_type']}', '{$_POST['summ']}', '{$_POST['summins']}', '{$_SESSION['id']}', '{$time}')";
							*/
							$query = "UPDATE `$dbase` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `summ`='{$_POST['summ']}', `discount`='{$discount}', `summins`='{$_POST['summins']}' WHERE `id`= :invoice_id";

                            $args = [
                                'invoice_id' => $_POST['invoice_id']
                            ];

                            //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                            $db::sql($query, $args);

							//ID старой позиции
							$mysql_insert_id = $_POST['invoice_id'];

							//Удаляем старое
							$query = "DELETE FROM `$dbase_ex` WHERE `invoice_id` = '{$mysql_insert_id}'";

                            $args = [
                            ];

                            //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                            $db::sql($query, $args);

							$query = "DELETE FROM `$dbase_ex_mkb` WHERE `invoice_id` = '{$mysql_insert_id}'";

                            $args = [
                            ];

                            //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                            $db::sql($query, $args);

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
                                            $query = "INSERT INTO `$dbase_ex` (`invoice_id`, `ind`, `jaw_select`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `gift`, `spec_koeff`, `discount`, `percent_cats`, `manual_price`, `itog_price`) 
										    VALUES (
										    '{$mysql_insert_id}', '{$ind}', '{$jaw_select}', '{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$price}', '{$guarantee}', '{$gift}', '{$spec_koeff}', '{$discount}', '{$percent_cat}', '{$manual_price}', '{$itog_price}')";

                                            $args = [
                                            ];

                                            //mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                                            $db::sql($query, $args);

                                        }

                                        if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind])){
                                            $mkb_data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['mkb'][$ind];
                                            foreach ($mkb_data as $mkb_id){
                                                //Добавляем в базу МКБ
                                                $query = "INSERT INTO `$dbase_ex_mkb` (`invoice_id`, `ind`, `mkb_id`) 
                                                VALUES (
											    '{$mysql_insert_id}', '{$ind}', '{$mkb_id}')";

                                                $args = [
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
                                        $query = "INSERT INTO `journal_invoice_ex` (`invoice_id`, `ind`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `gift`, `spec_koeff`, `discount`, `percent_cats`, `manual_price`, `itog_price`) 
									    VALUES (
									    '{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$price}', '{$guarantee}', '{$gift}', '{$spec_koeff}', '{$discount}', '{$percent_cat}', '{$manual_price}', '{$itog_price}')";

                                        $args = [
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