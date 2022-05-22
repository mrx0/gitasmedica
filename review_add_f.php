<?php 

//review_add_f.php
//Функция для добавления отзыва

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

                //Ищем добавившего
                $added = SelDataFromDB ('spr_workers', $_POST['added_name'], 'worker_full_name');
                if ($added != 0){
                    $added_person = $added[0]["id"];

                        $time = date('Y-m-d H:i:s', time());

                        $db = new DB();

                        $review_text = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['review_text'])))));
                        $sites = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['sites'])))));

                        //Вставить запись в БД:
                        $query = "INSERT INTO `journal_reviews` (
                                `date`,
                                `filial_id`,
                                `worker_id`,
                                `review_text`,
                                `added_person`,
                                `sites`,
                                `status`,
                                `create_time`,
                                `create_person`
                                )
                                VALUES (
                                :date,
                                :filial_id,
                                :worker_id,
                                :review_text,
                                :added_person,
                                :sites,
                                :status,
                                :create_time,
                                :create_person
                                )";

                        $args = [
                            'date' => date('Y-m-d', strtotime($_POST['date'])),
                            'filial_id' => $_POST['filial_id'],
                            'worker_id' => $worker_id,
                            'review_text' => $review_text,
                            'added_person' => $added_person,
                            'sites' => $sites,
                            'status' => $_POST['status'],
                            'create_time' => $time,
                            'create_person' => $_SESSION['id']
                        ];

                        $db::sql($query, $args);

                        echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Запись добавлена</div>'));

                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">В нашей базе нет такого сотрудника  (кто добавил)</div>'));
                }

            }else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">В нашей базе нет такого сотрудника (врач)</div>'));
            }

		}
	}
?>