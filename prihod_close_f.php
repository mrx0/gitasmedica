<?php 

//prihod_close_f.php
//Провести приходную накладную, поставить статус 7

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){
			if (!isset($_POST['prihod_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                //include_once 'DBWork.php';
                /*!!!Тест PDO*/
                include_once('DBWorkPDO.php');

                //$date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in'].""));
                $date_in = date('Y-m-d H:i:s', time());

                $db = new DB();

                $args = [
                    'prihod_id' => $_POST['prihod_id']
                ];

                //Обновляем статус на приходованно
                $query = "UPDATE `sclad_prihod` SET `status` = '7' WHERE `id`= :prihod_id";

                $db::sql($query, $args);

                //Обновление количества на складе
                //Нужно выбрать сначала некоторые данные из приходной накладной, а потом обновить количество
                $query = "
                SELECT scp_ex.*, scp.filial_id 
                FROM `sclad_prihod_ex` scp_ex
                RIGHT JOIN `sclad_prihod` scp
                ON scp.id = scp_ex.prihod_id
                WHERE scp_ex.prihod_id = :prihod_id";

                $prihod_data = $db::getRows($query, $args);

                //Добавим/обновим наличие
                foreach ($prihod_data as $prihod_item){
                    $args = [
                        'filial_id' => $prihod_item['filial_id'],
                        'sclad_item_id' => $prihod_item['sclad_item_id'],
                        'exp_garant_type' => $prihod_item['exp_garant_type'],
                        'exp_garant_date' => $prihod_item['exp_garant_date']
                    ];

                    //Выбираем, смотрим, есть ли уже такое
                    $query = "
                    SELECT sc_av.* 
                    FROM `sclad_availability` sc_av
                    WHERE sc_av.filial_id = :filial_id AND sc_av.sclad_item_id = :sclad_item_id AND sc_av.exp_garant_type = :exp_garant_type AND  sc_av.exp_garant_date = :exp_garant_date
                    LIMIT 1";

                    $data = $db::getRow($query, $args);

                    //Если нет, вставляем
                    if (empty($data)){

                        $args['quantity'] = $prihod_item['quantity'];

                        //Вставить запись прихода в БД:
                        $query = "INSERT INTO `sclad_availability` (
                            `filial_id`,
                            `sclad_item_id`,
                            `quantity`,
                            `exp_garant_type`,
                            `exp_garant_date`
                            )
                            VALUES (
                            :filial_id,
                            :sclad_item_id,
                            :quantity,
                            :exp_garant_type,
                            :exp_garant_date
                            )";

                        $db::sql($query, $args);

                    }else{
                        $args = [
                            'quantity' => $prihod_item['quantity'] + $data['quantity']
                        ];

                        //Обновить количество в наличии
                        $query = "UPDATE `sclad_availability` SET `quantity` = :quantity WHERE `id`='{$data['id']}';";

                        $db::sql($query, $args);

                    }
                }

//        UPDATE  dlp.address AS t1
//        INNER JOIN
//                (
//                    SELECT  addressuuid, MIN(last_updated) minDate
//            FROM    dlp.address
//            GROUP BY addressuuid
//        ) AS t2
//            ON t1.addressuuid = t2.addressuuid
//SET     t1.created_on = t2.minDate

				echo json_encode(array('result' => 'success', 'data' => 'OK'));
			}
		}
	}

?>