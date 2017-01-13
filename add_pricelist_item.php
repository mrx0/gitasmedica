<?php

//add_serviceitem.php
//Добавить услугу

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			//операции со временем						
			$day = date('d');		
			$month = date('m');		
			$year = date('Y');
			
			//тип график (космет/стомат/...)
			if (isset($_GET['who'])){
				if ($_GET['who'] == 'stom'){
					$who = '&who=stom';
					$whose = 'Стоматология ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
				}elseif($_GET['who'] == 'cosm'){
					$who = '&who=cosm';
					$whose = 'Косметология ';
					$selected_stom = ' ';
					$selected_cosm = ' selected';
					$datatable = 'scheduler_cosm';
					$kabsForDoctor = 'cosm';
					$type = 6;
				}else{
					$who = '&who=stom';
					$whose = 'Стоматология ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
					$_GET['who'] = 'stom';
				}
			}else{
				$who = '&who=stom';
				$whose = 'Стоматология ';
				$selected_stom = ' selected';
				$selected_cosm = ' ';
				$datatable = 'scheduler_stom';
				$kabsForDoctor = 'stom';
				$type = 5;
				$_GET['who'] = 'stom';
			}
			
			echo '
				<div id="status">
					<header>
						<h2>Добавить новую позицию<!--'.$whose.'--></h2>
						Заполните поля
					</header>';

			echo '
					<div id="data">';
			echo '
						<div id="errror"></div>';
			echo '
						<form action="add_servicename_f.php" style="font-size: 90%;" class="input_form">
					
							<div class="cellsBlock2" style="margin-bottom: 5px;">
								<div class="cellLeft">Название</div>
								<div class="cellRight">
									<textarea name="pricename" id="pricename" style="width:90%; overflow:auto; height: 50px;"></textarea>
									<label id="pricename_error" class="error"></label>
								</div>
							</div>
							<div class="cellsBlock2">
								<div class="cellLeft">Цена</div>
								<div class="cellRight">
									<input type="text" name="price" id="price" value="0"  style="width: 50px;"> руб.
									<label id="price_error" class="error"></label>
								</div>
							</div>';
					//Календарик	
					echo '
	
								<div class="cellsBlock2">
									<div class="cellLeft">С какого числа:</div>
									<div class="cellRight">
										<input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
										onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
									</div>
								</div>';
				echo '
							<input type="button" class="b" value="Добавить" onclick="Ajax_add_priceitem('.$_SESSION['id'].')">
						</form>
					</div>';	
				
			echo '
					</div>
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>