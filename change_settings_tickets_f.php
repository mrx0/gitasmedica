<?php 

//change_settings_tickets_f.php
//Функция для изменения настроек для заявок

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
        /*!!!Тест PDO*/
        //include_once('DBWorkPDO.php');
		//var_dump ($_POST);

		if ($_POST){
            $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

//            $query = "SELECT * FROM `journal_tickets_logs`
//                WHERE `ticket_id` = '{$_POST['ticket_id']}'
//                ORDER BY `create_time` ASC";
//
//            $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);
//
//            $number = mysqli_num_rows($res);
//
//            if ($number != 0) {
//                while ($arr = mysqli_fetch_assoc($res)) {
//                    array_push($log, $arr);
//                }
//            }
//
//

            //Вставить запись в БД:
            $query = "INSERT INTO `settings` 
                                (`worker_id`, `option`, `value`)
                            VALUES 
                                ('{$_POST['worker_id']}', '{$_POST['option']}', '{$_POST['value']}')
                            ON DUPLICATE KEY UPDATE
					            `value` = '{$_POST['value']}'";


            $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2) . ' -> ' . $query);

            CloseDB ($msql_cnnct2);

            echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Ok</div>'));

		}
	}
?>