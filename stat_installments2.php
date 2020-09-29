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
                $query = "
                    SELECT j_i.*, j_p.id AS payment_id, j_p.summ AS payment_summ, j_p.date_in AS payment_date, s_c.full_name
                    FROM `journal_payment` j_p
                    INNER JOIN `journal_installments` j_i
                    ON j_i.invoice_id = j_p.invoice_id
                    RIGHT JOIN `spr_clients` s_c
                    ON s_c.id = j_p.client_id
                    WHERE j_i.status = 1";


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
                    var_dump($installment_j[18576]['data'][77587][0]);

                    echo '
					    <div id="data">';
                    echo '
                            <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';

                    echo '
                                <li class="cellsBlock" style="font-weight:bold;">	
                                    <div class="cellFullName" style="text-align: center">
                                        Полное имя
                                    </div>
                                    <!--<div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">
                                        Наряд
                                    </div>-->
                                    <div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">
                                        Долг
                                    </div>
                                    <div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">
                                        Доступно на счету
                                    </div>
                                    <div class="cellText" style="text-align: center; border: 0;"></div>
							    </li>';

                    foreach ($installment_j as $client_id => $client_data){
                        //var_dump($client_data);

                        echo '
                                <li class="cellsBlock" style="font-weight:bold;">	
                                    <div class="cellFullName" style="text-align: left">';

                        echo $client_data['name'];

                        echo '
                                    </div>';
                        echo '
                                    <div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">
                                    </div>
                                    <div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;"></div>
                                    <div class="cellText" style="text-align: center; border: 0;"></div>
							    </li>';

                        foreach ($client_data['data'] as $invoice_id => $payment_data) {
                            echo '
                                <li class="cellsBlock" style="font-weight:bold; width: 50vw; ">	
                                    <div class="cellFullName" style="text-align: left">';

                            //echo $client_data['name'];
                            echo 'Наряд #'.$invoice_id;
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


                            echo '
                                <li class="cellsBlock" style="font-weight:bold; width: 50vw; ">	
                                    
                                    <div class="cellFullName" style="text-align: left; max-width: 250px;">';

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
//                                var_dump($value->format( "Y-m" ));
//                                var_dump($value);

                                $summ = 0;

                                if(isset($payments_month[$value->format( "Y" )])){
                                    if(isset($payments_month[$value->format( "Y" )][$value->format( "m" )])){
                                        $summ = $payments_month[$value->format( "Y" )][$value->format( "m" )];
                                    }
                                }
                                if ($summ > 0){
                                    echo ' 
                                    <div style="display: table-cell; width: 83px; min-width: 83px; border: 1px solid #BFBCB5; background: lawngreen; padding: 10px;">';
                                }else{
                                    echo '
                                    <div style="display: table-cell; width: 83px; min-width: 83px; border: 1px solid #BFBCB5; background: #ff7777; padding: 10px;">';
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
							    </li>';

                        }
                    }

                }

                echo '
                            </ul>';
                echo '
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