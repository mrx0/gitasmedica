<?php 

//ajax_tempzapis_edit_enter_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
        //var_dump ($_POST);

        include_once 'DBWork.php';

        $data = '';

		if ($_POST){
			/*if ($_POST['datatable'] == 'scheduler_stom'){
				$datatable = 'zapis_stom';
			}elseif ($_POST['datatable'] == 'scheduler_cosm'){
				$datatable = 'zapis_cosm';
			}else{
				$datatable = 'zapis_stom';
			}*/

            $msql_cnnct = ConnectToDB ();

			$time = time();

            //Расчетные листы
            $query = "SELECT COUNT(*) AS total FROM(
                      SELECT `id` FROM `journal_invoice` WHERE `zapis_id`={$_POST['id']} AND `status` <> '9'
                      UNION ALL
                      SELECT `id` FROM `journal_tooth_status` WHERE `zapis_id`={$_POST['id']} AND `status` <> '9'
                      UNION ALL
                      SELECT `id` FROM `journal_cosmet1` WHERE `zapis_id`={$_POST['id']} AND `status` <> '9'
                      ) rez";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $arr = mysqli_fetch_assoc($res);

            if ($arr['total'] == 0) {
                $query = "UPDATE `zapis` SET `enter`='{$_POST['enter']}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$_POST['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                echo json_encode(array('result' => 'success', 'data' => $data, 'search_error' => 0));
            }else{
                $data = 'Уже созданы документы на основании записи, нельзя изменить статус!';
                echo json_encode(array('result' => 'error', 'data' => $data, 'search_error' => 1));
            }
		}else{
            echo json_encode(array('result' => 'error', 'data' => $data, 'search_error' => 0));
        }
	}
?>