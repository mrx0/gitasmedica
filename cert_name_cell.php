<?php

//cert_name_cell.php
//Выдать пациенту именной сертификат (история про акцию пригласи друга, получи 500 руб.)

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($finances['add_own'] == 1) || ($finances['add_new'] == 1) || $god_mode){

            include_once('DBWorkPDO.php');
			include_once 'functions.php';

            require 'variables.php';

            $filials_j = getAllFilials(false, false, false);
            //var_dump($filials_j);

			
			if ($_GET){
				//if (isset($_GET['client_id'])){
					
					//$invoice_j = SelDataFromDB('journal_invoice', $_GET['invoice_id'], 'id');

					//if ($invoice_j != 0){
						//var_dump($invoice_j);
						//array_push($_SESSION['invoice_data'], $_GET['client']);
						//$_SESSION['invoice_data'] = $_GET['client'];
						
						//$sheduler_zapis = array();
//						$invoice_ex_j = array();
//						$invoice_ex_j_mkb = array();

                    $client_j = 0;

                    $client_name = '';
                    $client_id = '';

                    if (isset($_GET['client_id'])) {
                        $client_j = SelDataFromDB('spr_clients', $_GET['client_id'], 'user');
                        //var_dump($client_j);
                    }

                    if ($client_j != 0){
                        $client_name = $client_j[0]['full_name'];
                        $client_id = $client_j[0]['id'];
                    }

                    $cert_j = 0;

                    $cert_name = '';
                    $cert_id = '';

                    if (isset($_GET['cert_id'])) {
                        $cert_j = SelDataFromDB('journal_cert_name', $_GET['cert_id'], 'id');
                        //var_dump($cert_j);
                    }

                    if ($cert_j != 0){
                        $cert_name = $cert_j[0]['num'];
                        $cert_id = $cert_j[0]['id'];
                    }
                        //$msql_cnnct = ConnectToDB();
						
//						$query = "SELECT * FROM `zapis` WHERE `id`='".$invoice_j[0]['zapis_id']."'";
//
//                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//						$number = mysqli_num_rows($res);
//						if ($number != 0){
//							while ($arr = mysqli_fetch_assoc($res)){
//								array_push($sheduler_zapis, $arr);
//							}
//						}
						//var_dump ($sheduler_zapis);



						//if ($client !=0){
						//if (!empty($sheduler_zapis)){
						
							//сортируем зубы по порядку
							//ksort($_SESSION['invoice_data'][$_GET['client']][$_GET['invoice_id']]['data']);

							//var_dump($_SESSION);
							//var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['invoice_id']]['data']);
							//var_dump($_SESSION['invoice_data'][$_GET['client']][$_GET['invoice_id']]['mkb']);

//                            if ($invoice_j[0]['type'] != 88) {
//
//                                if ($sheduler_zapis[0]['month'] < 10) $month = '0' . $sheduler_zapis[0]['month'];
//                                else $month = $sheduler_zapis[0]['month'];
//                            }

							echo '
							<div id="status">
								<header>

									<h2>Выдать пациенту <a href="client.php?id='.$client_id.'" class="ahref">'.$client_name.'</a> именной сертификат </h2>';

							echo '
                                </header>';

							echo '
								<div id="data">';
					

//                            if (isset($_SESSION['filial'])) {

                                /*echo '
                                        <div>
                                            <a href="certificate_payment_add.php?invoice_id=' . $_GET['invoice_id'] . '" class="b">Оплатить сертификатом</a>
                                        </div>';*/



                                echo '	
										<div id="paymentAddRezult" class="cellsBlock" style="font-size: 90%;" >
											<div class="cellText2" style="padding: 2px 4px;">
                                                <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">';

                                echo '
                                                <li style="margin-top: 5px; margin-bottom: 5px; font-size: 90%;">
                                                   <b>Пациент: </b><br>
                                                   <input type="text" size="40" name="searchdata" id="search_client" placeholder="Введите первые три буквы для поиска" value="' . $client_name . '" class="who"  autocomplete="off" style="margin-top: 5px; font-size: 120%;">
                                                   <!--!!!Изменить-->
                                                   <span id="changeNewPayer_id" class="button_tiny" style="font-size: 110%; cursor: pointer" onclick="changeCertificateNameMaster();"><i class="fa fa-check-square" style=" color: green;"></i> Изменить</span>
                                                   <ul id="search_result" class="search_result"></ul><br>
                                                </li>
                                                <input type="hidden" id="new_payer_id" value="'.$client_id.'">';

//                                echo '
//                                                <li style="margin-top: 5px; margin-bottom: 5px; font-size: 90%;">
//                                                   <b>Сертификат: </b><br>
//                                                   <input type="text" size="40" name="searchdata" id="search_client" placeholder="Введите первые три буквы для поиска" value="' . $client_name . '" class="who"  autocomplete="off" style="margin-top: 5px; font-size: 120%;">
//                                                   <!--!!!Изменить-->
//                                                   <span id="changeNewPayer_id" class="button_tiny" style="font-size: 110%; cursor: pointer" onclick="changeCertificateNameMaster();"><i class="fa fa-check-square" style=" color: green;"></i> Изменить</span>
//                                                   <ul id="search_result" class="search_result"></ul><br>
//                                                </li>
//                                                <input type="hidden" id="new_payer_id" value="'.$client_id.'">';


                                echo '
                                                        <div>
                                                            <div id="errror"></div>
                                                            <input type="hidden" id="client_id" name="client_id" value="' . $client_id . '">
                                                            <input type="hidden" id="cert_id" name="cert_id" value="' . $cert_id . '">
                                                            <input type="button" class="b" value="Сохранить" onclick="showPaymentAdd(\'add\', true)">
                                                        </div>';


                                echo '
                                                </ul>
											</div>';
                                echo '
										</div>';
                                echo '			
                                    </div>';
//                            }else{
//                                echo '
//								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
//                            }
							echo '
									</div>';
							echo '
								</div>
								<div id="search_cert_input" style="display: none;">
							        <input type="text" size="30" name="searchdata" id="search_cert" placeholder="Наберите номер сертификата для поиска" value="" class="who"  autocomplete="off" style="width: 90%;">
							        <br><span class="lit_grey_text" style="font-size: 75%">Нажмите на галочку, чтобы добавить</span>
                                    <div id="search_result_cert" class="search_result_cert" style="text-align: left;"></div>
							    </div>

                            <!-- Подложка только одна -->
					        <div id="overlay"></div>';

                        echo '
							<script>
                                $(".search_result").on("click", "li", function(){

                                    let client_id = $(this).attr("client_id");
                                    //console.log(client_id);
                                    
                                    $("#new_payer_id").val(client_id);
        
                                    $("#changeNewPayer_id").css({
                                        "border": "1px dashed red",
                                        "color": "red",
                                    })
                                    
                                });
                            </script>';


						/*}else{
							echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
						}*/
//					}else{
//						echo '<h1>1Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
//					}
//				}else{
//					echo '<h1>2Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
//				}
			}else{
				echo '<h1>3Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>