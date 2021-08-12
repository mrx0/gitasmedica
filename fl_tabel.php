<?php

//fl_tabel.php
//Табель

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if ($_GET){
            if (isset($_GET['id'])){

                include_once('DBWorkPDO.php');
                include_once 'DBWork.php';
                include_once 'functions.php';

                //Опция доступа к филиалам конкретных сотрудников
                $optionsWF = getOptionsWorkerFilial($_SESSION['id']);
                //var_dump($optionsWF);

                $tabel_j = SelDataFromDB('fl_journal_tabels', $_GET['id'], 'id');
//                var_dump($tabel_j);

                if ($tabel_j != 0){
                    //var_dump($tabel_j);
                    //array_push($_SESSION['invoice_data'], $_GET['client']);
                    //$_SESSION['invoice_data'] = $_GET['client'];
                    //var_dump($calculate_j[0]['closed_time'] == 0);

                    //$invoice_type = $tabel_j[0]['type'];

                    //Получим сразу название категории
                    $category_j = SelDataFromDB('spr_categories', $tabel_j[0]['category'], 'id');
                    //var_dump($category_j);
                    //...и должность
                    $permission_j = SelDataFromDB('spr_permissions', $tabel_j[0]['type'], 'id');
                    //var_dump($permission_j);

                    if (($finances['see_all'] == 1) || $god_mode || ($tabel_j[0]['worker_id'] == $_SESSION['id'])) {
                        include_once 'ffun.php';

                        require 'variables.php';

                        require 'config.php';

                        $edit_options = false;
                        $upr_edit = false;
                        $admin_edit = false;
                        $stom_edit = false;
                        $cosm_edit = false;
                        $finance_edit = false;

                        //var_dump($_SESSION);
                        //unset($_SESSION['invoice_data']);
			
                        $filials_j = getAllFilials(false, false, true);
                        //var_dump(microtime(true) - $script_start);
                        //var_dump($filials_j);

						//$sheduler_zapis = array();
                        $tabel_ex_calculates_j = array();
						$tabel_deductions_j = array();
						$tabel_surcharges_j = array();
						$tabel_paidouts_j = array();

                        //$invoice_j = array();

						//$client_j = SelDataFromDB('spr_clients', $calculate_j[0]['client_id'], 'user');
						//var_dump($client_j);

                        $db = new DB();

                        //Замечания в этом месяце
                        $query = "
                            SELECT j_rte.*
                            FROM `journal_remark_to_employee` j_rte
                            WHERE j_rte.worker_id = :worker_id AND MONTH(j_rte.date_in) = :month AND YEAR(j_rte.date_in) = :year
                            ORDER BY j_rte.date_in";

                        $args = [
                            'month' => $tabel_j[0]['month'],
                            'year' => $tabel_j[0]['year'],
                            'worker_id' => $tabel_j[0]['worker_id']
                        ];

                        $remarks_j = $db::getRows($query, $args);
                        //var_dump($remarks_j);


                        //Другие табели этого сотрудника в этом месяце
                        $query = "
                            SELECT j_t_all.id, j_t_all.office_id
                            FROM `fl_journal_tabels` j_t_all
                            WHERE j_t_all.worker_id = :worker_id AND j_t_all.month = :month AND j_t_all.year = :year AND j_t_all.office_id <> :filial_id";

                        $args = [
                            'month' => $tabel_j[0]['month'],
                            'year' => $tabel_j[0]['year'],
                            'worker_id' => $tabel_j[0]['worker_id'],
                            'filial_id' => $tabel_j[0]['office_id']
                        ];

                        $tabels_all_j = $db::getRows($query, $args);
                        //var_dump($tabels_all_j);


                        echo '
                                <div id="status">
                                    <header>
                                        <div class="nav">';
                        if ($tabel_j[0]['worker_id'] == $_SESSION['id']){
                            echo '
                                            <a href="fl_my_tabels.php" class="b">Табели</a>';
                        }else {
                            echo '
                                            <a href="fl_tabels.php?who='.$tabel_j[0]['type'].'" class="b">Важный отчёт</a>
                                            <a href="fl_tabels2.php?who='.$tabel_j[0]['month'].'" class="b">Отчёт по часам</a>
                                            <a href="fl_tabels_check.php" class="b">Проверка табелей</a>
                                            <a href="fl_tabels_simple_pay.php?&filial='.$tabel_j[0]['office_id'].'&m='.$tabel_j[0]['month'].'&y='.$tabel_j[0]['year'].'&who='.$tabel_j[0]['type'].'" class="b">Проверка табелей 2</a>
                                            ';
                        }
                        echo '
                                        </div>
    
                                        <h2>Табель #'.$_GET['id'].'';

                        if (($finances['edit'] == 1) || $god_mode){
                            /*if ($calculate_j[0]['status'] != 9){
                                echo '
                                            <a href="invoice_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                            }*/
                            /*if (($calculate_j[0]['status'] == 9) && (($finances['close'] == 1) || $god_mode)){
                                echo '
                                    <a href="#" onclick="Ajax_reopen_tabel('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
                            }*/
                        }

                        if (($finances['close'] == 1) || $god_mode){
                            if (($tabel_j[0]['status'] != 9) && ($tabel_j[0]['status'] != 7)){
                                echo '
                                                    <span class="info" style="font-size: 100%; cursor: pointer;" title="Удалить" onclick="fl_deleteTabelItem('.$_GET['id'].');" ><i class="fa fa-trash-o" aria-hidden="true"></i></span>';
                            }else{
                                if ($tabel_j[0]['status'] == 9){
                                    echo '<br><i style="color:red;">Удалён (заблокирован).</i><br>';
                                }
                            }
                        }

                        if ($tabel_j[0]['status'] != 9) {
                            if ($tabel_j[0]['status'] == 7) {
                                echo ' <span style="color: green">Проведён <i class="fa fa-check" aria-hidden="true" style="color: green;"></i></span>';

                                if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)) {
                                    if (in_array($tabel_j[0]['office_id'], $optionsWF[$_SESSION['id']]) || $god_mode) {
                                        //if (($finances['reopen'] == 1) || ($god_mode)){
                                        echo '<span style="margin-left: 20px; font-size: 60%; color: red; cursor:pointer;" onclick="deployTabelDelete(' . $_GET['id'] . ');">   Снять отметку о проведении <i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 150%;"></i></span>';
                                        //}
                                    }
                                }

                            } else {
                                echo '<span style="margin-left: 20px; font-size: 60%; color: red;">Не проведён </span>';
                            }
                        }
                        if ($tabel_j[0]['status'] == 9){
                            echo '<i style="color:red;"> удален (заблокирован).</i><br>';
                        }

                        echo '			
                                        </h2>
                                    </header>';
                                   
                        echo '
                                    <div id="data" style="margin: 0;">
                                    
                                        <div style="font-size: 90%; margin-bottom: 5px;">';
                         echo '
                                            <div style="color: #252525; font-weight: bold;">'.$monthsName[$tabel_j[0]['month']].' '.$tabel_j[0]['year'].'</div>
                                            <div>
                                                Сотрудник <b>'.WriteSearchUser('spr_workers', $tabel_j[0]['worker_id'], 'user_full', true).'</b> ';
                        if ($permission_j != 0){
                            echo '/ <i style="font-size: 95%;">'.$permission_j[0]['name'].'</i>';
                        }
                        echo '
                                             </div>';

                        //Врачи
                        if (($tabel_j[0]['type'] == 5) || ($tabel_j[0]['type'] == 6) || ($tabel_j[0]['type'] == 10)) {
                            echo '
                                            <div>Филиал <b>' . $filials_j[$tabel_j[0]['office_id']]['name'] . '</b></div>';
                        }

                        //Табели в других филиалах
                        if (!empty($tabels_all_j)){
                            echo '<div><span style="font-size:80%;  color: rgb(255 134 0);">Табели в других филиалах за этот месяц:</span><div>';
                            foreach ($tabels_all_j as $all_tabel_data){
                                echo '<a href="fl_tabel.php?id='.$all_tabel_data['id']. '" class="b" style="padding: 3px 5px; font-size: 80%; border-color: rgb(0 127 237);"><b>Табель #' .$all_tabel_data['id'].'</b><br><i>' . $filials_j[$all_tabel_data['office_id']]['name'] . '</i></a>';
                            }
                        }

                        //Админы, ассистенты, санитарки, уборщицы, дворники
                        if (($tabel_j[0]['type'] == 4) || ($tabel_j[0]['type'] == 7) || ($tabel_j[0]['type'] == 13) || ($tabel_j[0]['type'] == 14) || ($tabel_j[0]['type'] == 15)) {
                            echo '
                                            <div>Филиал, к которому прикреплен сотрудник ';

                            if ($tabel_j[0]['office_id'] == 0){
                                echo '<span style="color: rgb(243, 0, 0);">не прикреплен</span>';
                            }else {
                                echo '<b>'.$filials_j[$tabel_j[0]['office_id']]['name'].'</b>';
                            }

                            echo '
                                            </div>';
                        }
                        echo '
		        						</div>';


                        //Выведем замечания, если они есть
                        if (!empty($remarks_j)){
                            echo '
                                <div>
                                    <span style="font-size:80%;  color: #555; background-color: rgb(255 255 164);">
                                        У сотрудника есть в этом месяце замечания:
                                    </span>
                                </div>
                                <div style="height: 20px;">';

                            foreach ($remarks_j as $remarks_data){
                                //var_dump($remarks_data);

                                $descr = $remarks_data['descr'];

                                //$descr = mb_strimwidth($remarks_data['descr'], 0, 25, "...", 'utf-8');

                                echo '
                                    <div id="hoverShowText" class="cellsBlockHover" style="/*background-color: rgba(151,255,255,0);*//* width: 170px;*/ margin: 1px 0; padding: 1px 5px; border: 1px solid rgba(179,210,210,0.4); display: inline-block; vertical-align: top;">
                                        <span style="font-size: 70%; margin: 1px 0; padding: 1px 5px; ">
                                            ' . $descr . '
                                        </span>
                                    </div>';
                            }
                            echo '
                                </div>';
                        }


                        //Получение данных
                        $summCalc = 0;
                        //var_dump(microtime(true) - $script_start);

                        $msql_cnnct = ConnectToDB2 ();
                        //var_dump(microtime(true) - $script_start);

                        //Отметки по дополнительным опциям
                        //!!! Здесь функция большая и избыточная, но лень переписывать
                        $spec_prikaz8_checked = '';
                        $spec_oklad_checked = '';
                        $spec_oklad_work_checked = '';

                        $query = "SELECT * FROM `options_worker_spec` WHERE `worker_id`='{$tabel_j[0]['worker_id']}' LIMIT 1";
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
                        //var_dump($spec_prikaz8);
//                        var_dump($spec_oklad);

                        //Категории процентов
                        $percent_cats_j = array();
                        //Для сортировки по названию
                        $percent_cats_j_names = array();
                        //$percent_cats_j = SelDataFromDB('fl_spr_percents', '', '');
                        $query = "SELECT `id`, `name` FROM `fl_spr_percents`";
                        //var_dump( $percent_cats_j);

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                $percent_cats_j[$arr['id']] = $arr['name'];
                                //array_push($percent_cats_j_names, $arr['name']);
                            }
                        }

                        //Основные данные
                        $query = "SELECT jcalc.*, 
                            GROUP_CONCAT(DISTINCT jcalcex.percent_cats ORDER BY jcalcex.percent_cats ASC SEPARATOR ',') AS percent_cats 
                            FROM `fl_journal_calculate` jcalc
                            LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalc.id = jcalcex.calculate_id
                            LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '".$tabel_j[0]['id']."' AND jtabex.noch = '0'
                            WHERE jtabex.calculate_id = jcalc.id
                            GROUP BY jcalc.id";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($tabel_ex_calculates_j, $arr);
                            }
                        }else{
                            //$sheduler_zapis = 0;
                            //var_dump ($sheduler_zapis);
                        }
                        //var_dump(microtime(true) - $script_start);

                        //var_dump($query);
                        //var_dump($tabel_ex_calculates_j);


                        $rezult = '';

                        foreach ($tabel_ex_calculates_j as $rezData){

                            //Наряды
                            $query = "SELECT `summ`, `summins`, `zapis_id`, `type`, `create_time` FROM `journal_invoice` WHERE `id`='{$rezData['invoice_id']}' LIMIT 1";

                            /*$query2 = "SELECT `summ` AS `summ`, `summins` AS `summins` FROM `journal_invoice` WHERE `id`='{$rezData['invoice_id']}'
                            UNION ALL (
                              SELECT `name` AS `name`, `full_name` AS `full_name` FROM `spr_clients` WHERE `id`='{$rezData['client_id']}'
                            )";*/

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {
                                /*while ($arr = mysqli_fetch_assoc($res)) {
                                    array_push($rez, $arr);
                                }*/

                                $arr = mysqli_fetch_assoc($res);
                                $summ = $arr['summ'];
                                $summins = $arr['summins'];
                                $invoice_create_time = date('d.m.y', strtotime($arr['create_time']));
                                $invoice_create_time2 = date('y.m.d', strtotime($arr['create_time']));
                                $invoice_create_time3 = $arr['create_time'];
                                $zapis_id = $arr['zapis_id'];
                                $invoice_type = $arr['type'];
                                //var_dump($zapis_id);
                                //var_dump($invoice_type);
                            }

                            $query = "SELECT `name`, `full_name` FROM `spr_clients` WHERE `id`='{$rezData['client_id']}' LIMIT 1";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {
                                /*while ($arr = mysqli_fetch_assoc($res)) {
                                    array_push($rez, $arr);
                                }*/

                                $arr = mysqli_fetch_assoc($res);
                                $name = $arr['name'];
                                $full_name = $arr['full_name'];
                            }

                            //Зубные формулы и запись врача
                            $doctor_mark = '';
                            $background_color = 'background-color: rgb(255, 255, 255);';

                            if ($invoice_type == 5) {
                                $query = "SELECT `id` FROM `journal_tooth_status` WHERE `zapis_id`='$zapis_id' LIMIT 1";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                $number = mysqli_num_rows($res);
                            }
                            if ($invoice_type == 6) {
                                $query = "SELECT `id` FROM `journal_cosmet1` WHERE `zapis_id`='$zapis_id' LIMIT 1";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                $number = mysqli_num_rows($res);
                            }

                            if ($number == 0) {
                                $doctor_mark = '<i class="fa fa-thumbs-down" aria-hidden="true" style="color: red; font-size: 110%;" title="Нет отметки врача"></i>';
                                $background_color = 'background-color: rgba(255, 141, 141, 0.2);';
                            }

                            $rezult .=
                                '
                                <div class="cellsBlockHover" data-sort="'.$invoice_create_time2.'" style="'.$background_color.' border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
                                    <div style="display: inline-block; width: 200px;">
                                        <div>
                                        <a href="fl_calculate.php?id='.$rezData['id'].'" class="ahref">
                                            <div>
                                                <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">';

                            //Если время не соответсвует текущему месяцу/году, сигнализируем
//                                $rezult .= date('y.m.01', time()).'<br>';
//                                $rezult .= $rezData['in_create_time'];
//                                $rezult .= $invoice_create_time2;
                            //$rezult .= date('y.m.01', time()) > $invoice_create_time2;

                            if (($tabel_j[0]['year'] ==  date('Y', strtotime($invoice_create_time3)))
                                && ($tabel_j[0]['month'] ==  date('m', strtotime($invoice_create_time3)))){
                            }else{
                                $rezult .= '
                                                        <i class="fa fa-warning" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(111, 111, 111, 0.8);" title="Дата наряда отличается от даты табеля"></i>';
                            }



                            $rezult .= '
                                                    <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                </div>
                                                <div style="display: inline-block; vertical-align: middle; font-size: 90%;">
                                                    <b>РЛ #'.$rezData['id'].'</b> <span style="font-size: 70%; color: rgb(115, 112, 112);">создано: '.date('d.m.y H:i', strtotime($rezData['create_time'])).'</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                    Сумма расчёта: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">'.$rezData['summ'].'</span> руб.
                                                </div>
                                            </div>
                                            
                                        </a>
                                        </div>
                                        <div style="margin: 5px 0 0 3px; font-size: 80%;">
                                            <b>Наряд: <a href="invoice.php?id='.$rezData['invoice_id'].'" class="ahref">#'.$rezData['invoice_id'].'</a> от '.$invoice_create_time.'<br>пац.: <a href="client.php?id='.$rezData['client_id'].'" class="ahref">'.$name.'</a><br>
                                            <!--Сумма: '.$summ.' р. Страх.: '.$summins.' р.</b> <br>-->
                                            Сумма: <span class="invoice_summ">' . $summ . '</span> р. Страх.: <span class="invoice_summ_ins">' . $summins . '</span> р.</b> <br>
                                            
                                        </div>
                                        <div style="margin: 5px 0 5px 3px; font-size: 80%;">';

                            //Категории процентов(работ)
                            $percent_cats_arr = explode(',', $rezData['percent_cats']);

                            foreach ($percent_cats_arr as $percent_cat){
                                if ($percent_cat > 0) {
                                    $bgColor = "";

                                    if (($percent_cat == 58) || ($percent_cat == 59) || ($percent_cat == 60) || ($percent_cat == 61)){
                                        $bgColor = "background-color: yellow;";
                                    }
                                    $rezult .= '<i class="percentCatID_'.$percent_cat .'" style="color: rgb(15, 6, 142); font-size: 110%; '.$bgColor.'">' . $percent_cats_j[$percent_cat] . '</i><br>';
                                }else{
                                    $rezult .= '<i style="color: red; font-size: 100%;">Ошибка #66</i><br>';
                                }
                                //$rezult .= '<i style="color: rgb(15, 6, 142); font-size: 110%;">'.$percent_cats_j[$percent_cat].'</i><br>';
                            }

                            $rezult .= '                                            
                                        </div>
                                    </div>';

                            if ($tabel_j[0]['status'] != 7) {
                                $rezult .= '
                                    <div style="display: inline-block; vertical-align: top;">
                                        <div class="settings_text" style="border: 1px solid #CCC; padding: 3px; margin: 1px; width: 12px; text-align: center;"  onclick="contextMenuShow(' . $tabel_j[0]['id'] . ', ' . $rezData['id'] . ', event, \'tabel_calc_options\');">
                                            <i class="fa fa-caret-down"></i>
                                        </div>
                                    </div>';
                            }

                            $rezult .= '
                                    <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                    <div style="position: absolute; bottom: 2px; right: 3px;">
                                        '.$doctor_mark.'
                                    </div>
                                </div>';

                            $summCalc += $rezData['summ'];

                        }
                        //var_dump(microtime(true) - $script_start);

                        //Вычеты на этом филиале
                        //$query = "SELECT * FROM `fl_journal_deductions` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

                        //Вычеты этому человеку за этот месяц везде
                        $query = "
                              SELECT fl_jd.*, fl_jt.month, fl_jt.year, fl_jt.office_id FROM 
                              `fl_journal_tabels` fl_jt
                              RIGHT JOIN `fl_journal_deductions` fl_jd ON fl_jt.id = fl_jd.tabel_id 
                              WHERE fl_jt.worker_id = '{$tabel_j[0]['worker_id']}' AND fl_jt.month = '{$tabel_j[0]['month']}' AND fl_jt.year = '{$tabel_j[0]['year']}' AND (fl_jt.status <> '9' OR fl_jt.id = '{$tabel_j[0]['id']}');";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($tabel_deductions_j, $arr);
                            }
                        }else{
                            //$sheduler_zapis = 0;
                            //var_dump ($sheduler_zapis);
                        }
                        //var_dump($tabel_deductions_j);
                        //var_dump(microtime(true) - $script_start);

                        //Всего удержано
                        $rezultD = '';

                        //Что уже внесено в других табелях во всех филиалах
                        $rezultSall = '';


                        if (!empty($tabel_deductions_j)) {

                            foreach ($tabel_deductions_j as $rezData) {
                                if ($rezData['tabel_id'] == $tabel_j[0]['id']) {
                                    $rezultD .=
                                        '
                                    <div class="cellsBlockHover" style="background-color: #ffffff; border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
                                        <div style="display: inline-block; width: 200px;">
                                            <div>
                                            <a href="#" class="ahref">
                                                <div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                    </div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 90%;">
                                                        <b>';
                                    if ($rezData['type'] == 2) {
                                        $rezultD .= ' налог ';
                                    } elseif ($rezData['type'] == 3) {
                                        $rezultD .= ' штраф/вычет ';
                                    } elseif ($rezData['type'] == 4) {
                                        $rezultD .= ' ссуда ';
                                    } elseif ($rezData['type'] == 5) {
                                        $rezultD .= ' за обучение ';
                                    } else {
                                        $rezultD .= ' за материалы ';
                                    }
                                    $rezultD .=
                                        ' #' . $rezData['id'] . '</b> <span style="    color: rgb(115, 112, 112);"><br>создано: ' . date('d.m.y H:i', strtotime($rezData['create_time'])) . '</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                        Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                                    </div>
                                                </div>
                                                
                                            </a>
                                            </div>';
                                    if (mb_strlen($rezData['descr']) > 0) {
                                        $rezultD .= '
                                            <div style="margin: 5px 0 0 3px; font-size: 80%;">
                                                <b>Комментарий:</b> ' . $rezData['descr'] . '                                                
                                            </div>';
                                    }
                                    $rezultD .= '
                                        </div>';
                                    if ($tabel_j[0]['status'] != 7) {
                                        $rezultD .= '
                                        <div style="display: inline-block; vertical-align: top;">
                                            <div class="settings_text" style="border: 1px solid #CCC; padding: 3px; margin: 1px; width: 12px; text-align: center;"  onclick="contextMenuShow(' . $tabel_j[0]['id'] . ', ' . $rezData['id'] . ', event, \'tabel_deduction_options\');">
                                                <i class="fa fa-caret-down"></i>
                                            </div>
                                        </div>';
                                    }
                                    $rezultD .= '
                                        <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                    </div>';

                                    //$summCalc += $rezData['summ'];
                                }

                                $rezultSall .= '
                                    <a href="fl_tabel.php?id=' . $rezData['tabel_id'] . '" class="b" style="font-size: 80%; padding: 5px; border-color: red; ">
                                        <div style="font-weight: bold;">';
                                if ($rezData['type'] == 2) {
                                    $rezultSall .= ' налог   ';
                                } elseif ($rezData['type'] == 4) {
                                    $rezultSall .= ' ссуда ';
                                } elseif ($rezData['type'] == 5) {
                                    $rezultSall .= ' обучение ';
                                } else {
                                    $rezultSall .= ' штраф ';
                                }
                                $rezultSall .= '
                                            #' . $rezData['id'] . '
                                        </div>
                                        <div style="margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                            Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                        </div>
                                        <div style="font-size: 80%;">
                                            В табеле '.$rezData['tabel_id'].'';
                                            if ($rezData['tabel_id'] == $_GET['id']){
                                                $rezultSall .= '<b>[в этом]</b>';
                                            }else {
                                                $rezultSall .= '';
                                            }
                                            $rezultSall .= '<br> (['.$filials_j[$rezData['office_id']]['name2'].'] '.$monthsName[$rezData['month']].' '.$rezData['year'].')
                                        </div>
                                    </a>';
                            }
                        }
                        //var_dump(microtime(true) - $script_start);

                        //Надбавки к этому табелю
                        //$query = "SELECT * FROM `fl_journal_surcharges` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

                        //Надбавки этому человеку за этот месяц везде
                        $query = "
                              SELECT fl_js.*, fl_jt.month, fl_jt.year, fl_jt.office_id FROM 
                              `fl_journal_tabels` fl_jt
                              RIGHT JOIN `fl_journal_surcharges` fl_js ON fl_jt.id = fl_js.tabel_id 
                              WHERE fl_jt.worker_id = '{$tabel_j[0]['worker_id']}' AND fl_jt.month = '{$tabel_j[0]['month']}' AND fl_jt.year = '{$tabel_j[0]['year']}' AND (fl_jt.status <> '9' OR fl_jt.id = '{$tabel_j[0]['id']}');";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($tabel_surcharges_j, $arr);
                            }
                        }else{
                            //$sheduler_zapis = 0;
                        }
                        //var_dump ($tabel_surcharges_j);
                        //var_dump(microtime(true) - $script_start);

                        //Начислено в этот табель
                        $rezultS = '';

                        if (!empty($tabel_surcharges_j)) {

                            foreach ($tabel_surcharges_j as $rezData) {
//                                var_dump($rezData);

                                if ($rezData['tabel_id'] == $tabel_j[0]['id']) {

                                    $rezultS .=
                                        '
                                        <div class="cellsBlockHover" style="background-color: #ffffff; border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
                                            <div style="display: inline-block; width: 200px;">
                                                <div>
                                                <a href="#" class="ahref">
                                                    <div>
                                                        <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                            <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                        </div>
                                                        <div style="display: inline-block; vertical-align: middle; font-size: 90%;">
                                                            <b>';
                                    if ($rezData['type'] == 2) {
                                        $rezultS .= ' отпускной ';
                                    } elseif ($rezData['type'] == 3) {
                                        $rezultS .= ' больничный ';
                                    } else {
                                        $rezultS .= ' прочее ';
                                    }
                                    $rezultS .=
                                        '#' . $rezData['id'] . '</b> <span style="    color: rgb(115, 112, 112);"><br>создано: ' . date('d.m.y H:i', strtotime($rezData['create_time'])) . '</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                            Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                                        </div>
                                                    </div>
                                                    
                                                </a>
                                                </div>';

                                        $rezultS .= '
                                                <div style="margin: 5px 0 0 3px; font-size: 80%;">
                                                    <b>Комментарий:</b> ' . $rezData['descr'] . '                                                
                                                </div>';

                                    $rezultS .= '
                                            </div>';
                                    if ($tabel_j[0]['status'] != 7) {
                                        $rezultS .= '
                                            <div style="display: inline-block; vertical-align: top;">
                                                <div class="settings_text" style="border: 1px solid #CCC; padding: 3px; margin: 1px; width: 12px; text-align: center;"  onclick="contextMenuShow(' . $tabel_j[0]['id'] . ', ' . $rezData['id'] . ', event, \'tabel_surcharge_options\');">
                                                    <i class="fa fa-caret-down"></i>
                                                </div>
                                            </div>';
                                    }
                                    $rezultS .= '
                                            <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                        </div>';

                                    //$summCalc += $rezData['summ'];
                                }

                                $rezultSall .= '
                                    <a href="fl_tabel.php?id='.$rezData['tabel_id'].'" class="b" style="font-size: 80%; padding: 5px; border-color: green;">
                                        <div style="font-weight: bold;">';
                                if ($rezData['type'] == 2) {
                                    $rezultSall .= ' отпускной ';
                                } elseif ($rezData['type'] == 3) {
                                    $rezultSall .= ' больничный ';
                                } else {
                                    $rezultSall .= ' прочее ';
                                }
                                $rezultSall .= '
                                            #'.$rezData['id'].'
                                        </div>
                                        <div style="margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                            Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                        </div>
                                        <div style="font-size: 80%;">
                                            В табеле '.$rezData['tabel_id'].'';
                                            if ($rezData['tabel_id'] == $_GET['id']){
                                                $rezultSall .= '<b>[в этом]</b>';
                                            }else {
                                                $rezultSall .= '';
                                            }
                                            $rezultSall .= '<br> (['.$filials_j[$rezData['office_id']]['name2'].'] '.$monthsName[$rezData['month']].' '.$rezData['year'].')
                                        </div>
                                    </a>';
//                                var_dump($rezData['id']);
                            }
                        }

                        //var_dump(microtime(true) - $script_start);

                        //Выплаты
                        $query = "SELECT * FROM `fl_journal_paidouts` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($tabel_paidouts_j, $arr);
                            }
                        }else{
                            //$sheduler_zapis = 0;
                            //var_dump ($sheduler_zapis);
                        }
                        //var_dump(microtime(true) - $script_start);

                        $rezultP = '';

                        if (!empty($tabel_paidouts_j)) {

                            foreach ($tabel_paidouts_j as $rezData) {

                                $rezultP .=
                                    '
                                    <div class="cellsBlockHover" style="background-color: #ffffff; border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
                                        <div style="display: inline-block; width: 200px;">
                                            <div>
                                            <a href="#" class="ahref">
                                                <div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                    </div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 90%;">
                                                        <b>';
                                if ($rezData['type'] == 1){
                                    $rezultP .= ' аванс ';
                                }elseif ($rezData['type'] == 2){
                                    $rezultP .= ' отпускной ';
                                }elseif ($rezData['type'] == 3){
                                    $rezultP .= ' больничный ';
                                }elseif ($rezData['type'] == 4){
                                    $rezultP .= ' на карту ';
                                }elseif ($rezData['type'] == 7){
                                    $rezultP .= ' зп ';
                                }elseif ($rezData['type'] == 5){
                                    $rezultP .= ' ночь ';
                                }else{
                                    $rezultP .= ' !!!ошибка данных ';
                                }
                                $rezultP .=
                                                        '#' . $rezData['id'] . '</b> <span style="    color: rgb(115, 112, 112);"><br>создано: ' . date('d.m.y H:i', strtotime($rezData['create_time'])) . '</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                        Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                                    </div>
                                                </div>
                                                
                                            </a>
                                            </div>';
                                if (mb_strlen($rezData['descr']) > 0){
                                    $rezultP .= '
                                            <div style="margin: 5px 0 0 3px; font-size: 80%;">
                                                <b>Комментарий:</b> '.$rezData['descr'].'                                                
                                            </div>';
                                }
                                $rezultP .= '
                                        </div>';


                                if ($tabel_j[0]['status'] != 7) {
                                    $rezultP .= '
                                        <div style="display: inline-block; vertical-align: top;">
                                            <div class="settings_text" style="border: 1px solid #CCC; padding: 3px; margin: 1px; width: 12px; text-align: center;"  onclick="contextMenuShow(' . $tabel_j[0]['id'] . ', ' . $rezData['id'] . ', event, \'tabel_paidout_options\');">
                                                <i class="fa fa-caret-down"></i>
                                            </div>
                                        </div>';
                                }
                                $rezultP .= '
                                        <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                    </div>';

                                //$summCalc += $rezData['summ'];

                            }
                        }
                        //var_dump(microtime(true) - $script_start);

                        //Ночные смены с суммами врачи !!!! стоит сюда добавить ассистов
                        if (($tabel_j[0]['type'] == 5) || ($tabel_j[0]['type'] == 6) || ($tabel_j[0]['type'] == 10) || ($tabel_j[0]['type'] == 7)) {
                            //Смена/график
                            $rezultShed = array();
                            $nightSmena = 0;

                            $query = "SELECT `id`, `day`, `smena`, `kab`, `worker` FROM `scheduler` WHERE `worker` = '{$tabel_j[0]['worker_id']}' AND `month` = '" . (int)$tabel_j[0]['month'] . "' AND `year` = '{$tabel_j[0]['year']}' AND `filial`='{$tabel_j[0]['office_id']}'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {
                                while ($arr = mysqli_fetch_assoc($res)) {
                                    //Раскидываем в массив
                                    array_push($rezultShed, $arr);
                                    //Если ночная смена
                                    if ($arr['smena'] == 3) {
                                        $nightSmena++;
                                    }
                                }
                            }

                            /*var_dump($query);
                            var_dump(count($rezultShed));
                            var_dump($rezultShed);*/
                            //var_dump(microtime(true) - $script_start);

//                            $tabels_noch_j = array();
//
//                            $query = "SELECT * FROM `fl_journal_reports_noch` WHERE `tabel_id` = '{$tabel_j[0]['id']}'";
//
//                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                            $number = mysqli_num_rows($res);
//
//                            if ($number != 0) {
//                                while ($arr = mysqli_fetch_assoc($res)) {
//                                    //Раскидываем в массив
//                                    array_push($tabels_noch_j, $arr);
//                                }
//                            }
                            //var_dump($tabels_noch_j);

                        }

                        //Админы, ассистенты, санитарки, уборщицы, дворники
                        if (($tabel_j[0]['type'] == 4) || ($tabel_j[0]['type'] == 7) || ($tabel_j[0]['type'] == 13) || ($tabel_j[0]['type'] == 14) || ($tabel_j[0]['type'] == 15) || ($tabel_j[0]['type'] == 11)
                        || $spec_oklad_work) {
                            //Часы работника
                            $w_hours = 0;
                            $w_normaSmen = 0;

                            if ($tabel_j[0]['hours_count'] != NULL) {
                                $hours_arr = explode(',', $tabel_j[0]['hours_count']);
                                //var_dump($hours_arr);

                                $w_hours = $hours_arr[0];
                                $w_normaSmen = $hours_arr[1];
                            }

                            $w_percentHours = $tabel_j[0]['hours_percent'];
                        }

                        //Фиксированные выплаты и удержания, которые надо внести (налоги, прочее, ...)
                        //Налоги
                        $tax_arr = array();

                        $query = "SELECT * FROM  `fl_journal_taxes` WHERE `worker_id` = '{$tabel_j[0]['worker_id']} LIMIT 1';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0) {
                            $arr = mysqli_fetch_assoc($res);

                            array_push($tax_arr, $arr);
                        }
                        //var_dump($tax_arr);

                        $surcharge_arr = array();

                        //Прочие доплаты сотрудникам (фиксированные)
                        $query = "SELECT * FROM `fl_spr_surcharges` WHERE `worker_id` = '{$tabel_j[0]['worker_id']}' AND `type` = '1'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0) {
                            $arr = mysqli_fetch_assoc($res);

                            array_push($surcharge_arr, $arr);
                        }
                        //var_dump($surcharge_arr);

                        //Выводим на экран
                        if (!empty($tax_arr) || !empty($surcharge_arr)){
                            echo '<div><span style="font-size:80%;  color: #555;">Фиксированные удержания и документы к выплате, которые необходимо внести:</span><div>';
                            if (!empty($tax_arr)){
                                foreach ($tax_arr as $rezData) {
//                                var_dump($rezData);

                                    echo '
                                    <div class="cellsBlockHover" style="width: 130px; margin: 1px 0; padding: 1px 5px; border: 1px dashed red; display: inline-block;">
                                        <div style="font-size: 70%; font-weight: bold;">
                                            налог
                                        </div>
                                        <div style="font-size: 65%; margin: 1px 0; padding: 1px 5px;">
                                            Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                        </div>
                                    </div>';
//                                var_dump($rezData['id']);
                                }
                            }
                            if (!empty($surcharge_arr)){
                                foreach ($surcharge_arr as $rezData) {
//                                var_dump($rezData);

                                    echo '
                                    <div class="cellsBlockHover" style="width: 130px; margin: 1px 0; padding: 1px 5px; border: 1px dashed green;  display: inline-block;">
                                        <div style="font-size: 70%; font-weight: bold;">';
                                    if ($rezData['type'] == 2) {
                                        echo ' отпускной ';
                                    } elseif ($rezData['type'] == 3) {
                                        echo ' больничный ';
                                    } else {
                                        echo ' прочее ';
                                    }
                                    echo '
                                        </div>
                                        <div style="font-size: 65%; margin: 1px 0; padding: 1px 5px;">
                                            Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                        </div>
                                    </div>';
//                                var_dump($rezData['id']);
                                }
                            }
                            echo '</div></div>';
                        }



                        echo '<span style="font-size:80%;  color: #555;">Удержания и документы к выплате, уже выписанные данному сотруднику в этом месяце:</span>';

                        //var_dump($rezultSall);
                        if (mb_strlen($rezultSall) > 0){
                            echo '<br>'.$rezultSall;
                        }else {
                            echo '<span style="color: red; font-size: 80%;"> ничего нет</span>';
                        }
                        echo '<br>';

                        //Врачи
                        if (($tabel_j[0]['type'] == 5) || ($tabel_j[0]['type'] == 6) || ($tabel_j[0]['type'] == 10) || ($tabel_j[0]['type'] == 7)) {
                            //все те же, кроме ассист
                            if (($tabel_j[0]['type'] == 5) || ($tabel_j[0]['type'] == 6) || ($tabel_j[0]['type'] == 10)){
                                //Смены
                                echo '
                                    <div style="background-color: rgba(181, 165, 165, 0.16); border: 1px dotted #AAA; margin: 5px 0 5px; padding: 1px 3px; ">
                                        <div>
                                            <div style="margin-bottom: 5px;">
                                                <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
                                                    Всего смен в этом месяце в этом филиале: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">' . count($rezultShed) . '</span>
                                                </div>
                                            </div>';

                                //!!!Цена одной пустой смены (!!! перенести потом отдельно в какой-нибудь справочник что ли)
                                $empty_smena_price = 500;

                                echo '
                                            <div style="margin: 10px 0;">
                                                <div style="font-size: 90%;  color: #555;">
                                                    <span style="color: rgba(10, 10, 10, 1);">Надбавка за "пустые смены".</span> ('.$empty_smena_price.' руб. за одну "пустую" смену)
                                                </div>';

                                if ($tabel_j[0]['empty_smena'] == 0) {
                                    echo '
                                            <div style="font-size: 90%;  color: #555;">
                                                Введите количество "пустых" смен: <input type="number" value="" min="0" max="99" size="2" name="emptySmens" id="emptySmens" class="who2" placeholder="0" style="font-size: 13px; text-align: center;">
                                            </div>';
                                    if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9) && (($finances['see_all'] == 1) || $god_mode)) {
                                        echo ' 
                                            <button class="b" style="font-size: 80%;" onclick="showEmptySmenaAddINTabel(' . $_GET['id'] . ');">Добавить в табель оплату <b>пустых</b> смен</button>';
                                    }

                                } else {
                                    echo '<div style="font-size: 80%; color: rgb(7, 199, 41); padding-top: 5px;">В табель уже включена сумма за "пустые" смены <span style="font-size: 120%; font-weight: bold;">' . $tabel_j[0]['empty_smena'] . '</span> руб. <span style="font-size: 120%; color: #247624;">За ' . ($tabel_j[0]['empty_smena'] / $empty_smena_price) . ' смен</span>';
                                    if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9)) {

                                        if ($tabel_j[0]['empty_smena'] / $empty_smena_price != 1) {
                                            echo '<a href="#" class="b" style="font-size: 80%; padding: 2px 7px; color: black;" title="Убрать одну смену" onclick="Ajax_emptySmenaAddINTabel (' . $_GET['id'] . ', ' . ($tabel_j[0]['empty_smena'] / $empty_smena_price - 1) . ');">-1</a>';
                                        }

                                        echo '<a href="#" class="b" style="font-size: 80%; padding: 2px 7px; color: black;" title="Добавить одну смену" onclick="Ajax_emptySmenaAddINTabel (' . $_GET['id'] . ', '.($tabel_j[0]['empty_smena'] / $empty_smena_price + 1).');">+1</a>';

                                        echo '<span style="margin-left: 20px; font-size: 90%; color: red; cursor:pointer;" onclick="emptySmenaTabelDelete(' . $_GET['id'] . ');"><i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 150%;"></i> Удалить из табеля "пустые" смены</span>';
                                    }
                                    echo '</div>';
                                }

                                echo '
                                            </div>
                                        </div>
                                         <!--<div><a href = "fl_deduction_in_tabel_add.php?tabel_id=' . $_GET['id'] . '" class="b" style = "font - size: 80 %;" > Добавить вычет </a ></div >-->
                                    </div>';
                            }

                            //Ночные
//                            if (!empty($tabels_noch_j)) {
//
//                                echo '
//                                <div style="background-color: rgba(181, 165, 165, 0.16); border: 1px dotted #AAA; margin: 2px 0 10px; padding: 1px 3px; ">
//                                    <div>
//                                        <div style="margin-bottom: 5px;">
//                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
//                                                Всего ночных смен по графику: <b>'.$nightSmena.'</b>. Оформлено: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">' . count($tabels_noch_j) . '</span> смен <a href="fl_report_noch.php" class="ahref button_tiny">Отчёт ночь</a>
//                                            </div>
//                                            <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; margin-bottom: 10px; margin-top: 10px; vertical-align: top;">
//                                                <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
//                                                <!--Ночные--> ';
//
//                                echo '
//                                                    <div id="allNightTabelsIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allNightTabelsIsHere\');">подробно:</div>
//                                                    </div >
//                                                    <div id = "allNightTabelsIsHere" style = "" >';
//
//                                $noch_summ = 0;
//
//                                //Выведем все рассчеты по ночам
//                                foreach ($tabels_noch_j as $tabels_noch_item){
//                                    //var_dump($tabels_noch_item);
//
//                                    $noch_summ += $tabels_noch_item['summ'];
//
//                                    echo '
//                                                        <div class="cellsBlockHover" style="background-color: #ffffff; border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
//                                                            <div style="display: inline-block; width: 200px;">
//                                                                <div>
//                                                                <!--<a href="#" class="ahref">-->
//                                                                    <div>
//                                                                        <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
//                                                                            <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
//                                                                        </div>
//                                                                        <div style="display: inline-block; vertical-align: middle; font-size: 95%;">
//                                                                            <b>#' . $tabels_noch_item['id'] . ' за ночн. смену</b> <br>
//                                                                            от '.$tabels_noch_item['day'].'.'.$tabels_noch_item['month'].'.'.$tabels_noch_item['year'].'<br>
//                                                                            <span style="font-size: 85%; color: rgb(115, 112, 112);">создано: ' . date('d.m.y H:i', strtotime($tabels_noch_item['create_time'])) . '</span>
//                                                                        </div>
//                                                                    </div>
//                                                                    <div>
//                                                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
//                                                                            Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $tabels_noch_item['summ'] . '</span> руб.
//                                                                        </div>
//                                                                    </div>
//
//                                                                <!--</a>-->
//                                                                </div>';
//
//                                    echo '
//                                                            </div>';
//
//
//                                    if ($tabel_j[0]['status'] != 7) {
//                                        echo '
//                                                            <div style="display: inline-block; vertical-align: top;">
//                                                                <div class="settings_text" style="border: 1px solid #CCC; padding: 3px; margin: 1px; width: 12px; text-align: center;"  onclick="contextMenuShow(' . $tabel_j[0]['id'] . ', ' . $tabels_noch_item['id'] . ', event, \'tabel_night_options\');">
//                                                                    <i class="fa fa-caret-down"></i>
//                                                                </div>
//                                                            </div>';
//                                    }
//                                    echo '
//                                                        </div>';
//                                }
//
//                                echo '
//                                                    </div>';
//                                echo '
//                                                </div>
//                                            </div>';
//
//                                echo '
//                                            <div>Всего начислено за ночь: <span class="calculateOrder" style="font-size: 13px;">' . $noch_summ . '</span> руб.</div>';
//                                echo '
//                                        </div>
//                                    </div>
//                                </div>';
//
//                            }


                        }


                        //Админы, ассистенты, санитарки, уборщицы, дворники, особые отметки
                        if (($tabel_j[0]['type'] == 4) || ($tabel_j[0]['type'] == 7) || ($tabel_j[0]['type'] == 13) || ($tabel_j[0]['type'] == 14) || ($tabel_j[0]['type'] == 15) || ($tabel_j[0]['type'] == 11)
                        || $spec_oklad_work) {

//                            if ($tabel_j[0]['type'] == 11) {
//                                !!! хотел тут рассчтитать нормы рабочих дней для прочее, но лень
//                            }

                            //Часы
                            echo '
                                <div style="background-color: rgba(181, 165, 165, 0.16); border: 1px dotted #AAA; margin: 5px 0 10px; padding: 1px 3px; ">
                                    <div>
                                        <div style="margin-bottom: 5px;">
                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
                                                Оклад: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">' . $tabel_j[0]['salary'] . ' руб.</span>
                                            </div>
                                        </div>';

                            if ($tabel_j[0]['type'] != 11) {
                                echo '
                                        <div style="margin-bottom: 5px;">
                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
                                                Категория: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">' . $category_j[0]['name'] . '</span>
                                            </div>
                                        </div>';
                            }

                            echo '
                                        <div style="margin-bottom: 7px;">
                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1); display: inline;">
                                                Всего часов в этом месяце: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">' . $w_hours . '</span>
                                            </div>
                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1); display: inline;">
                                                (<span class="allMonthHours" style="font-size: 12px; /*font-weight: bold; text-shadow: 1px 1px rgba(111, 111, 111, 0.8);*/">' . $w_percentHours . '</span>% от нормы ';

                            if (($tabel_j[0]['type'] != 11) && !$spec_oklad_work) {
                                echo $w_normaSmen . ' часов';
                            }

                            echo ')
                                            </div>
                                        </div>';
                            echo '
                                        <div style="margin-bottom: 5px;">
                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
                                               Начислено за время: <span class="" style="font-size: 14px; color: #555;  font-weight: bold;">' . number_format($tabel_j[0]['per_from_salary'], 0, '.', '') . ' руб. </span>
                                            </div>
                                        </div>';
                            //Админы, ассистенты
                            if (($tabel_j[0]['type'] == 4) || ($tabel_j[0]['type'] == 7)){
                                echo '
                                        <div style="margin-bottom: 5px;">
                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
                                                <!--Процент с выручки: <span class="" style="font-size: 14px; color: #555;  font-weight: bold;">' . number_format($tabel_j[0]['percent_summ'], 0, '.', '') . ' руб. <span style="font-weight: normal;">('.$tabel_j[0]['revenue_percent'].'%)</span></span>-->
                                                Сумма от процентов с выручки: <span class="" style="font-size: 14px; color: #555;  font-weight: bold;">' . number_format($tabel_j[0]['percent_summ'], 0, '.', '') . ' руб. </span>
                                                
                                            </div>
                                        </div>
                                        ';
                            }
                            echo '
                                        </div>
                                    </div>
                                     <!--<div><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'" class="b" style = "font - size: 80 %;" > Добавить вычет </a ></div >-->
                                </div>';
                        }


                        //Врачи
                        if ((($tabel_j[0]['type'] == 5) || ($tabel_j[0]['type'] == 6) || ($tabel_j[0]['type'] == 10))/* && !$spec_oklad_work*/) {
                            echo '
                                        <div style="background-color: rgba(230, 203, 72, 0.34); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            Сумма всех РЛ: <span class="calculateOrder" style="font-size: 13px">' . $tabel_j[0]['summ_calc'] . '</span> руб. <div style="display: inline; color: #5f5f5f; font-size: 90%; font-style: italic;">Сумма всех нарядов: <span id="invoiceSumm"></span>  руб. (включая страховые)</div>
                                        </div>';
                        }
                        //Админы, ассистенты
                        if (($tabel_j[0]['type'] == 4) || ($tabel_j[0]['type'] == 7)) {
                            if (($tabel_j[0]['type'] == 7) || $spec_oklad_work){
                                echo '
                                        <div style="font-size: 90%; background-color: rgba(230, 203, 72, 0.34); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            Сумма всех РЛ: <span class="calculateOrder" style="font-size: 13px">' . $tabel_j[0]['summ_calc'] . '</span> руб.
                                        </div>';
                            }
                            echo '
                                        <div style="background-color: rgba(230, 203, 72, 0.34); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            Расчёт: <span class="calculateOrder" style="font-size: 13px">' . ($tabel_j[0]['summ'] + $tabel_j[0]['summ_calc']) . '</span> руб.
                                        </div>';
                        }
                        //Санитарки, уборщицы, дворники
                        if (($tabel_j[0]['type'] == 13) || ($tabel_j[0]['type'] == 14) || ($tabel_j[0]['type'] == 15)) {
                            echo '
                                        <div style="background-color: rgba(230, 203, 72, 0.34); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            Расчёт: <span class="calculateOrder" style="font-size: 13px">' . ($tabel_j[0]['summ'] + $tabel_j[0]['summ_calc']) . '</span> руб.
                                        </div>';
                        }


                        echo '
                                        <div style="background-color: rgba(72, 230, 194, 0.16); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">';

                        if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9) && (($finances['see_all'] == 1) || $god_mode)) {
                            echo '<div style="display: inline;"><a href="fl_surcharge_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=2" class="b" style = "font-size: 80%;" >Добавить отпускной</a ></div>';
                            echo '<div style="display: inline;"><a href="fl_surcharge_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=3" class="b" style="font-size: 80%;">Добавить больничный</a></div>';
                            echo '<div style="display: inline;"><a href="fl_surcharge_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=1" class="b" style="font-size: 80%;">Добавить прочее</a></div>';
                            echo '<div style="display: inline;"><span class="b" style = "font-size: 80%;" onclick="showKoeffInTabelAdd('.$_GET['id'].', false, '.$tabel_j[0]['k_plus'].')">Коэффициент +</span></div >';
                        }

                        //Надбавки
                        if (mb_strlen($rezultS) > 0) {
                            echo '
                                <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; margin-bottom: 10px; vertical-align: top;">
                                    <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
                                        <!--Начислено--> ';

                            echo '
                                        <div id="allSurchargesIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allSurchargesIsHere\');">подробно:</div>
                                    </div>
                                    <div id="allSurchargesIsHere" style="">
                                        '.$rezultS.'
                                    </div>';
                            echo '
                                </div>';
                        }/*else{
                            echo ' [отсутствуют]</div>';
                        }
                        echo '
                                </div>';*/

                        if ($tabel_j[0]['k_plus'] != 0) {
                            echo '<div style="font-size: 80%; color: #555;">Применён коэффициент: +' . $tabel_j[0]['k_plus'] . '%</div>';
                        }
                        echo '
                                                
                                                <div>Всего дополнительно начислено: <span class="calculateOrder" style="font-size: 13px">' . $tabel_j[0]['surcharge'] . '</span> руб.</div>
                                            </div>
                                        </div>';



                        echo '
                                        <div style="background-color: rgba(230, 72, 72, 0.16); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">';

                        if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9) && (($finances['see_all'] == 1) || $god_mode)) {
                            //echo '<div style="display: inline;"><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=1" class="b" style = "font-size: 80%;" >За материалы +</a ></div >';
                            echo '<div style="display: inline;"><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=2" class="b" style = "font-size: 80%;" >Добавить налог</a ></div >';
                            echo '<div style="display: inline;"><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=3" class="b" style = "font-size: 80%;" >Добавить штраф/вычет</a ></div >';
                            echo '<div style="display: inline;"><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=4" class="b" style = "font-size: 80%;" >Добавить ссуду</a ></div >';
                            echo '<div style="display: inline;"><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=5" class="b" style = "font-size: 80%;" >Добавить обучение</a ></div >';
                            echo '<div style="display: inline;"><span class="b" style = "font-size: 80%;" onclick="showKoeffInTabelAdd('.$_GET['id'].', true, '.$tabel_j[0]['k_minus'].')">Коэффициент -</span></div >';
                        }

                        //Вычеты
                        if (mb_strlen($rezultD) > 0) {
                            echo '
                                <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; margin-bottom: 10px; vertical-align: top;">
                                    <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
                                        <!--Удержано--> ';

                            echo '
                                            <div id="allDeductionssIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allDeductionssIsHere\');">подробно:</div>
                                    </div>
                                    <div id="allDeductionssIsHere" style="">
                                        ' . $rezultD . '
                                    </div>';
                            echo '
                                </div>';
                        }/*else{
                            echo ' [отсутствуют]</div>';
                        }
                        echo '
                                </div>';*/

                        if ($tabel_j[0]['k_minus'] != 0){
                            echo '<div style="font-size: 80%; color: #555;">Применён коэффициент: -' . $tabel_j[0]['k_minus'] . '%</div>';
                        }

                        echo '
                                            <div>Всего удержано: <span class="calculateInvoice" style="font-size: 13px">' . $tabel_j[0]['deduction'] . '</span> руб.</div>
                                        </div>';

                        echo '
                                        <div style="background-color: rgba(1, 94, 255, 0.22); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">';

                        if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9) && (($finances['see_all'] == 1) || $god_mode)) {
                            echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=1&filial_id='.$tabel_j[0]['office_id'].'" class="b" style = "font-size: 80%;">Выплата аванса</a ></div>';
                            echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=7&filial_id='.$tabel_j[0]['office_id'].'" class="b" style = "font-size: 80%;">Выплата ЗП</a></div>';
                            echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=2&filial_id='.$tabel_j[0]['office_id'].'" class="b" style="font-size: 80%;">Выплата отпускного</a></div>';
                            echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=3&filial_id='.$tabel_j[0]['office_id'].'" class="b" style="font-size: 80%;">Выплата больничного</a></div>';
                            echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=4&filial_id='.$tabel_j[0]['office_id'].'" class="b" style="font-size: 80%;">Выплата на карту</a></div>';
                            //echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=5&filial_id='.$tabel_j[0]['office_id'].'" class="b" style="font-size: 80%;">За ночь +</a></div>';
                        }

                        //Выплачено
                        if (mb_strlen($rezultP) > 0) {
                            echo '
                                <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; vertical-align: top;">
                                    <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
                                        <!--Выплачено--> ';
                            echo '
                                        <div id="allPaidoutsIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allPaidoutsIsHere\');">подробно:</div>
                                    </div>
                                    <div id="allPaidoutsIsHere" style="">
                                        '.$rezultP.'
                                    </div>';
                            echo '
                                </div>';
                        }/*else{
                            echo ' [отсутствуют]</div>';
                        }
                        echo '
                                </div>';*/

                        echo '
                                                <div>Всего выплачено: <span class="calculateOrder" style="font-size: 13px; color: rgb(12, 0, 167);">' . $tabel_j[0]['paidout'] . '</span> руб.</div>
                                            </div>
                                        </div>';

//                        var_dump($tabel_j[0]['summ']);
//                        var_dump($tabel_j[0]['surcharge']);
//                        var_dump($tabel_j[0]['night_smena']);
//                        var_dump($tabel_j[0]['empty_smena']);
//                        var_dump($tabel_j[0]['paid']);
//                        var_dump($tabel_j[0]['paidout']);


//                        //Общая сумма, которую осталось выплатить = сумма (РЛ) + % с оклада + % с выручки + надбавки + за ночь + пустые смены - вычеты - оплачено - выплачено
//                        $summItog = $tabel_j[0]['summ'] + $tabel_j[0]['per_from_salary'] + $tabel_j[0]['percent_summ'] + $tabel_j[0]['surcharge'] + $tabel_j[0]['night_smena'] + $tabel_j[0]['empty_smena'] - $tabel_j[0]['deduction'] - $tabel_j[0]['paid'] - $tabel_j[0]['paidout'];
                        //Общая сумма, которую осталось выплатить = сумма (РЛ) + надбавки + за ночь + пустые смены
                        $summItog = $tabel_j[0]['summ'] + $tabel_j[0]['surcharge'] + $tabel_j[0]['night_smena'] + $tabel_j[0]['empty_smena'];
//                        var_dump($summItog);

                        //Если ассистент, то плюсуем сумму за РЛ
                        if (($tabel_j[0]['type'] == 7) || $spec_oklad_work){
                            $summItog += $tabel_j[0]['summ_calc'];
                        }
                        //var_dump($summItog);

                        // Если оклад с работой
                        if ($spec_oklad_work){
//                            $summItog += $tabel_j[0]['per_from_salary'];
                        }

                        //Коэффициенты +/-
                        if (($tabel_j[0]['k_plus'] != 0) || ($tabel_j[0]['k_minus'] != 0)){
                            $summItog = $summItog + $summItog/100*($tabel_j[0]['k_plus'] - $tabel_j[0]['k_minus']);
                        }
                        //var_dump($summItog);

                        //Общая сумма, которую осталось выплатить = всего сумма - вычеты - оплачено - выплачено
                        $summItog = $summItog - $tabel_j[0]['deduction'] - $tabel_j[0]['paid'] - $tabel_j[0]['paidout'];
//                        var_dump($summItog);


                        echo '
                                        <div style="background-color: rgba(56, 245, 70, 0.36); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">';

                        //Коэффициенты надбавки и вычета
                        if (($tabel_j[0]['k_plus'] != 0) || ($tabel_j[0]['k_minus'] != 0)){
                            $koeff = '';

                            if (($tabel_j[0]['k_plus'] != 0) && ($tabel_j[0]['k_minus'] != 0)) {
                                //echo $tabel_j[0]['k_plus'] . '% -' . $tabel_j[0]['k_minus'] . '% = ' ;

                                if ($tabel_j[0]['k_plus']-$tabel_j[0]['k_minus'] >= 0){
                                    //echo '+';
                                    $koeff = '+';
                                }

//                                if ($tabel_j[0]['k_plus']-$tabel_j[0]['k_minus'] < 0){
//                                    echo '-';
//                                }

                                //echo ($tabel_j[0]['k_plus'] - $tabel_j[0]['k_minus']) . '%';
                                $koeff .= ($tabel_j[0]['k_plus'] - $tabel_j[0]['k_minus']) . '%';

                            }

                            if ($tabel_j[0]['k_plus'] == 0) {
                                //echo '-' . $tabel_j[0]['k_minus'] . '%';
                                $koeff = '-' . $tabel_j[0]['k_minus'] . '%';
                            }

                            if ($tabel_j[0]['k_minus'] == 0) {
                                //echo '+'.$tabel_j[0]['k_plus'].'%';
                                $koeff = '+'.$tabel_j[0]['k_plus'].'%';
                            }


                            //$summItogOld = $summItog;
                            //Рассчет
                            //$summItog = $summItog + $summItog/100*($tabel_j[0]['k_plus'] - $tabel_j[0]['k_minus']);

                            echo '<div style="font-size: 80%; color: #555;">С учётом коэффициентов ('.$koeff.') :';
                            //echo ' '.$summItogOld.' руб. '.$koeff.' = '.intval($summItog).' руб.';
                            echo '</div>';


                        }

                        echo '
                                            <div>Итого осталось выплатить: <span id="summItog" class="calculateOrder" style="font-size: 16px; ', ($summItog) <= 0 ? 'color: red;' : '' ,'">' . intval($summItog) . '</span> руб.<br>
                                            <span style="font-size: 80%; color: #8C8C8C;">сумма округляется до целого для удобства расчетов</span></div>
                                            <div>';
                        if ($spec_prikaz8) {
                            echo '<span style="color: red; font-size: 90%;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%"></i> Сотруднику применяется приказ №8</span><br>';
                        }

                        //Если табель не удален, не закрыт, у пользователя есть права...
                        if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9) && (($finances['see_all'] == 1) || $god_mode)) {
                            //Если косметолог
                            if ($tabel_j[0]['type'] == 6) {
                                //Если сотруднику применяется приказ №8
                                if ($spec_prikaz8) {
                                    echo '
                                                <button class="b" style="font-size: 80%;color: white; background: #ff3636;" onclick="prikazNomerVosem(' . $tabel_j[0]['worker_id'] . ', ' . $_GET['id'] . ');">Применить приказ №8</button>';
                                }
                                //Пересчёт ботокса
                                echo '
                                                <button id="botoksButton" class="b" style="/*display: none; */font-size: 80%; background: rgb(252 255 54);" onclick="prikazNomerBotoks(' . $tabel_j[0]['worker_id'] . ', ' . $_GET['id'] . ');">Посчитать кол-во ботокса</button>';

                            }
                            echo '
                                                <button class="b" style="font-size: 80%;" onclick="deployTabel(' . $_GET['id'] . ');">Провести табель</button>';
                        }else{
                            //if ($tabel_j[0]['status'] == 7) {
                                /*echo '
                                                <button class="b" style="font-size: 80%;" onclick="deployTabelOFF(' . $_GET['id'] . ');">Распровести табель</button>';*/
                           // }
                        }
                        /*if ($tabel_j[0]['status'] != 7) {
                            echo '
                                                <a href="fl_payroll_add.php?tabel_id=' . $tabel_j[0]['id'] . '&type=prepaid" class="b" style="font-size: 80%;" >Сделать выплату</a>';
                        }*/

                        echo '
                                                <a href="fl_tabel_print.php?tabel_id=' . $tabel_j[0]['id'] . '&noch=0" class="b" style="font-size: 80%;" >Распечатать</a>';

                        echo '
                                            </div>
                                        </div>';


                        //Врачи
                        if (($tabel_j[0]['type'] == 5) || ($tabel_j[0]['type'] == 6) || ($tabel_j[0]['type'] == 10) || ($tabel_j[0]['type'] == 7)) {
                            //Выводим
                            //Расчетные листы
                            echo '
                                <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; margin-bottom: 10px; vertical-align: top;">
                                    <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
                                        Расчётные листы <div id="allCalcsIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allCalcsIsHere\');">показать/скрыть</div>
                                    </div>
                                    <div id="allCalcsIsHere" style="">
                                        ' . $rezult . '
                                    </div>
                                </div>';
                        }

                        echo '	
						        <input type="hidden" name="noch" id="noch" value="0">
					        </div>
					        
					        <div id="doc_title">Табель #'.$_GET['id'].' - Асмедика</div>
					        </div>
					        <!-- Подложка только одна -->
					        <div id="overlay"></div>';

                        echo '
                            <script type="text/javascript">
                                $(document).ready(function(){
                                    
                                    //Посчитаем сумму нарядов
                                    let all_invoice_summ = 0;
                                    let all_invoice_summ_ins = 0;
                                    
                                    $(".invoice_summ").each(function(){
//                                        console.log($(this).html());

                                        all_invoice_summ += Number($(this).html());
                                    })
                                    
                                    $(".invoice_summ_ins").each(function(){
//                                        console.log($(this).html());

                                        all_invoice_summ_ins += Number($(this).html());
                                    })
//                                    console.log(all_invoice_summ);
//                                    console.log(all_invoice_summ_ins);
                                    
                                    //Выводим сумму
                                    $("#invoiceSumm").html(number_format(all_invoice_summ + all_invoice_summ_ins, 0, \'.\', \' \'));
                                    
                                    
                                    //Пересчёт по ботоксу, считаем сколько его было
                                    //Количество элементов с одинаковым классом percentCatID_23 (Ботокс)
                                    //$(".percentCatID_23").length
                                    
                                    //Если больше 10 открываем кнопку
                                    if ($(".percentCatID_23").length > 10){                                    
                                        $("#botoksButton").show();
                                    }
                                    
                                    
                                });


                            </script>';

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