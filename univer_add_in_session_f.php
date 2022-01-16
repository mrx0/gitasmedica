<?php 

//univer_add_in_session_f.php
//Добавление в сессию доп. данных для дальнейшей проверки и добавления в базу

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
            if (!isset($_POST['descr']) || !isset($_POST['workers']) || !isset($_POST['workers_type']) || !isset($_POST['filial'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                $time = date('Y-m-d H:i:s', time());

                //Что будем добавлять
                $theme = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['theme'])))));
                $descr = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['descr'])))));
                $workers = $_POST['workers'];
                if (!empty($_POST['workers'])) {
                    $workers = array_unique($workers);
                }
                $workers_type = $_POST['workers_type'];
                $filials = $_POST['filial'];

                if (isset($_SESSION['univer'])) {

                    $task = array();

                    $task['id'] = $_SESSION['univer']['id'];
                    $task['file_data'] = $_SESSION['univer']['file_data'];
                    $task['theme'] = $theme;
                    $task['descr'] = $descr;
                    $task['workers'] = $workers;
                    $task['workers_type'] = $workers_type;
                    $task['filial'] = $filials;
                    $task['status'] = $_SESSION['univer']['status'];

                    $_SESSION['univer'] = $task;

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Ожидайте...</div>'));
                }
			}
		}
	}
?>