<?php

//pay_blank_pdf_qr.php
//Бланк для оплаты пациенту

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if(isset($_GET['client_id'])) {
        if (($finances['see_all'] == 1) || $god_mode) {
            include_once 'DBWork.php';
            include_once 'functions.php';
            include_once 'ffun.php';

            $client_j = array();

            //$filials_j = getAllFilials(false, true, true);

            if ($_POST) {
            } else {
                echo '
					<header style="margin-bottom: 5px;">
						<h1>Создание бланка оплаты</h1>
					</header>';

                $msql_cnnct = ConnectToDB();

                //Соберём всех пациентов с открытыми рассрочками
                $query = "SELECT s_c.* FROM `spr_clients` s_c
                            WHERE s_c.id = '{$_GET['client_id']}' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($client_j, $arr);
                    }
                }
                //var_dump($client_j);

                if (!empty($client_j)){

                    $orgs_j = SelDataFromDB('spr_org', '', '');
                    //var_dump($orgs_j);

                    //Долги/авансы
                    //
                    //!!! @@@
                    //Баланс контрагента
                    $client_balance = json_decode(calculateBalance ($_GET['client_id']), true);
                    //Долг контрагента
                    $client_debt = json_decode(calculateDebt ($_GET['client_id']), true);
//                    var_dump($client_balance);
//                    var_dump($client_debt);

                    //доступный остаток
                    $dostOstatok = $client_balance['summ'] - $client_balance['debited'] - $client_balance['withdraw'] + $client_balance['refund'];

                    echo '
						<div id="data">';

                    echo '
                            <div style="font-size: 85%; color: #7D7D7D;">
                                Данные из карточки пациента
                            </div>
                            <div style="font-size: 85%; margin-bottom: 10px;">
                                <a href="client.php?id='.$client_j[0]['id'].'" class="ahref">'.$client_j[0]['full_name'].'</a>
                            </div>';

                    echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    ФИО
                                    <input id="thisFIO" name="thisFIO" value="0" type="radio" style="float: right;" checked>
                                </div>
                                <div class="cellRight">
                                    <input type="text" name="fio" id="fio" style="width: 98%; font-size: 14px;" value="'.$client_j[0]['full_name'].'">
                                </div>
                            </div>';

                    echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">Дата рождения</div>
                                <div class="cellRight">';
                    if ($client_j[0]['birthday2'] == '0000-00-00'){
					    echo 'не указана';
				    }else{
					    echo
						date('d.m.Y', strtotime($client_j[0]['birthday2'])).'<br>
						полных лет <b>'.getyeardiff(strtotime($client_j[0]['birthday2']), 0).'</b>';
				    }
				    echo '
                                </div>
                            </div>
                            </div>';

                    echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">Телефон</div>
                                <div class="cellRight">
                                    <div>
                                        <span style="font-size: 80%; color: #AAA">мобильный</span><br>
                                        '.$client_j[0]['telephone'].'
                                    </div>';
                    if ($client_j[0]['htelephone'] != ''){
                        echo '
                                    <div>
                                        <span style="font-size: 80%; color: #AAA">домашний</span><br>
                                        '.$client_j[0]['htelephone'].'
                                    </div>';
                    }
                    echo '
                                </div>
                            </div>';

                    echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">Email</div>
                                <div class="cellRight">
                                    '.$client_j[0]['email'].'';
                    echo '
                                </div>
                            </div>';

                    echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">Паспорт</div>
                                <div class="cellRight">
                                    <div>
                                        <span style="font-size: 70%; color: #AAA">Серия номер</span><br>
                                        '.$client_j[0]['passport'].'
                                    </div>';
                    if (($client_j[0]['alienpassportser'] != NULL) && ($client_j[0]['alienpassportnom'] != NULL)){
                        echo '
                                    <div>
                                        <span style="font-size: 70%; color: #AAA">Серия номер (иностр.)</span><br>
                                        '.$client_j[0]['alienpassportser'].'
                                        '.$client_j[0]['alienpassportnom'].'
                                    </div>';
                    }
                    echo '
                                    <div>
                                        <span style="font-size: 70%; color: #AAA">Выдан когда</span><br>
                                        '.$client_j[0]['passportvidandata'].'
                                    </div>
                                    <div>
                                        <span style="font-size: 70%; color: #AAA">Кем</span><br>
                                        '.$client_j[0]['passportvidankem'].'
                                    </div>
                                </div>
                            </div>';

                    echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">Адрес</div>
                                <div class="cellRight">
                                    <textarea name="address" id="address" cols="35" rows="2" style="width: 98%; font-size: 14px;">'.$client_j[0]['address'].'</textarea>
                                </div>
                            </div>';

                    if (($client_j[0]['fo'] != '') || ($client_j[0]['io'] != '')){
                        echo '
							<div class="cellsBlock2" style="margin-top: 2px; margin-bottom: 0; display: block;">
								<div class="cellLeft" style="font-weight: bold; width: 500px;">
									Опекун
								</div>
							</div>
							<div class="cellsBlock2">
								<div class="cellLeft">
								    ФИО
								    <input id="thisFIO" name="thisFIO" value="1" type="radio" style="float: right;">
                                </div>
								<div class="cellRight">
									<input type="text" name="fioo" id="fioo" style="width: 98%; font-size: 14px;" value="'.$client_j[0]['fo'].' '.$client_j[0]['io'].' '.$client_j[0]['oo'].'"></div>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Телефон</div>
								<div class="cellRight">
									<div>
										<span style="font-size: 80%; color: #AAA">мобильный</span><br>
										'.$client_j[0]['telephoneo'].'
									</div>';
                        if ($client_j[0]['htelephoneo'] != ''){
                            echo '
									<div>
										<span style="font-size: 80%; color: #AAA">домашний</span><br>
										'.$client_j[0]['htelephoneo'].'
									</div>';
                        }
                        echo '
								</div>
							</div>';
                    }
                    echo '					
                            <div class="cellsBlock2">
                                <div class="cellLeft">Комментарий</div>
                                <div class="cellRight">'.$client_j[0]['comment'].'</div>
                            </div>';

                    echo '
                            <div class="cellsBlock2" style="margin-top: 10px; margin-bottom: 10px;">
                                <div class="cellLeft">
                                    ИНН плательщика
                                </div>
                                <div class="cellRight">
                                    <input type="text" name="payerinn" id="payerinn" style="width: 98%; font-size: 14px;" value="">
                                </div>
                            </div>';

                    //Данные для оплаты
                    echo '
                            <div style="font-size: 85%; color: #7D7D7D; margin-bottom: 10px; margin-top: 10px;">
                                Данные для оплаты
                            </div>';

                    echo '					
                            <div class="cellsBlock2">
                                <div class="cellLeft">Юр. лицо</div>
                                <div class="cellRight">
                                    <select name="SelectOrg" id="SelectOrg">
                                        <option value="0" selected>Выберите из списка</option>';

                    if ($orgs_j != 0){
                        foreach ($orgs_j as $org_data) {
                            echo "<option value='" . $org_data['id'] . "'>" . $org_data['name'] . "</option>";
                        }
                    }
				    echo '
                                    </select>

								</div>
                            </div>';

                    echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">ИНН</div>
                                <div class="cellRight">
                                    <div id="inn"></div>
                                </div>
                            </div>
                            <div class="cellsBlock2">
                                <div class="cellLeft">КПП</div>
                                <div class="cellRight">
                                    <div id="kpp"></div>
                                </div>
                            </div>
                            <div class="cellsBlock2">
                                <div class="cellLeft">Полное наименование</div>
                                <div class="cellRight">
                                    <div id="org_full_name"></div>
                                </div>
                            </div>
                            <div class="cellsBlock2">
                                <div class="cellLeft">БИК</div>
                                <div class="cellRight">
                                    <div id="bik"></div>
                                </div>
                            </div>
                            <div class="cellsBlock2">
                                <div class="cellLeft">К.С.</div>
                                <div class="cellRight">
                                    <div id="ks"></div>
                                </div>
                            </div>
                            <div class="cellsBlock2">
                                <div class="cellLeft">Наименование банка</div>
                                <div class="cellRight">
                                    <div id="bank_name"></div>
                                </div>
                            </div>
                            <div class="cellsBlock2">
                                <div class="cellLeft">Р.С.</div>
                                <div class="cellRight">
                                    <div id="rs"></div>
                                </div>
                            </div>
                            ';


                    echo '					
                            <div class="cellsBlock2">
                                <div class="cellLeft">Назначение</div>
                                <div class="cellRight">
                                    <textarea name="comment" id="comment" cols="35" rows="2" style="width: 98%; font-size: 14px;">Оплата счета '.$client_j[0]['card'].'/Д от '.date("d.m.Y").'</textarea>
                                </div>
                            </div>';

                    echo '					
                            <div class="cellsBlock2">
                                <div class="cellLeft">Сумма</div>
                                <div class="cellRight">
                                    <input type="text" name="rub" id="rub" style="width: 60px; font-size: 14px;" value="'.($client_debt['summ'] - $dostOstatok).'">руб. 
                                    <input type="text" name="kop" id="kop" style="width: 20px; font-size: 14px;" value="00">коп. 
                                    <br>
                                    <span style="font-size: 75%; color: rgb(95, 95, 95); margin-bottom: 10px;">
                                    Сумма долга: <b>'.$client_debt['summ'].'</b>;
                                    Доступно на счету: <b>'.$dostOstatok.'</b>
                                    </span>
                                </span>
                            </div>';
                    echo '
    					</div>';


                    echo '	
                            <input type="button" class="b" style="padding: 4px 8px; margin-top: 10px;" value="Сформировать" onclick="createPayBlankPdfQRCode();">';



                }
                echo '
                        <div id="doc_title">Создание бланка оплаты - Асмедика</div>';


                echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';
                echo '

				<script type="text/javascript">

                    $(function() {
                        $("#SelectOrg").change(function(){
//                            $("#inn").html("#inn");
//                            $("#kpp").html("#kpp");
//                            $("#org_full_name").html("#org_full_name");
//                            $("#bik").html("#bik");
//                            $("#ks").html("#ks");
//                            $("#bank_name").html("#bank_name");
//                            $("#rs").html("#rs");

                            var reqData = {
                                org_id: $(this).val()
                            };
                            
                            
                            $.ajax({
                                url:"change_org_f.php",
                                global: false,
                                type: "POST",
                                dataType: "JSON",
                    
                                data:reqData,
                    
                                cache: false,
                                beforeSend: function() {
                                    //thisObj.html("<div style=\'width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...<br>загрузка<br>расч. листов</span></div>");
                                    blockWhileWaiting (true);
                                },
                                success: function(res){
//				                    console.log(res.data);
                                    
                                    $("#inn").html(res.data.inn);
                                    $("#kpp").html(res.data.kpp);
                                    $("#org_full_name").html(res.data.full_name);
                                    $("#bik").html(res.data.bik);
                                    $("#ks").html(res.data.ks);
                                    $("#bank_name").html(res.data.bank_name);
                                    $("#rs").html(res.data.rs);
                                    
                                    blockWhileWaiting (false);
                                }  
                            }); 
                        });
                    });
                
				</script>';


                //Выводим результат
//                if (!empty($clients_w_installment)) {
//
//                    echo '
//					    <div id="data">';
//
//                    echo '
//                            <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
//
//                    echo '
//							<li class="cellsBlock" style="font-weight:bold;">
//								<div class="cellFullName" style="text-align: center">
//                                    Полное имя';
//                    //echo $block_fast_filter;
//                    echo '
//                                </div>';
//                    echo '
//								<div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">Долг</div>
//								<div class="cellCosmAct" style="text-align: center; width: 100px; min-width: 100px; max-width: 100px;">Доступно на счету</div>
//								<div class="cellCosmAct" style="text-align: center;">Упр. сч.</div>
//								<div class="cellText" style="text-align: center; border: 0;"></div>
//							</li>';
//
//                    //Общая сумма долгов
//                    $debtAllSumm = 0;
//                    //Общая сумма доступно
//                    $dostOstatokAllSumm = 0;
//
//                    foreach ($clients_w_installment as $cl_data) {
//                        //var_dump($cl_data);
//
//                        //Долги/авансы
//                        //
//                        //!!! @@@
//                        //Баланс контрагента
//                        include_once 'ffun.php';
//                        $client_balance = json_decode(calculateBalance($cl_data['id']), true);
//                        //Долг контрагента
//                        $client_debt = json_decode(calculateDebt($cl_data['id']), true);
//
//                        //доступный остаток
//                        $dostOstatok = $client_balance['summ'] - $client_balance['debited'] - $client_balance['withdraw'] + $client_balance['refund'];
//
//                        echo '
//                            <li class="cellsBlock cellsBlockHover" style="">
//								<a href="client.php?id=' . $cl_data['id'] . '" class="cellFullName ahref 4filter" id="4filter" target="_blank" rel="nofollow noopener">' . $cl_data['full_name'] . '</a>';
//
//                        echo '
//								<div class="cellCosmAct" style="text-align: right; width: 100px; min-width: 100px; max-width: 100px;">
//								    <span class="calculateInvoice" style="">' . $client_debt['summ'] . '</span>
//                                </div>
//								<div class="cellCosmAct" style="text-align: right; width: 100px; min-width: 100px; max-width: 100px;">
//								    <span class="calculateOrder" style="font-size: 13px; color: grey;">' . $dostOstatok . '</span>
//                                </div>
//                                <a href="finance_account.php?client_id=' . $cl_data['id'] . '" class="ahref cellCosmAct" style="text-align: center;" target="_blank" rel="nofollow noopener">
//                                    <i class="fa fa-chevron-right" style="color: grey; float: right;" aria-hidden="true"></i>
//                                </a>';
//
//                        echo '
//                                <div class="cellText" style="text-align: center; border: 0;"></div>
//                            </li>';
//
//                        $debtAllSumm += $client_debt['summ'];
//                        $dostOstatokAllSumm += $dostOstatok;
//
//                    }
//
//
//                    echo '
//							<li class="cellsBlock" style="font-weight:bold;">
//								<div class="cellFullName" style="">
//                                    Общая сумма
//                                </div>
//								<div class="cellCosmAct" style="text-align: right; width: 100px; min-width: 100px; max-width: 100px;">
//								    <span class="calculateInvoice" style="font-size: 13px">' . number_format($debtAllSumm, 0, '.', ' ') . '</span>
//                                </div>
//								<div class="cellCosmAct" style="text-align: right; width: 100px; min-width: 100px; max-width: 100px;">
//								    <span class="calculateOrder" style="font-size: 13px; color: grey;">' . number_format($dostOstatokAllSumm, 0, '.', ' ') . '</span>
//                                </div>
//								<div class="cellCosmAct" style="text-align: left; width: 100px; min-width: 100px; max-width: 100px;">
//								    Итого:<br><span class="calculateOrder" style="font-size: 13px; color: blueviolet;">' . number_format(($debtAllSumm - $dostOstatokAllSumm), 0, '.', ' ') . '</span>
//                                </div>
//                                <div class="cellText" style="text-align: center; border: 0;"></div>
//                            </li>';
//
//                    echo '
//					        </ul>';
//                    echo '
//                        </div>';
//
//                } else {
//                    echo '<span style="color: red;">Ничего не найдено</span>';
//                }

                CloseDB($msql_cnnct);


            }
            //mysql_close();
        } else {
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
    }else{
        echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>

