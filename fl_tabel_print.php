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
			include_once 'filter.php';
			include_once 'filter_f.php';
		
			$errors = array();
			$query = '';
			$journal = 0;
			
			$offices = SelDataFromDB('spr_filials', '', '');
			$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');	
			foreach($actions_cosmet as $key=>$arr_temp){
				$data_nomer[$key] = $arr_temp['nomer'];
			}
			array_multisort($data_nomer, SORT_NUMERIC, $actions_cosmet);
			
			//var_dump ($actions_cosmet);
			//var_dump($offices);
			//print_r (pathinfo(__FILE__));

			echo '
				<style type="text/css">
					div.ZakazDemo { padding: 10px !important; width: 300px;}
					.ui-widget{font-size: 0.6em !important;}
				</style>';
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
											Расчетный листок  за Март 2018
										</div>
									</div>
									
									<div class="filterBlock" style="width: 650px;">
										<div class="filtercellLeft" style="border: none; width: 220px; min-width: 220px; max-width: 220px;">
										    <div style="padding: 5px; font-size: 120%; font-weight: bold;">
											    <i>Мустафин Д.К.</i>
											</div>
											<div style="background-color: rgba(216, 216, 216, 1); font-size: 130%; padding: 5px;">
											    <div style="display: inline;">К выплате:</div>
											    <div style="float: right; display: inline; text-align: right;">7786 р.</div>
											</div>
											
										</div>
										<div class="filtercellRight" style="border: none;">
											<div style="border-bottom: 1px dotted grey;">
											    <div style="display: inline;">Подразделение</div>
											    <div style="float: right; display: inline; text-align: right;"><b>ГР114</b></div>
											</div>
											<div style="border-bottom: 1px dotted grey;">
											    <div style="display: inline;">Норма смен</div>
											    <div style="float: right; display: inline; text-align: right;">15</div>
											</div>
											<div style="border-bottom: 1px dotted grey;">
											    <div style="display: inline;">Часов в смене</div>
											    <div style="float: right; display: inline; text-align: right;">12</div>
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
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td  class="border_tabel_print"style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                7786 р.
									            </td>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                налог
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                отпускные
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: left; padding: 3px 0 3px 3px;">
									                штраф
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td class="border_tabel_print" style="text-align: right; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                больничный
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                ссуда
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                премия
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                обучение
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									         
             						        <!--<tr>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>-->
									         
             						        <tr style="border: 2px solid #525252;">
            						            <td colspan="4" style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                <b><i>Всего начислено</i></b>
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0; font-size: 110%;">
									                <b>7786 р.</b>
									            </td>
            						            <td colspan="2" style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                <b><i>Всего удержано</i></b>
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0; font-size: 110%;">
									                <b></b>
									            </td>
									        </tr>
									        
									        <tr>
									            <td colspan="5" style="text-align: left; border: 2px solid #525252; padding: 5px 0 5px 5px;">
									                <b>3. -</b>
									            </td>
									            <td colspan="3" style="text-align: left; border: 2px solid #525252; padding: 5px 0 5px 5px;">
									                <b>4. Выплачено</b>
									            </td>
									        </tr>
									        
             						        <tr>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                аванс
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                отпускные
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                больничный
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									         
             						        <tr>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                на карту
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0;">
									                
									            </td>
									        </tr>
									        
             						        <tr style="border: 2px solid #525252;">
            						            <td colspan="4" style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                <b><i></i></b>
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0; font-size: 110%;">
									                <b></b>
									            </td>
            						            <td colspan="2" style="text-align: left; border: 1px solid #BFBCB5; padding: 3px 0 3px 3px;">
									                <b><i>Всего выплачено</i></b>
									            </td>
            						            <td style="text-align: right; border: 1px solid #BFBCB5; padding: 3px 3px 3px 0; font-size: 110%;">
									                <b></b>
									            </td>
									        </tr>
									         
                                        </table>
									
									</div>';
				
				
				/*echo '
									<div class="filterBlock" style="width: 600px;">
										<div class="cellText" style="width: 100px; min-width: 100px; text-align: center;">
											Вид
										</div>
										
										<div class="filtercellRight">
											<div class="wrapper-demo">
												<select id="dd2" class="wrapper-dropdown-2 b2" tabindex="2" name="filial">
													<ul class="dropdown">
														<li><option value="99" selected>Все</option></li>';
															if ($offices !=0){
																for ($i=0;$i<count($offices);$i++){
																	echo '<li><option value="'.$offices[$i]['id'].'" class="icon-twitter icon-large">'.$offices[$i]['name'].'</option></li>';
																}
															}
												
				echo '
													</ul>
												</select>
											</div>
										</div>
									</div>';
				//Пол					
				echo '			
									<div class="filterBlock" style="width: 600px;">
										<div class="filtercellLeft">
											Пол<br />
										</div>
										<div class="filtercellRight">
											<input id="sex" name="sex" value="1" type="radio">М<br />
											<input id="sex" name="sex" value="2" type="radio">Ж<br />
											<input id="sex" name="sex" value="3" checked type="radio">М+Ж<br />
											<span style="font-size:80%;">Показывать тех, у кого не указан пол <input type="checkbox" name="wo_sex" checked value="1"></span>
										</div>
									</div>';
				
				//Первичка
				echo '			
									<div class="filterBlock" style="width: 600px;">
										<div class="filtercellLeft">
											Первичные<br />
											<span style="font-size:80%;">На период выборки</span>
										</div>
										<div class="filtercellRight">
											<input id="pervich" name="pervich" value="1" checked type="radio"> Все<br />
											<input id="pervich" name="pervich" value="2" type="radio"> Только первичные<br />
											<input id="pervich" name="pervich" value="3" type="radio"> Только не первичные
										</div>
									</div>';
				
				
				//Возраст
				echo '			
									<div class="filterBlock" style="width: 600px;">
										<div class="filtercellLeft">
											Возраст<br />
										</div>
										<div class="filtercellRight">
											От <input type="number" size="2" name="start_age" id="start_age" min="0" max="99" value="0" class="mod"> 
											до <input type="number" size="2" name="finish_age" id="finish_age" min="0" max="99" value="99" class="mod"> лет

										</div>
									</div>';*/
				
				
				
				
				
				echo '										
							</div>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>