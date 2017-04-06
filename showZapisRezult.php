<?php 

//showZapisRezult.php
//функция формирует и показывает массив записи

    function showZapisRezult($ZapisHereQueryToday, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, $type){
        //var_dump($ZapisHereQueryToday);

        if ($ZapisHereQueryToday != 0) {

            include_once 'DBWork.php';
            include_once 'functions.php';

            require 'variables.php';

            $rezult = '';

            $office_j = SelDataFromDB('spr_office', '', '');
            //var_dump($office_j);
            //!!!
            $office_j_arr = array();

            foreach ($office_j as $office_item){
                $office_j_arr[$office_item['id']] = $office_item;
            }
            //var_dump($office_j_arr);


            for ($z = 0; $z < count($ZapisHereQueryToday); $z++) {

                $t_f_data_db = array();
                $cosmet_data_db = array();
                $invoice_data_db = array();
                $back_color = '';

                if (($ZapisHereQueryToday[$z]['enter'] != 8) || (($ZapisHereQueryToday[$z]['enter'] == 8) && $upr_edit)) {


                    if ($ZapisHereQueryToday[$z]['enter'] == 1) {
                        $back_color = 'background-color: rgba(119, 255, 135, 1);';
                    } elseif ($ZapisHereQueryToday[$z]['enter'] == 9) {
                        $back_color = 'background-color: rgba(239,47,55, .7);';
                    } elseif ($ZapisHereQueryToday[$z]['enter'] == 8) {
                        $back_color = 'background-color: rgba(137,0,81, .7);';
                    } else {
                        //Если оформлено не на этом филиале
                        if ($ZapisHereQueryToday[$z]['office'] != $ZapisHereQueryToday[$z]['add_from']) {
                            $back_color = 'background-color: rgb(119, 255, 250);';
                        } else {
                            $back_color = 'background-color: rgba(255,255,0, .5);';
                        }
                    }

                    $dop_img = '';

                    if ($ZapisHereQueryToday[$z]['insured'] == 1) {
                        $dop_img .= '<img src="img/insured.png" title="Страховое"> ';
                    }
                    if ($ZapisHereQueryToday[$z]['pervich'] == 1) {
                        $dop_img .= '<img src="img/pervich.png" title="Первичное"> ';
                    }
                    if ($ZapisHereQueryToday[$z]['noch'] == 1) {
                        $dop_img .= '<img src="img/night.png" title="Ночное"> ';
                    }

                    $rezult .= '
                                        <li class="cellsBlock" style="width: auto;">
                                            <!--<div class="cellCosmAct">-->';

                    //Формулы зубные
                    $query = "SELECT `id`, `zapis_date`  FROM `journal_tooth_status` WHERE `zapis_id` = '{$ZapisHereQueryToday[$z]['id']}' ORDER BY `create_time`";
                    $res = mysql_query($query) or die(mysql_error() . ' -> ' . $query);
                    $number = mysql_num_rows($res);
                    if ($number != 0) {
                        while ($arr = mysql_fetch_assoc($res)) {
                            array_push($t_f_data_db, $arr);
                        }
                    } else
                        $t_f_data_db = 0;
                    //var_dump($t_f_data_db);

                    if ($t_f_data_db != 0) {
                        foreach ($t_f_data_db as $ids) {

                        }
                    }


                    //Посещения косметологов
                    $query = "SELECT `id`, `zapis_date`  FROM `journal_cosmet1` WHERE `zapis_id` = '{$ZapisHereQueryToday[$z]['id']}' ORDER BY `create_time`";
                    $res = mysql_query($query) or die(mysql_error() . ' -> ' . $query);
                    $number = mysql_num_rows($res);
                    if ($number != 0) {
                        while ($arr = mysql_fetch_assoc($res)) {
                            array_push($cosmet_data_db, $arr);
                        }
                    } else
                        $cosmet_data_db = 0;
                    //var_dump($cosmet_data_db);

                    if ($cosmet_data_db != 0) {
                        foreach ($cosmet_data_db as $ids) {
                            //
                        }
                    }

                    //Наряды
                    $query = "SELECT * FROM `journal_invoice` WHERE `zapis_id` = '{$ZapisHereQueryToday[$z]['id']}' AND `status` <> '9' ORDER BY `create_time`";
                    $res = mysql_query($query) or die(mysql_error() . ' -> ' . $query);
                    $number = mysql_num_rows($res);
                    if ($number != 0) {
                        while ($arr = mysql_fetch_assoc($res)) {
                            array_push($invoice_data_db, $arr);
                        }
                    } else
                        $invoice_data_db = 0;
                    //var_dump($invoice_data_db);

                    if ($invoice_data_db != 0) {
                        foreach ($invoice_data_db as $ids) {
                            //
                        }
                    }

                    $rezult .= '
                                            <!--</div>-->
                                            <div class="cellName" style="position: relative; ' . $back_color . '">';
                    $start_time_h = floor($ZapisHereQueryToday[$z]['start_time'] / 60);
                    $start_time_m = $ZapisHereQueryToday[$z]['start_time'] % 60;
                    if ($start_time_m < 10) $start_time_m = '0' . $start_time_m;
                    $end_time_h = floor(($ZapisHereQueryToday[$z]['start_time'] + $ZapisHereQueryToday[$z]['wt']) / 60);
                    if ($end_time_h > 23) $end_time_h = $end_time_h - 24;
                    $end_time_m = ($ZapisHereQueryToday[$z]['start_time'] + $ZapisHereQueryToday[$z]['wt']) % 60;
                    if ($end_time_m < 10) $end_time_m = '0' . $end_time_m;

                    $day = $ZapisHereQueryToday[$z]['day'];

                    if ($ZapisHereQueryToday[$z]['month'] < 10) $month = '0' . $ZapisHereQueryToday[$z]['month'];
                    else $month = $ZapisHereQueryToday[$z]['month'];

                    $year = $ZapisHereQueryToday[$z]['year'];

                    $rezult .=
                        '<b>' . $day . ' ' . $monthsName[$month] . ' ' . $year . '</b><br>' .
                        $start_time_h . ':' . $start_time_m . ' - ' . $end_time_h . ':' . $end_time_m;

                    $rezult .= '
                                                <div style="position: absolute; top: 1px; right: 1px;">' . $dop_img . '</div>';
                    $rezult .= '
                                            </div>';
                    $rezult .= '
                                            <div class="cellName">';
                    $rezult .=
                        'Пациент <br /><b>' . WriteSearchUser('spr_clients', $ZapisHereQueryToday[$z]['patient'], 'user', true) . '</b>';
                    $rezult .= '
                                            </div>';
                    $rezult .= '
                                            <div class="cellName">';
                    $rezult .=
                        'Филиал:<br>' .
                        $office_j_arr[$ZapisHereQueryToday[$z]['office']]['name'];
                    $rezult .= '
                                            </div>';
                    $rezult .= '
                                            <div class="cellName">';
                    $rezult .=
                        $ZapisHereQueryToday[$z]['kab'] . ' кабинет<br>' . 'Врач: <br><b>' . WriteSearchUser('spr_workers', $ZapisHereQueryToday[$z]['worker'], 'user', true) . '</b>';
                    $rezult .= '
                                            </div>';
                    $rezult .= '
                                            <div class="cellName" style="max-width: 120px; overflow: auto;">';
                    $rezult .=
                        '<b><i>Описание:</i></b><br><div style="text-overflow: ellipsis; overflow: hidden; white-space: inherit; display: block; width: 120px;" title="' . $ZapisHereQueryToday[$z]['description'] . '">' . $ZapisHereQueryToday[$z]['description'] . '</div>';
                    $rezult .= '
                                            </div>';
                    $rezult .= '
                                            <div class="cellName">';
                    $rezult .= '
                                                Добавлено<br>' . date('d.m.y H:i', $ZapisHereQueryToday[$z]['create_time']) . '<br>
                                                Кем: ' . WriteSearchUser('spr_workers', $ZapisHereQueryToday[$z]['create_person'], 'user', true);
                    if (($ZapisHereQueryToday[$z]['last_edit_time'] != 0) || ($ZapisHereQueryToday[$z]['last_edit_person'] != 0)) {
                        $rezult .= '<hr>
                                                Изменено: ' . date('d.m.y H:i', $ZapisHereQueryToday[$z]['last_edit_time']) . '<br>
                                                Кем: ' . WriteSearchUser('spr_workers', $ZapisHereQueryToday[$z]['last_edit_person'], 'user', true) . '';
                    }
                    $rezult .= '
                                            </div>';

                    //Формулы посещения наряды -->
                    $rezult .= '
                                            <div class="cellName" style="vertical-align: top;">';

                    if ($t_f_data_db != 0) {
                        foreach ($t_f_data_db as $ids) {
                            $rezult .= '
                                                <div class="cellsBlockHover" style="border: 1px solid #BFBCB5; margin-top: 1px;">
                                                    <a href="task_stomat_inspection.php?id=' . $ids['id'] . '" class="ahref">
                                                        <div style="display: inline-block; vertical-align: middle;"><img src="img/tooth2.svg" width="20px" height="20px"></div><div style="display: inline-block; vertical-align: middle;">' . date('d.m.y H:i', $ids['zapis_date']) . '</div>
                                                    </a>	
                                                </div>';
                        }
                    }

                    if ($cosmet_data_db != 0) {
                        foreach ($cosmet_data_db as $ids) {
                            $rezult .= '
                                                    <div class="cellsBlockHover" style="border: 1px solid #BFBCB5; margin-top: 1px;">
                                                        <a href="task_cosmet.php?id=' . $ids['id'] . '" class="ahref">
                                                            <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding-left: 2px; font-weight: bold; font-style: italic;">K</div> <div style="display: inline-block; vertical-align: middle;">' . date('d.m.y H:i', $ids['zapis_date']) . '</div>
                                                        </a>	
                                                    </div>';
                        }
                    }

                    if ($invoice_data_db != 0) {
                        //var_dump($invoice_data_db);
                        foreach ($invoice_data_db as $ids) {

                            //Отметка об объеме оплат
                            $paid_mark = '<i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;"></i>';

                            if ($ids['summ'] == $ids['paid']) {
                                $paid_mark = '<i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i>';
                            }

                            $rezult .= '
                                                <div class="cellsBlockHover" style="border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
                                                    <a href="invoice.php?id=' . $ids['id'] . '" class="ahref">
                                                        <div>
                                                            <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                                <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                            </div>
                                                            <div style="display: inline-block; vertical-align: middle;">
                                                                ' . date('d.m.y', strtotime($ids['create_time'])) . '
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                                <span class="calculateInvoice" style="font-size: 11px">' . $ids['summ'] . '</span> руб.
                                                            </div>';
                            if ($ids['summins'] != 0) {
                                $rezult .= '
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                                Страховка:<br>
                                                                <span class="calculateInsInvoice" style="font-size: 11px">' . $ids['summins'] . '</span> руб.
                                                            </div>';
                            }
                            $rezult .= '
                                                        </div>
                                                        
                                                    </a>
                                                    <span style="position: absolute; top: 2px; right: 3px;">'.$paid_mark.'</span>
                                                </div>';
                        }
                    }
                    //<-- Формулы посещения наряды

                    $rezult .= '
                                            </div>';

                    //Управление настройки -->

                    $rezult .= '
                                            <div class="cellName settings_text" style="background-color: rgb(240, 240, 240); text-align: center; vertical-align: middle; width: 8 0px; min-width: 80px; max-width: 80px;" onclick="contextMenuShow(' . $ZapisHereQueryToday[$z]['id'] . ', 0, event, \'zapis_options\');">';

                    $rezult .= 'Меню [опции]';

                    $rezult .= '
                                                <ul id="zapis_options' . $ZapisHereQueryToday[$z]['id'] . '" class="zapis_options" style="display: none;">';

                    if (isset($_SESSION['filial'])) {

                        if ($_SESSION['filial'] == $ZapisHereQueryToday[$z]['office']) {
                            if ($ZapisHereQueryToday[$z]['office'] != $ZapisHereQueryToday[$z]['add_from']) {
                                if ($ZapisHereQueryToday[$z]['enter'] != 8) {
                                    $rezult .= '<li><div onclick="Ajax_TempZapis_edit_OK(' . $ZapisHereQueryToday[$z]['id'] . ', ' . $ZapisHereQueryToday[$z]['office'] . ')">Подтвердить</div></li>';
                                }
                            }
                            if ($ZapisHereQueryToday[$z]['office'] == $ZapisHereQueryToday[$z]['add_from']) {
                                if ($ZapisHereQueryToday[$z]['enter'] != 8) {
                                    $rezult .=
                                        '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $ZapisHereQueryToday[$z]['id'] . ', 1)">Пришёл</div></li>';
                                    $rezult .=
                                        '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $ZapisHereQueryToday[$z]['id'] . ', 9)">Не пришёл</div></li>';
                                    $rezult .=
                                        '<li><div onclick="ShowSettingsAddTempZapis('.$ZapisHereQueryToday[$z]['office']. ', \'' .  $office_j_arr[$ZapisHereQueryToday[$z]['office']]['name'] . '\', ' . $ZapisHereQueryToday[$z]['kab'] . ', ' . $year . ', ' . $month . ',' . $day . ', 0, ' . $ZapisHereQueryToday[$z]['start_time'] . ', ' . $ZapisHereQueryToday[$z]['wt'] . ', ' . $ZapisHereQueryToday[$z]['worker'] . ', \'' . WriteSearchUser('spr_workers', $ZapisHereQueryToday[$z]['worker'], 'user_full', false) . '\', \'' . WriteSearchUser('spr_clients', $ZapisHereQueryToday[$z]['patient'], 'user_full', false) . '\', \'' . str_replace(array("\r", "\n"), " ", $ZapisHereQueryToday[$z]['description']) . '\', ' . $ZapisHereQueryToday[$z]['insured'] . ', ' . $ZapisHereQueryToday[$z]['pervich'] . ', ' . $ZapisHereQueryToday[$z]['noch'] . ', ' . $ZapisHereQueryToday[$z]['id'] . ')">Редактировать</div></li>';

                                    //var_dump($ZapisHereQueryToday[$z]['create_time']);
                                    //var_dump($ZapisHereQueryToday[$z]['description']);
                                    //var_dump(time());

                                    if (($ZapisHereQueryToday[$z]['enter'] == 1) && ($finance_edit)) {
                                        $rezult .=
                                            '<li>
                                                                <div>
                                                                    <a href="invoice_add.php?client=' . $ZapisHereQueryToday[$z]['patient'] . '&filial=' . $ZapisHereQueryToday[$z]['office'] . '&date=' . strtotime($ZapisHereQueryToday[$z]['day'] . '.' . $month . '.' . $ZapisHereQueryToday[$z]['year'] . ' ' . $start_time_h . ':' . $start_time_m) . '&id=' . $ZapisHereQueryToday[$z]['id'] . '&worker=' . $ZapisHereQueryToday[$z]['worker'] . '&type=' . $ZapisHereQueryToday[$z]['type'] . '" class="ahref">
                                                                        Внести наряд
                                                                    </a>
                                                                </div>
                                                            </li>';
                                    }

                                    $zapisDate = strtotime($ZapisHereQueryToday[$z]['day'] . '.' . $ZapisHereQueryToday[$z]['month'] . '.' . $ZapisHereQueryToday[$z]['year']);
                                    if (time() < $zapisDate + 60 * 60 * 24) {
                                        $rezult .=
                                            '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $ZapisHereQueryToday[$z]['id'] . ', 8)">Ошибка, удалить из записи</div></li>';
                                    }
                                }
                                $rezult .= '
                                                        <li>
                                                            <div onclick="Ajax_TempZapis_edit_Enter(' . $ZapisHereQueryToday[$z]['id'] . ', 0)">
                                                                Отменить все изменения
                                                            </div>
                                                        </li>';
                            }
                        } else {
                            $rezult .=
                                '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $ZapisHereQueryToday[$z]['id'] . ', 8)">Ошибка, удалить из записи</div></li>';
                            $rezult .=
                                '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $ZapisHereQueryToday[$z]['id'] . ', 0)">Отменить все изменения</div></li>';
                        }
                    }

                    //Дополнительное расширение прав на добавление посещений для специалистов, god_mode и управляющих
                    if ($edit_options) {
                        if ($ZapisHereQueryToday[$z]['office'] == $ZapisHereQueryToday[$z]['add_from']) {
                            if ($ZapisHereQueryToday[$z]['enter'] == 1) {
                                //var_dump($ZapisHereQueryToday[$z]['type']);

                                if (($ZapisHereQueryToday[$z]['type'] == 5) && $stom_edit) {
                                    $rezult .= '
                                                    <li>
                                                        <div>
                                                            <a href="add_task_stomat.php?client=' . $ZapisHereQueryToday[$z]['patient'] . '&filial=' . $ZapisHereQueryToday[$z]['office'] . '&insured=' . $ZapisHereQueryToday[$z]['insured'] . '&pervich=' . $ZapisHereQueryToday[$z]['pervich'] . '&noch=' . $ZapisHereQueryToday[$z]['noch'] . '&date=' . strtotime($ZapisHereQueryToday[$z]['day'] . '.' . $month . '.' . $ZapisHereQueryToday[$z]['year'] . ' ' . $start_time_h . ':' . $start_time_m) . '&id=' . $ZapisHereQueryToday[$z]['id'] . '&worker=' . $ZapisHereQueryToday[$z]['worker'] . '" class="ahref">
                                                                Внести Осмотр/Зубную формулу
                                                            </a>
                                                        </div>
                                                    </li>';
                                }
                                if (($ZapisHereQueryToday[$z]['type'] == 6) && $cosm_edit) {
                                    $rezult .= '
                                                    <li>
                                                        <div>
                                                            <a href="add_task_cosmet.php?client=' . $ZapisHereQueryToday[$z]['patient'] . '&filial=' . $ZapisHereQueryToday[$z]['office'] . '&insured=' . $ZapisHereQueryToday[$z]['insured'] . '&pervich=' . $ZapisHereQueryToday[$z]['pervich'] . '&noch=' . $ZapisHereQueryToday[$z]['noch'] . '&date=' . strtotime($ZapisHereQueryToday[$z]['day'] . '.' . $month . '.' . $ZapisHereQueryToday[$z]['year'] . ' ' . $start_time_h . ':' . $start_time_m) . '&id=' . $ZapisHereQueryToday[$z]['id'] . '&worker=' . $ZapisHereQueryToday[$z]['worker'] . '" class="ahref">
                                                                Внести посещение косм.
                                                            </a>
                                                        </div>
                                                    </li>';
                                }
                            }
                        } else {
                            $rezult .= "&nbsp";
                        }
                    }

                    $rezult .= '</ul>';

                    $rezult .= '
                                        </div>';
                    //<-- Управление настройки

                    $rezult .= '
                                    </li>';
                }
            }
            $rezult .= '
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
                                                    <input type="text" size="30" name="searchdata" id="search_client" placeholder="Введите ФИО пациента" value="" class="who"  autocomplete="off" style="width: 90%;"> <a href="add_client.php" class="ahref"><i class="fa fa-plus-square" title="Добавить пациента" style="color: green; font-size: 120%;"></i></a>
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
                                </div>';


            /*$rezult .= '
                            </div>';*/
            $rezult .= '
                            <!--</div>-->
                            <div id="req"></div>';


            return $rezult;

        }

    }
	
?>