<?php 

//add_price_id_cosm_in_invoice_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();
			$iExist = false;
			$existID = 0;
			
			if (!isset($_POST['price_id']) || !isset($_POST['client']) || !isset($_POST['zapis_id']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);
				
				//if ($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'] != 0){
				//	$t_number_active = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active'];
					
					if (isset($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
						
						//проверим, нет ли уже такой позиции в акте
						/*if (!empty($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'])){
							$data = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'];
							foreach ($data as $key => $invoice_data){
								if ($invoice_data['id'] == $_POST['price_id']){
									$iExist = true;
									$existID = $key;
								}
							}
						}*/
						
						//if ($iExist){
						//	$_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$existID]['quantity']++;
						//}else{
							$temp_arr['id'] = (int)$_POST['price_id'];
							$temp_arr['quantity'] = 1;
							$temp_arr['insure'] = 0;
							$temp_arr['insure_approve'] = 0;
							$temp_arr['price'] = 0;
							$temp_arr['start_price'] = 0;
							$temp_arr['guarantee'] = 0;
							$temp_arr['gift'] = 0;
							$temp_arr['spec_koeff'] = 0;
							$temp_arr['discount'] = 0;

							//$temp_arr['percent_cats'] = 0;

                            include_once 'DBWork.php';

                            $msql_cnnct = ConnectToDB ();

                            //выбрать первую из категорий указанного типа
                            $query = "SELECT `id` FROM `fl_spr_percents` WHERE `type`='6' LIMIT 1;";
                            //var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {
                                $arr = mysqli_fetch_assoc($res);
                                /*$temp_arr2['percent_cats'] = (int)$arr['id'];
                                $temp_arr2['work_percent'] = (int)$arr['work_percent'];
                                $temp_arr2['material_percent'] = (int)$arr['material_percent'];*/

                                //$percents_j = getPercents( $invoice_j[0]['worker_id'], (int)$arr['id']);
                                //var_dump($percents_j);

                                $temp_arr['percent_cats'] = (int)$arr['id'];
                                //$temp_arr2['work_percent'] = (int)$percents_j[(int)$arr['id']]['work_percent'];
                                //$temp_arr2['material_percent'] = (int)$percents_j[(int)$arr['id']]['material_percent'];

                            } else {
                                $temp_arr['percent_cats'] = 0;
                                //$invoice_ex_j = 0;
                            }

                            CloseDB ($msql_cnnct);


							$temp_arr['manual_price'] = false;
							$temp_arr['itog_price'] = 0;
							$temp_arr['manual_itog_price'] = 0;

                            //переменная для цены
                            $price['price'] = 0;
                            $price['start_price'] = 0;
                            //переменная для массива цен
                            $prices = array();
                            //!!! @@@
                            include_once 'ffun.php';

                            //получим цены
                            $prices = takePrices ((int)$_POST['price_id'], (int)$_POST['client_insure']);
                            //var_dump($prices);

                            if (!empty($prices)) {

                                $price = returnPriceWithKoeff(0, $prices, (int)$_POST['client_insure'], false, 0);

                            }

                        /*$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".(int)$_POST['price_id']."' ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";

                        //Если посещение страховое и у пациента прописана страховая
                        if (isset($_POST['zapis_insure'])){
                            if (isset($_POST['client_insure'])){
                                if ($_POST['zapis_insure'] != 0){
                                    if ($_POST['client_insure'] != 0){
                                        $temp_arr['insure'] = (int)$_POST['client_insure'];

                                        $query = "SELECT `price` FROM `spr_priceprices_insure` WHERE `item`='".(int)$_POST['price_id']."' AND `insure`='".(int)$_POST['client_insure']."' ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";
                                    }
                                }
                            }
                        }

                        require 'config.php';
                        mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
                        mysql_select_db($dbName) or die(mysql_error());
                        mysql_query("SET NAMES 'utf8'");

                        $arr = array();
                        $rez = array();
                        $price = 0;

                        //$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `create_time` DESC LIMIT 1";
                        //$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$_GET['id']."' ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";

                        $res = mysql_query($query) or die($query);

                        $number = mysql_num_rows($res);
                        if ($number != 0){
                            $arr = mysql_fetch_assoc($res);
                            $price = $arr['price'];
                        }else{
                            $price = '?';
                        }*/

							$temp_arr['price'] = (int)$price['price'];
                            $temp_arr['start_price'] = (int)$price['start_price'];
                            $temp_arr['manual_itog_price'] = (int)$price['price'];

							//mysql_close();
							
							array_push($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'], $temp_arr);
						//}
					}
					
				//}

				
				//echo json_encode(array('result' => 'success', 't_number_active' => $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['t_number_active']));
			}
		}
	}
?>