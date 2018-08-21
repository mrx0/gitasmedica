<?php 

//fl_deleteCalcsByIDsFromDB.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

        if (isset($_POST['calcArr'])){
            if (!empty($_POST['calcArr'])){

                include_once 'DBWork.php';

                $msql_cnnct = ConnectToDB ();

                $calcsArrForDelete = implode(",", $_POST['calcArr']);

                //
                $query = "DELETE FROM `fl_journal_calculate` WHERE `id` IN ($calcsArrForDelete)";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                //
                $query = "DELETE FROM `fl_journal_calculate_ex` WHERE `calculate_id` IN ($calcsArrForDelete)";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                echo json_encode(array('result' => 'success', 'data' => $query));
            }
        }
	}
?>