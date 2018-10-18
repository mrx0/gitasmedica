<?php

//fl_createDailyReport.php
//Добавить ежедневный отчёт администратор

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){

            include_once 'DBWork.php';
            include_once 'functions.php';

            $filials_j = getAllFilials(false, false);
            //var_dump($filials_j);

            echo '
                <div id="status">
                    <header>
                        <h2>Добавить ежедневный отчёт</h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';
            echo '
                    
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    Дата отчёта
                                </div>
                                <div class="cellRight">
                                    <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
                                                onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
                                    <span class="button_tiny" style="font-size: 80%; cursor: pointer" onclick="iWantThisDate2(\'fl_createDailyReport.php?filial_id=14\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>            
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Z-отчёт, руб.</div>
                                <div class="cellRight">
                                    <input type="text" name="zreport" id="zreport" value="">
                                </div>
                            </div>';

            echo '				
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    Филиал
                                </div>
                            <div class="cellRight">';


            $filial_id = 0;

            if (($finances['see_all'] == 1) || $god_mode){

                echo '
                            <select name="SelectFilial" id="SelectFilial">';

                foreach ($filials_j as $filial_item){

                    $selected = '';
                    if (isset($_GET['filial_id'])){
                        $filial_id = $_GET['filial_id'];

                        if ($_GET['filial_id'] == $filial_item['id']) {
                            $selected = 'selected';
                        }
                    }else {
                        if (isset($_SESSION['filial'])) {
                            $filial_id = $_SESSION['filial'];

                            if ($_SESSION['filial'] == $filial_item['id']) {
                                $selected = 'selected';
                            }
                        }
                    }

                    echo '<option value="'.$filial_item['id'].'" '.$selected.'>'.$filial_item['name'].'</option>';
                }

                echo '
                            </select>';
            }else{
                if (isset($_SESSION['filial'])) {
                    $filial_id = $_SESSION['filial'];

                    echo $filials_j[$_SESSION['filial']]['name'].'<input type="hidden" id="filial" name="filial" value="'.$_SESSION['filial'].'">';
                }
            }

            echo '
										</div>
									</div>
								</div>';


            $invoice_arr = array();

            //$today = date('Y-m-d', time());
            $year = date('Y', time());
            $month = date('n', time());
            $day = date('d', time());


            $msql_cnnct = ConnectToDB ();

            $query = "SELECT * FROM `journal_invoice` WHERE `zapis_id` IN (
                      SELECT `id` FROM `zapis` WHERE `year`='$year' AND `month`='$month' AND `day`='$day')   
                      AND `office_id` = '15' AND `status` <> '9'";
            var_dump($query);

            //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            //$number = mysqli_num_rows($res);

            /*if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {
                    array_push($invoice_arr, $arr);
                }
            }*/

            if (!empty($invoice_arr)){
                var_dump($invoice_arr);


            }




            //Вкладки для отчёта
            echo '
						<div id="tabs_w" style="font-family: Arial, sans-serif; font-size: 100% !important;">
							<ul style="border-top-left-radius: 0; border-top-right-radius: 0; border-bottom-left-radius: 0; border-bottom-right-radius: 0; ">
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-1" style="padding: 3px 10px; font-size: 12px;">Приём</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-2" style="padding: 3px 10px; font-size: 12px;">Терапия - Кариесология</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-3" style="padding: 3px 10px; font-size: 12px;">Хирургия</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-4" style="padding: 3px 10px; font-size: 12px;">Имплантация</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-5" style="padding: 3px 10px; font-size: 12px;">Пародонтология</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-6" style="padding: 3px 10px; font-size: 12px;">Ортопедия</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-7" style="padding: 3px 10px; font-size: 12px;">Ортопедия на имплантах</a></li>
							    <li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-8" style="padding: 3px 10px; font-size: 12px;">Ортодонтия</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-9" style="padding: 3px 10px; font-size: 12px;">Гигиена</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-10" style="padding: 3px 10px; font-size: 12px;">Дополнительно</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-11" style="padding: 3px 10px; font-size: 12px;">Дети</a></li>
							</ul>';

            $invoices_arr = array();

            $msql_cnnct = ConnectToDB ();

            $today = date('Y-m-d', time());

            $query = "SELECT jc.* FROM `journal_invoice` ji 
                      LEFt JOIN `fl_journal_calculate` jc ON jc.invoice_id = ji.id
                      WHERE 
                      DATE_FORMAT(ji.closed_time, '%Y-%m-%d') ='{$today}'
                      AND ji.status = '5'
                      AND ji.summ = ji.paid
                      AND ji.office_id = '{$filial_id}'";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($invoices_arr, $arr['id']);
                }
            }

            if (!empty($invoices_arr)){
                var_dump($invoices_arr);
            }




            echo '
							<div id="tabs-1">';





            echo '
							</div>';
            echo '
							<div id="tabs-2">';
            echo '
							</div>';
            echo '
							<div id="tabs-3">';
            echo '
							</div>';
            echo '
							<div id="tabs-4">';
            echo '
							</div>';
            echo '
							<div id="tabs-5">';
            echo '
							</div>';
            echo '
							<div id="tabs-6">';
            echo '
							</div>';
            echo '
							<div id="tabs-7">';
            echo '
							</div>';
            echo '
							<div id="tabs-8">';
            echo '
							</div>';
            echo '
							<div id="tabs-9">';
            echo '
							</div>';
            echo '
							<div id="tabs-10">';
            echo '
							</div>';
            echo '
							<div id="tabs-11">';
            echo '
							</div>';


            echo '
							
				        </div>';

















            echo '
                            <input type="button" class="b" value="Добавить" onclick="">';

            echo '
                    </div>
                </div>
                <div id="doc_title">Ежедневный отчёт - Асмедика</div>';


            echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

            echo '
					<script>
					
						$(function() {
							$("#SelectFilial").change(function(){
							    
							    blockWhileWaiting (true);
							    
								document.location.href = "?filial_id="+$(this).val();
							});
						});
						
					</script>';

        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>