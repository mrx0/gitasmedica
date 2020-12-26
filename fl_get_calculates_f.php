<?php

//fl_get_calculates_f.php
//Функция поиска данных расчётов за период

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'fl_DBWork.php';

            //разбираемся с правами
            $god_mode = FALSE;
            require_once 'permissions.php';

            $rez = array();
            $arr = array();

            $summCalc = 0;

            $rezult = '';

            $invoice_rez_str = '';

            if (!isset($_POST['permission']) || !isset($_POST['worker']) || !isset($_POST['office']) || !isset($_POST['month'])){
                echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {

                $msql_cnnct = ConnectToDB();

                //Отметки по дополнительным опциям
                //!!! Здесь функция большая и избыточная, но лень переписывать
                $spec_prikaz8_checked = '';
                $spec_oklad_checked = '';

                $query = "SELECT * FROM `options_worker_spec` WHERE `worker_id`='{$_POST['worker']} LIMIT 1'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                $spec_prikaz8 = false;
                $spec_oklad = false;

                if ($number != 0){
                    $arr = mysqli_fetch_assoc($res);
                    if ($arr['prikaz8'] == 1){
                        $spec_prikaz8 = true;
                    }
                    if ($arr['oklad'] == 1){
                        $spec_oklad = true;
                    }
                }

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
                //20200421 добавил ограничение, чтоб не собирались ночные посещения, так как ночи давно нет
                $query = "SELECT jcalc.*, ji.summ AS in_summ, ji.summins AS in_summins, ji.zapis_id AS in_zapis_id, ji.type AS in_type, ji.create_time AS in_create_time, zapis.noch AS noch,
                            GROUP_CONCAT(DISTINCT jcalcex.percent_cats ORDER BY jcalcex.percent_cats ASC SEPARATOR ',') AS percent_cats 
                            FROM `fl_journal_calculate` jcalc

                            LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalc.id = jcalcex.calculate_id
                            LEFT JOIN `journal_invoice` ji ON jcalc.invoice_id = ji.id
                            LEFT JOIN `zapis` zapis ON ji.zapis_id = zapis.id

                            WHERE jcalc.type='{$_POST['permission']}' AND jcalc.worker_id='{$_POST['worker']}' AND jcalc.office_id='{$_POST['office']}' AND jcalc.status <> '7'
                                            AND jcalc.id NOT IN ( SELECT `calculate_id` from `fl_journal_tabels_ex` WHERE `calculate_id`=jcalc.id ) 
                            AND jcalc.date_in > '2018-05-31'
                            
                            AND zapis.noch = '0'
                            
                            GROUP BY jcalc.id";
                //$query = "SELECT jcalc.* FROM `fl_journal_calculate` jcalc WHERE jcalc.type='{$_POST['permission']}' AND jcalc.worker_id='{$_POST['worker']}' AND jcalc.office_id='{$_POST['office']}' AND jcalc.status <> '7' AND jcalc.id NOT IN (SELECT `calculate_id` from `fl_journal_tabels_ex` WHERE `calculate_id`=jcalc.id) AND jcalc.date_in > '2018-05-31';";
                //$query = "SELECT jcalc.* FROM `fl_journal_calculate` jcalc WHERE jcalc.type='{$_POST['permission']}' AND jcalc.worker_id='{$_POST['worker']}' AND jcalc.office_id='{$_POST['office']}' AND jcalc.status <> '7' AND jcalc.id NOT IN (SELECT `calculate_id` from `fl_journal_tabels_ex` WHERE `calculate_id`=jcalc.id);";
                //$query = "SELECT * FROM `fl_journal_calculate` WHERE `type`='{$_POST['permission']}' AND `worker_id`='{$_POST['worker']}' AND `office_id`='{$_POST['office']}' AND MONTH(`create_time`) = '09' AND `status` <> '7';";


                /*Собираем данные с дополнительными
                $query = "SELECT jcalc.*, jcalc.id as calc_id, jcalcex.*
                FROM `fl_journal_calculate_ex` jcalcex
                RIGHT JOIN (
                  SELECT * FROM `fl_journal_calculate` WHERE `type`='{$_POST['permission']}' AND `worker_id`='{$_POST['worker']}' AND `office_id`='{$_POST['office']}' AND MONTH(`create_time`) = '09' AND `status` <> '7'
                ) jcalc ON jcalc.id = jcalcex.calculate_id";*/

                /*$query = "SELECT jcalc.*, jcalcex.*
                FROM `journal_announcing_readmark` jannrm
                RIGHT JOIN (
                  SELECT * FROM `journal_announcing` WHERE `status` <> '9'
                ) jcalc ON jcalc.id = jannrm.announcing_id
                AND jannrm.create_person = '{$_SESSION['id']}'
                ORDER BY `create_time` DESC";*/


                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($rez, $arr);
                    }

                    if (!empty($rez)){

                        //include_once 'fl_showCalculateRezult.php';

                        $disabled_chkbox = '';

                        //Если спец отметка оклад, то отключаем добавление РЛ
                        if ($spec_oklad){
                            $disabled_chkbox = 'disabled';
                        }

                        $rezult .= '
                            <div style="margin: 5px 0; padding: 2px; text-align: center; color: #0C0C0C; font-weight: bold;">
                                Необработанные расчётные листы
                            </div>
                            <div style="margin: 5px 0; padding: 2px; text-align: center; color: #0C0C0C;">
                                Выделить всё <input type="checkbox" id="chkBox_'.$_POST['permission'].'_'.$_POST['worker'].'_'.$_POST['office'].'" name="checkAll" class="checkAll" chkBoxData="chkBox_'.$_POST['permission'].'_'.$_POST['worker'].'_'.$_POST['office'].'" value="1" '.$disabled_chkbox.'>
                            </div>
                            <div id="calcs_list_'.$_POST['permission'].'_'.$_POST['worker'].'_'.$_POST['office'].'">';

                        $rezArrayTemp = array();

                        foreach ($rez as $rezData){

                            //Наряды
                            //$query = "SELECT `summ`, `summins`, `zapis_id`, `type`, `create_time` FROM `journal_invoice` WHERE `id`='{$rezData['invoice_id']}' LIMIT 1";

                            /*$query2 = "SELECT `summ` AS `summ`, `summins` AS `summins` FROM `journal_invoice` WHERE `id`='{$rezData['invoice_id']}'
                            UNION ALL (
                              SELECT `name` AS `name`, `full_name` AS `full_name` FROM `spr_clients` WHERE `id`='{$rezData['client_id']}'
                            )";*/

//                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                            $number = mysqli_num_rows($res);
//
//                            if ($number != 0) {
//                                /*while ($arr = mysqli_fetch_assoc($res)) {
//                                    array_push($rez, $arr);
//                                }*/
//
//                                $arr = mysqli_fetch_assoc($res);
//                                $summ = $arr['summ'];
//                                $summins = $arr['summins'];
//                                $invoice_create_time = date('d.m.y', strtotime($arr['create_time']));
//                                $zapis_id = $arr['zapis_id'];
//                                $invoice_type = $arr['type'];
//                            }

                            //ji.summ AS in_summ, ji.summins AS in_summins, ji.zapis_id AS in_zapis_id, ji.type AS in_type, ji.create_time AS in_create_time,
                            $summ = $rezData['in_summ'];
                            $summins = $rezData['in_summins'];
                            $invoice_create_time = date('d.m.y', strtotime($rezData['in_create_time']));
                            $invoice_create_time2 = date('y.m.d', strtotime($rezData['in_create_time']));
                            $zapis_id = $rezData['in_zapis_id'];
                            $invoice_type = $rezData['in_type'];
                            $noch = $rezData['noch'];


                            //Клиент
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
                            $worker_mark = 1;
                            $doctor_mark = '';
                            $background_color = 'background-color: rgb(255, 255, 255);';

                            if ($invoice_type == 5) {
                                $query = "SELECT `id` FROM `journal_tooth_status` WHERE `zapis_id`='$zapis_id' LIMIT 1";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                $number = mysqli_num_rows($res);
                            }

                            if ($invoice_type == 6) {
                                $query = "SELECT `id` FROM `journal_cosmet1` WHERE `zapis_id`='$zapis_id' AND `status` <> '9' LIMIT 1";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                $number = mysqli_num_rows($res);
                            }

                            if ($number == 0){
                                $worker_mark = 0;
                                $doctor_mark = '<i class="fa fa-thumbs-down" aria-hidden="true" style="color: red; font-size: 110%;" title="Нет отметки врача"></i>';
                                $background_color = 'background-color: rgba(255, 141, 141, 0.2);';
                            }

                            if ($noch == 1){
                                $noch_str = '<img src="img/night.png" style="width: 11px;" title="Ночное">';
                            }else{
                                $noch_str = '';
                            }

                            if (($noch != 1) || ($_POST['permission'] == 7)) {
                                $rezult .= '
                                <div class="cellsBlockHover calculateBlockItem" data-sort="'.$invoice_create_time2.'" worker_mark="' . $worker_mark . '" style="' . $background_color . ' width: 217px; display: inline-block; border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
                                    <div style="display: inline-block; width: 190px;">
                                        <div>
                                            <a href="fl_calculate.php?id=' . $rezData['id'] . '" class="ahref">
                                                <div>
                                                    <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">';
                                                        
                                //Если время наряда меньше текущего месяца, сигнализируем
//                                $rezult .= date('y.m.01', time()).'<br>';
//                                $rezult .= $rezData['in_create_time'];
//                                $rezult .= $invoice_create_time2;
                                //$rezult .= date('y.m.01', time()) > $invoice_create_time2;

                                if (date('y.m.01', time()) > $invoice_create_time2){
                                    $rezult .= '
                                                        <i class="fa fa-warning" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(111, 111, 111, 0.8);" title="РЛ за прошедший период"></i>';
                                }

                                $rezult .= '
                                                        <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                    </div>
                                                    <div style="display: inline-block; vertical-align: middle;">
                                                        <b>#' . $rezData['id'] . '</b> <span style="font-size: 75%; color: rgb(115, 112, 112);">' . date('d.m.y H:i', strtotime($rezData['create_time'])) . '</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                        Сумма расчёта: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                                    </div>
                                                </div>

                                            </a>
                                        </div>
                                        <div style="margin: 5px 0 5px 3px; font-size: 80%;">
                                            <b>Наряд: <a href="invoice.php?id=' . $rezData['invoice_id'] . '" class="ahref">#' . $rezData['invoice_id'] . '</a> от ' . $invoice_create_time . ' ' . $noch_str . '<br>пац.: <a href="client.php?id=' . $rezData['client_id'] . '" class="ahref">' . $name . '</a><br>
                                            Сумма: <span class="invoice_summ">' . $summ . '</span> р. Страх.: <span class="invoice_summ_ins">' . $summins . '</span> р.</b> <br>

                                        </div>
                                        <div style="margin: 5px 0 5px 3px; font-size: 80%;">';

                                //Категории процентов(работ)
                                $percent_cats_arr = explode(',', $rezData['percent_cats']);

                                foreach ($percent_cats_arr as $percent_cat) {
                                    $bgColor = "";

                                    if (($percent_cat == 58) || ($percent_cat == 59) || ($percent_cat == 60) || ($percent_cat == 61)){
                                        $bgColor = "background-color: yellow;";
                                    }
                                    if ($percent_cat > 0) {
                                        $rezult .= '<i style="color: rgb(15, 6, 142); font-size: 110%; '.$bgColor.'">' . $percent_cats_j[$percent_cat] . '</i><br>';
                                    } else {
                                        $rezult .= '<i style="color: red; font-size: 100%;">Ошибка #72</i><br>';
                                    }
                                }

                                $rezult .= '                                            
                                        </div>
                                    </div>
                                    <div style="display: inline-block; vertical-align: top;">
                                        <div style="/*border: 1px solid #CCC;*/ padding: 3px; margin: 1px;" title="Выделить">
                                            <input type="checkbox" class="chkBoxCalcs chkBox_' . $_POST['permission'] . '_' . $_POST['worker'] . '_' . $_POST['office'] . '" name="nPaidCalcs_' . $rezData['id'] . '" chkBoxData="chkBox_' . $_POST['permission'] . '_' . $_POST['worker'] . '_' . $_POST['office'] . '" value="1" '.$disabled_chkbox.'>
                                        </div>
                                    </div>
                                    <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                    <div style="position: absolute; bottom: 2px; right: 3px;">
                                        ' . $doctor_mark . '
                                    </div>
                                </div>';

                                $summCalc += $rezData['summ'];
                            }
                        }


                        $rezult .= '
                            </div>
                            <div style="margin: 15px 0 5px; padding: 2px; text-align: right;">
                                Сумма: <span class="summCalcsNPaid calculateInvoice">0</span> руб.
                            </div>';

                        if (($finances['see_all']) || $god_mode) {

                            $rezult .= '
                                <div style="margin: 5px 0; padding: 2px; text-align: right;">
                                    <div id="errrror"></div>';
                            if ($_POST['permission'] != 7) {
                                $rezult .= '
                                    <input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Сформировать новый табель" onclick="fl_addNewTabelIN2(true, ' . $invoice_type . ', ' . $_POST['worker'] . ', ' . $_POST['office'] . ');"><br>';
                            }
                            $rezult .= '
                                    <input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Добавить в существующий табель" onclick="fl_addNewTabelIN2(false, '.$invoice_type.', '.$_POST['worker'].', '.$_POST['office'].');"><br>';

                            if ($_POST['permission'] != 7) {
//                                $rezult .= '
//                                    <input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Создать пустой табель" onclick="fl_addNewClearTabelIN(true, ' . $invoice_type . ', ' . $_POST['worker'] . ', ' . $_POST['office'] . ');"><br>';
                            }
                            $rezult .= '
                                    <br>
                                    <!--<input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Сформировать рассчет за ночь" onclick="fl_addNoch(true, '.$invoice_type.', '.$_POST['worker'].', '.$_POST['office'].');"><br><br>-->
                                    <input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Удалить выделенные" onclick="fl_deleteMarkedCalculates($(this).parent().parent());"><br>
                                    <input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Перерасчитать (не более 10 РЛ за раз)" onclick="fl_reloadPercentsMarkedCalculates($(this).parent().parent());">
                                </div>';
                        }

                        echo json_encode(array('result' => 'success', 'status' => '1', 'data' => $rezult, 'summCalc' => $summCalc));
                    }else{
                        echo json_encode(array('result' => 'success', 'status' => '0', 'data' => '', 'summCalc' => $summCalc));
                    }
                } else {
                    echo json_encode(array('result' => 'success', 'status' => '0', 'data' => '', 'summCalc' => 0));
                }
                //echo json_encode(array('result' => 'success', 'data' => $query));

            }
        }else{
            echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Какая-то ошибка.</div>', 'summCalc' => 0));
        }
    }
?>