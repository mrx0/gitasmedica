<?php

//fl_tabel_noch.php
//Табель ночной

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if ($_GET){
            if (isset($_GET['id'])){

                include_once 'DBWork.php';
                include_once 'functions.php';

                $tabel_j = SelDataFromDB('fl_journal_tabels_noch', $_GET['id'], 'id');
                //var_dump($tabel_j);

                if ($tabel_j != 0){
                    //var_dump($tabel_j);
                    //array_push($_SESSION['invoice_data'], $_GET['client']);
                    //$_SESSION['invoice_data'] = $_GET['client'];
                    //var_dump($calculate_j[0]['closed_time'] == 0);

                    //$invoice_type = $tabel_j[0]['type'];

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


                        echo '
                                <div id="status">
                                    <header>
                                        <div class="nav">';
                        if ($tabel_j[0]['worker_id'] == $_SESSION['id']){
                            echo '
                                            <a href="fl_my_tabels.php" class="b">Табели</a>';
                        }else {
                            echo '
                                            <a href="fl_tabels.php" class="b">Важный отчёт</a>';
                        }
                        echo '
                                        </div>
    
                                        <h2>Табель ночной #'.$_GET['id'].'';

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
                                if ($finances['close'] == 1) {
                                    echo '<br><i style="color:red;">Удалён (заблокирован).</i><br>';
                                }
                            }
                        }

                        if ($tabel_j[0]['status'] != 9) {
                            if ($tabel_j[0]['status'] == 7) {
                                echo ' <span style="color: green">Проведён <i class="fa fa-check" aria-hidden="true" style="color: green;"></i></span>';

                                echo '<span style="margin-left: 20px; font-size: 60%; color: red; cursor:pointer;" onclick="deployTabelDelete(' . $_GET['id'] . ');">Снять отметку о проведении <i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 150%;"></i></span>';

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
                                    
                                        <div style="font-size: 90%; margin-bottom: 20px;">
                                            <div style="color: #252525; font-weight: bold;">'.$monthsName[$tabel_j[0]['month']].' '.$tabel_j[0]['year'].'</div>
                                            <div>Сотрудник <b>'.WriteSearchUser('spr_workers', $tabel_j[0]['worker_id'], 'user_full', true).'</b></div>';

                        //Врачи
                        if (($tabel_j[0]['type'] == 5) || ($tabel_j[0]['type'] == 6) || ($tabel_j[0]['type'] == 10)) {
                            echo '
                                            <div>Филиал <b>' . $filials_j[$tabel_j[0]['filial_id']]['name'] . '</b></div>';
                        }

                        //Админы, ассистенты
                        if (($tabel_j[0]['type'] == 4) || ($tabel_j[0]['type'] == 7)) {
                            echo '
                                            <div>Филиал, к которому прикреплен сотрудник ';

                            if ($tabel_j[0]['filial_id'] == 0){
                                echo '<span style="color: rgb(243, 0, 0);">не прикреплен</span>';
                            }else {
                                echo '<b>'.$filials_j[$tabel_j[0]['filial_id']]['name'].'</b>';
                            }

                            echo '
                                            </div>';
                        }
                        echo '
		        						</div>';


                        //Получение данных
                        $summCalc = 0;
                        //var_dump(microtime(true) - $script_start);

                        $msql_cnnct = ConnectToDB2 ();
                        //var_dump(microtime(true) - $script_start);

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
                            LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '".$tabel_j[0]['id']."' AND jtabex.noch = '1'
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
                                <div class="cellsBlockHover" style="'.$background_color.' border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
                                    <div style="display: inline-block; width: 200px;">
                                        <div>
                                        <a href="fl_calculate.php?id='.$rezData['id'].'" class="ahref">
                                            <div>
                                                <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
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
                                            Сумма: '.$summ.' р. Страх.: '.$summins.' р.</b> <br>
                                            
                                        </div>
                                        <div style="margin: 5px 0 5px 3px; font-size: 80%;">';

                            //Категории процентов(работ)
                            $percent_cats_arr = explode(',', $rezData['percent_cats']);

                            foreach ($percent_cats_arr as $percent_cat){
                                $bgColor = "";

                                if (($percent_cat == 58) || ($percent_cat == 59) || ($percent_cat == 60) || ($percent_cat == 61)){
                                    $bgColor = "background-color: yellow;";
                                }

                                if ($percent_cat > 0) {
                                    $rezult .= '<i style="color: rgb(15, 6, 142); font-size: 110%;'.$bgColor.'">' . $percent_cats_j[$percent_cat] . '</i><br>';
                                }else{
                                    $rezult .= '<i style="color: red; font-size: 100%;">Ошибка #68</i><br>';
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

                        //Вычеты
                        //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."'";
                        $query = "SELECT * FROM `fl_journal_deductions` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

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
                        //var_dump(microtime(true) - $script_start);

                        $rezultD = '';

                        if (!empty($tabel_deductions_j)) {

                            foreach ($tabel_deductions_j as $rezData) {

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
                                if ($rezData['type'] == 2){
                                    $rezultD .= ' налог ';
                                }elseif ($rezData['type'] == 3){
                                    $rezultD .= ' штраф/вычет ';
                                }elseif ($rezData['type'] == 4){
                                    $rezultD .= ' ссуда ';
                                }elseif ($rezData['type'] == 5){
                                    $rezultD .= ' за обучение ';
                                }else {
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
                                if (mb_strlen($rezData['descr']) > 0){
                                    $rezultD .= '
                                            <div style="margin: 5px 0 0 3px; font-size: 80%;">
                                                <b>Комментарий:</b> '.$rezData['descr'].'                                                
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
                        }
                        //var_dump(microtime(true) - $script_start);

                        //Надбавки
                        $query = "SELECT * FROM `fl_journal_surcharges` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($tabel_surcharges_j, $arr);
                            }
                        }else{
                            //$sheduler_zapis = 0;
                            //var_dump ($sheduler_zapis);
                        }
                        //var_dump(microtime(true) - $script_start);

                        $rezultS = '';

                        if (!empty($tabel_surcharges_j)) {

                            foreach ($tabel_surcharges_j as $rezData) {

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
                                if ($rezData['type'] == 2){
                                    $rezultS .= ' отпускной ';
                                }elseif ($rezData['type'] == 3){
                                    $rezultS .= ' больничный ';
                                }else {
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
                                if (mb_strlen($rezData['descr']) > 0){
                                    $rezultS .= '
                                            <div style="margin: 5px 0 0 3px; font-size: 80%;">
                                                <b>Комментарий:</b> '.$rezData['descr'].'                                                
                                            </div>';
                                }
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
                        }
                        //var_dump(microtime(true) - $script_start);

                        //Выплаты
                        $query = "SELECT * FROM `fl_journal_paidouts` WHERE `tabel_noch_id`='".$tabel_j[0]['id']."';";

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

                            $query = "SELECT `id`, `day`, `smena`, `kab`, `worker` FROM `scheduler` WHERE `worker` = '{$tabel_j[0]['worker_id']}' AND `month` = '" . (int)$tabel_j[0]['month'] . "' AND `year` = '{$tabel_j[0]['year']}' AND `filial`='{$tabel_j[0]['filial_id']}'";

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

                            $tabels_noch_j = array();

                            $query = "SELECT * FROM `fl_journal_tabels_noch_ex` WHERE  `tabel_id`='{$_GET['id']}' ORDER BY `day`";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {
                                while ($arr = mysqli_fetch_assoc($res)) {
                                    //Раскидываем в массив
                                    array_push($tabels_noch_j, $arr);
                                }
                            }
                            //var_dump($tabels_noch_j);

                        }

//                        //Админы, ассистенты
//                        if (($tabel_j[0]['type'] == 4) || ($tabel_j[0]['type'] == 7)) {
//                            //Часы работника
//                            $w_hours = 0;
//                            $w_normaSmen = 0;
//
//                            if ($tabel_j[0]['hours_count'] != NULL) {
//                                $hours_arr = explode(',', $tabel_j[0]['hours_count']);
//                                //var_dump($hours_arr);
//
//                                $w_hours = $hours_arr[0];
//                                $w_normaSmen = $hours_arr[1];
//                            }
//
//                            $w_percentHours = $tabel_j[0]['hours_percent'];
//                        }

                        //Врачи
                        if (($tabel_j[0]['type'] == 5) || ($tabel_j[0]['type'] == 6) || ($tabel_j[0]['type'] == 10) || ($tabel_j[0]['type'] == 7)) {
                            //все те же, кроме ассист
//                            if (($tabel_j[0]['type'] == 5) || ($tabel_j[0]['type'] == 6) || ($tabel_j[0]['type'] == 10)){
//                                //Смены
//                                echo '
//                                    <div style="background-color: rgba(181, 165, 165, 0.16); border: 1px dotted #AAA; margin: 5px 0 5px; padding: 1px 3px; ">
//                                        <div>
//                                            <div style="margin-bottom: 5px;">
//                                                <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
//                                                    Всего смен в этом месяце в этом филиале: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">' . count($rezultShed) . '</span>
//                                                </div>
//                                            </div>';
//
//                                echo '
//                                            <div style="margin: 10px 0;">
//                                                <div style="font-size: 90%;  color: #555;">
//                                                    <span style="color: rgba(10, 10, 10, 1);">Надбавка за "пустые смены".</span> (250 руб. за одну "пустую" смену)
//                                                </div>';
//
//                                if ($tabel_j[0]['empty_smena'] == 0) {
//                                    echo '
//                                            <div style="font-size: 90%;  color: #555;">
//                                                Введите количество "пустых" смен: <input type="number" value="" min="0" max="99" size="2" name="emptySmens" id="emptySmens" class="who2" placeholder="0" style="font-size: 13px; text-align: center;">
//                                            </div>';
//                                    if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9) && (($finances['see_all'] == 1) || $god_mode)) {
//                                        echo '
//                                            <button class="b" style="font-size: 80%;" onclick="showEmptySmenaAddINTabel(' . $_GET['id'] . ');">Добавить в табель оплату <b>пустых</b> смен</button>';
//                                    }
//
//                                } else {
//                                    echo '<div style="font-size: 80%; color: rgb(7, 199, 41); padding-top: 5px;">В табель уже включена сумма за "пустые" смены <span style="font-size: 120%; font-weight: bold;">' . $tabel_j[0]['empty_smena'] . '</span> руб.';
//                                    if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9)) {
//                                        echo '<span style="margin-left: 20px; font-size: 90%; color: red; cursor:pointer;" onclick="emptySmenaTabelDelete(' . $_GET['id'] . ');"><i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 150%;"></i> Удалить из табеля "пустые" смены</span>';
//                                    }
//                                    echo '</div>';
//                                }
//
//                                echo '
//                                            </div>
//                                        </div>
//                                         <!--<div><a href = "fl_deduction_in_tabel_add.php?tabel_id=' . $_GET['id'] . '" class="b" style = "font - size: 80 %;" > Добавить вычет </a ></div >-->
//                                    </div>';
//                            }

                            //Ночные
                            if (!empty($tabels_noch_j)) {

                                echo '
                                <div style="background-color: rgba(181, 165, 165, 0.16); border: 1px dotted #AAA; margin: 2px 0 10px; padding: 1px 3px; ">
                                    <div>
                                        <div style="margin-bottom: 5px;">
                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
                                                Всего ночных смен по графику: <b>'.$nightSmena.'</b>. Оформлено: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">' . count($tabels_noch_j) . '</span> смен <a href="fl_report_noch.php" class="ahref button_tiny">Отчёт ночь</a>
                                            </div>
                                            <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; margin-bottom: 10px; margin-top: 10px; vertical-align: top;">
                                                <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
                                                <!--Ночные--> ';

                            echo '
                                                    <div id="allNightTabelsIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allNightTabelsIsHere\');">подробно:</div>
                                                    </div >
                                                    <div id = "allNightTabelsIsHere" style = "" >';

                            $noch_summ = 0;

                            //Выведем все рассчеты по ночам
                            foreach ($tabels_noch_j as $tabels_noch_item){
                                //var_dump($tabels_noch_item);

                                $noch_summ += $tabels_noch_item['summ'];

                                echo '
                                                        <div class="cellsBlockHover" style="background-color: #ffffff; border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
                                                            <div style="display: inline-block; width: 200px;">
                                                                <div>
                                                                <!--<a href="#" class="ahref">-->
                                                                    <div>
                                                                        <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                                            <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                                        </div>
                                                                        <div style="display: inline-block; vertical-align: middle; font-size: 95%;">
                                                                            <b>#' . $tabels_noch_item['id'] . ' за ночн. смену</b> <br>
                                                                            от '.$tabels_noch_item['day'].'.'.$tabels_noch_item['month'].'.'.$tabels_noch_item['year'].'<br>
                                                                            <span style="font-size: 85%; color: rgb(115, 112, 112);">создано: ' . date('d.m.y H:i', strtotime($tabels_noch_item['create_time'])) . '</span>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                                            Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $tabels_noch_item['summ'] . '</span> руб.
                                                                        </div>
                                                                    </div>
                                                                    
                                                                <!--</a>-->
                                                                </div>';

                                echo '
                                                            </div>';


                                if ($tabel_j[0]['status'] != 7) {
                                    echo '
                                                            <div style="display: inline-block; vertical-align: top;">
                                                                <div class="settings_text" style="border: 1px solid #CCC; padding: 3px; margin: 1px; width: 12px; text-align: center;"  onclick="contextMenuShow(' . $tabel_j[0]['id'] . ', ' . $tabels_noch_item['id'] . ', event, \'tabel_night_options\');">
                                                                    <i class="fa fa-caret-down"></i>
                                                                </div>
                                                            </div>';
                                }
                                echo '
                                                        </div>';
                            }

                            echo '       
                                                    </div>';
                            echo '
                                                </div>
                                            </div>';

                                echo '
                                            <div>Всего начислено за ночь: <span class="calculateOrder" style="font-size: 13px;">' . $noch_summ . '</span> руб.</div>';
                                echo '
                                        </div>
                                    </div>
                                </div>';

                            }


                        }


//                        //Админы, ассистенты
//                        if (($tabel_j[0]['type'] == 4) || ($tabel_j[0]['type'] == 7)) {
//                            //Часы
//                            echo '
//                                <div style="background-color: rgba(181, 165, 165, 0.16); border: 1px dotted #AAA; margin: 5px 0 10px; padding: 1px 3px; ">
//                                    <div>
//                                        <div style="margin-bottom: 5px;">
//                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
//                                                Оклад: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">' . $tabel_j[0]['salary'] . ' руб.</span>
//                                            </div>
//                                        </div>
//                                        <div style="margin-bottom: 5px;">
//                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
//                                                Категория: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">id = ' . $tabel_j[0]['category'] . '</span>
//                                            </div>
//                                        </div>
//                                        <div style="margin-bottom: 7px;">
//                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1); display: inline;">
//                                                Всего часов в этом месяце: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">' . $w_hours . '</span>
//                                            </div>
//                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1); display: inline;">
//                                                (<span class="allMonthHours" style="font-size: 12px; /*font-weight: bold; text-shadow: 1px 1px rgba(111, 111, 111, 0.8);*/">' . $w_percentHours . '</span>% от нормы ' . $w_normaSmen . ' часов)
//                                            </div>
//                                        </div>
//                                        <div style="margin-bottom: 5px;">
//                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
//                                               Начислено за время: <span class="" style="font-size: 14px; color: #555;  font-weight: bold;">' . number_format($tabel_j[0]['per_from_salary'], 0, '.', '') . ' руб. </span>
//                                            </div>
//                                        </div>
//                                        <div style="margin-bottom: 5px;">
//                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
//                                                Процент с выручки: <span class="" style="font-size: 14px; color: #555;  font-weight: bold;">' . number_format($tabel_j[0]['percent_summ'], 0, '.', '') . ' руб. <span style="font-weight: normal;">('.$tabel_j[0]['revenue_percent'].'%)</span></span>
//                                            </div>
//                                        </div>
//                                        ';
//
//                            echo '
//                                        </div>
//                                    </div>
//                                     <!--<div><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'" class="b" style = "font - size: 80 %;" > Добавить вычет </a ></div >-->
//                                </div>';
//                        }
//
//
//                        //Врачи
//                        if (($tabel_j[0]['type'] == 5) || ($tabel_j[0]['type'] == 6) || ($tabel_j[0]['type'] == 10)) {
//                            echo '
//                                        <div style="background-color: rgba(230, 203, 72, 0.34); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
//                                            Сумма всех РЛ: <span class="calculateOrder" style="font-size: 13px">' . $tabel_j[0]['summ'] . '</span> руб.
//                                        </div>';
//                        }
//                        //Админы, ассистенты
                        if (($tabel_j[0]['type'] == 4) || ($tabel_j[0]['type'] == 7)) {
                            if ($tabel_j[0]['type'] == 7){
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


//                        echo '
//                                        <div style="background-color: rgba(72, 230, 194, 0.16); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">';
//
//                        if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9) && (($finances['see_all'] == 1) || $god_mode)) {
//                            echo '<div style="display: inline;"><a href="fl_surcharge_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=2" class="b" style = "font-size: 80%;" >Отпускной +</a ></div>';
//                            echo '<div style="display: inline;"><a href="fl_surcharge_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=3" class="b" style="font-size: 80%;">Больничный +</a></div>';
//                            echo '<div style="display: inline;"><a href="fl_surcharge_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=1" class="b" style="font-size: 80%;">Премия +</a></div>';
//                        }
//
//                        //Надбавки
//                        if (mb_strlen($rezultS) > 0) {
//                            echo '
//                                <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; margin-bottom: 10px; vertical-align: top;">
//                                    <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
//                                        <!--Начислено--> ';
//
//                            echo '
//                                        <div id="allSurchargesIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allSurchargesIsHere\');">подробно:</div>
//                                    </div>
//                                    <div id="allSurchargesIsHere" style="">
//                                        '.$rezultS.'
//                                    </div>';
//                            echo '
//                                </div>';
//                        }/*else{
//                            echo ' [отсутствуют]</div>';
//                        }
//                        echo '
//                                </div>';*/
//
//
//
//                        echo '
//
//                                                <div>Всего начислено: <span class="calculateOrder" style="font-size: 13px">' . $tabel_j[0]['surcharge'] . '</span> руб.</div>
//                                            </div>
//                                        </div>';
//
//
//
//                        echo '
//                                        <div style="background-color: rgba(230, 72, 72, 0.16); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">';
//
//                        if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9) && (($finances['see_all'] == 1) || $god_mode)) {
//                            //echo '<div style="display: inline;"><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=1" class="b" style = "font-size: 80%;" >За материалы +</a ></div >';
//                            echo '<div style="display: inline;"><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=2" class="b" style = "font-size: 80%;" >Налог +</a ></div >';
//                            echo '<div style="display: inline;"><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=3" class="b" style = "font-size: 80%;" >Штраф/Вычет +</a ></div >';
//                            echo '<div style="display: inline;"><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=4" class="b" style = "font-size: 80%;" >Ссуда +</a ></div >';
//                            echo '<div style="display: inline;"><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=5" class="b" style = "font-size: 80%;" >Обучение +</a ></div >';
//                        }
//
//                        //Вычеты
//                        if (mb_strlen($rezultD) > 0) {
//                            echo '
//                                <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; margin-bottom: 10px; vertical-align: top;">
//                                    <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
//                                        <!--Удержано--> ';
//
//                            echo '
//                                            <div id="allDeductionssIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allDeductionssIsHere\');">подробно:</div>
//                                    </div>
//                                    <div id="allDeductionssIsHere" style="">
//                                        ' . $rezultD . '
//                                    </div>';
//                            echo '
//                                </div>';
//                        }/*else{
//                            echo ' [отсутствуют]</div>';
//                        }
//                        echo '
//                                </div>';*/
//
//                        echo '
//
//                                            <div>Всего удержано: <span class="calculateInvoice" style="font-size: 13px">' . $tabel_j[0]['deduction'] . '</span> руб.</div>
//                                        </div>';

                        echo '
                                        <div style="background-color: rgba(1, 94, 255, 0.22); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">';

                        if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9) && (($finances['see_all'] == 1) || $god_mode)) {
//                            echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=1" class="b" style = "font-size: 80%;">Аванс +</a ></div>';
//                            echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=7" class="b" style = "font-size: 80%;">ЗП +</a ></div>';
//                            echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=2" class="b" style="font-size: 80%;">Отпускные +</a></div>';
//                            echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=3" class="b" style="font-size: 80%;">Больничный +</a></div>';
//                            echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=4" class="b" style="font-size: 80%;">На карту +</a></div>';
                                echo '<div style="display: inline;"><a href="fl_paidout_in_tabel_add.php?tabel_id='.$_GET['id'].'&type=5&noch=1" class="b" style="font-size: 80%;">За ночь +</a></div>';
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

//                        //Общая сумма, которую осталось выплатить = сумма (РЛ) + % с оклада + % с выручки + надбавки + за ночь + пустые смены - вычеты - оплачено - выплачено
//                        $summItog = $tabel_j[0]['summ'] + $tabel_j[0]['per_from_salary'] + $tabel_j[0]['percent_summ'] + $tabel_j[0]['surcharge'] + $tabel_j[0]['night_smena'] + $tabel_j[0]['empty_smena'] - $tabel_j[0]['deduction'] - $tabel_j[0]['paid'] - $tabel_j[0]['paidout'];
                        //Общая сумма, которую осталось выплатить = сумма (РЛ) + надбавки + за ночь + пустые смены - вычеты - оплачено - выплачено
                        $summItog = $tabel_j[0]['summ'] - $tabel_j[0]['paidout'];
                        //Если ассистент, то плюсуем сумму за РЛ
                        if ($tabel_j[0]['type'] == 7){
                            $summItog += $tabel_j[0]['summ_calc'];
                        }

                        echo '
                                        <div style="background-color: rgba(56, 245, 70, 0.36); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            <div>Итого осталось выплатить: <span class="calculateOrder" style="font-size: 16px; ', ($summItog) <= 0 ? 'color: red;' : '' ,'">' . intval($summItog) . '</span> руб.<br>
                                            <span style="font-size: 80%; color: #8C8C8C;">сумма округляется до целого для удобства расчетов</span></div>
                                            <div>';

                        if (($tabel_j[0]['status'] != 7) && ($tabel_j[0]['status'] != 9) && (($finances['see_all'] == 1) || $god_mode)) {
                            if ($tabel_j[0]['type'] == 6) {
                                echo '
                                                <button class="b" style="font-size: 80%;" onclick="prikazNomerVosem(' . $tabel_j[0]['worker_id'] . ', ' . $_GET['id'] . ');">Применить приказ №8</button>';
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
                                                <a href="fl_tabel_print.php?tabel_id=' . $tabel_j[0]['id'] . '&noch=1" class="b" style="font-size: 80%;" >Распечатать</a>
                                                <a href="fl_akt_print.php?tabel_id=' . $tabel_j[0]['id'] . '&noch=1"  target="_blank" rel="nofollow noopener" class="b" style="font-size: 80%;" >Акт</a>';

                        echo '
                                            </div>
                                        </div>';


//                        //Врачи
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
			                    <input type="hidden" name="noch" id="noch" value="1">
					        </div>
					        
					        <div id="doc_title">Табель ночь #'.$_GET['id'].' - Асмедика</div>
					        </div>
					        <!-- Подложка только одна -->
					        <div id="overlay"></div>';

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