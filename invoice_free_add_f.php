<?php 

//invoice_free_add_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			$temp_arr = array();

			if (!isset($_POST['invoice_type']) || !isset($_POST['summ']) || !isset($_POST['summins']) || !isset($_POST['client']) || !isset($_POST['date_in']) || !isset($_POST['filial']) || !isset($_POST['worker'])){
                //echo json_encode(array('result' => 'error', 'data' => $_POST));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);

                include_once 'DBWork.php';

                if ($_POST['client'] != '') {

                    $clientSearch = SelDataFromDB('spr_clients', $_POST['client'], 'client_full_name');

                    if ($clientSearch == 0) {
                        echo json_encode(array('result' => 'error', 'data' => '<span class="query_neok">Не найден пациент.</span>'));
                    }else{
                        if ($_POST['worker'] != '') {

                            $workerSearch = SelDataFromDB('spr_workers', $_POST['worker'], 'worker_full_name');

                            if ($workerSearch == 0) {
                                echo json_encode(array('result' => 'error', 'data' => '<span class="query_neok">Не найден исполнитель.</span>'));
                            } else {

                                if (isset($_SESSION['invoice_data']['free_invoice']['data'])) {
                                    if (!empty($_SESSION['invoice_data']['free_invoice']['data'])) {
                                        $data = $_SESSION['invoice_data']['free_invoice']['data'];

                                        $msql_cnnct = ConnectToDB();

                                        $date_in_temp = strtotime($_POST['date_in']." 09:00:00");
                                        $date_in = date('Y-m-d H:i:s', $date_in_temp);

                                        //if ($_POST['invoice_type'] == 5) {
                                        //$discount = $_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['discount'];
                                        // }
                                        //if ($_POST['invoice_type'] == 6){
                                        $discount = $_SESSION['invoice_data']['free_invoice']['discount'];
                                        //}
                                        //Добавляем в базу
                                        $query = "INSERT INTO `journal_invoice` (`zapis_id`, `office_id`, `client_id`, `worker_id`, `type`, `summ`, `discount`, `summins`, `create_person`, `create_time`)
                                        VALUES (
                                        '0', '{$_POST['filial']}', '{$clientSearch[0]['id']}', '{$workerSearch[0]['id']}', '{$_POST['invoice_type']}', '{$_POST['summ']}', '{$discount}', '{$_POST['summins']}', '{$_SESSION['id']}', '{$date_in}')";

                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                        //ID новой позиции
                                        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

                                        foreach ($data as $ind => $invoice_data) {

                                            if (!empty($invoice_data)) {

                                                $price_id = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['id'];
                                                $quantity = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['quantity'];
                                                $insure = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['insure'];
                                                $insure_approve = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['insure_approve'];
                                                $price = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['price'];
                                                $guarantee = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['guarantee'];
                                                $gift = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['gift'];
                                                $spec_koeff = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['spec_koeff'];
                                                $discount = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['discount'];
                                                $manual_price = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['manual_price'];
                                                $itog_price = $_SESSION['invoice_data']['free_invoice']['data'][$ind]['itog_price'];

                                                //Добавляем в базу
                                                $query = "INSERT INTO `journal_invoice_ex` (`invoice_id`, `ind`, `price_id`, `quantity`, `insure`, `insure_approve`, `price`, `guarantee`, `gift`, `spec_koeff`, `discount`, `manual_price`, `itog_price`)
                                                VALUES (
                                              '{$mysql_insert_id}', '{$ind}', '{$price_id}', '{$quantity}', '{$insure}', '{$insure_approve}', '{$price}', '{$guarantee}' '{$gift}', '{$spec_koeff}', '{$discount}', '{$manual_price}', '{$itog_price}')";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                                //unset($_SESSION['invoice_data']);
                                            }
                                        }
                                        unset($_SESSION['invoice_data']);

                                        //!!! @@@ Пересчет долга
                                        /*include_once 'ffun.php';
                                        calculateDebt($_POST['client']);*/

                                        echo json_encode(array('result' => 'success', 'data' => $mysql_insert_id, 'data2' => $itog_price));
                                    }
                                }

                            }
                        }else{
                            echo json_encode(array('result' => 'error', 'data' => '<span class="query_neok">Не указан исполнитель</span>'));
                        }
                    }
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<span class="query_neok">Не указан клиент</span>'));
                }
			}
		}
	}
?>