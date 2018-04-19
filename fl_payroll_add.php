<?php

//fl_payroll_add.php
//Новая выплата

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['see_all'] == 1) || $god_mode){

        include_once 'DBWork.php';
        include_once 'functions.php';

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

        if ($_GET){
            if (isset($_GET['tabel_id']) && isset($_GET['type'])){

                $tabel_j = SelDataFromDB('fl_journal_tabels', $_GET['tabel_id'], 'id');
                //var_dump($tabel_j);

                if ($_GET['type'] == 'prepaid'){
                    $payType = 'Аванс';
                }

                if ($tabel_j != 0){
                    //var_dump($tabel_j);
                    //array_push($_SESSION['invoice_data'], $_GET['client']);
                    //$_SESSION['invoice_data'] = $_GET['client'];
                    //var_dump($calculate_j[0]['closed_time'] == 0);

                    $filials_j = getAllFilials(false, false);

                    //$sheduler_zapis = array();
                    $tabel_ex_calculates_j = array();
                    $tabel_deductions_j = array();
                    $tabel_surcharges_j = array();

                    //$invoice_j = array();

                    //$client_j = SelDataFromDB('spr_clients', $calculate_j[0]['client_id'], 'user');
                    //var_dump($client_j);


                    echo '
                                <div id="status">
                                    <header>
                                        <div class="nav">
                                            <!--<a href="fl_tabels.php" class="b">Важный отчёт</a>-->
                                        </div>
    
                                        <h2>'.$payType.' по табелю <a href="fl_tabel.php?id='.$_GET['tabel_id'].'" class="ahref">#'.$_GET['tabel_id'].'</a>';

                    if (($finances['edit'] == 1) || $god_mode){
                        /*if ($calculate_j[0]['status'] != 9){
                            echo '
                                        <a href="invoice_edit.php?id='.$_GET['tabel_id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                        }*/
                        /*if (($calculate_j[0]['status'] == 9) && (($finances['close'] == 1) || $god_mode)){
                            echo '
                                <a href="#" onclick="Ajax_reopen_tabel('.$_GET['tabel_id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
                        }*/
                    }

                    /*if (($finances['close'] == 1) || $god_mode){
                        if ($tabel_j[0]['status'] != 9){
                            echo '
                                                <span class="info" style="font-size: 100%; cursor: pointer;" title="Удалить" onclick="fl_deleteTabelItem('.$_GET['tabel_id'].');" ><i class="fa fa-trash-o" aria-hidden="true"></i></span>';
                        }
                    }*/

                    if ($tabel_j[0]['status'] == 7) {
                        echo ' <span style="color: green">(Проведён)<i class="fa fa-check" aria-hidden="true" style="color: green;"></i></span>';

                        //echo '<span style="margin-left: 20px; font-size: 60%; color: red; cursor:pointer;" onclick="deployTabelDelete('.$_GET['tabel_id'].');">Снять отметку о проведении <i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 150%;"></i></span>';

                    }else{
                        echo '<span style="margin-left: 20px; font-size: 60%; color: red;">(Не проведён)</span>';
                    }

                    echo '			
                                        </h2>
                                    </header>';

                    echo '
                                    <div id="data" style="margin: 0;">
                                    
                                        <div style="font-size: 90%; margin-bottom: 20px;">
                                            <div style="color: #252525; font-weight: bold;">'.$monthsName[$tabel_j[0]['month']].' '.$tabel_j[0]['year'].'</div>
                                            <div>Сотрудник <b>'.WriteSearchUser('spr_workers', $tabel_j[0]['worker_id'], 'user_full', true).'</b></div>
                                            <div>Филиал <b>'.$filials_j[$tabel_j[0]['office_id']]['name'].'</b></div>
		        						</div>';


                    //Получение данных
                    $summCalc = 0;

                    $msql_cnnct = ConnectToDB2 ();

                    //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."'";
                    $query = "SELECT jcalc.* FROM `fl_journal_calculate` jcalc WHERE jcalc.id IN (SELECT `calculate_id` FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."');";

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

                    //var_dump($query);
                    //var_dump($tabel_ex_calculates_j);


                    //$rezult = '';

                    foreach ($tabel_ex_calculates_j as $rezData){

                        //Наряды
                        $query = "SELECT `summ`, `summins`, `create_time` FROM `journal_invoice` WHERE `id`='{$rezData['invoice_id']}' LIMIT 1";

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


                        /*$rezult .=
                            '
                                <div class="cellsBlockHover" style=" border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
                                    <div style="display: inline-block; width: 200px;">
                                        <div>
                                        <a href="fl_calculate.php?id='.$rezData['id'].'" class="ahref">
                                            <div>
                                                <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                    <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                </div>
                                                <div style="display: inline-block; vertical-align: middle; font-size: 90%;">
                                                    <b>РЛ #'.$rezData['id'].'</b> <span style="    color: rgb(115, 112, 112);">'.date('d.m.y H:i', strtotime($rezData['create_time'])).'</span>
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
                                    </div>';*/
                        /*if ($tabel_j[0]['status'] != 7) {
                            $rezult .= '
                                    <div style="display: inline-block; vertical-align: top;">
                                        <div class="settings_text" style="border: 1px solid #CCC; padding: 3px; margin: 1px; width: 12px; text-align: center;"  onclick="contextMenuShow(' . $tabel_j[0]['id'] . ', ' . $rezData['id'] . ', event, \'tabel_calc_options\');">
                                            <i class="fa fa-caret-down"></i>
                                        </div>
                                    </div>';
                        }*/

                        /*$rezult .= '
                                    <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                </div>';*/

                        $summCalc += $rezData['summ'];

                    }

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

                    /*$rezultD = '';

                    if (!empty($tabel_deductions_j)) {

                        foreach ($tabel_deductions_j as $rezData) {

                            $rezultD .=
                                '
                                    <div class="cellsBlockHover" style=" border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
                                        <div style="display: inline-block; width: 200px;">
                                            <div>
                                            <a href="#" class="ahref">
                                                <div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                    </div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 90%;">
                                                        <b>Вычет #' . $rezData['id'] . '</b> <span style="    color: rgb(115, 112, 112);">' . date('d.m.y H:i', strtotime($rezData['create_time'])) . '</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                        Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                                    </div>
                                                </div>
                                                
                                            </a>
                                            </div>
                                            <div style="margin: 5px 0 0 3px; font-size: 80%;">
                                                <b>Комментарий:</b> '.$rezData['descr'].'                                                
                                            </div>
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
                    }*/

                    //Надбавки
                    //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."'";
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

                    $rezultS = '';

                    if (!empty($tabel_surcharges_j)) {

                        foreach ($tabel_surcharges_j as $rezData) {

                            $rezultS .=
                                '
                                    <div class="cellsBlockHover" style=" border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
                                        <div style="display: inline-block; width: 200px;">
                                            <div>
                                            <a href="#" class="ahref">
                                                <div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                    </div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 90%;">
                                                        <b>Вычет #' . $rezData['id'] . '</b> <span style="    color: rgb(115, 112, 112);">' . date('d.m.y H:i', strtotime($rezData['create_time'])) . '</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                        Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                                    </div>
                                                </div>
                                                
                                            </a>
                                            </div>
                                            <div style="margin: 5px 0 0 3px; font-size: 80%;">
                                                <b>Комментарий:</b> '.$rezData['descr'].'                                                
                                            </div>
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

                    //Смена/график
                    $rezultShed = array();
                    $nightSmena = 0;

                    $query = "SELECT `id`, `day`, `smena`, `kab`, `worker` FROM `scheduler` WHERE `worker` = '{$tabel_j[0]['worker_id']}' AND `month` = '".(int)$tabel_j[0]['month']."' AND `year` = '{$tabel_j[0]['year']}' AND `filial`='{$tabel_j[0]['office_id']}'";

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


                    //Смены
                    echo '
                                <div style="background-color: rgba(181, 165, 165, 0.16); border: 1px dotted #AAA; margin: 5px 0 10px; padding: 1px 3px; ">
                                    <div>
                                        <!--<div style="margin-bottom: 5px;">
                                            <div style="font-size: 90%; color: rgba(10, 10, 10, 1);">
                                                Всего смен в этом месяце: <span class="" style="font-size: 14px; color: #555; font-weight: bold;">' . count($rezultShed) . '</span>
                                            </div>
                                        </div>-->';
                    if ($nightSmena > 0) {
                        echo '
                                        <div>
                                            <!--<div style="font-size: 85%; color: #555;">
                                                Из них ночных: <span class="" style="font-size: 14px; font-weight: bold;"><input type="number" value="' . $nightSmena . '" min="0" max="99" size="2" name="nightSmens" id="nightSmens" class="who2" placeholder="0" style="font-size: 13px; text-align: center;"></span>. Надбавка за одну ночную смену: 1000 руб.<br>
                                            </div>-->';
                        if ($tabel_j[0]['night_smena'] == 0) {
                            /*if ($tabel_j[0]['status'] != 7) {
                                echo '
                                            <button class="b" style="font-size: 80%;" onclick="showNightSmenaAddINTabel(' . $_GET['tabel_id'] . ', $(\'#nightSmens\').val());">Добавить в табель оплату <b>ночных</b> смен</button>';
                            }*/
                        }else{
                            echo '<div style="font-size: 90%;padding-top: 5px;">В табель включена сумма за ночные смены: <span style="font-size: 120%; font-weight: bold;">'.$tabel_j[0]['night_smena'].'</span> руб.';
                            /*if ($tabel_j[0]['status'] != 7) {
                                echo '<span style="margin-left: 20px; font-size: 90%; color: red; cursor:pointer;" onclick="nightSmenaTabelDelete(' . $_GET['tabel_id'] . ');"><i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 150%;"></i> Удалить из табеля ночные смены</span>';
                            }*/
                            echo '</div>';
                        }
                        echo '
                                        </div>';
                    }else{
                        echo '
                                        <div>
                                            <div style="font-size: 85%; color: #555;">
                                                Ночных нет.<br>
                                            </div>';
                        echo '
                                        </div>';
                    }
                    echo '
                                        <div style="margin: 10px 0;">
                                            <!--<div style="font-size: 90%;  color: #555;">
                                                <span style="color: rgba(10, 10, 10, 1);">Надбавка за "пустые смены".</span> (250 руб. за одну "пустую" смену)
                                            </div>-->';

                    if ($tabel_j[0]['empty_smena'] == 0) {
                        /*echo '
                                        <div style="font-size: 90%;  color: #555;">
                                            Введите количество "пустых" смен: <input type="number" value="" min="0" max="99" size="2" name="emptySmens" id="emptySmens" class="who2" placeholder="0" style="font-size: 13px; text-align: center;">
                                        </div>';*/
                        /*if ($tabel_j[0]['status'] != 7) {
                            echo ' 
                                        <button class="b" style="font-size: 80%;" onclick="showEmptySmenaAddINTabel(' . $_GET['tabel_id'] . ');">Добавить в табель оплату <b>пустых</b> смен</button>';
                        }*/

                    } else {
                        echo '<div style="font-size: 90%; padding-top: 5px;">В табель включена сумма за "пустые" смены: <span style="font-size: 120%; font-weight: bold;">' . $tabel_j[0]['empty_smena'] . '</span> руб.';
                        /*if ($tabel_j[0]['status'] != 7) {
                            echo '<span style="margin-left: 20px; font-size: 90%; color: red; cursor:pointer;" onclick="emptySmenaTabelDelete(' . $_GET['tabel_id'] . ');"><i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 150%;"></i> Удалить из табеля "пустые" смены</span>';
                        }*/
                        echo '</div>';
                    }

                    echo ' 
                                        </div>
                                    </div>
                                     <!--<div><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['tabel_id'].'" class="b" style = "font - size: 80 %;" > Добавить вычет </a ></div >-->
                                </div>';



                    echo '
                                        <div style="background-color: rgba(230, 203, 72, 0.34); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            Сумма всех РЛ: <span class="calculateOrder" style="font-size: 13px">' . $tabel_j[0]['summ'] . '</span> руб.
                                        </div>';

                    echo '
                                        <div style="background-color: rgba(230, 72, 72, 0.16); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            <div>Сумма всех вычетов: <span class="calculateInvoice" style="font-size: 13px">' . $tabel_j[0]['deduction'] . '</span> руб.</div>';
                    /*if ($tabel_j[0]['status'] != 7) {
                        echo '<div ><a href = "fl_deduction_in_tabel_add.php?tabel_id='.$_GET['tabel_id'].'" class="b" style = "font-size: 80%;" > Добавить вычет </a ></div >';
                    }*/
                    echo '
                                        </div>';

                    echo '
                                        <div style="background-color: rgba(72, 230, 194, 0.16); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            <div>Сумма всех надбавок: <span class="calculateOrder" style="font-size: 13px">' . $tabel_j[0]['surcharge'] . '</span> руб.</div>';
                    /*if ($tabel_j[0]['status'] != 7) {
                        echo '<div><a href="fl_surcharge_in_tabel_add.php?tabel_id='.$_GET['tabel_id'].'" class="b" style="font-size: 80%;">Добавить надбавку</a></div>';
                    }*/
                    echo '
                                        </div>';


                    echo '
                                        <div style="background-color: rgba(220, 230, 72, 0.7); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            <div>Сумма всех выплат: <span class="calculateInvoice" style="font-size: 13px">' . $tabel_j[0]['paid'] . '</span> руб.</div>';
                    /*if ($tabel_j[0]['status'] != 7) {
                        echo '<div><a href="fl_surcharge_in_tabel_add.php?tabel_id='.$_GET['tabel_id'].'" class="b" style="font-size: 80%;">Добавить надбавку</a></div>';
                    }*/
                    echo '
                                        </div>';


                    echo '
                                        <div style="background-color: rgba(56, 245, 70, 0.36); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            <div>Итого к выплате: <input type="text" size="10" name="summ" id="summ" class="calculateOrder" style="font-size: 20px; ', ($tabel_j[0]['summ'] - $tabel_j[0]['deduction'] - $tabel_j[0]['paid'] + $tabel_j[0]['surcharge'] + $tabel_j[0]['night_smena'] + $tabel_j[0]['empty_smena']) <= 0 ? 'color: red;' : 'color: rgb(2, 108, 33);'  ,'" value="'. intval($tabel_j[0]['summ'] - $tabel_j[0]['deduction'] - $tabel_j[0]['paid'] + $tabel_j[0]['surcharge'] + $tabel_j[0]['night_smena'] + $tabel_j[0]['empty_smena']).'" autocomplete="off"> руб.<br>
                                            <span style="font-size: 80%; color: #8C8C8C;">сумма округляется до целого для удобства расчетов</span>

                                            <div>';
                    /*if ($tabel_j[0]['status'] != 7) {
                        echo '
                                                <button class="b" style="font-size: 80%;" onclick="deployTabel(' . $_GET['tabel_id'] . ');">Провести табель</button>';
                    }else{
                        if ($tabel_j[0]['status'] == 7) {
                            /*echo '
                                            <button class="b" style="font-size: 80%;" onclick="deployTabelOFF(' . $_GET['tabel_id'] . ');">Распровести табель</button>';*/
                        /*}
                    }*/

                    echo '
                                               <button class="b" style="font-size: 80%;" onclick="Ajax_add_payroll(' . $_GET['tabel_id'] . ');">Применить</button>';


                    echo '
                                            </div>
                                        </div>';


                    //Выводим

                    //Расчетные листы
                    /*echo '
                                <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; margin-bottom: 10px; vertical-align: top;">
                                    <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
                                        Расчётные листы <div id="allCalcsIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allCalcsIsHere\');">показать/скрыть</div>
                                    </div>
                                    <div id="allCalcsIsHere" style="display: none;">
                                        '.$rezult.'
                                    </div>
                                </div>';*/

                    //Вычеты
                    /*echo '
                                <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; margin-bottom: 10px; vertical-align: top;">
                                    <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
                                        Вычеты ';
                    if (mb_strlen($rezultD) > 0) {
                        echo '
                                            <div id="allDeductionssIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allDeductionssIsHere\');">показать/скрыть</div>
                                    </div>
                                    <div id="allDeductionssIsHere" style="display: none;">
                                        ' . $rezultD . '
                                    </div>';
                    }else{
                        echo ' [отсутствуют]</div>';
                    }
                    echo '
                                </div>';*/

                    //Надбавки
                    /*echo '
                                <div style="border: 1px dotted #b3c0c8; display: block; font-size: 12px; padding: 2px; margin-right: 10px; vertical-align: top;">
                                    <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">
                                        Надбавки ';
                    if (mb_strlen($rezultS) > 0) {
                        echo '
                                        <div id="allSurchargesIsHere_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allSurchargesIsHere\');">показать/скрыть</div>
                                    </div>
                                    <div id="allSurchargesIsHere" style="display: none;">
                                        '.$rezultS.'
                                    </div>';
                    }else{
                        echo ' [отсутствуют]</div>';
                    }
                    echo '
                                </div>';*/

                    echo '	
						
					        </div>
					        
					        <div id="doc_title">'.$payType.' по табелю #'.$_GET['tabel_id'].' - Асмедика</div>
					        </div>
					        <!-- Подложка только одна -->
					        <div id="overlay"></div>';

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
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>