<?php 

//fl_editLampReport_add_f.php
//Функция для редактирования ежедневного отчёта по счётчика ламп

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
            if (isset($_POST['day']) && isset($_POST['month']) && isset($_POST['year']) && isset($_POST['filial_id']) && isset($_POST['dataLamp'])){

                if (!empty($_POST['dataLamp'])) {
                    include_once('DBWorkPDO.php');

                    $time = time();

                    $db = new DB();

                    $create_time = date('Y-m-d H:i:s', time());

                    //Смотрим не было ли уже отчета на этом филиале за этот день
                    $args = [
                        'filial_id' => $_POST['filial_id'],
                        'd' => $_POST['day'],
                        'm' => $_POST['month'],
                        'y' => $_POST['year']
                    ];

                    //!!!!!Сначала удалим, что было
                    $query = "DELETE FROM `fl_journal_lamp_report` WHERE `filial_id`=:filial_id AND `day`=:d AND `month`=:m AND `year`=:y";

                    $db::sql($query, $args);

                    //Теперь добавим новое
                    foreach ($_POST['dataLamp'] as $id => $data) {
                        foreach ($data as $e => $count) {
                            $args = [
                                'filial_id' => $_POST['filial_id'],
                                'd' => $_POST['day'],
                                'm' => $_POST['month'],
                                'y' => $_POST['year'],
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

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Отчёт обновлён</div>'));

			    }
			}else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}
		}
	}
?>