<?php 

//showZapisRezult3.php
//функция формирует и показывает массив записи

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        include_once 'DBWork.php';
        include_once 'functions.php';
        //var_dump ($_POST);

        require 'variables.php';

        if ($_POST){


            $data = $_POST['data'];

            $filials_j = getAllFilials(false, false, false);

            $rezult = '';
            
    //function showZapisRezult($ZapisHereQueryToday, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, $type, $format, $menu){
        //var_dump($ZapisHereQueryToday);



            //$office_j = SelDataFromDB('spr_filials', '', '');
            //var_dump($office_j);
            //!!!
            //$office_j_arr = array();

            //foreach ($office_j as $office_item){
                //$office_j_arr[$office_item['id']] = $office_item;
            //}
            //var_dump($office_j_arr);


            //for ($z = 0; $z < count($ZapisHereQueryToday); $z++) {

                //$t_f_data_db = array();
                //$cosmet_data_db = array();
                //$invoice_data_db = array();
                $back_color = '';
                $mark_enter = '';

                if (!empty($data)) {


                    if ($data['enter'] == 1) {
                        //$back_color = 'background-color: rgba(119, 255, 135, 0.69);';
                        //$mark_enter = 'пришёл';
                    }/* elseif ($data['enter'] == 9) {
                        $back_color = 'background-color: rgba(239,47,55, .7);';
                        $mark_enter = 'не пришёл';
                    } elseif ($data['enter'] == 8) {
                        $back_color = 'background-color: rgba(137,0,81, .7);';
                        $mark_enter = 'удалено';
                    } else {
                        //Если оформлено не на этом филиале
                        if ($data['office'] != $data['add_from']) {
                            $back_color = 'background-color: rgb(119, 255, 250);';
                            $mark_enter = 'подтвердить';
                        } else {
                            $back_color = 'background-color: rgba(255,255,0, .5);';
                            $mark_enter = '';
                        }
                    }*/

                    $dop_img = '';

                    if ($data['insured'] == 1) {
                        $dop_img .= '<img src="img/insured.png" title="Страховое"> ';
                    }

                    /*if ($data['pervich'] == 1) {
                        $dop_img .= '<img src="img/pervich.png" title="Первичное"> ';
                    }*/

                    if ($data['pervich'] == 1) {
                        $dop_img .= '<img src="img/pervich.png" title="Посещение для пациента первое без работы"> ';
                    }elseif ($data['pervich'] == 2) {
                        $dop_img .= '<img src="img/pervich_ostav_2.png" title="Посещение для пациента первое с работой"> ';
                    }elseif ($data['pervich'] == 3) {
                        $dop_img .= '<img src="img/vtorich_3.png" title="Посещение для пациента не первое"> ';
                    }elseif ($data['pervich'] == 4) {
                        $dop_img .= '<img src="img/vtorich_davno_4.png" title="Посещение для пациента не первое, но был более полугода назад"> ';
                    }elseif ($data['pervich'] == 5) {
                        $dop_img .= '<img src="img/prodolzhenie.png" title="Продолжение работы"> ';
                    }

                    if ($data['noch'] == 1) {
                        $dop_img .= '<img src="img/night.png" title="Ночное"> ';
                    }

                    /*$start_time_h = floor($data['start_time'] / 60);
                    $start_time_m = $data['start_time'] % 60;
                    if ($start_time_m < 10) $start_time_m = '0' . $start_time_m;
                    $end_time_h = floor(($data['start_time'] + $data['wt']) / 60);
                    if ($end_time_h > 23) $end_time_h = $end_time_h - 24;
                    $end_time_m = ($data['start_time'] + $data['wt']) % 60;
                    if ($end_time_m < 10) $end_time_m = '0' . $end_time_m;*/

                    $day = $data['day'];

                    if ($data['month'] < 10) $month = '0' . $data['month'];
                    else $month = $data['month'];

                    $year = $data['year'];

                    $rezult .= '
                                        <li id="'.$data['id'].'" class="cellsBlock zapisDiv" style="display: inline-block; width: auto; border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 0px 0px rgba(67, 255, 91, 0.49);">
                                            <!--<div class="cellCosmAct">-->';

                    //if ($data['type'] == 5) {
                        //Формулы зубные
                        //$query = "SELECT `id`, `zapis_date`  FROM `journal_tooth_status` WHERE `zapis_id` = '{$data['id']}' ORDER BY `create_time`";
                        //var_dump($query);
                        //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        //$number = mysqli_num_rows($res);
                        //if ($number != 0) {
                        //    while ($arr = mysqli_fetch_assoc($res)) {
                        //        array_push($t_f_data_db, $arr);
                        //    }
                        //} else
                            //$t_f_data_db = 0;
                        //var_dump($t_f_data_db);

                        //if ($t_f_data_db != 0) {
                            /*foreach ($t_f_data_db as $ids) {
                                //
                            }*/
                        //}
                    //}

                    /*if ($data['type'] == 6) {
                        //Посещения косметологов
                        $query = "SELECT `id`, `zapis_date`  FROM `journal_cosmet1` WHERE `zapis_id` = '{$data['id']}' ORDER BY `create_time`";
                        //var_dump($query);
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        $number = mysqli_num_rows($res);
                        if ($number != 0) {
                            while ($arr = mysqli_fetch_assoc($res)) {
                                array_push($cosmet_data_db, $arr);
                            }
                        } else
                            //$cosmet_data_db = 0;
                        //var_dump($cosmet_data_db);

                        if ($cosmet_data_db != 0) {
                            /*foreach ($cosmet_data_db as $ids) {
                                //
                            }
                        }
                    }*/

                    //Наряды
                    /*$query = "SELECT * FROM `journal_invoice` WHERE `zapis_id` = '{$data['id']}' AND `status` <> '9' ORDER BY `create_time`";
                    //var_dump($query);
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                    $number = mysqli_num_rows($res);
                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($invoice_data_db, $arr);
                        }
                    } else
                        $invoice_data_db = 0;*/
                    //var_dump($invoice_data_db);

                    /*if ($invoice_data_db != 0) {
                        foreach ($invoice_data_db as $ids) {
                            //
                        }
                    }*/


                    $rezult .= '
                                            <!--</div>-->
                                            <div class="cellName" style="position: relative; cursor: pointer;' . $back_color . '" onclick="">';

                    $rezult .=
                        '<b>' . $day . ' ' . $monthsName[$month] . ' ' . $year . '</b><br>'.
                        //$start_time_h . ':' . $start_time_m . ' - ' . $end_time_h . ':' . $end_time_m;
                        '<i>Филиал:<br>' . $filials_j[$data['office']]['name'].'</i>';

                    $rezult .= '
                                                <div style="position: absolute; top: 1px; right: 1px;">' . $dop_img . '</div>';
                    $rezult .= '
                                                <div style="position: absolute; bottom: 0; right: 1px; font-size: 80%;">' . $mark_enter . '</div>';
                    $rezult .= '
                                            </div>';
                    $rezult .= '
                                            <div class="cellName" style="width: 150px;">';
                    $rezult .=
                        'Пациент <br><b title="">' . WriteSearchUser('spr_clients', $data['patient'], 'user', true) . '</b><br>';
                    $rezult .=
                        'Врач: <br><b>' . WriteSearchUser('spr_workers', $data['worker'], 'user', true) . '</b>';
                    $rezult .= '
                                            </div>';
                    /*$rezult .= '
                                            <div class="cellName">';*/
                    //$rezult .=

                    /*$rezult .= '
                                            </div>';*/
                    /*$rezult .= '
                                            <div class="cellName">';*/
                    //$rezult .=
                        //$data['kab'] . ' кабинет<br>' . 'Врач: <br><b>' . WriteSearchUser('spr_workers', $data['worker'], 'user', true) . '</b>';
                    //    ;
                    /*$rezult .= '
                                            </div>';*/
                    /*$rezult .= '
                                            <div class="cellName" style="max-width: 120px; overflow: auto;">';*/
                    /*$rezult .=
                        '<b><i>Описание:</i></b><br><div style="text-overflow: ellipsis; overflow: hidden; white-space: inherit; display: block; width: 120px;" title="' . $data['description'] . '">' . $data['description'] . '</div>';*/
                    /*$rezult .= '
                                            </div>';*/
                    /*if ($format) {
                        $rezult .= '
                                                <div class="cellName">';
                        $rezult .= '
                                                    Добавлено<br>' . date('d.m.y H:i', $data['create_time']) . '<br>
                                                    Кем: ' . WriteSearchUser('spr_workers', $data['create_person'], 'user', true);
                        if (($data['last_edit_time'] != 0) || ($data['last_edit_person'] != 0)) {
                            $rezult .= '<hr>
                                                    Изменено: ' . date('d.m.y H:i', $data['last_edit_time']) . '<br>
                                                    Кем: ' . WriteSearchUser('spr_workers', $data['last_edit_person'], 'user', true) . '';
                        }
                        $rezult .= '
                                                </div>';
                    }*/
                    //Формулы посещения наряды -->
                    /*$rezult .= '
                                            <div class="cellName" style="vertical-align: top;">';

                    if (!empty($t_f_data_db)) {
                        foreach ($t_f_data_db as $ids) {
                            $rezult .= '
                                                <div class="cellsBlockHover" style="border: 1px solid #BFBCB5; margin-top: 1px;">
                                                    <a href="task_stomat_inspection.php?id=' . $ids['id'] . '" class="ahref">
                                                        <div style="display: inline-block; vertical-align: middle;"><img src="img/tooth2.svg" width="20px" height="20px"></div><div style="display: inline-block; vertical-align: middle;">' . date('d.m.y H:i', $ids['zapis_date']) . '</div>
                                                    </a>	
                                                </div>';
                        }
                    }

                    if (!empty($cosmet_data_db)) {
                        foreach ($cosmet_data_db as $ids) {
                            $rezult .= '
                                                <div class="cellsBlockHover" style="border: 1px solid #BFBCB5; margin-top: 1px;">
                                                    <a href="task_cosmet.php?id=' . $ids['id'] . '" class="ahref">
                                                        <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding-left: 2px; font-weight: bold; font-style: italic;">K</div> <div style="display: inline-block; vertical-align: middle;">' . date('d.m.y H:i', $ids['zapis_date']) . '</div>
                                                    </a>	
                                                </div>';
                        }
                    }
                    if ($format) {

                        $rezultInvoices = showInvoiceDivRezult($invoice_data_db, true, false, false, false, false, false);
                        //$data, $minimal, $show_categories, $show_absent, $show_deleted

                        $rezult .= $rezultInvoices['data'];


                    }*/

                    //<-- Формулы посещения наряды
                    /*$rezult .= '
                                            </div>';*/

                    /*if ($menu){
                        //Управление настройки -->

                        $rezult .= '
                                                <div class="cellName settings_text" style="background-color: rgb(240, 240, 240); text-align: center; vertical-align: middle; width: 80px; min-width: 80px; max-width: 80px;" onclick="contextMenuShow(' . $data['id'] . ', 0, event, \'zapis_options\');">';

                        $rezult .= 'Меню [опции]';

                        $rezult .= '
                                                    <ul id="zapis_options' . $data['id'] . '" class="zapis_options" style="display: none;">';

                        if (isset($_SESSION['filial'])) {

                            if ($_SESSION['filial'] == $data['office']) {

                                /*$smena = 0;

                                if (($data['start_time'] >= 540)  && ($data['start_time'] < 900)){
                                    $smena = 1;
                                }
                                if (($data['start_time'] >= 900)  && ($data['start_time'] < 1260)){
                                    $smena = 2;
                                }
                                if (($data['start_time'] >= 1260 )  && ($data['start_time'] < 1440)){
                                    $smena = 3;
                                }


                                if ($data['office'] != $data['add_from']) {
                                    if ($data['enter'] != 8) {
                                        $rezult .= '<li><div onclick="Ajax_TempZapis_edit_OK(' . $data['id'] . ', ' . $data['office'] . ')">Подтвердить</div></li>';
                                    }
                                }
                                if ($data['office'] == $data['add_from']) {
                                    if (($data['enter'] != 8) && ($data['enter'] != 9)) {
                                        $rezult .=
                                            '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $data['id'] . ', 1)">Пришёл</div></li>';
                                        $rezult .=
                                            '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $data['id'] . ', 9)">Не пришёл</div></li>';
                                        $rezult .=
                                            '<li><div onclick="ShowSettingsAddTempZapis(' . $data['office'] . ', \'' . $office_j_arr[$data['office']]['name'] . '\', ' . $data['kab'] . ', ' . $year . ', '.$month.', '.$day.', '.$smena.', '.$data['start_time'] . ', ' . $data['wt'] . ', ' . $data['worker'] . ', \'' . WriteSearchUser('spr_workers', $data['worker'], 'user_full', false) . '\', \'' . WriteSearchUser('spr_clients', $data['patient'], 'user_full', false) . '\', \'' . str_replace(array("\r", "\n"), " ", $data['description']) . '\', ' . $data['insured'] . ', ' . $data['pervich'] . ', ' . $data['noch'] . ', ' . $data['id'] . ', ' . $data['type'] . ', \'edit\')">Редактировать</div></li>';

                                        //var_dump($data['create_time']);
                                        //var_dump($data['description']);
                                        //var_dump(time());

                                        if (($data['enter'] == 1) && ($finance_edit)) {
                                            $rezult .=
                                                '<li>
                                                                    <div>
                                                                        <a href="invoice_add.php?client=' . $data['patient'] . '&filial=' . $data['office'] . '&date=' . strtotime($data['day'] . '.' . $month . '.' . $data['year'] . ' ' . $start_time_h . ':' . $start_time_m) . '&id=' . $data['id'] . '&worker=' . $data['worker'] . '&type=' . $data['type'] . '" class="ahref">
                                                                            Внести наряд
                                                                        </a>
                                                                    </div>
                                                                </li>';
                                        }

                                        $zapisDate = strtotime($data['day'] . '.' . $data['month'] . '.' . $data['year']);
                                        if (time() < $zapisDate + 60 * 60 * 24) {
                                            $rezult .=
                                                '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $data['id'] . ', 8)">Ошибка, удалить из записи</div></li>';
                                        }
                                    }
                                    $rezult .= '
                                                            <li>
                                                                <div onclick="Ajax_TempZapis_edit_Enter(' . $data['id'] . ', 0)">
                                                                    Отменить все изменения
                                                                </div>
                                                            </li>';

                                    $rezult .=
                                        '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $data['id'] . ', 8)">Ошибка, удалить из записи</div></li>';
                                }*/
                            /*} else {*/
                                /*$rezult .=
                                    '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $data['id'] . ', 8)">Ошибка, удалить из записи</div></li>';
                                $rezult .=
                                    '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $data['id'] . ', 0)">Отменить все изменения</div></li>';*/
/*                            }
                        }*/

                        //Дополнительное расширение прав на добавление посещений для специалистов, god_mode и управляющих
                    /*    if ($edit_options) {
                            if ($data['office'] == $data['add_from']) {
                                if ($data['enter'] == 1) {
                                    //var_dump($data['type']);

                                    if (($data['type'] == 5) && $stom_edit) {
                                        $rezult .= '
                                                        <li>
                                                            <div>
                                                                <a href="add_task_stomat.php?client=' . $data['patient'] . '&filial=' . $data['office'] . '&insured=' . $data['insured'] . '&pervich=' . $data['pervich'] . '&noch=' . $data['noch'] . '&date=' . strtotime($data['day'] . '.' . $month . '.' . $data['year'] . ' ' . $start_time_h . ':' . $start_time_m) . '&zapis_id=' . $data['id'] . '&worker=' . $data['worker'] . '" class="ahref">
                                                                    Внести Осмотр/Зубную формулу
                                                                </a>
                                                            </div>
                                                        </li>';
                                    }
                                    if (($data['type'] == 6) && $cosm_edit) {
                                        $rezult .= '
                                                        <li>
                                                            <div>
                                                                <a href="add_task_cosmet.php?client=' . $data['patient'] . '&filial=' . $data['office'] . '&insured=' . $data['insured'] . '&pervich=' . $data['pervich'] . '&noch=' . $data['noch'] . '&date=' . strtotime($data['day'] . '.' . $month . '.' . $data['year'] . ' ' . $start_time_h . ':' . $start_time_m) . '&zapis_id=' . $data['id'] . '&worker=' . $data['worker'] . '" class="ahref">
                                                                    Внести посещение косм.
                                                                </a>
                                                            </div>
                                                        </li>';
                                    }
                                }
                            } else {
                                $rezult .= "&nbsp";
                            }*/
                    /*        if ($upr_edit) {
                                if (($data['enter'] != 8) && ($data['enter'] != 9)){
                                    $rezult .= '
                                                        <li>
                                                            <div>
                                                                <a href="edit_zapis_change_client.php?client_id=' . $data['patient'] . '&zapis_id=' . $data['id'] . '" class="ahref">
                                                                    Изменить пациента
                                                                </a>
                                                            </div>
                                                        </li>';
                                }
                            }*/
                    //    }


                    /*    $rezult .= '</ul>';

                        $rezult .= '
                                        </div>';

                    }*/

                    //<-- Управление настройки

                    $rezult .= '
                                    </li>';
                }
            //}
            /*$rezult .= '
                                <div id="ShowSettingsAddTempZapis" style="position: absolute; left: 10px; top: 0; background: rgb(186, 195, 192) none repeat scroll 0% 0%; display:none; z-index:105; padding:10px;">
                                    <a class="close" href="#" onclick="HideSettingsAddTempZapis()" style="display:block; position:absolute; top:-10px; right:-10px; width:24px; height:24px; text-indent:-9999px; outline:none;background:url(img/close.png) no-repeat;">
                                        Close
                                    </a>
                                    
                                    <div id="SettingsAddTempZapis">
    
                                        <div style="display:inline-block;">
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
                                                <div class="cellLeft">Число</div>
                                                <div class="cellRight" id="month_date">
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
                                                <div class="cellLeft">Смена</div>
                                                <div class="cellRight" id="month_date_smena">
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
                                                <div class="cellLeft">Филиал</div>
                                                <div class="cellRight" id="filial_name">
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
                                                <div class="cellLeft">Кабинет №</div>
                                                <div class="cellRight" id="kab">
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
                                                <div class="cellLeft">Врач</div>
                                                <div class="cellRight" id="worker_name">
                                                    <input type="text" size="30" name="searchdata2" id="search_client2" placeholder="Введите ФИО врача" value="" class="who2"  autocomplete="off" style="width: 90%;">
                                                    <ul id="search_result2" class="search_result2"></ul><br />
                                                </div>
                                            </div>
    
                                            <div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Пациент</div>
                                                <div class="cellRight">
                                                    <input type="text" size="30" name="searchdata" id="search_client" placeholder="Введите ФИО пациента" value="" class="who"  autocomplete="off" style="width: 90%;"> <a href="client_add.php" class="ahref"><i class="fa fa-plus-square" title="Добавить пациента" style="color: green; font-size: 120%;"></i></a>
                                                    <ul id="search_result" class="search_result"></ul><br />
                                                </div>
                                            </div>
                                            <!--<div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Телефон</div>
                                                <div class="cellRight" style="">
                                                    <input type="text" size="30" name="contacts" id="contacts" placeholder="Введите телефон" value="" autocomplete="off">
                                                </div>
                                            </div>-->
                                            <div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Описание</div>
                                                <div class="cellRight" style="">
                                                    <textarea name="description" id="description" style="width:90%; overflow:auto; height: 100px;"></textarea>
                                                </div>
                                            </div>		
                                            <div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Первичный</div>
                                                <div class="cellRight">
                                                    <input type="checkbox" name="pervich" id="pervich" value="1"> да
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Страховой</div>
                                                <div class="cellRight">
                                                    <input type="checkbox" name="insured" id="insured" value="1"> да
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Ночной</div>
                                                <div class="cellRight">
                                                    <input type="checkbox" name="noch" id="noch" value="1"> да
                                                </div>
                                            </div>
                                        </div>';
            $rezult .= '
                                        <div style="display:inline-block; vertical-align: top; width: 360px; border: 1px solid #C1C1C1;">
                                            <div id="ShowTimeSettingsHere">
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                                <div class="cellLeft">Время начала</div>
                                                <div class="cellRight">
                                                    <!--<div id="work_time_h" style="display:inline-block;"></div>:<div id="work_time_m" style="display:inline-block;"></div>-->
                                                    
                                                    <input type="number" size="2" name="work_time_h" id="work_time_h" min="0" max="23" value="0" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> часов
                                                    <input type="number" size="2" name="work_time_m" id="work_time_m" min="0" max="59" step="5" value="30" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> минут
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                                <div class="cellLeft">Длительность</div>
                                                <div class="cellRight">
                                                    <!--<div id="work_time_h" style="display:inline-block;"></div>:<div id="work_time_m" style="display:inline-block;"></div>-->
    
                                                    <input type="number" size="2" name="change_hours" id="change_hours" min="0" max="11" value="0" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> часов
                                                    <input type="number" size="2" name="change_minutes" id="change_minutes" min="0" max="59" step="5" value="30" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> минут
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                                <div class="cellLeft">Время окончания</div>
                                                <div class="cellRight">
                                                    <div id="work_time_h_end" style="display:inline-block;"></div>:<div id="work_time_m_end" style="display:inline-block;"></div>
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                                <div class="cellRight">
                                                    <div id="exist_zapis" style="display:inline-block;"></div>
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                                <div class="cellRight">
                                                    <div id="errror"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

            $rezult .= '
                                    <input type="hidden" id="day" name="day" value="0">
                                    <input type="hidden" id="month" name="month" value="0">
                                    <input type="hidden" id="year" name="year" value="0">
                                    <input type="hidden" id="author" name="author" value="' . $_SESSION['id'] . '">
                                    <input type="hidden" id="filial" name="filial" value="0">
                                    <input type="hidden" id="start_time" name="start_time" value="0">
                                    <input type="hidden" id="wt" name="wt" value="0">
                                    <input type="hidden" id="worker_id" name="worker_id" value="0">
                                    <input type="hidden" id="zapis_id" name="zapis_id" value="0">
                                    <input type="hidden" id="type" name="type" value="' . $type . '">
                                    <!--<input type="button" class="b" value="Добавить" id="Ajax_add_TempZapis" onclick="Ajax_add_TempZapis(' . $type . ')">-->
                                    <input type="button" class="b" value="OK" onclick="if (iCanManage) Ajax_edit_TempZapis(' . $type . ')" id="Ajax_add_TempZapis">
                                    <input type="button" class="b" value="Отмена" onclick="HideSettingsAddTempZapis()">
                                </div>';*/


            /*$rezult .= '
                            </div>';*/
            /*$rezult .= '
                            <!--</div>-->
                            <div id="req"></div>';*/


            //echo $rezult;

        }

    }
	
?>