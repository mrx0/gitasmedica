<?php

//dentalpro_api.php
//Тест API DentalPro

    require_once 'header.php';
    require_once 'blocks_dom.php';

    if ($enter_ok){
            require_once 'header_tags.php';

            //if (($clients['see_all'] == 1) || ($clients['see_own'] == 1) || $god_mode){

//        if (isset($_POST['type'])){
            //$_POST['type'] = 5;

            $rezult = '';
            $rezult_arr = array();

            require 'config_dentalpro_api.php';

            include_once('DBWorkPDO.php');

            $db = new DB();

//            $zapis_id = $task[0]['zapis_id'];
//
//            $args = [
//                'zapis_id' => $zapis_id
//            ];
//
//            //Получаем данные по записи
//            //Получаем данные по записи
//            $query = "SELECT * FROM `zapis` WHERE `id`=:zapis_id LIMIT 1";
//
//            //Выбрать все
//            $sheduler_zapis = $db::getRows($query, $args);
//            //var_dump($sheduler_zapis);

//            $arr = array();
//
//            $query = "SELECT `value` FROM `settings` WHERE `option`='".$last_id_zapis_option."'";
//
//            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//            $number = mysqli_num_rows($res);
//            if ($number != 0){
//                $arr = mysqli_fetch_assoc($res);
//
//                $last = $arr['value'];
//                //var_dump($last);
//
//                $token = $token_4zapis;
//                //var_dump($token);

                $dopURL_api = 'lm/appointments';

                $date_from = date('Y-m-d', time());
                $date_to = date('Y-m-d', time());



                $query_req_str = 'date_from='.$date_from.'&date_to='.$date_to;


                $query = $URL_api . $dopURL_api . '?token=' . $token_api . '&' . 'secret=' . $secret_api . '&' . $query_req_str;
                var_dump($query);

                $ch = curl_init();
                //var_dump($ch);

                curl_setopt($ch, CURLOPT_URL, $query);

                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);

                $rezult = curl_exec($ch);
                //echo 'Ошибка curl: ' . curl_error($ch);
                //var_dump($rezult);

                $rezult_arr = json_decode($rezult, true);

                curl_close($ch);

                if (!empty($rezult_arr)) {
//                    var_dump($rezult_arr);

                    echo 'Всего записей: '.$rezult_arr['total'].'<br>';
                    echo 'Загружено: '.count($rezult_arr['data']).'<br>';

                    echo '
				
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';

                    echo '
							<li class="cellsBlock cellsBlockHover" style="font-weight:bold;">';
                    echo '
								<div class="cellFullName ahref">Пациент</div>';
                    echo '
								<div class="cellFullName ahref">Врач</div>';
                    echo '
								<div class="cellFullName ahref">Начало - Конец</div>';
                    echo '
								<div class="cellFullName ahref">Статус посещения</div>';
                    echo '
                            </li>';

                    foreach ($rezult_arr['data'] as $zapis_val){
                        echo '
							<li class="cellsBlock cellsBlockHover" style="">';
                        echo '
								<div class="cellFullName ahref">'.$zapis_val['client_id'].'</div>';
                        echo '
								<div class="cellFullName ahref">'.$zapis_val['doctor_id'].'</div>';
                        echo '
								<div class="cellFullName ahref">'.date('H:i', strtotime($zapis_val['start_time'])).' - '.date('H:i', strtotime($zapis_val['finish_time'])).'</div>';
                        echo '
								<div class="cellFullName ahref">'.$zapis_val['visited'].'</div>';
                        echo '
                            </li>';

                    }

                    echo '
                        </ul>
                        <div id="doc_title">DentalPro API</div>
				    </div>';


                    //echo json_encode(array('result' => 'success', 'data' => $query));



                    var_dump($rezult_arr);
                }else{
                    //echo json_encode(array('result' => 'success', 'data' => 0));
                }

//            }
//
//            //Выборка
////            if (empty($_SESSION['filial'])){
////                $query = "SELECT COUNT(*) AS total FROM `zapis_online` WHERE `status` <> '7' AND `status` <> '6' ";
////            }else{
////                $query = "SELECT COUNT(*) AS total FROM `zapis_online` WHERE `status` <> '7' AND `status` <> '6' AND `place` = '".$_SESSION['filial']."'";
////            }
//
//            $dop = '';
//            if (isset($_SESSION['filial'])){
//                $dop = "WHERE `place`='".$_SESSION['filial']."'";
//
//                //Если энгельса, то плюсуем пустые (без филиала)
//                if ($_SESSION['filial'] == 15){
//                    $dop = "WHERE (`place`='".$_SESSION['filial']."' OR `place`='')";
//                }
//                //Если 72, то плюсуем 54
//                if ($_SESSION['filial'] == 19){
//                    $dop = "WHERE (`place`='".$_SESSION['filial']."' OR `place`='13')";
//                }
//                //Если ком, то плюсуем авиа
//                if ($_SESSION['filial'] == 14){
//                    $dop = "WHERE (`place`='".$_SESSION['filial']."' OR `place`='12')";
//                }
//            }
//            $query = "SELECT COUNT(*) AS total FROM `zapis_online`  ".$dop."";
//
//            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//            $arr = mysqli_fetch_assoc($res);



//            echo json_encode(array('result' => 'success', 'data' => $arr['total']));
//        }
//    }



//		}else{
//			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
//		}
	}else{
		header("location: enter.php");
	}

	require_once 'footer.php';

?>
	