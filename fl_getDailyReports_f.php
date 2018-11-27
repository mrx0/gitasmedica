<?php 

//fl_getDailyReports_f.php
//Функция для добавления ежежневного отчёта

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['date']) && isset($_POST['filial_id'])){
				include_once 'DBWork.php';

                //разбираемся с правами
                $god_mode = FALSE;

                require_once 'permissions.php';

                $rez = array();

                $msql_cnnct = ConnectToDB ();

                $data_temp_arr = explode(".", $_POST['date']);

                $d = $data_temp_arr[0];
                $m = $data_temp_arr[1];
                $y = $data_temp_arr[2];

                $query = "SELECT * FROM `fl_journal_daily_report` WHERE `filial_id`='{$_POST['filial_id']}' AND `year`='$y' AND `month`='$m' AND `day`='$d' AND `status` <> '9'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($rez, $arr);
                    }
                }

                CloseDB ($msql_cnnct);

                if (!empty($rez)){

                    //Смотрим даты и права на всякие действия и на посмотреть, если заднее число
                    if ($d.'.'.$m.'.'.$y == date('d.n.Y', time())) {
                        $a = true;
                        echo json_encode(array('result' => 'success', 'data' => $rez[0], 'count' => count($rez)));
                    }else{
                        //Если есть права, то возвращаем данные
                        if (($finances['add_new'] == 1) || $god_mode) {
                            echo json_encode(array('result' => 'success', 'data' => $rez[0], 'count' => count($rez)));
                        //Если нет прав, то возвращаем пустой массив, но указываем кол-во элементов в нём
                        }else{
                            echo json_encode(array('result' => 'success', 'data' => array(), 'count' => count($rez)));
                        }
                    }
                }else{

                    echo json_encode(array('result' => 'success', 'data' => '', 'count' => 0));
                }


			}else{
                echo json_encode(array('result' => 'success', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'count' => 0));
			}
		}
	}
?>