<?php

//fl_tabel_print_choice.php
//

	require_once 'header.php';
	require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){

            include_once 'DBWork.php';
            include_once 'functions.php';
            include_once 'ffun.php';

            require 'variables.php';

            $msql_cnnct = ConnectToDB2();

			echo '
				<header class="never_print_it">
					<h1>
    				    Печать
					</h1>
					<span style="color:#777; font-size: 70%;">
					Для печати нажмите Ctrl+P<br>
					В дополнительных настройках выбирайте Поля: "Минимальные", В разделе "Параметры": Колонтитулы галочку убрать, Фон галочку поставить.<br>
					Настройки делаются один раз и запоминаются на компьютере.
					</span>
				</header>';


			echo '
						<div id="data">
                            <div class="no_print">
							    <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							        <li class="cellsBlock" style="background-color:#FEFEFE;">
                                        <div style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
                                            Выберите месяц и год
                                        </div>
									    <div>';
            echo '
									        <select name="SelectMonth" id="SelectMonth" style="margin-right: 5px;">';
            foreach ($monthsName as $mNumber => $mName){
                $selected = '';
                if ((int)$mNumber == (int)date('m')){
                    $selected = 'selected';
                }
                echo '
										        <option value="'.$mNumber.'" '.$selected.'>'.$mName.'</option>';
            }
            echo '
									        </select>

									        <select name="SelectYear" id="SelectYear">';
            for ($i = 2017; $i <= (int)date('Y')+2; $i++){
                $selected = '';
                if ($i == (int)date('Y')){
                    $selected = 'selected';
                }
                echo '
										        <option value="'.$i.'" '.$selected.'>'.$i.'</option>';
            }
            echo '
									        </select>
									    </div>
								    </li>
								
                                    <li class="cellsBlock" style="margin-top: 10px;">
                                        <div style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
                                            Выберите филиал
                                        </div>
                                        <div>';

            echo '
									        <select name="SelectFilialp" id="SelectFilialp" style="margin-right: 5px;">
									            <option value="0" selected>Все</option>';

            $offices_j = getAllFilials(true, false);

            foreach ($offices_j as $offices_val){

                echo '
										        <option value="'.$offices_val['id'].'">'.$offices_val['name'].'</option>';
            }
            echo '
									        </select>

									    </div>
            
                                    </li>
								
                                    <li class="cellsBlock" style="margin-top: 10px;">
                                        <div style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
                                            Выберите сотрудников  <span style="color: #000; font-size: 80%; margin-left: 10px;">[ Выделить всех <input type="checkbox" id="chkBox_" name="checkAll" class="checkAll" value="1" checked>] </span>
                                        </div>';

            $arr = array();
            $workers_rez = array();

            $query = "SELECT `id`, `name` FROM `spr_workers` WHERE `permissions` = '5' AND `fired` <> '1'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    $workers_rez[$arr['id']] = $arr['name'];
                }
            }

            if (!empty($workers_rez)){
                //var_dump($workers_rez);

                foreach ($workers_rez as $w_id => $w_name)
                echo '
                                        <div class="b4 worker" style="font-size: 105%; background-color: #83DB53; " worker_id="'.$w_id.'">
                                            <div style="display: inline-block; width: 130px;">
                                                '.$w_name.'
                                            </div>
                                            <div style="display: inline-block; vertical-align: top;">
                                                <div style="padding: 3px; margin: 1px;">
                                                    <input class="chkBoxCalcs chkBox_" name="WiD_'.$w_id.'" type="checkbox" value="'.$w_id.'" checked>
                                                </div>
                                            </div>
                                        </div>';
            }


            echo '
								    </li>
                                    <li class="cellsBlock" style="background-color:#FEFEFE;">
                                        <input type="button" class="b" style="font-size: 100%; padding: 4px 8px;" value="Применить" onclick="fl_printCheckedWorkersTabels ();">

                                    </li>';

			echo '
					            </ul>
		                    </div>';

            echo '
                            <div id="rezult"></div>
                            <div id="errror"></div>';

            echo '
					        <div id="doc_title">-</div>
					
				        </div>';

            echo '	
			<!-- Подложка только одна -->
			<div id="overlay" class="no_print"></div>';

            echo "
                    <script>
                        $(document).ready(function() {

                            $('#main').css({
								padding: '0 15px 20px'
							});
                            $('body').css({
								background: '#FFF'
							});
                            
                            $('.worker').each(function() {
                                var worker_id = $(this).attr('worker_id');
                                //console.log(worker_id);
                                
                                var link = '';
                                
                                var Data = {
                                    
                                };
                                
                                $.ajax({
                                    url: link,
                                    global: false,
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: Data,
                                    cache: false,
                                    beforeSend: function() {
                                        //$('#errrror').html(\"<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>\");
                                    },
                                    // действие, при ответе с сервера
                                    success: function(res){
                                        //console.log(res);
                        
                                        if(res.result == 'success'){
                                            //location.reload();
                                            
                                             $('#rezult').append(1);
                                             
                                        }else{
                                            $('#errror').html(res.data);
                                        }
                                    }
                                });
                            });
                        });
                        
                        
                    </script>";
            

            /*echo "
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
                            </script>";*/

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>