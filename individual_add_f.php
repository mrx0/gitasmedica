<?php 

//individual_add_f.php
//Функция для добавления обсуждения

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
        /*!!!Тест PDO*/
        include_once('DBWorkPDO.php');
		//var_dump ($_POST);

		if ($_POST){
			if ($_POST['worker'] == ''){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Не выбрали исполнителя. Попробуйте еще раз</div>'));
			}else{
				//Ищем работника
				$workers = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');
				if ($workers != 0){
					$worker_id = $workers[0]["id"];

                    if ($_POST['theme'] == ''){
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Не заполнили тему. Попробуйте еще раз</div>'));
                    }else {

                        $time = date('Y-m-d H:i:s', time());

                        $db = new DB();

                        $theme = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['theme'])))));

                        //Вставить запись в БД:
                        $query = "INSERT INTO `journal_individuals` (
                                `author_id`,
                                `student_id`,
                                `theme`,
                                `date`,
                                `create_time`,
                                `create_person`
                                )
                                VALUES (
                                :author_id,
                                :student_id,
                                :theme,
                                :date,
                                :create_time,
                                :create_person
                                )";

                        $args = [
                            'author_id' => $_SESSION['id'],
                            'student_id' => $worker_id,
                            'theme' => $theme,
                            'date' => date('Y-m-d', strtotime($_POST['date'])),
                            'create_time' => $time,
                            'create_person' => $_SESSION['id']
                        ];

                        $db::sql($query, $args);

                        echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Запись добавлена</div>'));
                    }
				}else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">В нашей базе нет такого сотрудника</div>'));
				}

			}
		}
	}
?>