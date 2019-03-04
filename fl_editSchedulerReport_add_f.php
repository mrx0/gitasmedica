<?php 

//fl_editSchedulerReport_add_f.php
//Функция для редактирования рабочих часов сотрудников на филиале

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
            if (isset($_POST['report_ids']) && isset($_POST['date']) && isset($_POST['filial_id']) && isset($_POST['workers_hours_data']) && isset($_POST['workers_types_data'])){

                include_once 'DBWork.php';

                $time = time();

                $data_temp_arr = explode(".", $_POST['date']);

                $d = $data_temp_arr[0];
                $m = $data_temp_arr[1];
                $y = $data_temp_arr[2];

                //Разберем $_GET['report_id'] в массив id-шников
                $report_ids_arr = explode(',', $_POST['report_ids']);
                //var_dump($report_ids_arr);

                //Уберем пустые значения
                $report_ids_arr = array_diff($report_ids_arr, array(''));
                //var_dump($report_ids_arr);

                //Смотрим не было ли уже отчета на этом филиале за этот день
                $dailyReports_j = array();

                $msql_cnnct = ConnectToDB();

                //Соберем строку для запроса
                $dop_query = implode(' OR `id` = ', $report_ids_arr);

                $dop_query = '`id` = '.$dop_query;

                //Удаляем из БД связки РЛ и табелей
                $query = "DELETE FROM `fl_journal_scheduler_report` WHERE {$dop_query};";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);


                $create_time = date('Y-m-d H:i:s', time());

                //Пройдемся по всем полученным данным с часами сотрудников
                foreach ($_POST['workers_hours_data'] as $worker_id => $hours){

                    $type = $_POST['workers_types_data'][$worker_id];

                    $query = "INSERT INTO `fl_journal_scheduler_report`
                    (`filial_id`, `worker_id`, `type`, `day`, `month`, `year`, `hours`, `create_time`, `create_person`)
                    VALUES ('{$_POST['filial_id']}', '{$worker_id}', '{$type}', '{$d}', '{$m}', '{$y}', '{$hours}', '{$create_time}', '{$_SESSION['id']}');";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                }

                //логирование
                //AddLog ();



                echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Отчёт обновлён</div>'));


                //CloseDB($msql_cnnct);

			}else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}
		}
	}
?>