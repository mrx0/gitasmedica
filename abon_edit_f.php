<?php 

//abon_edit_f.php
//Функция редактирования абонемента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			if (!isset($_POST['abon_id']) || !isset($_POST['num']) || !isset($_POST['abon_type'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
                include_once 'DBWork.php';

                $abonement_j = SelDataFromDB('journal_abonement_solar', $_POST['abon_id'], 'id');

                if ($abonement_j != 0) {

                    //Соберем данные по типу абонемента
                    $abon_types_j = array();

                    $msql_cnnct = ConnectToDB ();

                    $query = "SELECT * FROM `spr_solar_abonements` WHERE `id` = '{$_POST['abon_type']}' LIMIT 1";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        $arr = mysqli_fetch_assoc($res);

                        $min_count = $arr['min_count'];
                        $exp_days = $arr['exp_days'];
                        $summ = $arr['summ'];


                        $old = 'Абонемент. Номер [' . $abonement_j[0]['num'] . ']. Тип: [' . $abonement_j[0]['abon_type'] . '].';

                        //А нет ли уже такого номера в базе?
                        $query = "SELECT * FROM `journal_abonement_solar` WHERE `num`='{$_POST['num']}' AND `id`<>'{$_POST['abon_id']}'";
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0) {
                            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Абонемент с таким номером уже присутствует в базе.</div>'));
                        } else {
                            $time = date('Y-m-d H:i:s', time());

                            //Обновляем
                            $query = "UPDATE `journal_abonement_solar` SET  `num`='{$_POST['num']}', `abon_type`='{$_POST['abon_type']}', `min_count`='{$min_count}', `exp_days`='{$exp_days}', `summ`='{$summ}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$_POST['abon_id']}'";
                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            //логирование
                            AddLog(GetRealIp(), $_SESSION['id'], $old, 'Изменён абонемент [' . $_POST['abon_id'] . ']. [' . $time . ']. Номер [' . $_POST['num'] . ']. Тип: [' . $_POST['abon_type'] . '].');

                            echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok"><a href="abonement.php?id=' . $_POST['abon_id'] . '" class="ahref">Абонемент</a> обновлён.</div>'));
                        }
                    }

                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
			}
		}
	}
?>