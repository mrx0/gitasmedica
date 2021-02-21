<?php 

//fl_createLampReport_add_f.php
//Функция для добавления ежедневного отчёта по счётчика ламп

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
//		var_dump ($_POST);

		if ($_POST){
			if (isset($_POST['date']) && isset($_POST['filial_id']) && isset($_POST['dataLamp'])){

                if (!empty($_POST['dataLamp'])) {
                    include_once('DBWorkPDO.php');

                    $time = time();

                    $db = new DB();

                    $data_temp_arr = explode(".", $_POST['date']);

                    $d = $data_temp_arr[0];
                    $m = $data_temp_arr[1];
                    $y = $data_temp_arr[2];

                    //Смотрим не было ли уже отчета на этом филиале за этот день
                    $args = [
                        'filial_id' => $_POST['filial_id'],
                        'd' => $d,
                        'm' => $m,
                        'y' => $y
                    ];

                    $query = "SELECT * FROM `fl_journal_lamp_report` WHERE `filial_id`=:filial_id AND `day`=:d AND  `month`=:m AND  `year`=:y";

                    $dailyReports_j = $db::getRows($query, $args);

                    if (empty($dailyReports_j)) {

                        $create_time = date('Y-m-d H:i:s', time());

                        foreach ($_POST['dataLamp'] as $id => $data) {
                            foreach ($data as $e => $count) {
                                $args = [
                                    'filial_id' => $_POST['filial_id'],
                                    'd' => $d,
                                    'm' => $m,
                                    'y' => $y,
                                    'lamp_id' => $id,
                                    'counts' => $count,
                                    'create_time' => $create_time,
                                    'create_person' => $_SESSION['id']
                                ];
                                if ($e == 'e') {
                                    $args['evening'] = 1;
                                } else {
                                    $args['evening'] = 0;
                                }

                                $query = "INSERT INTO `fl_journal_lamp_report`
                                (`filial_id`, `day`, `month`, `year`, `evening`,
                                `lamp_id`, `count`,
                                `create_time`, `create_person`)
                                VALUES (:filial_id, :d, :m, :y, :evening, :lamp_id, :counts, :create_time, :create_person);";

                                $db::sql($query, $args);
                            }
                        }

                        echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Отчёт сформирован и отправлен</div>'));
                    } else {
                        echo json_encode(array('result' => 'success', 'data' => '<div class="query_neok">Отчёт за указаную дату для этого филиала уже был сформирован.</div>'));
                    }
                }
			}else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #79. Что-то пошло не так</div>'));
			}
		}
	}
?>