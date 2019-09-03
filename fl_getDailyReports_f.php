<?php 

//fl_getDailyReports_f.php
//Функция для добавления ежежневного отчёта

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['date']) && isset($_POST['filial_id'])){
				include_once 'DBWork.php';

                //!!! @@@
                include_once 'ffun.php';

                //разбираемся с правами
                $god_mode = FALSE;

                require_once 'permissions.php';

                //Дневной отчёт
                $report_j = array();
                //выдачи в банк
                $giveout_bank_summ = 0;
                //выдачи директору
                $giveout_director_summ = 0;

                $msql_cnnct = ConnectToDB ();

                $data_temp_arr = explode(".", $_POST['date']);

                $d = $data_temp_arr[0];
                $m = $data_temp_arr[1];
                $y = $data_temp_arr[2];

                //Получаем данные по отчёту за этот день
                $query = "SELECT * FROM `fl_journal_daily_report` WHERE `filial_id`='{$_POST['filial_id']}' AND `year`='$y' AND `month`='$m' AND `day`='$d' AND `status` <> '9' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($report_j, $arr);
                    }
                }

                //Получаем данные по банку и директору
                $query = "SELECT SUM(`summ`) AS `summ` FROM `fl_journal_in_bank` WHERE `filial_id`='{$_POST['filial_id']}' AND `year`='$y' AND `month`='$m' AND `day`='$d' AND `status` <> '9'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    //while ($arr = mysqli_fetch_assoc($res)){
                        //array_push($giveout_bank_j, $arr);
                    //}
                    $arr = mysqli_fetch_assoc($res);
                    $giveout_bank_summ = $arr['summ'];
                }

                $query = "SELECT SUM(`summ`) AS `summ` FROM `fl_journal_to_director` WHERE `filial_id`='{$_POST['filial_id']}' AND `year`='$y' AND `month`='$m' AND `day`='$d' AND `status` <> '9'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    //while ($arr = mysqli_fetch_assoc($res)){
                          //array_push($giveout_director_j, $arr);
                    //}
                    $arr = mysqli_fetch_assoc($res);
                    $giveout_director_summ = $arr['summ'];
                }


                //Попробуем собрать РЕАЛЬНЫЕ данные за эту дату
                $report_date = $d.'.'.$m.'.'.$y;

                $datastart = date('Y-m-d', strtotime($report_date.' 00:00:00'));
                $dataend = date('Y-m-d', strtotime($report_date.' 23:59:59'));

                $SummNal = 0;
                $SummBeznal = 0;
                $SummGiveOutCash = 0;

                $result = ajaxShowResultCashbox($datastart, $dataend, $_POST['filial_id'], 0, 1, false);

                if (!empty($result)) {
                    if (!empty($result['rezult'])) {
                        foreach ($result['rezult'] as $item) {
                            if ($item['summ_type'] == 1) {
                                $SummNal += $item['summ'];
                            }
                            if ($item['summ_type'] == 2) {
                                $SummBeznal += $item['summ'];
                            }
                        }
                    }
                    //сертификаты
                    if (!empty($result['rezult_cert'])) {

                        $CertCount = count($result['rezult_cert']);

                        foreach ($result['rezult_cert'] as $item) {
                            if ($item['summ_type'] == 1) {
                                $SummNal += $item['cell_price'];
                            }
                            if ($item['summ_type'] == 2) {
                                $SummBeznal += $item['cell_price'];
                            }
                        }
                    }
                    //абонементы
                    if (!empty($result['rezult_abon'])) {

                        $AbonCount = count($result['rezult_abon']);

                        foreach ($result['rezult_abon'] as $item) {
                            if ($item['summ_type'] == 1) {
                                $SummNal += $item['cell_price'];
                            }
                            if ($item['summ_type'] == 2) {
                                $SummBeznal += $item['cell_price'];
                            }
                        }
                    }
                    //солярий
                    if (!empty($result['rezult_solar'])) {

                        $SolarCount = count($result['rezult_solar']);

                        foreach ($result['rezult_solar'] as $item) {
                            if ($item['summ_type'] == 1) {
                                $SummNal += $item['summ'];
                            }
                            if ($item['summ_type'] == 2) {
                                $SummBeznal += $item['summ'];
                            }
                        }
                    }
                    //реализация
                    if (!empty($result['rezult_realiz'])) {

                        $RealizCount = count($result['rezult_realiz']);

                        foreach ($result['rezult_realiz'] as $item) {
                            if ($item['summ_type'] == 1) {
                                $SummNal += $item['summ'];
                            }
                            if ($item['summ_type'] == 2) {
                                $SummBeznal += $item['summ'];
                            }
                        }
                    }
                    //Расходы
                    if (!empty($result['rezult_give_out_cash'])){
                        foreach ($result['rezult_give_out_cash'] as $item) {
                            $SummGiveOutCash += $item['summ'];
                        }
                    }

                }
                //Итоговый массив из реальности
                $realSumms = array('nal' => $SummNal, 'beznal' => $SummBeznal, 'giveout' => $SummGiveOutCash);

                CloseDB ($msql_cnnct);

                if (!empty($report_j) || ($giveout_bank_summ > 0) || ($giveout_director_summ > 0)){

                    //Смотрим даты и права на всякие действия и на посмотреть, если заднее число
                    if ($d.'.'.$m.'.'.$y == date('d.n.Y', time())) {
                        //$a = true;
                        //Если есть права, то возвращаем данные
                        if (($finances['see_all'] == 1) || $god_mode) {
                            echo json_encode(array('result' => 'success', 'data' => $report_j, 'giveout_bank' => $giveout_bank_summ,  'giveout_director' => $giveout_director_summ, 'count' => count($report_j), 'real_summs' => $realSumms));
                        //Если нет прав, то возвращаем пустой массив, но указываем кол-во элементов в нём
                        }else{
                            echo json_encode(array('result' => 'success', 'data' => $report_j, 'giveout_bank' => 0,  'giveout_director' => 0, 'count' => count($report_j), 'real_summs' => $realSumms));
                        }
                    }else{
                        //Если есть права, то возвращаем данные
                        if (($finances['see_all'] == 1) || $god_mode) {
                            echo json_encode(array('result' => 'success', 'data' => $report_j, 'giveout_bank' => $giveout_bank_summ,  'giveout_director' => $giveout_director_summ, 'count' => count($report_j), 'real_summs' => $realSumms));
                        //Если нет прав, то возвращаем пустой массив, но указываем кол-во элементов в нём
                        }else{
                            echo json_encode(array('result' => 'success', 'data' => array(), 'giveout_bank' => array(),  'giveout_director' => array(), 'count' => count($report_j), 'real_summs' => array()));
                        }
                    }
                }else{

                    echo json_encode(array('result' => 'success', 'data' => '', 'count' => 0));
                }


			}else{
                echo json_encode(array('result' => 'success', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'count' => 0));
			}
		}
	}
?>