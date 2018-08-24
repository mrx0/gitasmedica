<?php 

//fl_addCalcsIDsINSessionForTabel.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

        if (isset($_POST['calcArr'])){

            $_SESSION['fl_calcs_tabels'] = array();

            if (!empty($_POST['calcArr'])){
                $_SESSION['fl_calcs_tabels'] = $_POST['calcArr'];

                /*foreach ($_POST['calcArr'] as $calcID){
                    array_push($_SESSION['fl_calcs_tabels'], $calcID);
                }*/
            }
        }

        if (!empty($_SESSION['fl_calcs_tabels'])) {
            echo json_encode(array('result' => 'success', 'data' => $_SESSION['fl_calcs_tabels']));
        }


	}
?>