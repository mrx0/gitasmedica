<?php

//fl_tabel_print.php
//Вывод табеля на печать

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($permissions);
		if (($report['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
            include_once 'ffun.php';
			include_once 'filter.php';
			include_once 'filter_f.php';

            require 'variables.php';
		
			/*echo '
				<style type="text/css">
					div.ZakazDemo { padding: 10px !important; width: 300px;}
					.ui-widget*{font-size: 0.6em !important;}
				</style>';*/

            if ($_GET) {

                if (isset($_GET['tabel_id'])){

                    $tabel_j = SelDataFromDB('fl_journal_tabels', $_GET['tabel_id'], 'id');
                    //var_dump($tabel_j);

                    if ($tabel_j != 0){

                        $msql_cnnct = ConnectToDB2 ();

                        $filials_j = getAllFilials(false, true);

                        //Смена/график !!! переделать ! нужно только колчичество
                        $rezultShed = array();
                        $nightSmena = 0;

                        $tabel_deductions_j = array();
                        $tabel_surcharges_j = array();
                        $tabel_surcharges_j = array();
                        $tabel_paidouts_j = array();

                        $query = "SELECT `id`, `day`, `smena`, `kab`, `worker` FROM `scheduler` WHERE `worker` = '{$tabel_j[0]['worker_id']}' AND `month` = '".(int)$tabel_j[0]['month']."' AND `year` = '{$tabel_j[0]['year']}' AND `filial`='{$tabel_j[0]['office_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                //Раскидываем в массив
                                array_push($rezultShed, $arr);
                                //Если ночная смена
                                if ($arr['smena'] == 3){
                                    $nightSmena++;
                                }
                            }
                        }
                        /*var_dump($query);
                        var_dump(count($rezultShed));
                        var_dump($rezultShed);*/

                        //Ночные смены
                        $nightSmenaCount = 0;
                        $nightSmenaPrice = 0;
                        $nightSmenaSumm = 0;

                        $query = "SELECT `price`, `count`, `summ` FROM `fl_journal_tabel_nightsmens` WHERE `tabel_id` = '{$tabel_j[0]['id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){

                            while ($arr = mysqli_fetch_assoc($res)){
                                //Раскидываем в массив
                                //array_push($rezultNightSmena, $arr);
                                $nightSmenaCount = $arr['count'];
                                $nightSmenaPrice = $arr['price'];
                                $nightSmenaSumm = $arr['summ'];
                            }
                            //var_dump($rezultNightSmena);

                        }

                        //Пустые смены
                        $emptySmenaCount = 0;
                        $emptySmenaPrice = 0;
                        $emptySmenaSumm = 0;

                        $query = "SELECT `price`, `count`, `summ` FROM `fl_journal_tabel_emptysmens` WHERE `tabel_id` = '{$tabel_j[0]['id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){

                            while ($arr = mysqli_fetch_assoc($res)){
                                //Раскидываем в массив
                                //array_push($rezultNightSmena, $arr);
                                $emptySmenaCount = $arr['count'];
                                $emptySmenaPrice = $arr['price'];
                                $emptySmenaSumm = $arr['summ'];
                            }
                            //var_dump($rezultNightSmena);

                        }

                        //Надбавки
                        //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."'";
                        $query = "SELECT * FROM `fl_journal_surcharges` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                if (!isset($tabel_surcharges_j[$arr['type']])){
                                    $tabel_surcharges_j[$arr['type']] = array();
                                    $tabel_surcharges_j[$arr['type']] = (int)$arr['summ'];
                                }else{
                                    $tabel_surcharges_j[$arr['type']] = $tabel_surcharges_j[$arr['type']] + $arr['summ'];
                                }
                                //array_push($tabel_surcharges_j[$arr['type']], $arr);
                            }
                        }

                        //Вычеты
                        //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."'";
                        $query = "SELECT * FROM `fl_journal_deductions` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                if (!isset($tabel_deductions_j[$arr['type']])){
                                    $tabel_deductions_j[$arr['type']] = array();
                                    $tabel_deductions_j[$arr['type']] = (int)$arr['summ'];
                                }else{
                                    $tabel_deductions_j[$arr['type']] = $tabel_deductions_j[$arr['type']] + $arr['summ'];
                                }
                                //array_push($tabel_surcharges_j[$arr['type']], $arr);
                            }
                        }

                        //Выплаты
                        //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."'";
                        $query = "SELECT * FROM `fl_journal_paidouts` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                if (!isset($tabel_paidouts_j[$arr['type']])){
                                    $tabel_paidouts_j[$arr['type']] = array();
                                    $tabel_paidouts_j[$arr['type']] = (int)$arr['summ'];
                                }else{
                                    $tabel_paidouts_j[$arr['type']] = $tabel_paidouts_j[$arr['type']] + $arr['summ'];
                                }
                                //array_push($tabel_surcharges_j[$arr['type']], $arr);
                            }
                        }

                        //var_dump($tabel_surcharges_j);
                        //var_dump($tabel_deductions_j);
                        //var_dump($tabel_paidouts_j);

                        echo '
                            <div class="no_print"> 
                                <header style="margin-bottom: 5px;">';

                        echo '
                                </header>
                            </div>';

                        echo '
                            <div>';

                        echo '		
								
									<input type="hidden" name="filter" value="yes">
									<!--<input type="hidden" name="template" id="type" value="5">-->';

                        echo '					
									<div class="filterBlock" style="width: 650px; border-bottom: 1px dotted grey;">
										<div class="filtercellLeft" style="width: 400px; text-align: center; border: none;">
											<a href="fl_tabel.php?id=146" class="ahref">Расчетный листок</a> за '.$monthsName[$tabel_j[0]['month']].' '.$tabel_j[0]['year'].'
										</div>
									</div>
									
									<div class="filterBlock" style="width: 650px;">
										<div class="filtercellLeft" style="border: none; width: 220px; min-width: 220px; max-width: 220px; padding: 5px 0;">
										    <div style="padding: 5px 0; font-size: 120%; font-weight: bold;">
											    <i>'.WriteSearchUser('spr_workers', $tabel_j[0]['worker_id'], 'user', false).'</i>
											</div>
											<div style="background-color: rgba(144,247,95, 0.4); font-size: 130%; padding: 5px;">
											    <div style="display: inline;">К выплате:</div>
											    <div style="float: right; display: inline; text-align: right; font-size: 110%;"><b><i><div class="pay_must" style="display: inline;">0</div> р.</i></b></div>
											</div>
											
										</div>
										<div class="filtercellRight" style="border: none;">
											<div style="border-bottom: 1px dotted grey;">
											    <div style="display: inline;">Подразделение</div>
											    <div style="float: right; display: inline; text-align: right;"><b>'.$filials_j[$tabel_j[0]['office_id']]['name2'].'</b></div>
											</div>
											<div style="border-bottom: 1px dotted grey;">
											    <div style="display: inline;">Норма смен/дней</div>
											    <div style="float: right; display: inline; text-align: right;">-</div>
											</div>
											<div style="border-bottom: 1px dotted grey;">
											    <div style="display: inline;">Часов в смене</div>
											    <div style="float: right; display: inline; text-align: right;">-</div>
											</div>
										</div>
									</div>';


                        echo '
									<div class="filterBlock" style="width: 650px;">
									
									    <table width="100%" style="border: 2px solid #525252; border-collapse: collapse;">
									        <tr>
									            <td rowspan="2" style="width: 100px; text-align: center; border: 2px solid #525252;">
                                                    Вид
									            </td>
									            <td rowspan="2" style="width: 60px; text-align: center; border: 2px solid #525252;">
                                                    тариф
									            </td>
									            <td colspan="2" style="width: 70px; text-align: center; border: 2px solid #525252;">
                                                    период
									            </td>
									            <td rowspan="2" style="width: 80px; text-align: center; border: 2px solid #525252;">
                                                    Сумма
									            </td>
									            <td rowspan="2" style="width: 100px; text-align: center; border: 2px solid #525252;">
                                                    Вид
									            </td>
									            <td style="width: 70px; text-align: center; border: 2px solid #525252;">
                                                    период
									            </td>
									            <td rowspan="2" style="width: 80px; text-align: center; border: 2px solid #525252;">
                                                    Сумма
									            </td>
									        </tr>
									        
									        
            							    <tr>
            							        <td style="width: 35px; text-align: center; border: 1px solid #BFBCB5; font-size: 80%;">
                                                    дней
            							        </td>
            							        <td style="width: 35px; text-align: center; border: 1px solid #BFBCB5; font-size: 80%;">
                                                    часов
            							        </td>
                                                    <td style="text-align: center; border: 1px solid #BFBCB5;">
                                                    дней
									            </td>
									        </tr>  
									         
									        <tr>
									            <td colspan="5" style="text-align: left; border: 2px solid #525252; padding: 5px 0 5px 5px;">
									                <b>1. Начислено</b>
									            </td>
									            <td colspan="3" style="text-align: left; border: 2px solid #525252; padding: 5px 0 5px 5px;">
									                <b>2. Удержано</b>
									            </td>
									        </tr>
									        
            						        <tr>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                з/п
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                ' . count($rezultShed) . '
									            </td>
            						            <td  class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_plus_part1" style="display: inline;">';
                        if (isset($tabel_deductions_j[1])){
                            echo intval($tabel_j[0]['summ'] - $tabel_deductions_j[1]);
                        }else{
                            intval($tabel_j[0]['summ']);
                        }

                        echo '
                                                    </div> р.
									            </td>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                налог
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_minus_part1" style="display: inline;">';
                        if (isset($tabel_deductions_j[2])){
                            echo $tabel_deductions_j[2];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                отпускные
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_plus_part1" style="display: inline;">';
                        if (isset($tabel_surcharges_j[2])){
                            echo $tabel_surcharges_j[2];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                штраф
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_minus_part1" style="display: inline;">';
                        if (isset($tabel_deductions_j[3])){
                            echo $tabel_deductions_j[3];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                больничный
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_plus_part1" style="display: inline;">';
                        if (isset($tabel_surcharges_j[3])){
                            echo $tabel_surcharges_j[3];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                ссуда
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_minus_part1" style="display: inline;">';
                        if (isset($tabel_deductions_j[4])){
                            echo $tabel_deductions_j[4];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                премия
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_plus_part1" style="display: inline;">';
                        if (isset($tabel_surcharges_j[1])){
                            echo $tabel_surcharges_j[1];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                обучение
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_minus_part1" style="display: inline;">';
                        if (isset($tabel_deductions_j[5])){
                            echo $tabel_deductions_j[5];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
									        </tr>
									         
             						        <tr style="border: 2px solid #525252;">
            						            <td colspan="4" style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                <b><i>Всего начислено</i></b>
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0; font-size: 110%;">
									                <b><div class="pay_plus1" style="display: inline;">0</div> р.</b>
									            </td>
            						            <td colspan="2" style="text-align: left; border-left: 2px solid #525252; padding: 3px 0 3px 3px;">
									                <b><i>Всего удержано</i></b>
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0; font-size: 110%;">
									                <b><div class="pay_minus1" style="display: inline;">0</div> р.</b>
									            </td>
									        </tr>
									        
									        <tr>
									            <td colspan="5" style="text-align: left; border: 2px solid #525252; padding: 5px 0 5px 5px;">
									                <b>3. Прочее</b>
									            </td>
									            <td colspan="3" style="text-align: left; border: 2px solid #525252; padding: 5px 0 5px 5px;">
									                <b>4. Выплачено</b>
									            </td>
									        </tr>
									        
             						        <tr>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                пустые смены
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                ',$emptySmenaCount == 0 ? '' : $emptySmenaPrice.' р.','
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                ',$emptySmenaCount == 0 ? '' : $emptySmenaCount,'
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_plus_part2" style="display: inline;">',$emptySmenaCount == 0 ? '0' : $emptySmenaSumm,'</div> р.
									            </td>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                аванс
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_minus_part2" style="display: inline;">';
                        if (isset($tabel_paidouts_j[1])){
                            echo $tabel_paidouts_j[1];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
									        </tr>	
									         
             						        <tr>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                ночные смены
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                 ',$nightSmenaCount == 0 ? '' : $nightSmenaPrice.' р.','
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                ',$nightSmenaCount == 0 ? '' : $nightSmenaCount,'
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_plus_part2" style="display: inline;">',$nightSmenaCount == 0 ? '0' : $nightSmenaSumm,'</div> р.
									            </td>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                отпускные
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_minus_part2" style="display: inline;">';
                        if (isset($tabel_paidouts_j[2])){
                            echo $tabel_paidouts_j[2];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_plus_part2" style="display: inline;">0</div> р.
									            </td>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                больничный
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_minus_part2" style="display: inline;">';
                        if (isset($tabel_paidouts_j[3])){
                            echo $tabel_paidouts_j[3];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td class="border_tabel_print" style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-right: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; border-left: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_plus_part2" style="display: inline;">0</div> р.
									            </td>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                на карту
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                <div class="pay_minus_part2" style="display: inline;">';
                        if (isset($tabel_paidouts_j[4])){
                            echo $tabel_paidouts_j[4];
                        }else{
                            echo 0;
                        }

                        echo '
                                                    </div> р.
									            </td>
									        </tr>
									        
             						        <tr style="border: 2px solid #525252;">
            						            <td colspan="4" style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                <b><i></i></b>
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0; font-size: 110%;">
									                <b><div class="pay_plus2" style="display: inline;">0</div> р.</b>
									            </td>
            						            <td colspan="2" style="text-align: left; border-left: 2px solid #525252; padding: 3px 0 3px 3px;">
									                <b><i>Всего выплачено</i></b>
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0; font-size: 110%;">
									                <b><div class="pay_minus2" style="display: inline;">0</div> р.</b>
									            </td>
									        </tr>
									         
                                        </table>
									
									</div>';

                        echo '										
							</div>';


                        echo "
                            <script>
                                $(document).ready(function() {
                                    //console.log();
                                    
                                    var pay_plus = 0;
                                    var pay_minus = 0;
                                    var pay_plus_part = 0;
                                    var pay_minus_part = 0;
                                    
                                    wait(function(runNext){
                                        
                                        setTimeout(function(){
                            
                                            $('.pay_plus_part1').each(function() {
                                                pay_plus_part += Number($(this).html());
                                                //console.log(Number($(this).html()));  
                                            });
                                            //console.log(pay_plus_part);
                                            
                                            runNext(pay_plus_part);
                            
                                        }, 100);                                        
                                        
                                    }).wait(function(runNext, pay_plus_part){
                                        //используем аргументы из предыдущего вызова
                                        
                                        $('.pay_plus1').html(pay_plus_part);
                                        pay_plus += pay_plus_part;
                                        pay_plus_part = 0;
                                        
                                        setTimeout(function(){
                            
                                            $('.pay_minus_part1').each(function() {
                                                pay_minus_part += Number($(this).html());
                                                //console.log(Number($(this).html()));  
                                            });
                                            //console.log(pay_minus_part);
                            
                                            runNext(pay_plus, pay_plus_part, pay_minus_part);
                            
                                        }, 100); 
                                        
                                    }).wait(function(runNext, pay_plus, pay_plus_part, pay_minus_part){
                                        //используем аргументы из предыдущего вызова
                                        
                                        $('.pay_minus1').html(pay_minus_part);
                                        pay_minus += pay_minus_part;
                                        pay_minus_part = 0;                                        
                                        
                                        setTimeout(function(){
                            
                                            $('.pay_plus_part2').each(function() {
                                                pay_plus_part += Number($(this).html());
                                                //console.log(Number($(this).html()));  
                                            });
                                            //console.log(pay_plus_part);
                                            
                                            runNext(pay_plus, pay_minus, pay_plus_part, pay_minus_part);
                            
                                        }, 100); 
                                        
                                    }).wait(function(runNext, pay_plus, pay_minus, pay_plus_part, pay_minus_part){
                                        //используем аргументы из предыдущего вызова
                                        
                                        $('.pay_plus2').html(pay_plus_part);
                                        pay_plus += pay_plus_part;
                                        
                                        setTimeout(function(){
                            
                                            $('.pay_minus_part2').each(function() {
                                                pay_minus_part += Number($(this).html());
                                                //console.log(Number($(this).html()));  
                                            });
                                            //console.log(pay_plus_part);
                                            
                                            runNext(pay_plus, pay_minus, pay_plus_part, pay_minus_part);
                            
                                        }, 100); 
                                        
                                    }).wait(function(runNext, pay_plus, pay_minus, pay_plus_part, pay_minus_part){

                                        $('.pay_minus2').html(pay_minus_part);
                                        pay_minus += pay_minus_part;
                                        
                                        $('.pay_must').html(pay_plus - pay_minus);
                                        
                                    });
                                    
                                });
                            </script>";


                    }else{
                        echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                    }
                }else{
                    echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                }
            }else{
                echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
            }

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>