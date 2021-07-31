<?php

//fl_tabel_print.php
//Вывод табеля на печать

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if ($_GET) {
            include_once 'DBWork.php';
            include_once 'functions.php';
            include_once 'ffun.php';
            include_once 'filter.php';
            include_once 'filter_f.php';

            require 'variables.php';

            $msql_cnnct = ConnectToDB2 ();

            if (isset($_GET['tabel_id'])){

                $link = 'fl_tabel.php';

                //метка ночного табеля
                $tabel_noch = false;

                if (isset($_GET['noch'])){
                    if ($_GET['noch'] == 1){
                        $tabel_noch = true;
                        $link = 'fl_tabel_noch.php';
                    }
                }

                //Данные по сотруднику
                $worker_j = array();

                //$tabel_j = SelDataFromDB('fl_journal_tabels', $_GET['tabel_id'], 'id');
                //var_dump($tabel_j[0]);

                $tabel_j = array();

                if (!$tabel_noch) {
                    $query = "SELECT * FROM `fl_journal_tabels` WHERE `id`='{$_GET['tabel_id']}' LIMIT 1;";
                }else{
                    $query = "SELECT * FROM `fl_journal_tabels_noch` WHERE `id`='{$_GET['tabel_id']}' LIMIT 1;";
                }

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)) {

                        //array_push($tabel_j, $arr);
                        $tabel_j = $arr;
                    }
                }
                //var_dump($tabel_j);

                if (!empty($tabel_j)){

                    if (!$tabel_noch) {
                        $filial_id = $tabel_j['office_id'];
                    }else {
                        $filial_id = $tabel_j['filial_id'];
                    }

                    //var_dump($permissions);
                    if (($report['see_all'] == 1) || $god_mode || ($tabel_j['worker_id'] == $_SESSION['id'])){

                        $filials_j = getAllFilials(false, true, true);

                        //Смена/график !!! переделать ! нужно только количество
                        $rezultShed = array();
                        $nightSmena = 0;

                        $dop = array();

                        $tabel_deductions_j = array();
                        $tabel_surcharges_j = array();
                        $tabel_surcharges_j = array();
                        $tabel_paidouts_j = array();

                        //Отметки по дополнительным опциям
                        //!!! Здесь функция большая и избыточная, но лень переписывать
                        $spec_prikaz8_checked = '';
                        $spec_oklad_checked = '';
                        $spec_oklad_work_checked = '';

                        $query = "SELECT * FROM `options_worker_spec` WHERE `worker_id`='{$tabel_j['worker_id']}' LIMIT 1";
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        $spec_prikaz8 = false;
                        $spec_oklad = false;
                        $spec_oklad_work = false;

                        if ($number != 0){
                            $arr = mysqli_fetch_assoc($res);
                            if ($arr['prikaz8'] == 1){
                                $spec_prikaz8 = true;
                            }
                            if ($arr['oklad'] == 1){
                                $spec_oklad = true;
                            }
                            if ($arr['oklad_work'] == 1){
                                $spec_oklad_work = true;
                            }
                        }
//                    var_dump($spec_prikaz8);
//                    var_dump($spec_oklad);
//                    var_dump($spec_oklad_work);

                        //Получаем всё по сотруднику
                        $query = "SELECT s_w.name, s_w.permissions AS type, s_p.name AS type_name, s_c.name AS cat_name
                          FROM  `spr_workers` s_w
                          LEFT JOIN `spr_permissions` s_p ON s_p.id = s_w.permissions
                          LEFT JOIN `journal_work_cat` j_wk ON j_wk.worker_id = s_w.id
                          LEFT JOIN `spr_categories` s_c ON s_c.id = j_wk.category
                          WHERE s_w.id = '{$tabel_j['worker_id']}'
                          LIMIT 1";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                //Раскидываем в массив
                                array_push($worker_j, $arr);
                                //Если ночная смена
                            }
                        }
                        //var_dump($worker_j);


                        //!!!? что-то тянем из графика... переделать описание этого комментария
                        $query = "SELECT `id`, `day`, `smena`, `kab`, `worker` FROM `scheduler` WHERE `worker` = '{$tabel_j['worker_id']}' AND `month` = '" . (int)$tabel_j['month'] . "' AND `year` = '{$tabel_j['year']}' AND `filial`='{$filial_id}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                //Раскидываем в массив
                                array_push($rezultShed, $arr);
                                //Если ночная смена
                                if ($arr['smena'] == 3){
                                    $nightSmena++;
                                }
                            }
                        }
                        /*var_dump($query);
                        var_dump(count($rezultShed));
                        var_dump($rezultShed);*/

                        //Ночные смены
                        $nightSmenaCount = 0;
                        $nightSmenaPrice = 0;
                        $nightSmenaSumm = 0;

                        $query = "SELECT `price`, `count`, `summ` FROM `fl_journal_tabel_nightsmens` WHERE `tabel_id` = '{$tabel_j['id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){

                            while ($arr = mysqli_fetch_assoc($res)){
                                //Раскидываем в массив
                                //array_push($rezultNightSmena, $arr);
                                $nightSmenaCount = $arr['count'];
                                $nightSmenaPrice = $arr['price'];
                                $nightSmenaSumm = $arr['summ'];
                            }
                            //var_dump($rezultNightSmena);

                        }

                        //Пустые смены
                        $emptySmenaCount = 0;
                        $emptySmenaPrice = 0;
                        $emptySmenaSumm = 0;

                        $query = "SELECT `price`, `count`, `summ` FROM `fl_journal_tabel_emptysmens` WHERE `tabel_id` = '{$tabel_j['id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){

                            while ($arr = mysqli_fetch_assoc($res)){
                                //Раскидываем в массив
                                //array_push($rezultNightSmena, $arr);
                                $emptySmenaCount = $arr['count'];
                                $emptySmenaPrice = $arr['price'];
                                $emptySmenaSumm = $arr['summ'];
                            }
                            //var_dump($rezultNightSmena);

                        }

                        //Надбавки
                        //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j['id']."'";
                        $query = "SELECT * FROM `fl_journal_surcharges` WHERE `tabel_id`='".$tabel_j['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                if (!isset($tabel_surcharges_j[$arr['type']])){
                                    $tabel_surcharges_j[$arr['type']] = array();
                                    $tabel_surcharges_j[$arr['type']] = (int)$arr['summ'];
                                }else{
                                    $tabel_surcharges_j[$arr['type']] = $tabel_surcharges_j[$arr['type']] + $arr['summ'];
                                }
                                //array_push($tabel_surcharges_j[$arr['type']], $arr);
                            }
                        }

                        //Вычеты
                        //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j['id']."'";
                        $query = "SELECT * FROM `fl_journal_deductions` WHERE `tabel_id`='".$tabel_j['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                if (!isset($tabel_deductions_j[$arr['type']])){
                                    $tabel_deductions_j[$arr['type']] = array();
                                    $tabel_deductions_j[$arr['type']] = (int)$arr['summ'];
                                }else{
                                    $tabel_deductions_j[$arr['type']] = $tabel_deductions_j[$arr['type']] + $arr['summ'];
                                }
                                //array_push($tabel_surcharges_j[$arr['type']], $arr);
                            }
                        }

                        //Выплаты
                        //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j['id']."'";
                        $query = "SELECT * FROM `fl_journal_paidouts` WHERE `tabel_id`='".$tabel_j['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                if (!isset($tabel_paidouts_j[$arr['type']])){
                                    $tabel_paidouts_j[$arr['type']] = array();
                                    $tabel_paidouts_j[$arr['type']] = (int)$arr['summ'];
                                }else{
                                    $tabel_paidouts_j[$arr['type']] = $tabel_paidouts_j[$arr['type']] + $arr['summ'];
                                }
                                //array_push($tabel_surcharges_j[$arr['type']], $arr);
                            }
                        }

                        //var_dump($tabel_surcharges_j);
                        //var_dump($tabel_deductions_j);
                        //var_dump($tabel_paidouts_j);

                        echo '
                            <div class="no_print"> 
                                <header style="margin-bottom: 5px;">';

                        echo '
                                </header>
                            </div>';

                        //var_dump($tabel_j);

                        $tabel_summ = intval($tabel_j['summ']);

                        //Если ассистент
                        if (($tabel_j['type'] == 7) || $spec_oklad_work){
                            $tabel_summ = intval($tabel_j['summ'] + $tabel_j['summ_calc']);
                        }

                        //Коэффициенты +/-
                        if (($tabel_j['k_plus'] != 0) || ($tabel_j['k_minus'] != 0)){
                            $tabel_summ = intval($tabel_summ + $tabel_summ/100*($tabel_j['k_plus'] - $tabel_j['k_minus']));
                        }

                        if (isset($tabel_deductions_j[1])){
                            $tabel_summ = intval($tabel_j['summ'] - $tabel_deductions_j[1]);
                        }
                        $tabel_deductions_j2 = 0;
                        if (isset($tabel_deductions_j[2])){
                            $tabel_deductions_j2 = $tabel_deductions_j[2];
                        }
                        $tabel_surcharges_j2 = 0;
                        if (isset($tabel_surcharges_j[2])){
                            $tabel_surcharges_j2 = $tabel_surcharges_j[2];
                        }
                        $tabel_deductions_j3 = 0;
                        if (isset($tabel_deductions_j[3])){
                            $tabel_deductions_j3 = $tabel_deductions_j[3];
                        }
                        $tabel_surcharges_j3 = 0;
                        if (isset($tabel_surcharges_j[3])){
                            $tabel_surcharges_j3 = $tabel_surcharges_j[3];
                        }
                        $tabel_deductions_j4 = 0;
                        if (isset($tabel_deductions_j[4])){
                            $tabel_deductions_j4 = $tabel_deductions_j[4];
                        }
                        $tabel_surcharges_j1 = 0;
                        if (isset($tabel_surcharges_j[1])){
                            $tabel_surcharges_j1 = $tabel_surcharges_j[1];
                        }
                        $tabel_deductions_j5 = 0;
                        if (isset($tabel_deductions_j[5])){
                            $tabel_deductions_j5 = $tabel_deductions_j[5];
                        }
                        $tabel_paidouts_j1 = 0;
                        if (isset($tabel_paidouts_j[1])){
                            $tabel_paidouts_j1 = $tabel_paidouts_j[1];
                        }
                        $tabel_paidouts_j2 = 0;
                        if (isset($tabel_paidouts_j[2])){
                            $tabel_paidouts_j2 = $tabel_paidouts_j[2];
                        }
                        $tabel_paidouts_j3 = 0;
                        if (isset($tabel_paidouts_j[3])){
                            $tabel_paidouts_j3 = $tabel_paidouts_j[3];
                        }
                        $tabel_paidouts_j4 = 0;
                        if (isset($tabel_paidouts_j[4])){
                            $tabel_paidouts_j4 = $tabel_paidouts_j[4];
                        }

                        //Если админ или еще
                        if (($tabel_j['type'] == 4) || ($tabel_j['type'] == 7) || ($tabel_j['type'] == 13) || ($tabel_j['type'] == 14) || ($tabel_j['type'] == 15)){

                            //Часы работы
                            $dop['hours_count'] = 0;
                            $dop['hours_norma'] = 0;

                            if (!$tabel_noch) {
                                if ($tabel_j['hours_count'] != NULL) {
                                    $hours_count_arr_temp = explode(',', $tabel_j['hours_count']);
                                    //var_dump($hours_count_arr_temp);

                                    $dop['hours_count'] = $hours_count_arr_temp[0];
                                    $dop['hours_norma'] = $hours_count_arr_temp[1];
                                }
                            }

                            if (!$tabel_noch) {

                                //Оклад
                                $dop['salary'] = $tabel_j['salary'];

                                //Процент от оклада
                                $dop['per_from_salary'] = $tabel_j['per_from_salary'];
                                $tabel_summ = number_format($dop['per_from_salary'], 0, '.', '');

                                //Процент от выручки
                                $dop['percent_summ'] = $tabel_j['percent_summ'];
                            }

                            //Сумма РЛ (В основном для ассистентов)
                            $dop['summ_calc'] = $tabel_j['summ_calc'];

                        }

                        //Пробуем вывести расчетный лист по табелю для печати
                        echo tabelPrintTemplate ($_GET['tabel_id'], $monthsName[$tabel_j['month']], $tabel_j['year'], $worker_j[0], $filials_j[$filial_id]['name2'], count($rezultShed),
                            $tabel_summ, $tabel_deductions_j2, $tabel_surcharges_j2, $tabel_deductions_j3,
                            $tabel_surcharges_j3, $tabel_deductions_j4, $tabel_surcharges_j1,
                            $tabel_deductions_j5, $emptySmenaCount, $emptySmenaPrice, $emptySmenaSumm,
                            $tabel_paidouts_j1, $tabel_paidouts_j4, $tabel_paidouts_j2, $nightSmenaCount,
                            $nightSmenaPrice, $nightSmenaSumm, $tabel_paidouts_j3, $dop, $tabel_noch, $link);


                        echo '
                            <div class="no_print" style="position: fixed; top: 50px; right: 10px; border: 1px solid #0C0C0C; border-radius: 5px; padding: 5px 5px; background-color: #FFFFFF">
                                <div class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;"
                                onclick="window.print();">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                </div>
                            </div>';


                        echo "
                            <script>
                                $(document).ready(function() {
    
                                    fl_tabulation (".$_GET['tabel_id'].");
                                    
                                });
                            </script>";

                    }else{
                        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
                    }
                }else{
                    echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                }
            }else{
                echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
            }
        }else{
            echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
        }
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>