<?php 

//fl_editDailyReport_add_f.php
//Функция для редактирования ежедневного отчёта

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['report_id'])
                && isset($_POST['allsumm']) && isset($_POST['itogSumm']) && isset($_POST['zreport'])
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

                /*$data_temp_arr = explode(".", $_POST['date']);

                $d = $data_temp_arr[0];
                $m = $data_temp_arr[1];
                $y = $data_temp_arr[2];*/

                $create_time = date('Y-m-d H:i:s', time());

                $query = "UPDATE `fl_journal_daily_report` SET
                  `itogSumm`='".str_replace(' ', '', $_POST['itogSumm'])."', `arenda`='{$_POST['arenda']}', `zreport`='{$_POST['zreport']}',
                  `summ`='".str_replace(' ', '', $_POST['allsumm'])."', `cashbox_nal`='{$_POST['SummNal']}',
                 `cashbox_beznal`='{$_POST['SummBeznal']}', `cashbox_cert_count`='{$_POST['CertCount']}', 
                 `cashbox_cert_nal`='{$_POST['SummCertNal']}', `cashbox_cert_beznal`='{$_POST['SummCertBeznal']}', 
                 `temp_orto_nal`='{$_POST['ortoSummNal']}', `temp_orto_beznal`='{$_POST['ortoSummBeznal']}', 
                 `temp_specialist_nal`='{$_POST['specialistSummNal']}', `temp_specialist_beznal`='{$_POST['specialistSummBeznal']}', 
                 `temp_analiz_nal`='{$_POST['analizSummNal']}', `temp_analiz_beznal`='{$_POST['analizSummBeznal']}',
                 `temp_solar_nal`='{$_POST['solarSummNal']}', `temp_solar_beznal`='{$_POST['solarSummBeznal']}', 
                 `temp_giveoutcash`='{$_POST['summMinusNal']}', 
                 `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' 
                WHERE `id`='{$_POST['report_id']}'";


                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //$mysql_insert_id = mysqli_insert_id($msql_cnnct);

                //логирование
                //AddLog ('0', $_SESSION['id'], '', 'Добавлен долг #'.$mysql_insert_id.'. Пациент ['.$_POST['client'].']. Сумма ['.$_POST['summ'].']. Срок истечения ['.$_POST['date_expires'].']. Тип ['.$_POST['type'].']. Комментарий ['.$_POST['comment'].'].');

                CloseDB($msql_cnnct);

                echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Отчёт обновлён</div>'));


			}else{
                echo json_encode(array('result' => 'success', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'post' => $_POST));
			}
		}
	}
?>