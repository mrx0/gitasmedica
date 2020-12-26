<?php 

//remark_to_employee_add_f.php
//Функция добавления нового замечания сотруднику в базу

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
            include_once('DBWorkPDO.php');
            //include_once 'DBWork.php';
            include_once 'functions.php';

            //разбираемся с правами
            $god_mode = FALSE;

            require_once 'permissions.php';

			$temp_arr = array();
			
			if (!isset($_POST['date_in']) || !isset($_POST['worker']) || !isset($_POST['comment'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                //$msql_cnnct = ConnectToDB();
                $db = new DB();

                $time = date('Y-m-d H:i:s', time());
                $date_in = date('Y-m-d', strtotime($_POST['date_in']." 09:00:00"));

                //Попытаемся получить worker_id по ФМО из $_POST['worker']
                $query = "SELECT `id` FROM `spr_workers` WHERE `full_name`='{$_POST['worker']}' LIMIT 1";

                $worker_id = $db::getValue($query, []);
                //var_dump($worker_id);
                //echo json_encode(array('result' => 'success', 'data' => $worker_id));

                //Если есть сотрудник
                if ($worker_id > 0) {

                    $comment = addslashes($_POST['comment']);

                    //Добавляем в базу
                    $query = "INSERT INTO `journal_remark_to_employee` (`worker_id`, `date_in`, `create_person`, `create_time`, `descr`)
                            VALUES (
                            '{$worker_id}', '{$date_in}', '{$_SESSION['id']}', '{$time}', '{$comment}')";

                    $args = [
                    ];

                    $db::sql($query, $args);

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Замечание добавлено.</div>'));
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #78. В базе нет такого сотрудника.</div>'));
                }
			}
		}
	}
?>