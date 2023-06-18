<?php

//check_client_by_name_f.php
//Проверяет пациента по имени и возвращает по нему данные

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
//        var_dump ($_POST);
        if ($_POST){
            $rezult_from = '';
            $rezult_to = '';
            $rezult_arr = array();

            include_once('DBWorkPDO.php');
            include_once 'functions.php';

            if (!isset($_POST['from_id']) || !isset($_POST['to_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

//                $fio_post = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['fio']))));
//                $temp_data_fio = explode(' ', $fio_post);
//                $f = $temp_data_fio[0];
//                $i = $temp_data_fio[1];
//                $o = $temp_data_fio[2];

                //Составляем ФИО заново для запроса в БД
//                $fio_str = $f.' '.$i;
//
//                if (mb_strlen($o) == 1){
//                    //Проверка строки на только русские буквы
//                    if (preg_match("/[^а-яА-ЯЁё\s]+/msi", $o)) {
//                        //Нет отчества
//                        $o = '*';
//                    }else{
//                        $fio_str .= ' '.$o;
//                    }
//                }else{
//                    $fio_str .= ' '.$o;
//                }
//                var_dump($fio_str);
//
//                $temp_data_bp_post = explode(';', $_POST['birth_phone']);
//                $birthday = $temp_data_bp_post[0];
////                var_dump($birthday);
////                $phone = $temp_data_bp_post[1];
//
//                $str_tel = preg_replace("/[^0-9]/", '', $phone);
//                //костыль
//                if (mb_strlen($str_tel) == 0){
//                    $str_tel = '_x_';
//                }
//                var_dump($str_tel);

                //Основные данные
                $args = [
                    'from_id' => $_POST['from_id'],
                    'to_id' => $_POST['to_id']
                ];
//                var_dump ($args);

                $db = new DB();

                $query = "SELECT * FROM `spr_clients` WHERE `id`=:from_id OR `id`=:to_id";

                $res = $db::getRows($query, $args);

//                var_dump($query);
//                var_dump($res);
//                var_dump($birthday);

                if (!empty($res)) {
//                    var_dump($res);

//                    //Если их больше чем 1
                    if (count($res) > 1){
                        foreach ($res as $client_data){
                            $temp_res = '';

                            $temp_res .= '<div class="cellsBlock2">
									<div class="cellLeft">Номер карты</div>
									<div class="cellRight" style="font-weight: bolder; font-size: 105%;">'.$client_data['card'].'</div>
								</div>
								<div class="cellsBlock2">
									<div class="cellLeft">ФИО</div>
									<div class="cellRight" style="font-weight: bolder; font-size: 105%;">
									    <a href="client.php?id='.$client_data['id'].'" class="ahref" target="_blank" rel="nofollow noopener">'.$client_data['full_name'].'</a>
                                    </div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Дата рождения</div>
									<div class="cellRight">';
                            if ($client_data['birthday2'] == '0000-00-00'){
                                $temp_res .= 'не указана';
                            }else{
                                $temp_res .=
                                    date('d.m.Y', strtotime($client_data['birthday2'])).'<br>
						полных лет <b>'.getyeardiff(strtotime($client_data['birthday2']), 0).'</b>';
                            }
                            $temp_res .= '						
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Пол</div>
									<div class="cellRight">';
                            if ($client_data['sex'] != 0){
                                if ($client_data['sex'] == 1){
                                    $temp_res .= 'М';
                                }
                                if ($client_data['sex'] == 2){
                                    $temp_res .= 'Ж';
                                }
                            }else{
                                $temp_res .= 'не указан';
                            }
                            $temp_res .=
                                '</div>
								</div>';

                            $temp_res .= '
								<div class="cellsBlock2">
									<div class="cellLeft">
									    Телефон
                                    </div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">мобильный</span><br>
											'.$client_data['telephone'].'
										</div>';
                            if ($client_data['htelephone'] != ''){
                                $temp_res .= '
										<div>
											<span style="font-size: 80%; color: #AAA">домашний</span><br>
											'.$client_data['htelephone'].'
										</div>';
                            }
                            $temp_res .= '
									</div>
								</div>';

                            $temp_res .= '
								<div class="cellsBlock2">
									<div class="cellLeft">Email</div>
									<div class="cellRight">
                                        '.$client_data['email'].'
									</div>
								</div>
								<div class="cellsBlock2">
									<div class="cellLeft">ИНН</div>
									<div class="cellRight">
                                        '.$client_data['inn'].'
									</div>
								</div>
								<div class="cellsBlock2">
									<div class="cellLeft">Паспорт</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 70%; color: #AAA">Серия номер</span><br>';
                            if ($client_data['id'] == 3459){
                                if (($_SESSION['id'] == 270) || ($_SESSION['id'] == 1)){
                                    $temp_res .= $client_data['passport'];
                                }
                            }else {
                                $temp_res .= $client_data['passport'];
                            }

                            $temp_res .= '
										</div>';
                            if (($client_data['alienpassportser'] != NULL) && ($client_data['alienpassportnom'] != NULL)){
                                $temp_res .= '
										<div>
											<span style="font-size: 70%; color: #AAA">Серия номер (иностр.)</span><br>
											'.$client_data['alienpassportser'].'
											'.$client_data['alienpassportnom'].'
										</div>';
                            }
                            $temp_res .= '
										<div>
											<span style="font-size: 70%; color: #AAA">Выдан когда</span><br>';
                            if ($client_data['id'] == 3459){
                                if (($_SESSION['id'] == 270) || ($_SESSION['id'] == 1)){
                                    $temp_res .= $client_data['passportvidandata'];
                                }
                            }else {
                                $temp_res .= $client_data['passportvidandata'];
                            }

                            $temp_res .= '
										</div>
										<div>
											<span style="font-size: 70%; color: #AAA">Кем</span><br>';
                            if ($client_data['id'] == 3459){
                                if (($_SESSION['id'] == 270) || ($_SESSION['id'] == 1)){
                                    $temp_res .= $client_data['passportvidankem'];
                                }
                            }else {
                                $temp_res .= $client_data['passportvidankem'];
                            }

                            $temp_res .= '
										</div>
									</div>
								</div>
								<div class="cellsBlock2">
									<div class="cellLeft">Адрес</div>
									<div class="cellRight">';

                            if ($client_data['id'] == 3459){
                                if (($_SESSION['id'] == 270) || ($_SESSION['id'] == 1)){
                                    $temp_res .= $client_data['address'];
                                }
                            }else {
                                $temp_res .= $client_data['address'];
                            }

                            $temp_res .= '
									</div>
								</div>';
                            if ($client_data['polis'] != ''){
                                $temp_res .= '
								<div class="cellsBlock2">
									<div class="cellLeft">Полис</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">Номер</span><br>
											'.$client_data['polis'].'
										</div>
										<div>
											<span style="font-size: 80%; color: #AAA">Дата</span><br>
											'.$client_data['polisdata'].'
										</div>';
                                if ($client_data['insure'] == 0){
                                    $insure = 'не указана';
                                }else{
                                    $insures_j = SelDataFromDB('spr_insure', $client_data['insure'], 'offices');
                                    if ($insures_j == 0){
                                        $insure = 'ошибка';
                                    }else{
                                        $insure = $insures_j[0]['name'];
                                    }
                                }
                                $temp_res .= '
										<div>
											<span style="font-size: 80%; color: #AAA">Страховая компания</span><br>
											'.$insure.'
										</div>		
									</div>
								</div>';
                            }

                            if (($client_data['fo'] != '') || ($client_data['io'] != '')){
                                $temp_res .= '
                                <div class="cellsBlock2" style="margin-top: 2px; margin-bottom: 0; display: block;">
                                    <div class="cellLeft" style="font-weight: bold; width: 500px; border-left: 1px ridge rgb(138 142 162); border-top: 1px ridge rgb(138 142 162); border-right: 1px ridge rgb(138 142 162);">
                                        Опекун
                                    </div>
                                </div>
                                
                                <div class="cellsBlock2">
                                    <div class="cellLeft" style=" border-left: 1px ridge rgb(138 142 162); ">Фамилия</div>
                                    <div class="cellRight" style=" border-right: 1px ridge rgb(138 142 162);">
                                        '.$client_data['fo'].'
                                    </div>
                                </div>
                                
                                <div class="cellsBlock2">
                                    <div class="cellLeft" style=" border-left: 1px ridge rgb(138 142 162); ">Имя</div>
                                    <div class="cellRight" style=" border-right: 1px ridge rgb(138 142 162);">
                                        '.$client_data['io'].'
                                    </div>
                                </div>
                                
                                <div class="cellsBlock2">
                                    <div class="cellLeft" style=" border-left: 1px ridge rgb(138 142 162); ">Отчество</div>
                                    <div class="cellRight" style=" border-right: 1px ridge rgb(138 142 162);">
                                        '.$client_data['oo'].'
                                    </div>
                                </div>
                                
                                <div class="cellsBlock2">
                                    <div class="cellLeft" style="border-bottom: 1px ridge rgb(138 142 162);  border-left: 1px ridge rgb(138 142 162);">Телефон</div>
                                    <div class="cellRight" style="border-bottom: 1px ridge rgb(138 142 162); border-right: 1px ridge rgb(138 142 162);">
                                        <div>
                                            <span style="font-size: 80%; color: #AAA">мобильный</span><br>
                                            '.$client_data['telephoneo'].'
                                        </div>';
                                    if ($client_data['htelephoneo'] != ''){
                                        $temp_res .= '
                                        <div>
                                            <span style="font-size: 80%; color: #AAA">домашний</span><br>
                                            '.$client_data['htelephoneo'].'
                                        </div>';
                                    }
                                    $temp_res .= '
                                    </div>
                                </div>
                                ';
                            }


                            if ($client_data['id'] == $_POST['from_id']){
                                $rezult_from = $temp_res;
                            }else{
                                $rezult_to = $temp_res;
                            }

                        }
//
//                        var_dump('$rezult_from');
//                        var_dump($rezult_from);
//                        var_dump('$rezult_to');
//                        var_dump($rezult_to);

                        echo json_encode(array('result' => 'success', 'rezult_from' => $rezult_from, 'rezult_to' => $rezult_to));
                    }


                }else{
                    $rezult .= '<span style="color: #ff0000; font-weight: normal;">Указанный пациент не найден. </span>';
                    echo json_encode(array('result' => 'success', 'data' => '<div>'.$rezult.'</div>'));
                }
                //var_dump($rezult);

                //echo json_encode(array('result' => 'error', 'data' => $rezult));


//                $query = "SELECT * FROM `spr_clients` WHERE (
//                  LOWER(`full_name`) RLIKE LOWER('^$search_data')
//                  OR
//                  UPPER(`card`) LIKE UPPER('%$search_data%')
//                  OR
//                  (REPLACE(REPLACE(REPLACE(`telephone`, '-', '' ), '(', ''), ')', '') LIKE '%$str_tel%' AND LENGTH(`telephone`) > 4)
//                  OR
//                  (REPLACE(REPLACE(REPLACE(`htelephone`, '-', '' ), '(', ''), ')', '') LIKE '%$str_tel%' AND LENGTH(`htelephone`) > 4)
//                  OR
//                  (REPLACE(REPLACE(REPLACE(`telephoneo`, '-', '' ), '(', ''), ')', '') LIKE '%$str_tel%' AND LENGTH(`telephoneo`) > 4)
//                  OR
//                  (REPLACE(REPLACE(REPLACE(`htelephoneo`, '-', '' ), '(', ''), ')', '') LIKE '%$str_tel%' AND LENGTH(`htelephoneo`) > 4)
//                  OR
//                  `passport` RLIKE '^$search_data'
//                  OR
//                  `id` RLIKE '^$search_data'
//                  ) AND `status`<> 9 ORDER BY `full_name` ASC LIMIT 10";

                //Если есть данные - работаем
//                if (!empty($rezult_arr)) {
//                    //Если запись, надо проверить, а все ли мы  выгрузили. Если нет, грузим все остальное
//                    if ($dopURL_api == 'lm/appointments'){
////                        var_dump('total');
////                        var_dump($rezult_arr['total']);
////                        var_dump('limit');
////                        var_dump($rezult_arr['limit']);
////                        var_dump('page');
////                        var_dump($rezult_arr['page']);
////                        var_dump($rezult_arr['data']);
//                        if ($rezult_arr['total'] > $rezult_arr['limit']){
////                            var_dump('> TRUE');
//
//                            //Даты от - до
////                            $date_from = '2023-03-22';
////                            $date_to = '2023-03-22';
//                            $date_from = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
//                            $date_to = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
//
//                            $query_req_str = 'date_from=' . $date_from . '&date_to=' . $date_to;
//
//                            $pages_count = (int)ceil($rezult_arr['total']/$rezult_arr['limit']);
//                            //var_dump($pages_count);
//
//                            for($i = 1; $i < $pages_count; $i++){
//                                $query_req_str_f = $query_req_str.'&page=' . $i;
//                                //var_dump($query_req_str_f);
//
//                                //Строка запроса
//                                $query = $URL_api . $dopURL_api . '?token=' . $token_api . '&' . 'secret=' . $secret_api . '&' . $query_req_str_f;
////                                var_dump($query);
//
//                                $rezult_arr_dop = getDataFromAPI_DP($query);
////                                var_dump($rezult_arr_dop['data']);
//
//                                if (!empty($rezult_arr_dop)){
//                                    if (!empty($rezult_arr_dop['data'])){
//                                        $rezult_arr['data'] = array_merge($rezult_arr['data'],$rezult_arr_dop['data']);
//                                    }
//                                }
//                            }
//                        }
//
//                        foreach ($rezult_arr['data'] as $zapis_data){
//                            array_push($client_ids_arr, $zapis_data['client_id']);
//                            array_push($doc_ids_arr, $zapis_data['doctor_id']);
//                        }
//
//                    }
//
////                    var_dump($rezult_arr['data']);
//                    echo json_encode(array('result' => 'success', 'data' => $rezult_arr,  'query' => $query, 'client_ids' => $client_ids_arr, 'doc_ids' => $doc_ids_arr));
//
//                }else{
//                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка! Данных нет.</div>'));
//                }


            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>'));
        }
    }

?>
	