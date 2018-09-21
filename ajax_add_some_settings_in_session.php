<?php 

//ajax_add_some_settings_in_session.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
        //var_dump ($_POST);
        if ($_POST) {
            if (!isset($_POST['doc_name'])) {

            }else{
                /*include_once 'DBWork.php';
                include_once 'functions.php';*/

                if (isset($_SESSION['options'])){
                    $_SESSION['options'][$_POST['doc_name']]['manage'] = $_POST['manage'];
                    echo json_encode(array('result' => 'success', 'data' => $_POST['manage']));
                }

            }

        }

	}
?>