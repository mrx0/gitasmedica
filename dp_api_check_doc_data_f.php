<?php

//dp_api_check_doc_data_f.php
//Тест API DentalPro функция проверки есть ли такой врач

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
//        var_dump ($_POST);
        if ($_POST){
            $rezult = '';
            $rezult_arr = array();

            $rezult_doc_id = 0;

            //ID клиентов и врачей
            $client_ids_arr = array();
            $doc_ids_arr = array();

            include_once('DBWorkPDO.php');
            include_once 'functions.php';

            if (!isset($_POST['fio'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $fio_post = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['fio']))));
                $temp_data_fio = explode(' ', $fio_post);
                $f = $temp_data_fio[0];
                $i = $temp_data_fio[1];
                $o = $temp_data_fio[2];

                //Составляем ФИО заново для запроса в БД
                $fio_str = $f.' '.$i;

                if (mb_strlen($o) == 1){
                    //Проверка строки на только русские буквы
                    if (preg_match("/[^а-яА-ЯЁё\s]+/msi", $o)) {
                        //Нет отчества
                        $o = '*';
                    }else{
                        $fio_str .= ' '.$o;
                    }
                }else{
                    $fio_str .= ' '.$o;
                }
//                var_dump($fio_str);

//                $temp_data_bp_post = explode(';', $_POST['birth_phone']);
//                $birthday = $temp_data_bp_post[0];
////                var_dump($birthday);
//                $phone = $temp_data_bp_post[1];

//                $str_tel = preg_replace("/[^0-9]/", '', $phone);
//                //костыль
//                if (mb_strlen($str_tel) == 0){
//                    $str_tel = '_x_';
//                }
////                var_dump($str_tel);

                //Основные данные
                $args = [
                ];
//                var_dump ($args);

                $db = new DB();

                $query = "SELECT * FROM `spr_workers` WHERE (
                  LOWER(`full_name`) RLIKE LOWER('^{$fio_str}')
                  ) LIMIT 10";

                $res = $db::getRows($query, $args);

//                var_dump($query);
//                var_dump($res);

                if (!empty($res)) {
                    //ФИО найдены
                    $rezult .= '<span style="color: #36ff00; font-size: 90%; font-weight: normal;">Врач найден. </span>';

                    //Если их больше чем 1
                    if (count($res) > 1){
                        $rezult .= 'Найдено больше одного врача с такими ФИО.';
                        //Проход по всем по очереди. Тут надо будет придумать, как с ними работать, если такое случится. Пока просто берём последнего в списке
                        foreach ($res as $item) {
                            //if ($item['birthday2'] == $birthday){
                                //$rezult .= '<span style="color: #36ff00; font-size: 90%; font-weight: normal;">ДР у <a href="client.php?id='.$item['id'].'" class="ahref" style="text-align: center; color: #2ebdbd; text-decoration: underline;" target="_blank" rel="nofollow noopener">одного</a> совпадает. Можно не добавлять. </span>';

                                $rezult_doc_id = $item['id'];
                            //}
                        }
                    }else{
                        //Проверка на дату рождения и телефон
                        //if ($res[0]['birthday2'] == $birthday){
                            //$rezult .= '<span style="color: #36ff00; font-size: 90%; font-weight: normal;">ДР <a href="client.php?id='.$res[0]['id'].'" class="ahref" style="text-align: center; color: #2ebdbd; text-decoration: underline;" target="_blank" rel="nofollow noopener">пациента</a> совпадает. Можно не добавлять. </span>';

                            $rezult_doc_id = $res[0]['id'];
                        //}
                    }

                    echo json_encode(array('result' => 'success', 'data' => '<div>'.$rezult.'</div>', 'doc_acc_id' => $rezult_doc_id));

                }else{
                    $rezult .= '<span style="color: #ff0000; font-weight: normal;">Врач не найден. Необходимо добавить врача в аккаунт! </span>';
                    echo json_encode(array('result' => 'success', 'data' => '<div>'.$rezult.'</div>'));
                }

            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>'));
        }
    }

?>
	