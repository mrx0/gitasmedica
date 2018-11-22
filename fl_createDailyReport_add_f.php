<?php 

//fl_createDailyReport_add_f.php
//Функция для добавления ежежневного отчёта

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['date']) && isset($_POST['filial_id']) && isset($_POST['allsumm'])
                && isset($_POST['SummNal']) && isset($_POST['SummBeznal'])
                && isset($_POST['CertCount']) && isset($_POST['SummCertNal']) && isset($_POST['SummCertBeznal'])
                && isset($_POST['ortoSummNal']) && isset($_POST['ortoSummBeznal'])
                && isset($_POST['specialistSummNal']) && isset($_POST['specialistSummBeznal'])
                && isset($_POST['analizSummNal']) && isset($_POST['analizSummBeznal'])
                && isset($_POST['solarSummNal']) && isset($_POST['solarSummBeznal'])
                && isset($_POST['summMinusNal'])
            ){
				include_once 'DBWork.php';

				$time = time();

                $msql_cnnct = ConnectToDB ();
				
				//$date_expires = strtotime($_POST['date_expires'].' 00:00:00');
				
				$query = "INSERT INTO `fl_journal_daily_report` 
                (`filial`, `day`, `month`, `year`, `summ`, `cashbox_nal`, `cashbox_beznal`, `cashbox_cert_count`, `cashbox_cert_nal`, `cashbox_cert_beznal`, `temp_orto_nal`, `temp_orto_beznal`, `temp_specialist_nal`, `temp_specialist_beznal`, `temp_analiz_nal`, `temp_analiz_beznal`, `temp_solar_nal`, `temp_solar_beznal`, `temp_giveoutcash`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `status`) 
                VALUES ('0', '00', '00', '0000', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0');";

                /*$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $mysql_insert_id = mysqli_insert_id($msql_cnnct);


					
                //логирование
                //AddLog ('0', $_SESSION['id'], '', 'Добавлен долг #'.$mysql_insert_id.'. Пациент ['.$_POST['client'].']. Сумма ['.$_POST['summ'].']. Срок истечения ['.$_POST['date_expires'].']. Тип ['.$_POST['type'].']. Комментарий ['.$_POST['comment'].'].');
				
                if ($_POST['type'] == 3){
                    $descr = 'Аванс';
                }
                if ($_POST['type'] == 4){
                    $descr = 'Долг';
                }*/

                CloseDB ($msql_cnnct);

                echo json_encode(array('result' => 'success', 'data' => $_POST));

                /*echo '
						<div class="query_ok">
							'.$descr.' <a href="finance_dp.php?id='.$mysql_insert_id.'">#'.$mysql_insert_id.'</a> добавлен.
							<br><br>
							<a href="client.php?id='.$_POST['client'].'" class="b">Карточка пациента</a>
							<a href="client_finance.php?client='.$_POST['client'].'" class="b">Счёт <i class="fa fa-rub"></i></a>
						</div>';*/
			}else{
                echo json_encode(array('result' => 'success', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}
		}
	}
?>