<?php

//another_pervich_analiz.php
//Первичка v3.0. Поиск тех, кто был первичкой, но потом пришёл
//Конкретно в этом файле выполняем поиск по условию

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){

        include_once('DBWorkPDO.php');
        include_once('functions.php');

        $db = new DB();

        $args = [
            'zapis_id' => $_POST['zapis_id']
        ];

        $query = "SELECT `patient`, `year`, `month`, `day` FROM `zapis` WHERE `id`=:zapis_id LIMIT 1";

        $zapis_j = $db::getRow($query, $args);

        //var_dump($zapis_j);

        if (!empty($zapis_j)) {

            $args = [
                'patient' => $zapis_j['patient'],
            ];

            $query = "SELECT COUNT(*) AS total FROM `zapis` WHERE (`enter`='0' OR `enter`='1') AND `patient` = :patient AND CONCAT_WS('-', year, LPAD(month, 2, '0'), LPAD(day, 2, '0')) > '".$zapis_j['year']."-".dateTransformation ($zapis_j['month'])."-".dateTransformation ($zapis_j['day'])."'";

//            var_dump($query);

            $total = $db::getValue($query, $args);

//            var_dump($journal_j);
            if ($total == 0){
                echo $_POST['zapis_id'];
            }

        }
        //Типы посещений - первичка/нет (количество) (pervich)
        //Памятка
        //1 - Посещение для пациента первое без работы
        //2 - Посещение для пациента первое с работой
        //3 - Посещение для пациента не первое
        //4 - Посещение для пациента не первое, но был более полугода назад
        //--
        //5 - Продолжение работы
        //6 - Без записи (enter)

        //Выводим результат
//        if (!empty($journal_j)) {
//
//            foreach ($journal_j as $arr) {
////                            var_dump($arr);
//
//                //Теперь суммы нарядов
//                //Пришел/не пришел/с улицы
//                if (!isset($zapis_summ[$arr['type']])) {
//                    $zapis_summ[$arr['type']] = array();
//
//                }
//                //тип стом, косм, ...
//                if (!isset($zapis_summ[$arr['type']][$arr['pervich']])) {
//                    $zapis_summ[$arr['type']][$arr['pervich']] = array();
//                    $zapis_summ[$arr['type']][$arr['pervich']]['data'] = array();
//                    $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'] = array();
//                }
//                //Если страховой
//                //if (($arr['insure'] == 1) || ()){
//                if (($arr['insure'] == 1) || ($arr['invoice_summins'] != 0)){
//                    if (!isset($zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']])) {
//                        $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']] = 0;
//                    }
//                    $zapis_summ[$arr['type']][$arr['pervich']]['insure_data'][$arr['zapis_id']] += (int)$arr['itog_price'];
//                } else {
//                    if (!isset($zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']])) {
//                        $zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']] = 0;
//                    }
//                    $zapis_summ[$arr['type']][$arr['pervich']]['data'][$arr['zapis_id']] += (int)$arr['itog_price'];
//                }
//
//            }
//            //var_dump($zapis_summ);
//            //var_dump($zapis_summ[5]);
//            //var_dump($zapis_summ[5]);
//
//            //сортируем по основным ключам
//            //ksort($zapis_summ);
//            //!!! тестовая проверка нового определения первичек
//            $pervich_summ_arr_new = array();
//
//            foreach ($zapis_summ as $type => $pervich_data) {
////                            var_dump($type);
//                //var_dump($pervich_data);
//
//                foreach ($pervich_data as $pervich => $zapis_data) {
////                                var_dump($pervich);
//                    //var_dump($zapis_data);
//
//                    if (!isset($pervich_summ_arr_new[$type])) {
//                        $pervich_summ_arr_new[$type] = $pervich_summ_arr;
//                    }
//
//                    if (isset($zapis_data['data'])) {
//                        if (!empty($zapis_data['data'])) {
//                            foreach ($zapis_data['data'] as $z_id => $i_summ) {
//                                if ($pervich == 1 || $pervich == 2) {
//                                    //Стоматология
//                                    if ($type == 5) {
//                                        if ($i_summ >= 0) {
//                                            if ($i_summ < 1100) {
//                                                //нас интересует сейчас это условие, первичка без работы
//                                                $pervich_summ_arr_new[$type][1]++;
//                                                //var_dump($z_id);
//                                                echo '<input type="hidden" value="'.$z_id.'" class="zapis_id">';
//                                            }/* else {
//                                                        $pervich_summ_arr_new[$type][2]++;
//                                                    }*/
//                                        }
//                                    }
//                                    //Косметология
//                                    //!!! Доделать
//                                    if ($type == 6) {
//                                        if ($i_summ >= 0) {
//                                            if ($i_summ < 550) {
//                                                //нас интересует сейчас это условие, первичка без работы
//                                                $pervich_summ_arr_new[$type][1]++;
//                                                //var_dump($z_id);
//                                                //echo '<a href="invoice.php?id='.$z_id.'" class="ahref button_tiny" style="margin: 0 3px; font-size: 90%;" target="_blank" rel="nofollow noopener">'.$z_id.'</a><br>';
//                                                echo '<input type="hidden" value="'.$z_id.'" class="zapis_id">';
//                                            }/* else {
//                                                        $pervich_summ_arr_new[$type][2]++;
//                                                    }*/
//                                        }
//                                    }
//                                    //Соматика
//                                    if ($type == 10) {
//                                        if ($i_summ >= 0) {
//                                            if ($i_summ < 990) {
//                                                //нас интересует сейчас это условие, первичка без работы
//                                                $pervich_summ_arr_new[$type][1]++;
//                                                //var_dump($z_id);
//                                                //echo '<a href="invoice.php?id='.$z_id.'" class="ahref button_tiny" style="margin: 0 3px; font-size: 90%;" target="_blank" rel="nofollow noopener">'.$z_id.'</a><br>';
//                                                echo '<input type="hidden" value="'.$z_id.'" class="zapis_id">';
//                                            }/* else {
//                                                        $pervich_summ_arr_new[$type][2]++;
//                                                    }*/
//                                        }
//                                    }
//                                }
////                                            if ($pervich == 3 || $pervich == 4 || $pervich == 5) {
////                                                if ($i_summ > 0) {
////                                                    $pervich_summ_arr_new[$type][3]++;
////                                                }
////                                            }
//                            }
//                        }
//                    }
//                }
//            }
//            //var_dump($pervich_summ_arr_new);
//
//            include_once 'functions.php';
//
//            // !!! **** тест с записью
//            //include_once 'showZapisRezult2.php';
//            include_once 'showZapisRezult.php';
//
//            if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode) {
//                $finance_edit = true;
//                $edit_options = true;
//            }
//
//            if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode) {
//                $stom_edit = true;
//                $edit_options = true;
//            }
//            if (($cosm['add_own'] == 1) || ($cosm['add_new'] == 1) || $god_mode) {
//                $cosm_edit = true;
//                $edit_options = true;
//            }
//
//            if (($zapis['add_own'] == 1) || ($zapis['add_new'] == 1) || $god_mode) {
//                $admin_edit = true;
//                $edit_options = true;
//            }
//
//            if (($scheduler['see_all'] == 1) || $god_mode) {
//                $upr_edit = true;
//                $edit_options = true;
//            }
//
//            //Если хотим видеть только уникальные пациенты
////                        if ($_POST['patientUnic'] == 1){
////                            //var_dump($journal);
////
////                            $journal_temp = array();
////
////                            foreach($journal as $journal_item){
////                                //Нам нужны фио пациентов чтоб потом сортировать их по фио
////
////                                $journal_temp[WriteSearchUser('spr_clients', $journal_item['patient'], 'user_full', false)] =  $journal_item;
////                            }
////
////                            ksort($journal_temp);
////                            $journal = $journal_temp;
////                            $journal = array_values($journal_temp);
////
////                        }
//            //($journal);
//
//
//            //echo showZapisRezult($journal, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, true, false, $dop);
//            //$ZapisHereQueryToday, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, $type, $format, $menu, $dop
//
//
////                        echo '
////                                    <li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
////                                        Всего : ' . count($journal) . '<br>
////                                    </li>
////
////                                    <!--<li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
////                                        '.$query.'
////                                    </li>-->';
//
//            echo '
//                                    </ul>
//                                </div>';
//        } else {
//            echo '<span style="color: red;">Ничего не найдено</span>';
//        }
    }
}

?>