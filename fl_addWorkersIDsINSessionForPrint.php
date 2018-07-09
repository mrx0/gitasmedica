<?php 

//fl_addWorkersIDsINSessionForPrint.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

        $_SESSION['fl_workers_id'] = array();

        if (isset($_POST['workersIDarr'])){
            if (!empty($_POST['workersIDarr'])){
                $_SESSION['fl_workers_id'] = $_POST['workersIDarr'];
            }
        }

        if (!empty($_SESSION['fl_workers_id'])) {
            echo json_encode(array('result' => 'success', 'data' => $_SESSION['fl_workers_id']));
        }


	}
?>