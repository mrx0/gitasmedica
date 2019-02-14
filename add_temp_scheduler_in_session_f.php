<?php 

//add_temp_scheduler_in_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){
			if (!isset($_POST['worker_id']) || !isset($_POST['filial_id']) || !isset($_POST['day']) || !isset($_POST['month']) || !isset($_POST['year']) || !isset($_POST['selected'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                if (!isset($_SESSION['scheduler3'])){
                    $_SESSION['scheduler3'] = array();
                }
                if (!isset($_SESSION['scheduler3'][$_POST['filial_id']])){
                    $_SESSION['scheduler3'][$_POST['filial_id']] = array();
                }
                if (!isset($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']])){
                    $_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']] = array();
                }
                if (!isset($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']])){
                    $_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']] = array();
                }
                if (!isset($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']][$_POST['day']])){
                    $_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']][$_POST['day']] = array();
                }

                if (!isset($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']][$_POST['day']][$_POST['worker_id']])){
                    $_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']][$_POST['day']][$_POST['worker_id']] = $_POST['selected'];
                }else{
                    //if ($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']][$_POST['day']][$_POST['worker_id']] == $_POST['selected']){
                        unset($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']][$_POST['day']][$_POST['worker_id']]);
                    //}
                }

                if (empty($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']][$_POST['day']])){
                    unset($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']][$_POST['day']]);
                }
                if (empty($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']])){
                    unset($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']]);
                }
                if (empty($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']])){
                    unset($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']]);
                }
                if (empty($_SESSION['scheduler3'][$_POST['filial_id']])){
                    unset($_SESSION['scheduler3'][$_POST['filial_id']]);
                }
                if (empty($_SESSION['scheduler3'])){
                    unset($_SESSION['scheduler3']);
                }

                if (!empty($_SESSION['scheduler3'])) {
                    echo json_encode(array('result' => 'success', 'isset' => TRUE));
                }else{
                    echo json_encode(array('result' => 'success', 'isset' => FALSE));
                }
			}
		}
	}
?>