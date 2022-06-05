<?php 

//individuals2_add_f.php
//Функция для добавления

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
        /*!!!Тест PDO*/
        include_once('DBWorkPDO.php');
		//var_dump ($_POST);

		if ($_POST){
            //Ищем работника
            $worker = SelDataFromDB ('spr_workers', $_POST['worker_name'], 'worker_full_name');
            if ($worker != 0){
                $worker_id = $worker[0]["id"];

                $time = date('Y-m-d H:i:s', time());

                $db = new DB();

                $plan_text = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['plan_text'])))));
                $rings_review = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['rings_review'])))));
                $work_w_patients = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['work_w_patients'])))));
                $error_correction = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['error_correction'])))));
                $ring_stat = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['ring_stat'])))));

                //Вставить запись в БД:
                $query = "INSERT INTO `journal_individuals2` (
                        `date`,
                        `worker_id`,
                        `plan`,
                        `rings_count`,
                        `rings_review`,
                        `work_w_patients`,
                        `error_correction`,
                        `ring_stat`,
                        `create_time`,
                        `create_person`
                        )
                        VALUES (
                        :date,
                        :worker_id,
                        :plan_text,
                        :rings_count,
                        :rings_review,
                        :work_w_patients,
                        :error_correction,
                        :ring_stat,
                        :create_time,
                        :create_person
                        )";

                $args = [
                    'date' => date('Y-m-d', strtotime($_POST['date'])),
                    'worker_id' => $worker_id,
                    'plan_text' => $plan_text,
                    'rings_count' => $_POST['rings_count'],
                    'rings_review' => $rings_review,
                    'work_w_patients' => $work_w_patients,
                    'error_correction' => $error_correction,
                    'ring_stat' => $ring_stat,
                    'create_time' => $time,
                    'create_person' => $_SESSION['id']
                ];

                $db::sql($query, $args);

                echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Запись добавлена</div>'));

            }else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">В нашей базе нет такого сотрудника</div>'));
            }

		}
	}
?>