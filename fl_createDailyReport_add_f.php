<?php 

//fl_createDailyReport_add_f.php
//Функция для добавления ежедневного отчёта

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['date']) && isset($_POST['filial_id'])
                && isset($_POST['allsumm']) && isset($_POST['itogSumm']) && isset($_POST['zreport']) && isset($_POST['arenda'])
                && isset($_POST['SummNal']) && isset($_POST['SummBeznal'])
                && isset($_POST['SummNalStomCosm']) && isset($_POST['SummBeznalStomCosm'])
                && isset($_POST['CertCount']) && isset($_POST['SummCertNal']) && isset($_POST['SummCertBeznal'])
                && isset($_POST['AbonCount']) && isset($_POST['SummAbonNal']) && isset($_POST['SummAbonBeznal'])
                && isset($_POST['SolarCount']) && isset($_POST['SummSolarNal']) && isset($_POST['SummSolarBeznal'])
                && isset($_POST['RealizCount']) && isset($_POST['SummRealizNal']) && isset($_POST['SummRealizBeznal'])
                && isset($_POST['ortoSummNal']) && isset($_POST['ortoSummBeznal'])
                && isset($_POST['specialistSummNal']) && isset($_POST['specialistSummBeznal'])
                && isset($_POST['analizSummNal']) && isset($_POST['analizSummBeznal'])
                && isset($_POST['solarSummNal']) && isset($_POST['solarSummBeznal'])
                && isset($_POST['summMinusNal'])
//                && isset($_POST['bankSummNal'])
//                && isset($_POST['directorSummNal'])
            ){

                if ($_POST['zreport'] == "") {
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Z-отчёт надо заполнять.</div>'));
                }else{

                    include_once 'DBWork.php';

                    //!!!Костыль
                    $_POST['bankSummNal'] = 0;
                    $_POST['directorSummNal'] = 0;

                    $time = time();

                    $msql_cnnct = ConnectToDB();

                    $data_temp_arr = explode(".", $_POST['date']);

                    $d = $data_temp_arr[0];
                    $m = $data_temp_arr[1];
                    $y = $data_temp_arr[2];

                    //Смотрим не было ли уже отчета на этом филиале за этот день
                    $dailyReports_j = array();

                    $query = "SELECT * FROM `fl_journal_daily_report` WHERE `filial_id`='{$_POST['filial_id']}' AND `day`='{$d}' AND  `month`='$m' AND  `year`='$y'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($dailyReports_j, $arr);
                        }
                    }

                    if (empty($dailyReports_j)) {

                        $create_time = date('Y-m-d H:i:s', time());

                        $query = "INSERT INTO `fl_journal_daily_report` 
                        (`filial_id`, `day`, `month`, `year`, 
                        `itogSumm`, `arenda`, `zreport`, `summ`,
                        `nal`, `beznal`, 
                        `cashbox_nal`, `cashbox_beznal`, 
                        `cashbox_cert_count`, `cashbox_cert_nal`, `cashbox_cert_beznal`, 
                        `cashbox_abon_count`, `cashbox_abon_nal`, `cashbox_abon_beznal`, 
                        `cashbox_solar_count`, `cashbox_solar_nal`, `cashbox_solar_beznal`, 
                        `cashbox_realiz_count`, `cashbox_realiz_nal`, `cashbox_realiz_beznal`, 
                        `temp_orto_nal`, `temp_orto_beznal`, 
                        `temp_specialist_nal`, `temp_specialist_beznal`, 
                        `temp_analiz_nal`, `temp_analiz_beznal`, 
                        `temp_solar_nal`, `temp_solar_beznal`, 
                        `temp_giveoutcash`, `bank_summ_nal`, `director_summ_nal`, 
                        `create_time`, `create_person`) 
                        VALUES ('{$_POST['filial_id']}', '{$d}', '{$m}', '{$y}', 
                        '" . str_replace(' ', '', $_POST['itogSumm']) . "', '{$_POST['arenda']}', '{$_POST['zreport']}', '" . str_replace(' ', '', $_POST['allsumm']) . "', 
                        '" . str_replace(' ', '', $_POST['SummNal']) . "', '" . str_replace(' ', '', $_POST['SummBeznal']) . "', 
                        '{$_POST['SummNalStomCosm']}', '{$_POST['SummBeznalStomCosm']}', 
                        '{$_POST['CertCount']}', '{$_POST['SummCertNal']}', '{$_POST['SummCertBeznal']}', 
                        '{$_POST['AbonCount']}', '{$_POST['SummAbonNal']}', '{$_POST['SummAbonBeznal']}', 
                        '{$_POST['SolarCount']}', '{$_POST['SummSolarNal']}', '{$_POST['SummSolarBeznal']}', 
                        '{$_POST['RealizCount']}', '{$_POST['SummRealizNal']}', '{$_POST['SummRealizBeznal']}', 
                        '{$_POST['ortoSummNal']}', '{$_POST['ortoSummBeznal']}', 
                        '{$_POST['specialistSummNal']}', '{$_POST['specialistSummBeznal']}', 
                        '{$_POST['analizSummNal']}', '{$_POST['analizSummBeznal']}', 
                        '{$_POST['solarSummNal']}', '{$_POST['solarSummBeznal']}', 
                        '{$_POST['summMinusNal']}', '{$_POST['bankSummNal']}', '{$_POST['directorSummNal']}', 
                        '{$create_time}', '{$_SESSION['id']}');";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //$mysql_insert_id = mysqli_insert_id($msql_cnnct);

                        //логирование
                        //AddLog ('0', $_SESSION['id'], '', 'Добавлен долг #'.$mysql_insert_id.'. Пациент ['.$_POST['client'].']. Сумма ['.$_POST['summ'].']. Срок истечения ['.$_POST['date_expires'].']. Тип ['.$_POST['type'].']. Комментарий ['.$_POST['comment'].'].');


                        echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Отчёт сформирован и отправлен</div>'));
                    } else {
                        echo json_encode(array('result' => 'success', 'data' => '<div class="query_neok">Отчёт за указаную дату для этого филиала уже был сформирован.</div>'));
                    }

                    CloseDB($msql_cnnct);
                }
			}else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #20. Что-то пошло не так</div>'));
			}
		}
	}
?>