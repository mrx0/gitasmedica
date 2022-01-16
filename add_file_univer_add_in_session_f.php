<?php 

//add_file_univer_add_in_session_f.php
//Добавление информации о файле в сессию при добавлении задания для UNIVER

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
            if (!isset($_POST['id']) || !isset($_POST['orig_name']) || !isset($_POST['new_name']) || !isset($_POST['extension'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                $time = date('Y-m-d H:i:s', time());

                //Что будем добавлять
//                $theme = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['theme'])))));
//                $descr = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['descr'])))));
//                $workers = $_POST['workers'];
//                if (!empty($_POST['workers'])) {
//                    $workers = array_unique($workers);
//                }
//                $workers_type = $_POST['workers_type'];
//                $filials = $_POST['filial'];

                if (isset($_SESSION['univer'])) {

                    $_SESSION['univer']['file_data']['id'] = $_POST['id'];
                    $_SESSION['univer']['file_data']['name'] = $_POST['orig_name'];
                    $_SESSION['univer']['file_data']['path_name'] = $_POST['new_name'].'.'.$_POST['extension'];
                    $_SESSION['univer']['file_data']['ext'] = $_POST['extension'];

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Ожидайте...</div>'));
                }
			}
		}
	}
?>