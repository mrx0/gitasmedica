<?php 

//ajax_show_result_cashbox_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST) {
            include_once 'DBWork.php';
            include_once 'functions.php';

            //разбираемся с правами
            $god_mode = FALSE;

            require_once 'permissions.php';

            //!!! @@@
            include_once 'ffun.php';

            $datastart = date('Y-m-d', strtotime($_POST['datastart'].' 00:00:00'));
            $dataend = date('Y-m-d', strtotime($_POST['dataend'].' 23:59:59'));

            $rezult_temp = ajaxShowResultCashbox($datastart, $dataend, $_POST['filial'], $_POST['summtype'], $_POST['certificatesShow']);


            $rezult = $rezult_temp['rezult'];
            $rezult_cert = $rezult_temp['rezult_cert'];

            //var_dump($query);
            //var_dump($rezult);
            //var_dump('-----------------------');
            //var_dump($rezult_cert);
            //var_dump('-----------------------');

            if (!empty($rezult) || !empty($rezult_cert)) {

                $office_j = SelDataFromDB('spr_filials', '', '');
                //var_dump($office_j);
                //!!!
                $office_j_arr = array();

                foreach ($office_j as $office_item) {
                    $office_j_arr[$office_item['id']] = $office_item;
                }
                //var_dump($office_j_arr);

                $result = '';

                $orderAll_str = '';
                $orderClose_str = '';
                $filialHeader_str = '';
                $filialData_str = '';
                $filialDataAll_str = '';
                $filialResult = array();
                //$filialResult_cert = array();

                $Summ = 0;

                //Сначала соберем полученные данные в массив по филиалам
                foreach ($rezult as $order_item) {
                    //var_dump($order_item['office_id']);
                    //var_dump(isset($filialResult[$order_item['office_id']]));
                    if (isset($filialResult[$order_item['office_id']])) {
                        array_push($filialResult[$order_item['office_id']]['data'], $order_item);
                    } else {
                        $filialResult[$order_item['office_id']]['data'] = array();
                        $filialResult[$order_item['office_id']]['data_cert'] = array();
                        $filialResult[$order_item['office_id']]['office_name'] = $office_j_arr[$order_item['office_id']]['name'];
                        array_push($filialResult[$order_item['office_id']]['data'], $order_item);
                    }
                    //var_dump($filialResult[$order_item['office_id']]);
                }

                //Сертификаты
                foreach ($rezult_cert as $order_item) {
                    //var_dump($order_item['office_id']);
                    //var_dump(isset($filialResult[$order_item['office_id']]));
                    if (isset($filialResult[$order_item['office_id']])) {
                        array_push($filialResult[$order_item['office_id']]['data_cert'], $order_item);
                    } else {
                        $filialResult[$order_item['office_id']]['data'] = array();
                        $filialResult[$order_item['office_id']]['data_cert'] = array();
                        $filialResult[$order_item['office_id']]['office_name'] = $office_j_arr[$order_item['office_id']]['name'];
                        array_push($filialResult[$order_item['office_id']]['data_cert'], $order_item);
                    }
                    //var_dump($filialResult[$order_item['office_id']]);
                }

                //var_dump($filialResult[17]);
                //var_dump($filialResult_cert);

                foreach ($filialResult as $filialID => $filialData) {

                    $filialData_str = '';
                    $Summ = 0;

                    $filialHeader_str .= '<li><a href="#tabs-' . $filialID . '">' . $filialData['office_name'] . '</a></li>';

                    $filialData_str .= '<div id="tabs-' . $filialID . '">';

                    if (!empty($filialData['data'])) {
                        foreach ($filialData['data'] as $order_item) {

                            $order_type_mark = '';

                            if ($order_item['summ_type'] == 1) {
                                $order_type_mark = '<i class="fa fa-money" aria-hidden="true" title="Нал" style="font-size: 15px; color: darkgreen;"></i>';
                            }

                            if ($order_item['summ_type'] == 2) {
                                $order_type_mark = '<i class="fa fa-credit-card" aria-hidden="true" title="Безнал" style="font-size: 15px; color: dodgerblue;"></i>';
                            }
                            $orderTemp_str = '';

                            $orderTemp_str .= '
                                                <li class="cellsBlock" style="width: auto;">';
                            $orderTemp_str .= '
                                                    <a href="order.php?id=' . $order_item['id'] . '" class="cellOrder ahref" style="position: relative;">
                                                        <b>Ордер #' . $order_item['id'] . '</b>  от ' . date('d.m.y', strtotime($order_item['date_in'])) . '<br>
                                                        ' . $office_j_arr[$order_item['office_id']]['name'] . '<br>
                                                        <span style="font-size:80%;  color: #555;">';

                            if (($order_item['create_time'] != 0) || ($order_item['create_person'] != 0)) {
                                $orderTemp_str .= '
                                                                Добавлен: ' . date('d.m.y H:i', strtotime($order_item['create_time'])) . '<br>
                                                                <!--Автор: ' . WriteSearchUser('spr_workers', $order_item['create_person'], 'user', true) . '<br>-->';
                            } else {
                                $orderTemp_str .= 'Добавлен: не указано<br>';
                            }
                            if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)) {
                                $orderTemp_str .= '
                                                                Последний раз редактировался: ' . date('d.m.y H:i', strtotime($order_item['last_edit_time'])) . '<br>
                                                                <!--Кем: ' . WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true) . '-->';
                            }
                            $orderTemp_str .= '
                                                        </span>
                                                        <span style="position: absolute; top: 2px; right: 3px;">' . $order_type_mark . '</span>
                                                    </a>
                                                    <div class="cellName">
                                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                            Сумма:<br>
                                                            <span class="calculateOrder" style="font-size: 13px">' . $order_item['summ'] . '</span> руб.
                                                        </div>';
                            /*if ($order_item['summins'] != 0){
                                echo '
                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                            Страховка:<br>
                                            <span class="calculateInsInvoice" style="font-size: 13px">'.$order_item['summins'].'</span> руб.
                                        </div>';
                            }*/
                            $orderTemp_str .= '
                                                    </div>';
                            $orderTemp_str .= '
                                                </li>';

                            if ($order_item['status'] != 9) {
                                $orderAll_str .= $orderTemp_str;

                                $Summ += $order_item['summ'];

                            } else {
                                $orderClose_str .= $orderTemp_str;
                            }
                        }
                    }

                    if (!empty($filialData['data_cert'])) {
                        foreach ($filialData['data_cert'] as $order_item) {

                            $order_type_mark = '';

                            //if ($order_item['summ_type'] == 1){
                            $order_type_mark = '<i class="fa fa-certificate" aria-hidden="true" title="Сертификат" style="font-size: 15px; color: darkred;"></i>';
                            if ($order_item['summ_type'] == 1) {
                                $order_type_mark = '<i class="fa fa-money" aria-hidden="true" title="Нал" style="font-size: 15px; color: darkgreen;"></i> ' . $order_type_mark;
                            }
                            if ($order_item['summ_type'] == 2) {
                                $order_type_mark = '<i class="fa fa-credit-card" aria-hidden="true" title="Безнал" style="font-size: 15px; color: dodgerblue;"></i> ' . $order_type_mark;
                            }
                            //}

                            /*if ($order_item['summ_type'] == 2){
                                $order_type_mark = '<i class="fa fa-credit-card" aria-hidden="true" title="Безнал"></i>';
                            }*/
                            $orderTemp_str = '';

                            $orderTemp_str .= '
                                                <li class="cellsBlock" style="width: auto;">';
                            $orderTemp_str .= '
                                                    <a href="certificate.php?id=' . $order_item['id'] . '" class="cellOrder ahref" style="position: relative;">
                                                        <b>Сертификат #' . $order_item['num'] . '</b>  от ' . date('d.m.y', strtotime($order_item['cell_time'])) . '<br>
                                                        ' . $office_j_arr[$order_item['office_id']]['name'] . '<br>
                                                        <span style="font-size:80%;  color: #555;">';

                            if (($order_item['create_time'] != 0) || ($order_item['create_person'] != 0)) {
                                $orderTemp_str .= '
                                                                Добавлен: ' . date('d.m.y H:i', strtotime($order_item['create_time'])) . '<br>
                                                                <!--Автор: ' . WriteSearchUser('spr_workers', $order_item['create_person'], 'user', true) . '<br>-->';
                            } else {
                                $orderTemp_str .= 'Добавлен: не указано<br>';
                            }
                            if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)) {
                                $orderTemp_str .= '
                                                                Последний раз редактировался: ' . date('d.m.y H:i', strtotime($order_item['last_edit_time'])) . '<br>
                                                                <!--Кем: ' . WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true) . '-->';
                            }
                            $orderTemp_str .= '
                                                        </span>
                                                        <span style="position: absolute; top: 2px; right: 3px;">' . $order_type_mark . '</span>
                                                    </a>
                                                    <div class="cellName">
                                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                            Сумма:<br>
                                                            <span class="calculateOrder" style="font-size: 13px">' . $order_item['cell_price'] . '</span> руб.
                                                        </div>';
                            /*if ($order_item['summins'] != 0){
                                echo '
                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                            Страховка:<br>
                                            <span class="calculateInsInvoice" style="font-size: 13px">'.$order_item['summins'].'</span> руб.
                                        </div>';
                            }*/
                            $orderTemp_str .= '
                                                    </div>';
                            $orderTemp_str .= '
                                                </li>';

                            if ($order_item['status'] != 9) {
                                $orderAll_str .= $orderTemp_str;

                                $Summ += $order_item['cell_price'];

                            } else {
                                $orderClose_str .= $orderTemp_str;
                            }
                        }
                    }

                    if (strlen($orderAll_str) > 1) {
                        $filialData_str .= '
                            <li class="cellsBlock" style="margin-bottom: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
                                <div>
                                    Всего<br>
                                    <!--Наличные:  руб.<br>
                                    Безналичные:  руб.<br>-->
                                    Общая сумма: ' . $Summ . ' руб.<br>
                                </div>
                                <div style="margin-top: 10px;">';
                        /*if (isset($_POST['filial'])) {
                            if (($_POST['filial'] != 99) && ($_POST['filial'] != 0)) {
                                $filialData_str .= '
                                     <a href="fl_createDailyReport.php" class="b">Ежедневный отчёт</a>';
                            }else{
                                $filialData_str .= '<span style="color: red"><i>Для формирования ежедневного отчёта должен быть выбран филиал</i></span>';
                            }
                        }else{
                            $filialData_str .= '<span style="color: red"><i>Для формирования ежедневного отчёта должен быть выбран филиал</i></span>';
                        }*/

                        //var_dump($datastart);
                        $datastart_array = explode('-', $datastart);
                        //var_dump($datastart_array);
                        $d = $datastart_array[2];
                        $m = $datastart_array[1];
                        $y = $datastart_array[0];


                        $filialData_str .= '
                                     <a href="fl_createDailyReport.php?filial_id=' . $order_item['office_id'] . '&d=' . $d . '&m=' . $m . '&y=' . $y . '" class="b">Ежедневный отчёт</a>';

                        $filialData_str .= '
                                </div>
                            </li>';

                        $filialData_str .= $orderAll_str;
                    } else {
                        $filialData_str .= '
                                     <a href="fl_createDailyReport.php?filial_id=' . $order_item['office_id'] . '&d=' . $d . '&m=' . $m . '&y=' . $y . '" class="b">Ежедневный отчёт</a>';
                        $filialData_str .= '<span style="color: red;">По запрошенным условиям ничего не найдено.</span>';
                    }

                    //Удалённые
                    if ((strlen($orderClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                        $filialData_str .= '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                        $filialData_str .= '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы ордеры</li>';
                        $filialData_str .= $orderClose_str;
                        $filialData_str .= '</div>';
                    }

                    $filialData_str .= '</div>';

                    $filialDataAll_str .= $filialData_str;

                    $orderAll_str = '';
                    $orderClose_str = '';

                }

                //начинаем формировать ответ
                $result .= '<div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">';

                //Заголовки вкладок
                $result .= '<ul>';
                $result .= $filialHeader_str;
                $result .= '</ul>';

                //Данные во вкладках
                $result .= $filialDataAll_str;

                $result .= '</div>';


                echo $result;

            } else {
                echo '
                     <span style="color: red;">По запрошенным условиям ничего не найдено.</span><br><br>
                     <a href="fl_createDailyReport.php" class="b">Ежедневный отчёт</a>';
            }
        }
	}
?>