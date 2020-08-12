<?php 

//sclad_prihod_edit_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			
			$temp_arr = array();

            if (!isset($_POST['prihod_id']) || !isset($_POST['provider_name']) || !isset($_POST['prov_doc']) || !isset($_POST['filial_id']) || !isset($_POST['prihod_time']) || !isset($_POST['summ'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				include_once 'DBWork.php';

                /*!!!Тест PDO*/
                include_once('DBWorkPDO.php');

                $dbase = 'sclad_prihod';
                $dbase_ex = 'sclad_prihod_ex';

				$prihod_j = SelDataFromDB($dbase, $_POST['prihod_id'], 'id');

                if ($prihod_j != 0){
                    if (isset($_SESSION['sclad'])){
                        if (isset($_SESSION['sclad']['items_prihod_data_edit'])) {
                            if (isset($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']])) {
                                if (!empty($_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']])) {

                                    $data = $_SESSION['sclad']['items_prihod_data_edit'][$_POST['prihod_id']];
                                    //var_dump($data);

                                    $time = date('Y-m-d H:i:s', time());

                                    $comment = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['comment'])))));

                                    $db = new DB();

                                    $query = "UPDATE `$dbase`
                                    SET 
                                    `filial_id` = :filial_id,
                                    `prihod_time` = :prihod_time,
                                    `provider_id` = :provider_id,
                                    `provider_name` = :provider_name,
                                    `prov_doc` = :prov_doc,
                                    `summ` = :summ,
                                    `comment` = :comment,
                                    `last_edit_time` = :last_edit_time,
                                    `last_edit_person` = :last_edit_person
                                    WHERE `id` = :id";

                                    $args = [
                                        'filial_id' => $_POST['filial_id'],
                                        'prihod_time' => date('Y-m-d', strtotime($_POST['prihod_time'] . ' 09:00:00')),
                                        'provider_id' => 0,
                                        'provider_name' => $_POST['provider_name'],
                                        'prov_doc' => $_POST['prov_doc'],
                                        'summ' => $_POST['summ'] * 100,
                                        'comment' => $comment,
                                        'last_edit_time' => $time,
                                        'last_edit_person' => $_SESSION['id'],
                                        'id' => $_POST['prihod_id']
                                    ];

                                    $db::sql($query, $args);

                                    //ID позиции
                                    $insert_id = $_POST['prihod_id'];

                                    //Удаляем старое
                                    $query = "DELETE FROM `$dbase_ex` WHERE `prihod_id` = ?";

                                    $db::sql($query, [$insert_id]);

                                    //Добавляем новое
                                    foreach ($data as $item_id => $prihod_data) {

                                        if (!empty($prihod_data)) {

                                            foreach ($prihod_data as $ind => $item) {

                                                if ($item['exp_garant_type'] == 0) {
                                                    $exp_garant_date = '0000-00-00';
                                                } else {
                                                    $exp_garant_date = date('Y-m-d', strtotime($item['exp_garant_date'] . ' 15:00:00'));
                                                }

                                                //Добавляем в базу позиции прихода
                                                $query = "INSERT INTO `$dbase_ex` (
                                                `prihod_id`,
                                                `sclad_item_id`,
                                                `ind`,
                                                `quantity`,
                                                `price`,
                                                `exp_garant_type`, 
                                                `exp_garant_date`
                                                )
                                                VALUES (
                                                :prihod_id,
                                                :sclad_item_id,
                                                :ind,
                                                :quantity,
                                                :price,
                                                :exp_garant_type,
                                                :exp_garant_date
                                                )";

                                                $args = [
                                                    'prihod_id' => $insert_id,
                                                    'sclad_item_id' => $item_id,
                                                    'ind' => $ind,
                                                    'quantity' => $item['quantity'],
                                                    'price' => $item['price'],
                                                    'exp_garant_type' => $item['exp_garant_type'],
                                                    'exp_garant_date' => $exp_garant_date
                                                ];
                                                $db::sql($query, $args);


                                            }
                                        }
                                    }

                                    unset($_SESSION['sclad']);

                                    echo json_encode(array('result' => 'success', 'data' => $insert_id));
                                }
                            }
						}
					}
				}else{
					echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
				}
			}
		}
	}
?>