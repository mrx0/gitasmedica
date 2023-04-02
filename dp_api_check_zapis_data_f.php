<?php

//dp_api_check_zapis_data_f.php
//Тест API DentalPro функция проверки есть ли запись

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
//        var_dump ($_POST);
        if ($_POST){
            $rezult = '';
            $rezult_arr = array();

            include_once('DBWorkPDO.php');
            include_once 'functions.php';

            if (!isset($_POST['client_id']) || !isset($_POST['doctor_id']) || !isset($_POST['day']) || !isset($_POST['month']) || !isset($_POST['year']) || !isset($_POST['time'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                if ($_POST['client_id'] != 0) {
                    $time_str = str_replace(" ", "", $_POST['time']);


                    $time_dp_arr = explode('-', $time_str);
                    $time_dp_start = $time_dp_arr[0];
                    $time_dp_end = $time_dp_arr[1];
//                    var_dump($time_start);
//                    var_dump($time_end);

                    //Выясняем разницу в минутах между временами (время работы)
                    $interval = date_diff(date_create($time_dp_start), date_create($time_dp_end));

                    $wt = $interval->days * 24 * 60;
                    $wt += $interval->h * 60;
                    $wt += $interval->i;
//                    var_dump($wt);

                    //Переведём в формат времени аккаунта
                    $time_arr = explode(':', $time_dp_start);
                    $time_start = $time_arr[0] * 60 +  $time_arr[1];
//                    var_dump($time_start);

                    //Основные данные
                    $args = [
                        'day' => (int)$_POST['day'],
                        'month' => (int)$_POST['month'],
                        'year' => $_POST['year'],
                        'patient' => $_POST['client_id'],
                        'worker' => $_POST['doctor_id'],
                        'start_time' => $time_start,
                        'wt' => $wt
                    ];
//                    var_dump ($args);

                    $db = new DB();

                    $query = "SELECT `id` FROM `zapis` WHERE
                      `day` = :day AND 
                      `month` = :month AND 
                      `year` = :year AND 
                      `patient` = :patient AND 
                      `worker` = :worker AND 
                      `start_time` = :start_time AND 
                      `wt` = :wt 
                      ";

                    $res = $db::getRow($query, $args);
//                    var_dump($query);
//                    var_dump($res);

                    if (!empty($res)) {
                        //ФИО найдены
                        $rezult .= '<span style="color: #36ff00; font-size: 90%; font-weight: normal;">Запись найдена. Можно не добавлять.</span>';

//                        //Если их больше чем 1 !!! с записями такого быть не должно
//                        if (count($res) > 1) {
//                            $rezult .= 'Найдено больше одного врача с такими ФИО.';
//                            //Проход по всем по очереди. Тут надо будет придумать, как с ними работать, если такое случится. Пока просто берём последнего в списке
//                            foreach ($res as $item) {
//                                //if ($item['birthday2'] == $birthday){
//                                //$rezult .= '<span style="color: #36ff00; font-size: 90%; font-weight: normal;">ДР у <a href="client.php?id='.$item['id'].'" class="ahref" style="text-align: center; color: #2ebdbd; text-decoration: underline;" target="_blank" rel="nofollow noopener">одного</a> совпадает. Можно не добавлять. </span>';
//
//                                $rezult_doc_id = $item['id'];
//                                //}
//                            }
//                        } else {
//                            //Проверка на дату рождения и телефон
//                            //if ($res[0]['birthday2'] == $birthday){
//                            //$rezult .= '<span style="color: #36ff00; font-size: 90%; font-weight: normal;">ДР <a href="client.php?id='.$res[0]['id'].'" class="ahref" style="text-align: center; color: #2ebdbd; text-decoration: underline;" target="_blank" rel="nofollow noopener">пациента</a> совпадает. Можно не добавлять. </span>';
//
//                            $rezult_doc_id = $res[0]['id'];
//                            //}
//                        }

                        echo json_encode(array('result' => 'success', 'data' => '<div>' . $rezult . '</div>', 'zapis_acc_id' => $res['id']));

                    } else {
                        $rezult .= '<span style="color: #ff0000; font-weight: normal;">Запись не найдена. Необходимо добавить. </span>';
                        echo json_encode(array('result' => 'success', 'data' => '<div>' . $rezult . '</div>'));
                    }
                }else{
                    $rezult .= '<span style="color: #ff0000; font-weight: normal;">Пациент не добавлен. Записи не должно быть. Пропуск проверки.</span>';
                    echo json_encode(array('result' => 'success', 'data' => '<div>' . $rezult . '</div>'));
                }
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>'));
        }
    }

?>
	