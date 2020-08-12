<?php 

//ajax_scheduler3_add_f.php
//Добавление (редактирование) графика
//Для изменения графика админов, ассистентов, ...  (scheduler3.php)

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){

			$temp_arr = array();

			if (!isset($_POST['filial_id']) || !isset($_POST['month']) || !isset($_POST['year']) || !isset($_POST['type'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//var_dump($_SESSION['invoice_data'][$_POST['client']][$_POST['zapis_id']]['data'][$_POST['zub']][$_POST['key']]);

                include_once 'DBWork.php';

                if (isset($_SESSION['scheduler3'])) {
                    if (isset($_SESSION['scheduler3'][$_POST['filial_id']])) {
                        if (isset($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']])) {
                            if (isset($_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']])) {

                                $data = $_SESSION['scheduler3'][$_POST['filial_id']][$_POST['year']][$_POST['month']];

                                $query_res = '';
                                //$query_count = 0;

                                if (!empty($data)) {

                                    $msql_cnnct = ConnectToDB();

                                    //2019.02.14 сначала хотел удалять все рабочие дни человека за конкретный день перед обновлением,
                                    //но может оказаться, что человек работает в этот день по полсмены в двух, например, местах..
                                    //поэтому гг

                                    //Проход по массиву и выполнение запросов
                                    foreach ($data as $day => $sch_item){
                                        foreach ($sch_item as $worker_id => $selected){
                                            if (($selected == 0) || ($selected == 2)){
                                                //Удаляем отметку о рабочей смене
                                                $query = "DELETE FROM `scheduler` WHERE `worker`='{$worker_id}' AND `filial`='{$_POST['filial_id']}' AND `year`='{$_POST['year']}' AND `month`='{$_POST['month']}' AND `day`='{$day}';";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                            }
                                            if (($selected == 1) || ($selected == 3)){
                                                //Добавляем отметку о рабочей смене
                                                //Если раздел "другие"
                                                if ($_POST['type'] == 99){

                                                    $query = "SELECT `permissions` FROM `spr_workers` WHERE `id` = '$worker_id';";
                                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                                    $arr = mysqli_fetch_assoc($res);
                                                    $type = $arr['permissions'];

                                                }else{
                                                    //Eсли все остальное
                                                    $type = $_POST['type'];
                                                }
                                                $query = "INSERT INTO `scheduler` (`worker`, `filial`, `year`, `month`, `day`, `type`, `create_person`) 
						                        VALUES (
                        						'{$worker_id}', '{$_POST['filial_id']}', '{$_POST['year']}', '{$_POST['month']}', '{$day}', '{$type}', '{$_SESSION['id']}');";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                            }


                                        }
                                    }

                                    CloseDB ($msql_cnnct);

                                    echo json_encode(array('result' => 'success', 'data' => $query_res));











                                }else{
                                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                                }
                            }else{
                                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                            }
                        }else{
                            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                        }
                    }else{
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                    }
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
			}
		}
	}
?>