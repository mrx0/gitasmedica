<?php 

//fl_createSchedulerReport_add_f.php
//Функция для добавления рабочих часов сотрудников на филиале

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['date']) && isset($_POST['filial_id']) && isset($_POST['workers_hours_data']) && isset($_POST['workers_types_data'])){

                include_once 'DBWork.php';

                $time = time();

                $msql_cnnct = ConnectToDB();

                $data_temp_arr = explode(".", $_POST['date']);

                $d = $data_temp_arr[0];
                $m = $data_temp_arr[1];
                $y = $data_temp_arr[2];

                //Смотрим не было ли уже отчета на этом филиале за этот день
                $dailyReports_j = array();

                $query = "SELECT * FROM `fl_journal_scheduler_report` WHERE `filial_id`='{$_POST['filial_id']}' AND `day`='{$d}' AND  `month`='$m' AND  `year`='$y'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($dailyReports_j, $arr);
                    }
                }


                if (empty($dailyReports_j)) {

                    $create_time = date('Y-m-d H:i:s', time());

                    //Пройдемся по всем полученным данным с часами сотрудников
                    foreach ($_POST['workers_hours_data'] as $worker_id => $hours){

                        $type = $_POST['workers_types_data'][$worker_id];
                        //округляем любую дробную часть до 0.5
                        $hours_cel = floor($hours);
                        $hours_drob = $hours - $hours_cel;
                        if ($hours_drob >= 0.5){
                            $hours = $hours_cel + 0.5;
                        }else{
                            $hours = $hours_cel;
                        }


                        $query = "INSERT INTO `fl_journal_scheduler_report`
                        (`filial_id`, `worker_id`, `type`, `day`, `month`, `year`, `hours`, `create_time`, `create_person`)
                        VALUES ('{$_POST['filial_id']}', '{$worker_id}', '{$type}', '{$d}', '{$m}', '{$y}', '{$hours}', '{$create_time}', '{$_SESSION['id']}');";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                    }

                    //логирование
                    //AddLog ('0', $_SESSION['id'], '', 'Добавлен долг #'.$mysql_insert_id.'. Пациент ['.$_POST['client'].']. Сумма ['.$_POST['summ'].']. Срок истечения ['.$_POST['date_expires'].']. Тип ['.$_POST['type'].']. Комментарий ['.$_POST['comment'].'].');



                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Отчёт сформирован и отправлен</div>'));
                } else {
                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_neok">Отчёт за указаную дату для этого филиала уже был сформирован.</div>'));
                }

                CloseDB($msql_cnnct);

                //echo json_encode(array('result' => 'success', 'data' => gettype($_POST['workers_hours_data'])));

			}else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #21. Что-то пошло не так</div>'));
			}
		}
	}
?>