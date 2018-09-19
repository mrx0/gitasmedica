<?php 

//fl_addCalcsIDsINSessionForReload.php
//!!! НЕ используем пока нигде

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

        if (isset($_POST['calcArr'])){
            if (!empty($_POST['calcArr'])){
                if (!empty($_POST['calcArr']['main_data'])) {

                    $_SESSION['fl_calcs_4reload'] = array();

                    $tempArr = explode('_', $_POST['calcArr']['data']);

                    $type = $tempArr[1];
                    $worker_id = $tempArr[2];
                    $filial_id = $tempArr[3];

                    $_SESSION['fl_calcs_4reload'] = $_POST['calcArr'];

                    foreach ($_POST['calcArr'] as $calcID){

                    }
                }
            }
        }

        if (!empty($_SESSION['fl_calcs_tabels'])) {
            echo json_encode(array('result' => 'success', 'data' => $tempArr));
        }

	}
?>