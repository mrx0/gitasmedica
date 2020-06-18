<?php 

//sclad_prihod_add_f.php
//Непосредственное добавление новой приходной накладной в БД

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
//		var_dump ($_POST);
		
		if ($_POST){

			$temp_arr = array();

			if (!isset($_POST['provider_name']) || !isset($_POST['prov_doc']) || !isset($_POST['filial_id']) || !isset($_POST['prihod_time'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                include_once 'DBWork.php';

                $db = 'sclad_prihod';
                $db_ex = 'sclad_prihod_ex';

                if (isset($_SESSION['sclad'])){
                    if (isset($_SESSION['sclad']['items_prihod_data'])) {
                        if (!empty($_SESSION['sclad']['items_prihod_data'])) {

                            $data = $_SESSION['sclad']['items_prihod_data'];
//                            var_dump($data);

                            $msql_cnnct = ConnectToDB();

                            $time = date('Y-m-d H:i:s', time());

                            //Добавляем в базу
                            $query = "INSERT INTO `$db` (`filial_id`, `prihod_time`, `provider_id`, `provider_name`, `prov_doc`, `comment`, `create_person`, `create_time`)
                            VALUES (
                            '{$_POST['filial_id']}', '{$_POST['prihod_time']}', '0', '{$_POST['provider_name']}', '{$_POST['prov_doc']}', '', '{$_SESSION['id']}', '{$time}')";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            //ID новой позиции
                            $mysql_insert_id = mysqli_insert_id($msql_cnnct);

                            foreach ($data as $item_id => $prihod_data) {

                                if (!empty($prihod_data)) {

                                    foreach ($prihod_data as $ind => $item) {

                                        if ($item['exp_garant_type'] == 0){
                                            $exp_garant_date = '0000-00-00';
                                        }else{
                                            $exp_garant_date = date('Y-m-d', strtotime($item['exp_garant_date'].' 15:00:00'));
                                        }

                                        //Добавляем в базу
                                        $query = "INSERT INTO `$db_ex` (`prihod_id`, `sclad_item_id`, `ind`, `quantity`, `price`, `exp_garant_type`, `exp_garant_date`)
                                        VALUES (
                                        '{$mysql_insert_id}', '{$item_id}', '{$ind}', '{$item['quantity']}', '{$item['price']}', '{$item['exp_garant_type']}', '".$exp_garant_date."')";

                                        mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                    }
                                }
                            }
                            unset($_SESSION['sclad']);

                            //!!! @@@ Пересчет долга
//                            include_once 'ffun.php';
//                            calculateDebt($_POST['client']);

                            echo json_encode(array('result' => 'success', 'data' => $mysql_insert_id));

                        }
                    }
				}
			}
		}
	}
?>