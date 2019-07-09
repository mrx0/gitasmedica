<?php 

//sheduler_change_f.php
//Функция для редактирования расписания

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
//        var_dump($_POST['filials_arr']);
//        var_dump(in_array(15, $_POST['filials_arr']));

		if ($_POST) {
            if (isset($_POST['all_fililas_chckd']) && isset($_POST['filials_arr'])){
                if (($_POST['all_fililas_chckd'] == 1) || (!empty($_POST['filials_arr']))){

                    //Максимально кол-во дней в месяце, где будем менять
                    $max_days = cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);

                    if ($_POST['day'] < 0) {
                        $day = 1;
                    } elseif ($_POST['day'] > $max_days) {
                        $day = $max_days;
                    } else {
                        $day = $_POST['day'];
                    }

                    $all_filials = false;
                    $dop_query = '';
                    $filial_ids_arr = array();

                    //Если не отмечено, что все филиалы
                    if ($_POST['all_fililas_chckd'] == 1) {
                        $all_filials = true;
                    }//else{
                    //                //var_dump($_POST['filials_arr']);
                    //
                    //                foreach ($_POST['filials_arr'] as $filial_id) {
                    //                    array_push($filial_ids_arr, "`filial` = '".$filial_id."'");
                    //                }
                    //
                    //                //Соберем строку для запроса
                    //                $dop_query = ' AND ('.implode(' OR ', $filial_ids_arr).')';
                    //            }
                    //var_dump($dop_query);

                    $month = $_POST['month'];
                    $year = $_POST['year'];

                    //var_dump ($day);
                    $canUpdate = TRUE;

                    $msql_cnnct = ConnectToDB();

                    //получаем шаблон графика из базы
                    $query = "SELECT `filial`, `day`, `smena`, `kab`, `worker`, `type` FROM `sheduler_template`";

                    $shedTemplate = 0;

                    $arr = array();
                    $rez = array();

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            $rez[$arr['day']][$arr['smena']][$arr['filial']][$arr['type']][$arr['kab']][$arr['worker']] = true;
                        }
                        $shedTemplate = $rez;
                    } else {
                        $shedTemplate = 0;
                    }
                    //var_dump($shedTemplate[5][1]);

                    //Если шаблон графика есть
                    if ($shedTemplate != 0) {
                        //Пробегаемся с указанного дня до последнего дня месяца
                        for ($i = $day; $i <= $max_days; $i++) {
                            $month_stamp = mktime(0, 0, 0, $month, $i, $year);
                            //Узнаем номер дня недели
                            $weekday = date("N", $month_stamp);
                            //var_dump($weekday);
                            //var_dump($shedTemplate[$weekday]);

                            //foreach ($shedTemplate as $dayW => $valueW){
                            //Если есть в шаблоне такой день недели
                            if (isset($shedTemplate[$weekday])) {
                                foreach ($shedTemplate[$weekday] as $smena => $valueS) {
                                    //по филиалам
                                    foreach ($valueS as $filial => $valueF) {
                                        //Если филиал в массиве тех, которые хотим менять
                                        if (in_array($filial, $_POST['filials_arr'])) {
                                            foreach ($valueF as $type => $valueT) {
                                                foreach ($valueT as $kab => $valueK) {
                                                    //Смотрим нет ли такой записи
                                                    $workerHere = FALSE;

                                                    $query = "SELECT `worker` FROM `scheduler` WHERE `day`='{$i}' AND `month`='{$month}' AND `year`='{$year}' AND `smena`='{$smena}' AND `filial`='{$filial}' AND `kab`='{$kab}' AND `type`='{$type}'";

                                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                                    $number = mysqli_num_rows($res);

                                                    if ($number != 0) {
                                                        $workerHere = TRUE;
                                                    }
                                                    //var_dump($workerHere);

                                                    //Если есть тут запись
                                                    if ($workerHere) {
                                                        //.. но мы её игнорируем,
                                                        //то тупо удаляем и вставляем новую
                                                        if ($_POST['ignoreshed'] == 1) {
                                                            $query = "DELETE FROM `scheduler` WHERE `day`>='{$day}' AND `month`='{$month}' AND `year`='{$year}' AND (`type`='5' OR `type`='6' OR `type`='10')";

                                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                                            foreach ($valueK as $worker => $val) {
                                                                //Вставляем запись
                                                                $query = "INSERT INTO `scheduler` (`year`, `month`, `day`, `filial`, `kab`, `smena`, `smena_t`, `worker`, `type`)
                                                                VALUES
                                                                ('{$year}', '{$month}', '{$i}', '{$filial}', '{$kab}', '{$smena}', NULL, '{$worker}', '{$type}')";

                                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                                            }
                                                        } else {
                                                            $canUpdate = FALSE;
                                                            break 5;
                                                        }
                                                    } else {
                                                        foreach ($valueK as $worker => $val) {
                                                            //Вставляем запись
                                                            $query = "INSERT INTO `scheduler` (`year`, `month`, `day`, `filial`, `kab`, `smena`, `smena_t`, `worker`, `type`)
                                                            VALUES
                                                            ('{$year}', '{$month}', '{$i}', '{$filial}', '{$kab}', '{$smena}', NULL, '{$worker}', '{$type}')";

                                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            //}
                        }

                        CloseDB($msql_cnnct);

                        if (!$canUpdate) {
                            echo '
                                <div class="query_neok">
                                    График был заполнен ранее.<br><br>
                                </div>';
                        } else {
                            echo '
                                <div class="query_ok">
                                    График заполнен.<br><br>
                                </div>';
                            AddLog('0', $_SESSION['id'], '', 'График заполнен пользователем. Год [' . $year . ']. Месяц[' . $month . ']. С числа[' . $day . '].');
                        }
                    }
                }
            }
        }
	}
?>