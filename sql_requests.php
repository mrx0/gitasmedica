<?php

//sql_requests.php
//тесты с mysql запросами

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if ($god_mode){
			include_once 'DBWork.php';
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

            $msql_cnnct = ConnectToDB ();

            //Выбор всех пациентов (ФИО, телефон, комментарий), у которых было зафиксированно хотя бы одно посещение
            $query = "SELECT `full_name` , `telephone` , `comment`
                FROM `spr_clients`
                WHERE `id`
                IN (
                
                SELECT `patient`
                FROM `zapis`
                WHERE `type` = '6'
                AND (
                `enter` = '1'
                OR `enter` = '6'
                )
                GROUP BY `patient`
                )
                ORDER BY `full_name`";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                //Сразу будем выводить

                echo 'Всего: '.$number;

                echo '<table style="border: 1px solid #BFBCB5; margin: 5px; font-size: 80%;">';

                while ($arr = mysqli_fetch_assoc($res)){
                    echo '<tr>';
                    echo '<td style="outline: 1px solid #BFBCB5; padding: 2px;">'.$arr['full_name'].'</td>';
                    echo '<td style="outline: 1px solid #BFBCB5; padding: 2px;">'.$arr['telephone'].'</td>';
                    echo '<td style="outline: 1px solid #BFBCB5; padding: 2px;">'.$arr['comment'].'</td>';
                    echo '</tr>';
                }

                echo '</table>';

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

?>