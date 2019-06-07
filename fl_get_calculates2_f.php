<?php

//fl_get_calculates2_f.php
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

            $calculateData = array();
            //$arr = array();

            $result = array();
            $allCalcSumm = 0;

            if (!isset($_POST['permission']) || !isset($_POST['worker']) || !isset($_POST['month'])){
                echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {

                $msql_cnnct = ConnectToDB();

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
                //!!! 2019.05.24 отметки врачей не всегда верно отображаются, не понимаю пока почему
                //Если в условие добавить один филиал, то будет норм
                //
//                SELECT
//                            jcalc.id, jcalc.create_time, jcalc.summ, jcalc.invoice_id, jcalc.office_id, jcalc.zapis_id, jcalc.type, jcalc.client_id,
//                            ji.summ AS invoice_summ, ji.summins AS invoice_summins, ji.create_time  AS invoice_create_time, ji.zapis_id AS invoice_zapis_id, ji.create_time AS 		                    invoice_create_time,
//                            zapis.noch AS noch,
//                            sc.name AS client_name, sc.full_name AS client_full_name,
//                            wm.id AS worker_mark,
//                            GROUP_CONCAT(DISTINCT jcalcex.percent_cats ORDER BY jcalcex.percent_cats ASC SEPARATOR ',') AS percent_cats
//                            FROM `fl_journal_calculate` jcalc
//
//                            LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalc.id = jcalcex.calculate_id
//                            LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
//                            LEFT JOIN `zapis` zapis ON ji.zapis_id = zapis.id
//                            LEFT JOIN `spr_clients` sc ON sc.id = jcalc.client_id
//                            LEFT JOIN `journal_tooth_status` wm ON jcalc.zapis_id = wm.zapis_id
//                            WHERE jcalc.type='5' AND jcalc.worker_id='492' AND jcalc.status <> '7'
//                AND jcalc.id NOT IN ( SELECT `calculate_id` from `fl_journal_tabels_ex` WHERE `calculate_id`=jcalc.id )
//                            AND jcalc.date_in > '2018-05-31'
//
//                            GROUP BY jcalc.id

                $query = "
                            SELECT 
                            jcalc.id, jcalc.create_time, jcalc.summ, jcalc.invoice_id, jcalc.office_id, jcalc.zapis_id, jcalc.type, jcalc.client_id,
                            ji.summ AS invoice_summ, ji.summins AS invoice_summins, ji.create_time  AS invoice_create_time, ji.zapis_id AS invoice_zapis_id, ji.create_time AS invoice_create_time, 
                            zapis.noch AS noch,
                            sc.name AS client_name, sc.full_name AS client_full_name,";

//                $query = "
//                            SELECT
//                            jcalc.id, jcalc.create_time, jcalc.summ, jcalc.invoice_id, jcalc.office_id, jcalc.zapis_id, jcalc.type, jcalc.client_id,
//                            ji.summ AS invoice_summ, ji.summins AS invoice_summins, ji.create_time  AS invoice_create_time,
//                            sc.name AS client_name, sc.full_name AS client_full_name,";

                if (($_POST['permission'] == 5) || ($_POST['permission'] == 6)) {
                    $query .= "
                            wm.id AS worker_mark,";
                }
                $query .= "
                            GROUP_CONCAT(DISTINCT jcalcex.percent_cats ORDER BY jcalcex.percent_cats ASC SEPARATOR ',') AS percent_cats 
                            FROM `fl_journal_calculate` jcalc
                            
                            LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalc.id = jcalcex.calculate_id
                            LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
                            LEFT JOIN `zapis` zapis ON ji.zapis_id = zapis.id
                            LEFT JOIN `spr_clients` sc ON sc.id = jcalc.client_id";

                if ($_POST['permission'] == 5) {
                    $query .= "
                            LEFT JOIN `journal_tooth_status` wm ON wm.zapis_id = jcalc.zapis_id";
                }

                if($_POST['permission'] == 6){
                    $query .= "
                            LEFT JOIN `journal_cosmet1` wm ON wm.zapis_id = jcalc.zapis_id";
                }

                $query .= "
                            WHERE jcalc.type='{$_POST['permission']}' AND jcalc.worker_id='{$_POST['worker']}' AND jcalc.status <> '7'
                            AND jcalc.id NOT IN ( SELECT `calculate_id` from `fl_journal_tabels_ex` WHERE `calculate_id`=jcalc.id ) 
                            AND jcalc.date_in > '2018-05-31'
                            GROUP BY jcalc.id";

                //GROUP BY jcalc.id ORDER BY jcalc.id DESC";

/*                $query = "SELECT jcalc.*,
                            GROUP_CONCAT(DISTINCT jcalcex.percent_cats ORDER BY jcalcex.percent_cats ASC SEPARATOR ',') AS percent_cats
                            FROM `fl_journal_calculate` jcalc
                            LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalc.id = jcalcex.calculate_id
                            WHERE jcalc.type='{$_POST['permission']}' AND jcalc.worker_id='{$_POST['worker']}' AND jcalc.office_id='{$_POST['office']}' AND jcalc.status <> '7'
                                            AND jcalc.id NOT IN ( SELECT `calculate_id` from `fl_journal_tabels_ex` WHERE `calculate_id`=jcalc.id )
                            AND jcalc.date_in > '2018-05-31'
                            GROUP BY jcalc.id";*/

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        if (!isset($calculateData[$arr['office_id']])){
                            $calculateData[$arr['office_id']] = array();
                        }

                        array_push($calculateData[$arr['office_id']], $arr);
                    }
                    //var_dump($calculateData);

                    if (!empty($calculateData)){

                        //include_once 'fl_showCalculateRezult.php';

                        $rezArrayTemp = array();

                        //Пройдем по каждому филиалу
                        foreach ($calculateData as $filial_id => $filialData){

                            //$status = 0;
                            $resultFilialStr = '';

                            $resultFilialStr .= '
                                <div style="margin: 5px 0; padding: 2px; text-align: center; color: #0C0C0C; font-weight: bold;">
                                    Необработанные расчётные листы
                                </div>
                                <div style="margin: 5px 0; padding: 2px; text-align: center; color: #0C0C0C;">
                                    Выделить всё <input type="checkbox" id="chkBox_'.$_POST['permission'].'_'.$_POST['worker'].'_'.$filial_id.'" name="checkAll" class="checkAll" chkBoxData="chkBox_'.$_POST['permission'].'_'.$_POST['worker'].'_'.$filial_id.'" value="1">
                                </div>
                                <div id="calcs_list_'.$_POST['permission'].'_'.$_POST['worker'].'_'.$filial_id.'">';

                            //Сумма всех РЛ филиала
                            $summCalc = 0;

                            foreach ($filialData as $rezData) {

                                $invoice_summ = $rezData['invoice_summ'];
                                $invoice_summins = $rezData['invoice_summins'];
                                $invoice_create_time = date('d.m.y', strtotime($rezData['invoice_create_time']));
                                $invoice_create_time2 = date('y.m.d', strtotime($rezData['invoice_create_time']));

                                $zapis_id = $rezData['zapis_id'];
                                $invoice_type = $rezData['type'];

                                $name = $rezData['client_name'];
                                $full_name = $rezData['client_full_name'];

                                $noch = $rezData['noch'];

                                //Зубные формулы и запись врача
                                $worker_mark = 1;
                                $worker_mark_str = '';
                                $background_color = 'background-color: rgb(255, 255, 255);';

                                if (($_POST['permission'] == 5) || ($_POST['permission'] == 6)){
                                    if ($rezData['worker_mark'] == NULL) {
                                        $worker_mark = 0;
                                        $worker_mark_str = '<i class="fa fa-thumbs-down" aria-hidden="true" style="color: red; font-size: 110%;" title="Нет отметки врача"></i>';
                                        $background_color = 'background-color: rgba(255, 141, 141, 0.2);';
                                    }
                                }

                                if ($noch == 1){
                                    $noch_str = '<img src="img/night.png" style="width: 11px;" title="Ночное">';
                                }else{
                                    $noch_str = '';
                                }

                                if ($noch != 1) {
                                    $resultFilialStr .= '
                                    <div class="cellsBlockHover calculateBlockItem" data-sort="'.$invoice_create_time2.'" worker_mark="' . $worker_mark . '" style="' . $background_color . ' width: 217px; display: inline-block; border: 1px solid #BFBCB5; margin-top: 1px; position: relative;">
                                        <div style="display: inline-block; width: 190px;">
                                            <div>
                                                <a href="fl_calculate.php?id=' . $rezData['id'] . '" class="ahref">
                                                    <div>
                                                        <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                            <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                        </div>
                                                        <div style="display: inline-block; vertical-align: middle;">
                                                            <b>#' . $rezData['id'] . '</b> <span style="font-size: 70%; color: rgb(115, 112, 112);">' . date('d.m.y H:i', strtotime($rezData['create_time'])) . '</span>
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
                                                Сумма: ' . $invoice_summ . ' р. Страх.: ' . $invoice_summins . ' р.</b> <br>
                                                
                                            </div>
                                            <div style="margin: 5px 0 5px 3px; font-size: 80%;">';

                                    //Категории процентов(работ)
                                    $percent_cats_arr = explode(',', $rezData['percent_cats']);

                                    foreach ($percent_cats_arr as $percent_cat) {
                                        if ($percent_cat > 0) {
                                            $resultFilialStr .= '<i style="color: rgb(15, 6, 142); font-size: 110%;">' . $percent_cats_j[$percent_cat] . '</i><br>';
                                        } else {
                                            $resultFilialStr .= '<i style="color: red; font-size: 100%;">Ошибка #17</i><br>';
                                        }
                                    }

                                    $resultFilialStr .= '                                            
                                            </div>
                                        </div>
                                        <div style="display: inline-block; vertical-align: top;">
                                            <div style=" padding: 3px; margin: 1px;" title="Выделить">
                                                <input type="checkbox" worker_mark="' . $worker_mark . '" class="chkBoxCalcs chkBox_' . $_POST['permission'] . '_' . $_POST['worker'] . '_' . $filial_id . '" name="nPaidCalcs_' . $rezData['id'] . '" chkBoxData="chkBox_' . $_POST['permission'] . '_' . $_POST['worker'] . '_' . $filial_id . '" value="1">
                                            </div>
                                        </div>
                                        <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                        <div style="position: absolute; bottom: 2px; right: 3px;">
                                            ' . $worker_mark_str . '
                                        </div>
                                    </div>';

                                    $summCalc += $rezData['summ'];
                                    $allCalcSumm += $summCalc;
                                }
                            }
                            $resultFilialStr .= '
                                </div>
                                <div style="margin: 15px 0 5px; padding: 2px; text-align: right;">
                                    Сумма: <span class="summCalcsNPaid calculateInvoice">'.$summCalc.'</span> руб.
                                </div>';

                            if (($finances['see_all']) || $god_mode) {

                                $resultFilialStr .= '
                                <div style="margin: 5px 0; padding: 2px; text-align: right;">
                                    <div id="errrror"></div>';
                                if ($_POST['permission'] != 7) {
                                    $resultFilialStr .= '
                                    <input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Сформировать новый табель" onclick="fl_addNewTabelIN2(true, ' . $invoice_type . ', ' . $_POST['worker'] . ', ' . $filial_id . ');"><br>';
                                }
                                $resultFilialStr .= '
                                    <input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Добавить в существующий табель" onclick="fl_addNewTabelIN2(false, '.$invoice_type.', '.$_POST['worker'].', '.$filial_id.');"><br><br>
                                    <!--<input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Сформировать рассчет за ночь" onclick="fl_addNoch(true, '.$invoice_type.', '.$_POST['worker'].', '.$filial_id.');"><br><br>-->
                                    <input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Удалить выделенные" onclick="fl_deleteMarkedCalculates($(this).parent().parent());"><br>
                                    <input type="button" class="b" style="font-size: 80%; padding: 4px 8px;" value="Перерасчитать (не более 10 РЛ за раз)" onclick="fl_reloadPercentsMarkedCalculates($(this).parent().parent());">
                                </div>';
                            }


                            $result[$filial_id]['data'] = $resultFilialStr;
                            //$result[$filial_id]['summCalc'] = $summCalc;
                        }

                        //var_dump(json_encode(array('result' => 'success', 'status' => '1', 'data' => $result)));

                        //echo json_encode(array('result' => 'success', 'status' => '1', 'data' => $result));
                        //echo $result;

                    }

                    //echo json_encode(array('result' => 'success', 'status' => '1', 'data' => $result, 'summCalc' => $allCalcSumm));

                }

                echo json_encode(array('result' => 'success', 'status' => '0', 'data' => $result));
                //echo json_encode(array('result' => 'success', 'data' => $query));

            }
        }else{
            echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Какая-то ошибка.</div>', 'summCalc' => 0));
        }
    }
?>