<?php

//stat_installments2.php
//Статистика по пациентам с открытыми рассрочками (новое)

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($_SESSION);
        //if (($_SESSION['permissions'] == 3) || ($_SESSION['id'] == 364) || $god_mode){
        if (($finances['see_all'] == 1) || $god_mode){
			//include_once 'DBWork.php';

            /*!!!Тест PDO*/
            include_once('DBWorkPDO.php');

            require 'variables.php';

			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
            include_once 'ffun.php';

            $clients_w_installment = array();

            //$filials_j = getAllFilials(false, true, true);

			if ($_POST){
			}else{
				echo '
					<header style="margin-bottom: 5px;">
						<h1>Список пациентов с открытыми рассрочками</h1>
					</header>';

                $db = new DB();

                $args = [
                    'installment' => 1
                ];

                //Соберём все платежи по нарядам, которые есть в рассрочках
//                $query = "
//                    SELECT j_i.*, j_p.id AS payment_id, j_p.summ AS payment_summ, j_p.date_in AS payment_date, s_c.full_name
//                    FROM `journal_installments` j_i
//                    RIGHT JOIN `journal_payment` j_p
//                    ON j_i.invoice_id = j_p.invoice_id
//                    LEFT JOIN `spr_clients` s_c
//                    ON s_c.id = j_p.client_id
//                    WHERE j_i.status = 1";


                $query = "
                    SELECT j_inst.*, j_p.id AS payment_id, j_p.summ AS payment_summ, j_p.date_in AS payment_date, s_c.full_name, j_i.summ AS invoice_summ, j_i.paid AS invoice_paid 
                    FROM `journal_installments` j_inst
                    LEFT JOIN `journal_payment` j_p
                    ON j_inst.invoice_id = j_p.invoice_id
                    LEFT JOIN `spr_clients` s_c
                    ON s_c.id = j_inst.client_id
                    LEFT JOIN `journal_invoice` j_i
                    ON j_i.id = j_inst.invoice_id
                    WHERE j_inst.status = '1' AND j_inst.invoice_id <> '0'";

                $clients_w_installment = $db::getRows($query, $args);
//                var_dump($clients_w_installment);

                //Массив, где будем хранить нужные данные
                $installment_j = array();

                //Выводим результат
                if (!empty($clients_w_installment)){
                    foreach ($clients_w_installment as $item){
                        if (!isset($installment_j[$item['client_id']])){
                            $installment_j[$item['client_id']] = array();
                            $installment_j[$item['client_id']]['data'] = array();
                            $installment_j[$item['client_id']]['name'] = $item['full_name'];
                        }
                        if (!isset($installment_j[$item['client_id']]['data'][$item['invoice_id']])){
                            $installment_j[$item['client_id']]['data'][$item['invoice_id']] = array();
                        }
                        array_push($installment_j[$item['client_id']]['data'][$item['invoice_id']], $item);
                    }
//                    var_dump($installment_j);
//                    var_dump($installment_j[39762]['data'][113055]);

                    echo '
					    <div id="data">';
                    echo '
                            <ul class="live_filter" id="livefilter-list" style="width: 697px; margin-left: 5px; /*box-shadow: 2px 2px 2px rgba(4,21,101,0.5);*//*box-shadow: rgba(146, 146, 146, 0.82) 0px 4px 10px;*/">';

                    echo '
                                <li class="cellsBlock" style="font-weight: bold;">	
                                    <div class="cellFullName" style="width: 463px; min-width: 463px; text-align: center; border-bottom: 0; border-right: 0;">
                                        Полное имя
                                    </div>
                                    <!--<div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">
                                        Наряд
                                    </div>-->
                                    <div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px; border-bottom: 0; border-right: 0;">
                                        Общий долг
                                    </div>
                                    <div class="cellCosmAct" style="text-align: center; width: 103px; min-width: 103px; max-width: 103px; border-bottom: 0;">
                                        Доступно на счету
                                    </div>
                                    <div class="cellText" style="text-align: center; border: 0;"></div>
							    </li>
                            </ul>';

                    foreach ($installment_j as $client_id => $client_data){
                        //var_dump($client_data);

                        //Баланс контрагента
                        $client_balance = json_decode(calculateBalance ($client_id), true);
                        //Долг контрагента
                        $client_debt = json_decode(calculateDebt ($client_id), true);

                        //доступный остаток
                        $dostOstatok = $client_balance['summ'] - $client_balance['debited'] - $client_balance['withdraw'] + $client_balance['refund'];

                        echo '
                                <ul class="live_filter cellsBlockHover" id="livefilter-list" style="width: 694px; margin-left: 6px; border: 1px solid #BFBCB5; /*box-shadow: 2px 2px 2px rgba(84, 90, 121, 0.5);*/box-shadow: rgba(146, 146, 146, 0.82) 0px 4px 10px;">
                                    <li class="cellsBlock" style="font-weight:bold;">	
                                        <div class="cellFullName" style="width: 462px; min-width: 462px; text-align: left; border: 0; font-size: 130%; font-style: italic;">';

                        echo '<!--<a href="client.php?id='.$client_id.'" class="ahref" target="_blank" rel="nofollow noopener">-->'.$client_data['name'].'<!--</a>-->';

                        echo '
                                            <div style="/*float: right;*/ font-weight: normal; font-size: 70%;">
                                                <a href="client.php?id='.$client_id.'" class="ahref b4" id="" target="_blank" rel="nofollow noopener">
                                                    <i class="fa fa-user" aria-hidden="true" style=" font-size: 120%;" title="Карточка пациента"></i> Карточка пациента
                                                </a>
                                                        
                                                <a href="finance_account.php?client_id='.$client_id.'" class="ahref b4" style="text-align: center;" target="_blank" rel="nofollow noopener">
                                                    <i class="fa fa-chevron-right" style="color: grey;" aria-hidden="true"></i> Управление счётом
                                                </a>
                                                        
                                                <a href="pay_blank_pdf_qr.php?client_id='.$client_id.'" class="ahref b4" style="text-align: center;" target="_blank" rel="nofollow noopener" title="Выписать счет на оплату">
                                                    <i class="fa fa-file-text" style="font-size: 140%; color: rgb(74, 148, 70); /*float: right;*/" aria-hidden="true"></i> Выписать счёт на оплату
                                                </a>
                                            </div>';

                        echo '
                                        </div>';
                        echo '
                                        <div class="cellCosmAct calculateInvoice" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px; font-size: 110%; border-top: 0; border-bottom: 0; border-right: 0;">
                                            <!--<span style="font-style: italic; font-weight: normal; font-size: 80%; color: #8C8C8C;">общий долг</span><br>-->
                                            '.$client_debt['summ'].'
                                        </div>
                                        <div class="cellCosmAct calculateOrder" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px; font-size: 112%; border-top: 0; border-bottom: 0; border-right: 0;">
                                            '.$dostOstatok.'
                                        </div>
                                        <div class="cellText" style="text-align: center; border: 0;">
                                        
                                        </div>
                                    </li>';

                        foreach ($client_data['data'] as $invoice_id => $payment_data) {
//                            var_dump($payment_data);

                            echo '
                                <div id="cl_data_main_'.$payment_data[0]['client_id'].'">
                                    <li class="cellsBlock" style="font-weight:bold; /*width: 50vw;*/ ">	
                                        <div class="cellFullName" style="text-align: left">';

                            //echo $client_data['name'];
                            echo '
                                            <div style="float: left;">
                                                Наряд <a href="invoice.php?id='.$invoice_id.'" class="ahref" target="_blank" rel="nofollow noopener">#'.$invoice_id.'</a> 
                                                <span style="font-weight: normal;">Рассрочка с '.date('d.m.Y' ,strtotime($payment_data[0]['date_in'])).'</span>';


                            echo ' Осталось внести: <span style="font-weight: normal;">'. ($payment_data[0]['invoice_summ'] - $payment_data[0]['invoice_paid']).' руб.</span>';
                            echo '
                                            </div>';

                            if ($payment_data[0]['invoice_summ'] - $payment_data[0]['invoice_paid'] <= 0) {
                                echo '
                                            <div class="" style="font-size: 80%; text-align: left; float: right; font-weight: normal;">
                                                <span class="info ahref  b4"  style="display: inline; color: red; margin-left: 0px; font-size: 100%; padding: 2px 5px; cursor: pointer;" onclick="changeInstallmentStatus2(' . $payment_data[0]['id'] . ', ' . $payment_data[0]['client_id'] . ', ' . $invoice_id . ', ' . $payment_data[0]['status'] . ', false);">
                                                    <i class="fa fa-database" aria-hidden="true" style=" font-size: 120%;" title="Есть незакрытая рассрочка"></i> Закрыть рассрочку
                                                </span>
                                            </div>';
                            }

                            echo '
                                        </div>';
//                            echo '
//                                    <div class="cellText" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">';
//
//                            foreach ($payment_data as $payment){
//
//                            }
//
//                            echo '
//                                    </div>';

                            echo '       
                                    </li>';

//                            echo '
//                            <li id="user_options_'.$payment_data[0]['client_id'].'" class="user_options" style="width: 50vw; /*display: none; *//*border: 1px solid rgb(140, 140, 140);*/ /*box-shadow: rgba(146, 146, 146, 0.82) 0px 4px 10px;*/ padding: 5px 10px 1px; /*background: #f7ffe8; */font-size: 80%;">
//
//                                <!--<div class="" style="text-align: left;">
//                                    Рассрочка с '.date('d.m.Y' ,strtotime($payment_data[0]['date_in'])).'
//                                </div>
//
//                                <div class="" style="width: 90%; margin: 10px; padding-bottom: 10px; text-align: left;">
//                                    <span class="ahref button_tiny" style="font-size:80%;  color: #555;" onclick="getPayments4Installments('.$payment_data[0]['id'].', \''.$payment_data[0]['date_in'].'\');">Показать платежи</span>
//                                    <div id="client_orders_by_period_'.$payment_data[0]['id'].'" style="width: 90%; margin-top: 10px; overflow-x: scroll; overflow-y: hidden; ">
//
//                                    </div>
//                                </div>-->
//                            </li>';

                            echo '
                                <li class="cellsBlock" style="font-weight:bold; /*width: 50vw;*/ ">	
                                    
                                    <div class="cellFullName" style="border: 0; text-align: left; max-width: 250px;">';

//                            echo '
//                                <div class="" style="text-align: left; font-weight: normal;">
//                                    Рассрочка с '.date('d.m.Y' ,strtotime($payment_data[0]['date_in'])).'
//                                </div>';

                            echo '
                                <div id="" style="width: 90%; margin-top: 10px; overflow-x: scroll; overflow-y: hidden; ">';

                            //Сформируем массив с суммами по месяцам
                            $payments_month = array();

                            foreach ($payment_data as $payment){
                                if (!isset($payments_month[date('Y', strtotime($payment['payment_date']))])){
                                    $payments_month[date('Y', strtotime($payment['payment_date']))] = array();
                                }
                                if (!isset($payments_month[date('Y', strtotime($payment['payment_date']))][date('m', strtotime($payment['payment_date']))])){
                                    $payments_month[date('Y', strtotime($payment['payment_date']))][date('m', strtotime($payment['payment_date']))] = 0;
                                }
                                $payments_month[date('Y', strtotime($payment['payment_date']))][date('m', strtotime($payment['payment_date']))] += $payment['payment_summ'];
                            }
                            //var_dump($payments_month);
                            //var_dump($payment_data);

                            if ($payment_data[0]['date_in'] != date('Y-m-d', time())) {

                                //вернуть все даты между двумя датами в массиве
                                $period = new DatePeriod(
                                    new DateTime($payment_data[0]['date_in']),
                                    new DateInterval('P1M'),
                                    new DateTime(date('Y-m-d', time()))
                                );

                            }else{
                                $period[0] = new DateTime(date('Y-m-d', time()));
                            }
//                            $period = array_reverse($period, true);
//                            var_dump($period);
//                            var_dump(count($period));

                            foreach ($period as $value) {
//                                var_dump($value->format( "Y" ));
//                                var_dump(date('Y', time()));


                                $todayBorder = '';
                                if (($value->format( "Y" ) == date('Y', time())) && ($value->format( "m" ) == date('m', time()))) {
                                    $todayBorder = 'outline: 1px solid #dc06dc; border: 3px solid yellow;';
                                }

                                $summ = 0;

                                if(isset($payments_month[$value->format( "Y" )])){
                                    if(isset($payments_month[$value->format( "Y" )][$value->format( "m" )])){
                                        $summ = $payments_month[$value->format( "Y" )][$value->format( "m" )];
                                    }
                                }
                                if ($summ > 0){
                                    echo ' 
                                        <div style="display: table-cell; width: 83px; min-width: 83px; border: 1px solid #BFBCB5; background: lawngreen; padding: 10px; '.$todayBorder.'">';
                                }else{
                                    echo '
                                            <div style="display: table-cell; width: 83px; min-width: 83px; border: 1px solid #BFBCB5; background: #ff7777; padding: 10px; '.$todayBorder.'">';
                                }

                                echo '
                                                <div style="margin-bottom: 5px; font-size: 80%;">'.$monthsName[$value->format( "m" )].' '.$value->format( "Y" ).'</div>
                                                <div>'.$summ.'</div>
                                            </div>';
                            }





//                            foreach ($payment_data as $payment){
//                                echo '
//                                            <div style="display: table-cell; width: 83px; min-width: 83px; border: 1px solid #BFBCB5; background: #ff7777; padding: 10px;">
//                                                <div style="margin-bottom: 5px;">'.$monthsName[date('m', strtotime($payment['payment_date']))].'</div>
//                                                <div>'.$payment['payment_summ'].'</div>
//                                            </div>';
//                         }


                            echo '
                                            </div>';
                            echo '
                                        </div>';
//                            echo '
//                                    <div class="cellText" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">';
//
//                            foreach ($payment_data as $payment){
//
//                            }
//
//                            echo '
//                                    </div>';

                            echo '       
							        </li>
							        </div>';

                        }
                        echo '
                                </ul>';
                    }

                }

                /*echo '
                            </ul>';*/
                echo '
                            <div id="doc_title">Открытые рассрочки - Асмедика</div>
                        </div>';

                echo '
                <script>
                    $(document).ready(function() {

                    });';

			    echo '
			    </script>';
						

			}
			//mysql_close();
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>