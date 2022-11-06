<?php

//ajax_show_result_stat_client_finance3.php
//Пациенты, которые потратили до указанной суммы за период

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
            $query = '';
            $queryDop = '';

            include_once('DBWorkPDO.php');
            include_once 'functions.php';
            include_once 'ffun.php';

            $clientInvoices = array();

            //$filials_j = getAllFilials(false, true, true);
            //var_dump($filials_j);

            //Основные данные
            $args = [
                'orderYear' => $_POST['orderYear'],
                'orderSumm' => $_POST['orderSumm']+1
            ];

            if ($_POST['filial_id'] != 99){
                $queryDop = " AND jord.office_id = :filial_id";
                $args['filial_id'] = $_POST['filial_id'];
            }
//            var_dump($args);

            $db = new DB();

            $query = "SELECT jord.client_id, SUM(jord.summ) AS summ, sc.full_name, sc.telephone
                        FROM `journal_order` jord
                        LEFT JOIN `spr_clients` sc ON sc.id = jord.client_id
                        WHERE jord.status <> '9' {$queryDop} AND YEAR(jord.date_in) = :orderYear
                        GROUP BY jord.client_id
                        HAVING summ <= :orderSumm";

            $res = $db::getRows($query, $args);
//            var_dump($query);
//            var_dump($res);


            //Выводим результат
            if (!empty($res)){
                echo 'Всего: '.count($res);
                echo '
                    <table style="border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">';

                foreach ($res as $data) {
                    echo '
                            <tr class="cellsBlock" style="font-weight: bold; width: auto; margin: 2px">
                                <td style="width: 260px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: left;">
                                    <a href="client.php?id='.$data['client_id'].'" class="ahref" target="_blank" rel="nofollow noopener">'.$data['full_name'].'</a>
                                </td>
                                <td style="width: 160px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: left;">
                                    '.$data['telephone'].'
                                </td>
                                <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">
                                    '.$data['summ'].' руб.
                                </td>
                            </tr>';


                }
                echo '</table>';

            }else{
                echo '<span style="color: red;">Ничего не найдено</span>';
            }



		}
	}
?>