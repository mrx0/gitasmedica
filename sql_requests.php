<?php

//sql_requests.php
//тесты с mysql запросами

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if ($god_mode){
            /*!!!Тест PDO*/
            include_once('DBWorkPDO.php');

			include_once 'functions.php';
			include_once 'ffun.php';
            require 'variables.php';

            echo '
                <div id="status">
                    <header id="header">
                        <h2 style="">sql_requests.php</h2>
                    </header>';

            echo '
                    <div id="data">';

            $rezult = array();

            $db = new DB();

            //Выбираем всех, у кого есть рассрочка и вставляем их в journal_installments
            $query = "SELECT *
                FROM `journal_cert`
                WHERE `status` = '7'";
            //var_dump($query);

            $data = $db::getRows($query, []);
            //var_dump( $data);

            if (!empty($data)){
                //Сразу будем выводить

                foreach ($data as $item) {
                    //Вставить рассрочке в БД
					if ($item['nominal'] == $item['debited']) {
						var_dump($item['id']);

						$query = "UPDATE `journal_cert`
                   		SET `status`='5', `closed_time`='2020-11-14 00:00:01' WHERE `id`='{$item['id']}'
                    	";

						$args = [
						];

						$db::sql($query, $args);
					}

                }

            }else{
                echo '<span class="query_neok" style="padding-top: 0">Ничего не найдено</span>';
            }




            echo '
                        <div id="errrror"></div>';


            echo '
                    </div>
                </div>';



		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';


////Выбор всех пациентов (ФИО, телефон, комментарий), у которых было зафиксированно хотя бы одно посещение
//$query = "SELECT `full_name` , `telephone` , `comment`
//                FROM `spr_clients`
//                WHERE `id`
//                IN (
//
//                SELECT `patient`
//                FROM `zapis`
//                WHERE `type` = '6'
//                AND (
//                `enter` = '1'
//                OR `enter` = '6'
//                )
//                GROUP BY `patient`
//                )
//                ORDER BY `full_name`";
////var_dump($query);


?>



