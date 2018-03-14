<?php 

//fl_delete_material_consumption_f.php
//Функция для Удаления затрат наматериалы

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		include_once 'functions.php';
		include_once 'ffun.php';
		if ($_POST){

            if (!isset($_POST['mat_cons_id']) || !isset($_POST['invoice_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                //Проверки, проверочки
                include_once 'DBWork.php';

                //!!! @@@
                include_once 'ffun.php';

                $time = date('Y-m-d H:i:s', time());

                $msql_cnnct = ConnectToDB2 ();



                $mat_cons_j_ex = array();

                //Затраты на материалы
                $query = "SELECT jimc.*, jimcex.*, jimc.id as mc_id, jimc.summ as all_summ FROM `journal_inv_material_consumption` jimc
                                LEFT JOIN `journal_inv_material_consumption_ex` jimcex
                                ON jimc.id = jimcex.inv_mat_cons_id
                                WHERE jimc.invoice_id = '".$_POST['invoice_id']."';";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {

                        //array_push($mat_cons_j, $arr);

                        if (!isset($mat_cons_j_ex['data'])){
                            $mat_cons_j_ex['data'] = array();
                        }

                        if (!isset($mat_cons_j_ex['data'][$arr['inv_pos_id']])){
                            $mat_cons_j_ex['data'][$arr['inv_pos_id']] = $arr['summ'];
                        }

                        $mat_cons_j_ex['create_person'] = $arr['create_person'];
                        $mat_cons_j_ex['create_time'] = $arr['create_time'];
                        $mat_cons_j_ex['all_summ'] = $arr['all_summ'];
                        $mat_cons_j_ex['descr'] = $arr['descr'];
                        $mat_cons_j_ex['id'] = $arr['mc_id'];
                    }
                } else {

                }

                //Удаляем из БД
                $query = "DELETE FROM `journal_inv_material_consumption` WHERE `id`='{$_POST['mat_cons_id']}' AND `invoice_id`='{$_POST['invoice_id']}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $query = "DELETE FROM `journal_inv_material_consumption_ex` WHERE `inv_mat_cons_id`='{$_POST['mat_cons_id']}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                //Обновим расчётный лист
                fl_updateCalculatesData ($_POST['invoice_id'], $mat_cons_j_ex, true);


                echo json_encode(array('result' => 'success', 'data' => 'Затраты удалены'));

            }
        }

	}
	
?>