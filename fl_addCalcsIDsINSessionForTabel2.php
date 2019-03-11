<?php 

//fl_addCalcsIDsINSessionForTabel2.php
//Добавление ID расчетных листов в сессию для дальнейшего добавления их в Табель

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

        if (isset($_POST['calc_id_arr']) && isset($_POST['add_status']) && isset($_POST['type']) && isset($_POST['worker_id']) && isset($_POST['filial_id'])){

            //$_SESSION['fl_calcs_tabels2'] = array();

            if (!empty($_POST['calc_id_arr'])) {
                if (!isset($_SESSION['fl_calcs_tabels2'])) {
                    $_SESSION['fl_calcs_tabels2'] = array();
                }
                if (!isset($_SESSION['fl_calcs_tabels2']['data'])) {
                    $_SESSION['fl_calcs_tabels2']['data'] = array();
                }

                foreach ($_POST['calc_id_arr'] as $calc_id) {
                    if (!isset($_SESSION['fl_calcs_tabels2']['data'][$calc_id])) {
                        if ($_POST['add_status'] == 1) {
                            $_SESSION['fl_calcs_tabels2']['data'][$calc_id] = 1;
                            $_SESSION['fl_calcs_tabels2']['type'] = $_POST['type'];
                            $_SESSION['fl_calcs_tabels2']['worker_id'] = $_POST['worker_id'];
                            $_SESSION['fl_calcs_tabels2']['filial_id'] = $_POST['filial_id'];
                        }
                    } else {
                        if ($_POST['add_status'] == 1) {
                            $_SESSION['fl_calcs_tabels2']['data'][$calc_id] = 1;
                            $_SESSION['fl_calcs_tabels2']['type'] = $_POST['type'];
                            $_SESSION['fl_calcs_tabels2']['worker_id'] = $_POST['worker_id'];
                            $_SESSION['fl_calcs_tabels2']['filial_id'] = $_POST['filial_id'];
                        } else {
                            unset($_SESSION['fl_calcs_tabels2']['data'][$calc_id]);
                        }
                    }
                }

//                if (empty($_SESSION['fl_calcs_tabels2'])) {
//                    unset($_SESSION['fl_calcs_tabels2']);
//                }

                if (!empty($_SESSION['fl_calcs_tabels2'])) {
                    echo json_encode(array('result' => 'success', 'isset' => TRUE));
                } else {
                    unset($_SESSION['fl_calcs_tabels2']);
                    echo json_encode(array('result' => 'success', 'isset' => FALSE));
                }
            }else{
                unset($_SESSION['fl_calcs_tabels2']);
                echo json_encode(array('result' => 'success', 'isset' => FALSE));
            }
        }else{
            unset($_SESSION['fl_calcs_tabels2']);
            echo json_encode(array('result' => 'success', 'isset' => FALSE));
        }
	}
?>