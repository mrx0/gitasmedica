<?php

//fl_tabel_print_choice.php
//

	require_once 'header.php';
	require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){



			echo '
				<header>
					<h1>---</h1>
				</header>';

            echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">';



            echo '
					</div>';


			echo '
						<div id="data">

							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';

			
			include_once 'DBWork.php';



			echo '
					        </ul>
					
					        <div id="doc_title">-</div>
					
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
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>