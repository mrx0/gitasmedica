<?php

//stat_installments.php
//Статистика по пациентам с открытыми рассрочками

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($_SESSION);
        //if (($_SESSION['permissions'] == 3) || ($_SESSION['id'] == 364) || $god_mode){
        if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
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

                $msql_cnnct = ConnectToDB ();

                //Соберём всех пациентов с открытыми рассрочками
                $query = "SELECT s_c.* FROM `spr_clients` s_c
                            WHERE s_c.installment = '1' ORDER BY `full_name` ASC";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($clients_w_installment, $arr);
                    }
                }else{
                }
                //var_dump($clients_w_installment);

                //Выводим результат
                if (!empty($clients_w_installment)){

                    echo '
					    <div id="data">';

                    echo '
                            <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';

                    echo '
							<li class="cellsBlock" style="font-weight:bold;">	
								<div class="cellFullName" style="text-align: center">
                                    Полное имя';
                    //echo $block_fast_filter;
                    echo '
                                </div>';
                    echo '
								<div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">Долг</div>
								<div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">Доступно на счету</div>
								<div class="cellCosmAct" style="text-align: center;">-</div>
								<div class="cellCosmAct" style="text-align: center;">Упр. сч.</div>
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
                            <li class="cellsBlock cellsBlockHover" style="">
								<a href="client.php?id='.$cl_data['id'].'" class="cellFullName ahref 4filter" id="4filter" target="_blank" rel="nofollow noopener">'.$cl_data['full_name'].'</a>';

						echo '
								<div class="cellCosmAct" style="text-align: right; width: 100px; min-width: 100px; max-width: 100px;">
								    <span class="calculateInvoice" style="">'.$client_debt['summ'].'</span>
                                </div>
								<div class="cellCosmAct" style="text-align: right; width: 100px; min-width: 100px; max-width: 100px;">
								    <span class="calculateOrder" style="font-size: 13px; color: grey;">'.$dostOstatok.'</span>
                                </div>
                                <a href="pay_blank_pdf_qr.php?client_id='.$cl_data['id'].'" class="ahref cellCosmAct" style="text-align: center;" target="_blank" rel="nofollow noopener" title="Выписать счет на оплату">
                                    <i class="fa fa-file-text" style="font-size: 140%; color: rgb(74, 148, 70); /*float: right;*/" aria-hidden="true"></i>
                                </a>
                                <a href="finance_account.php?client_id='.$cl_data['id'].'" class="ahref cellCosmAct" style="text-align: center;" target="_blank" rel="nofollow noopener">
                                    <i class="fa fa-chevron-right" style="color: grey; float: right;" aria-hidden="true"></i>
                                </a>';

                        echo '
                                <div class="cellText" style="text-align: center; border: 0;"></div>
                            </li>';

                        $debtAllSumm += $client_debt['summ'];
                        $dostOstatokAllSumm += $dostOstatok;

				    }


                    echo '
							<li class="cellsBlock" style="font-weight:bold;">	
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

                CloseDB($msql_cnnct);



						

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