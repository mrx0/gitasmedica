<?php

//stat_installments.php
//Статистика по пациентам с открытыми рассрочками

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($_SESSION);
        //if (($_SESSION['permissions'] == 3) || ($_SESSION['id'] == 364) || $god_mode){
        if (($finances['see_all'] == 1) || $god_mode){
			//include_once 'DBWork.php';

            /*!!!Тест PDO*/
            include_once('DBWorkPDO.php');

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

                //$msql_cnnct = ConnectToDB ();
                $db = new DB();

                $args = [
                    'installment' => 1
                ];

                //Соберём всех пациентов с открытыми рассрочками
                $query = "
                    SELECT s_c.id, s_c.full_name, s_c.installment, j_i.id AS installment_id, j_i.date_in AS installment_date
                    FROM `spr_clients` s_c
                    LEFT JOIN `journal_installments` j_i
                    ON j_i.client_id = s_c.id
                    WHERE s_c.installment = :installment 
                    ORDER BY s_c.full_name ASC";

                //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

//                $number = mysqli_num_rows($res);
//
//                if ($number != 0){
//                    while ($arr = mysqli_fetch_assoc($res)){
//                        array_push($clients_w_installment, $arr);
//                    }
//                }else{
//                }

                $clients_w_installment = $db::getRows($query, $args);
//                var_dump($clients_w_installment);

                //Выводим результат
                if (!empty($clients_w_installment)){

                    echo '
					    <div id="data">';

                    //echo '<div id="allPayments_shbtn" style="color: #000005; cursor: pointer; display: inline;" onclick="toggleSomething (\'#allCalcsIsHere\');">скрыть всё</div>';

                    echo '
                            <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';

                    echo '
							<li class="cellsBlock" style="font-weight:bold;">	
							    <div class="cellCosmAct" style="text-align: center;">-</div>
								<div class="cellFullName" style="text-align: center">
                                    Полное имя';
                    //echo $block_fast_filter;
                    echo '
                                </div>';
                    echo '
								<div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">Долг</div>
								<div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">Доступно на счету</div>
								<!--<div class="cellCosmAct" style="text-align: center;">-</div>-->
								<!--<div class="cellCosmAct" style="text-align: center;">Упр. сч.</div>-->
								<div class="cellText" style="text-align: center; border: 0;"></div>
							</li>';

                    //Общая сумма долгов
                    $debtAllSumm = 0;
                    //Общая сумма доступно
                    $dostOstatokAllSumm = 0;

                    foreach ($clients_w_installment as $cl_data) {
                        //var_dump($cl_data);

                        //Долги/авансы
                        //
                        //!!! @@@
                        //Баланс контрагента
                        include_once 'ffun.php';
                        $client_balance = json_decode(calculateBalance ($cl_data['id']), true);
                        //Долг контрагента
                        $client_debt = json_decode(calculateDebt ($cl_data['id']), true);

                        //доступный остаток
                        $dostOstatok = $client_balance['summ'] - $client_balance['debited'] - $client_balance['withdraw'] + $client_balance['refund'];

						echo '
                            <li id="cl_data_main_'.$cl_data['id'].'" class="cellsBlock cellsBlockHover cl_data" cl_id="'.$cl_data['id'].'" cl_installment_date="'.$cl_data['installment_date'].'" style="">
                                <div class="cellCosmAct" style="text-align: center;">
                                    <span class="info"  style="display: inline; color: darkblue; margin-left: 0px; font-size: 100%; padding: 2px 5px; cursor: pointer;" onclick="toggleSomething(\'#user_options_'.$cl_data['id'].'\'); getPayments4Installments('.$cl_data['id'].', \''.$cl_data['installment_date'].'\');">
                                        <i class="fa fa-cog" aria-hidden="true" style=" font-size: 140%;" title="Опции"></i>
                                    </span>
                                </div>
                            
								<div class="cellFullName 4filter" id="4filter">
								    '.$cl_data['full_name'].' <div id="current_month_payment_'.$cl_data['id'].'" style="display: none; color: red;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> Не было платежей в этом месяце.</div>
                                </div>';

						echo '
								<div class="cellCosmAct" style="text-align: right; width: 100px; min-width: 100px; max-width: 100px;">
								    <span class="calculateInvoice" style="">'.$client_debt['summ'].'</span>
                                </div>
								<div class="cellCosmAct" style="text-align: right; width: 100px; min-width: 100px; max-width: 100px;">
								    <span class="calculateOrder" style="font-size: 13px; color: grey;">'.$dostOstatok.'</span>
                                </div>
                                <!--<a href="pay_blank_pdf_qr.php?client_id='.$cl_data['id'].'" class="ahref cellCosmAct" style="text-align: center;" target="_blank" rel="nofollow noopener" title="Выписать счет на оплату">
                                    <i class="fa fa-file-text" style="font-size: 140%; color: rgb(74, 148, 70); /*float: right;*/" aria-hidden="true"></i>
                                </a>-->
                                <!--<a href="finance_account.php?client_id='.$cl_data['id'].'" class="ahref cellCosmAct" style="text-align: center;" target="_blank" rel="nofollow noopener">
                                    <i class="fa fa-chevron-right" style="color: grey; float: right;" aria-hidden="true"></i>
                                </a>-->';
                        echo '
                                <div class="cellText" style="text-align: center; border: 0;"></div>
                            </li>
                            
                            <li id="user_options_'.$cl_data['id'].'" class="user_options" style="width: 50vw; display: none; border: 1px solid rgb(140, 140, 140); box-shadow: rgba(146, 146, 146, 0.82) 0px 4px 10px; padding: 5px 10px 15px; background: #f7ffe8; font-size: 80%;">
                                
                                <div class="" style="text-align: left;">
                                    <a href="client.php?id='.$cl_data['id'].'" class="ahref b4" id="" target="_blank" rel="nofollow noopener">
                                        <i class="fa fa-user" aria-hidden="true" style=" font-size: 120%;" title="Карточка пациента"></i> Карточка пациента
                                    </a>
                                    
                                    <a href="finance_account.php?client_id='.$cl_data['id'].'" class="ahref b4" style="text-align: center;" target="_blank" rel="nofollow noopener">
                                        <i class="fa fa-chevron-right" style="color: grey;" aria-hidden="true"></i> Управление счётом 
                                    </a>
                                    
                                    <a href="pay_blank_pdf_qr.php?client_id='.$cl_data['id'].'" class="ahref b4" style="text-align: center;" target="_blank" rel="nofollow noopener" title="Выписать счет на оплату">
                                        <i class="fa fa-file-text" style="font-size: 140%; color: rgb(74, 148, 70); /*float: right;*/" aria-hidden="true"></i> Выписать счёт на оплату
                                    </a>
                                    
                                    <span class="info ahref  b4"  style="display: inline; color: red; margin-left: 0px; font-size: 100%; padding: 2px 5px; cursor: pointer;" onclick="changeInstallmentStatus('.$cl_data['id'].', '.$cl_data['installment'].', false);">
                                        <i class="fa fa-database" aria-hidden="true" style=" font-size: 120%;" title="Есть незакрытая рассрочка"></i> Закрыть рассрочку
                                    </span>
                                    
                                </div>
                                
                                <div class="" style="text-align: left;">
                                    Рассрочка с '.date('d.m.Y' ,strtotime($cl_data['installment_date'])).'
                                </div>
                                
                                <div class="" style="width: 90%; margin: 10px; padding-bottom: 10px; text-align: left;">
                                    <span class="ahref button_tiny" style="font-size:80%;  color: #555;" onclick="getPayments4Installments('.$cl_data['id'].', \''.$cl_data['installment_date'].'\');">Показать платежи</span>
                                    <div id="client_orders_by_period_'.$cl_data['id'].'" style="width: 90%; margin-top: 10px; overflow-x: scroll; overflow-y: hidden; ">
                                    
                                    </div>
                                </div>
                            </li>';

                        $debtAllSumm += $client_debt['summ'];
                        $dostOstatokAllSumm += $dostOstatok;

				    }


                    echo '
							<li class="cellsBlock" style="font-weight:bold;">	
							    <div class="cellCosmAct" style="text-align: center;">-</div>
								<div class="cellFullName" style="">
                                    Общая сумма
                                </div>
								<div class="cellCosmAct" style="text-align: right; width: 100px; min-width: 100px; max-width: 100px;">
								    <span class="calculateInvoice" style="font-size: 13px">'.number_format($debtAllSumm, 0, '.', ' ').'</span>
                                </div>
								<div class="cellCosmAct" style="text-align: right; width: 100px; min-width: 100px; max-width: 100px;">
								    <span class="calculateOrder" style="font-size: 13px; color: grey;">'.number_format($dostOstatokAllSumm, 0, '.', ' ').'</span>
                                </div>
								<div class="cellCosmAct" style="text-align: left; width: 100px; min-width: 100px; max-width: 100px;">
								    Итого:<br><span class="calculateOrder" style="font-size: 13px; color: blueviolet;">'.number_format(($debtAllSumm-$dostOstatokAllSumm), 0, '.', ' ').'</span>
                                </div>
                                <div class="cellText" style="text-align: center; border: 0;"></div>
                            </li>';

                    echo '
					        </ul>';
                    echo '
                        </div>';

                }else{
                    echo '<span style="color: red;">Ничего не найдено</span>';
                }

                //CloseDB($msql_cnnct);

                echo '
                <script>  
                    $(document).ready(function() {
                        $(".cl_data").each(function(){
                            //console.log($(this).attr("cl_id"));
                            //console.log($(this).attr("cl_installment_date"));
                            
                            getPayments4Installments($(this).attr("cl_id"), $(this).attr("cl_installment_date"));
                        })
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