<?php

//client_add.php
//Добавить клиента

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($clients['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			$orgs = SelDataFromDB('spr_org', '', '');
			$permissions = SelDataFromDB('spr_permissions', '', '');
			
			//Для того, если заводим пациента через GET
			$value_f = '';
			$value_i = '';
			$value_o = '';
			
			$fio_arr = array();
			
			if (isset($_GET['fio'])){
				$fio_arr = explode(' ', $_GET['fio']);
				if (isset($fio_arr[0]))
					$value_f = firspUpperCase($fio_arr[0]);
				if (isset($fio_arr[1]))
					$value_i = firspUpperCase($fio_arr[1]);
				if (isset($fio_arr[2]))
					$value_o = firspUpperCase($fio_arr[2]);
			}
			//var_dump($fio_arr );
			
			echo '
				<div id="status">
					<header>
						<h2>Добавить пациента</h2>
						Заполните поля
					</header>';

            echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">';

            echo $block_fast_search_client;

            echo '
					</div>';

			echo '
					<div id="data">';
			echo '
						<div id="errrror"></div>';
			echo '
						<form action="client_add_f.php" style="font-size: 90%;" class="input_form">
					
							<div class="cellsBlock2">
								<div class="cellLeft">Фамилия</div>
								<div class="cellRight">
									<input type="text" name="f" id="f" value="'.$value_f.'">
									<label id="fname_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Имя</div>
								<div class="cellRight">
									<input type="text" name="i" id="i" value="'.$value_i.'">
									<label id="iname_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Отчество</div>
								<div class="cellRight">
									<input type="text" name="o" id="o" value="'.$value_o.'">
									<label id="oname_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Дата рождения</div>
								<div class="cellRight">';
								
			echo selectDate (0, 0, 0);

			echo '	
									<label id="sel_date_error" class="error"></label>
									<label id="sel_month_error" class="error"></label>
									<label id="sel_year_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Пол</div>
								<div class="cellRight">
									<input id="sex" name="sex" value="1" type="radio"> М
									<input id="sex" name="sex" value="2" type="radio"> Ж
									<label id="sex_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Телефон</div>
								<div class="cellRight">
									<div>
										<span style="font-size: 80%; color: #AAA">мобильный</span><br>
										<input type="text" name="telephone" id="telephone" value="">
									</div>
									<div>
										<span style="font-size: 80%; color: #AAA">домашний</span><br>
										<input type="text" name="htelephone" id="htelephone" value="">
									</div>
								</div>
							</div>
							
							<div id="toggleDiv1" class="cellsBlock2" style="margin-top: 2px; margin-bottom: -1px; display: block; margin: 3px 0;">
								<div class="cellLeft" style="font-weight: bold; width: 500px; cursor: pointer;">
									Паспортные данные
								</div>
							</div>
							
							<div id="div1">
								<div class="cellsBlock2">
									<div class="cellLeft">Паспорт</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">Серия номер</span><br>
											<input type="text" name="passport" id="passport" value="" size="10"><br>
										</div>
										<div>
											<span style="font-size: 80%; color: #AAA">Серия номер (иностр.)</span><br>
											<input type="text" name="alienpassportser" id="alienpassportser" value="" size="5">
											<input type="text" name="alienpassportnom" id="alienpassportnom" value="" size="10"><br>
										</div>
										<div>
											<span style="font-size: 80%; color: #AAA">Выдан когда</span><br>
											<input type="text" name="passportvidandata" id="passportvidandata" value="" size="10">
										</div>
										<div>
											<span style="font-size: 80%; color: #AAA">Кем</span><br>
											<textarea name="passportvidankem" id="passportvidankem" cols="25" rows="2"></textarea>
										</div>
									</div>
								</div>
							
								<div class="cellsBlock2">
									<div class="cellLeft">Адрес</div>
									<div class="cellRight"><textarea name="address" id="address" cols="35" rows="2"></textarea></div>
								</div>
							</div>
							
							<div id="toggleDiv2" class="cellsBlock2" style="margin-top: 2px; margin-bottom: 0; display: block; margin: 3px 0;">
								<div class="cellLeft" style="font-weight: bold; width: 500px; cursor: pointer;">
									Данные страховой компании
								</div>
							</div>
							
							<div id="div2">
								<div class="cellsBlock2">
									<div class="cellLeft">
										Полис
									</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">Номер</span><br>
											<input type="text" name="polis" id="polis" value="">
										</div>
										<div>
											<span style="font-size: 80%; color: #AAA">Дата</span><br>
											<input type="text" name="polisdata" id="polisdata" value="" size="10">
										</div>
										<div>
											<span style="font-size: 80%; color: #AAA">Страховая компания</span><br>';
							echo '
											<select name="insurecompany" id="insurecompany">
												<option value="0">Выберите страховую</option>';
				$insures_j = SelDataFromDB('spr_insure', '', '');
				
				if ($insures_j != 0){
					for ($i=0;$i<count($insures_j);$i++){

						echo "<option value='".$insures_j[$i]['id']."'>".$insures_j[$i]['name']."</option>";
					}
				}
				echo '
											</select>
										</div>
									</div>
								</div>
							</div>
							
							<div id="toggleDiv3" class="cellsBlock2" style="margin-top: 2px; margin-bottom: 0; display: block; margin: 3px 0;">
								<div class="cellLeft" style="font-weight: bold; width: 500px; cursor: pointer;">
									Опекун
								</div>
							</div>
							
							<div id="div3">
								<div class="cellsBlock2">
									<div class="cellLeft">Фамилия</div>
									<div class="cellRight">
										<input type="text" name="fo" id="fo" value="">
										<label id="fname_error" class="error"></label>
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Имя</div>
									<div class="cellRight">
										<input type="text" name="io" id="io" value="">
										<label id="iname_error" class="error"></label>
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Отчество</div>
									<div class="cellRight">
										<input type="text" name="oo" id="oo" value="">
										<label id="oname_error" class="error"></label>
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Телефон</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">мобильный</span><br>
											<input type="text" name="telephoneo" id="telephoneo" value="">
										</div>
										<div>
											<span style="font-size: 80%; color: #AAA">домашний</span><br>
											<input type="text" name="htelephoneo" id="htelephoneo" value="">
										</div>
									</div>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Номер мед.карты</div>
								<div class="cellRight">
									<input type="text" name="card" id="card" value="">
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight"><textarea name="comment" id="comment" cols="35" rows="2"></textarea></div>
							</div>
														
							<div class="cellsBlock2">
								<div class="cellLeft">
									Лечащий врач<br />
									<span style="font-size: 80%">стоматология</span>
								</div>
								<div class="cellRight">
									<input type="text" size="50" name="searchdata2" id="search_client2" placeholder="Введите первые три буквы для поиска" value="" class="who2"  autocomplete="off">
									<ul id="search_result2" class="search_result2"></ul><br>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">
									Лечащий врач<br />
									<span style="font-size: 80%">косметология</span>
								</div>
								<div class="cellRight">
									<input type="text" size="50" name="searchdata4" id="search_client4" placeholder="Введите первые три буквы для поиска" value="" class="who4"  autocomplete="off">
									<ul id="search_result4" class="search_result4"></ul><br>
								</div>
							</div>
						
							<!--
							<div class="cellsBlock2">
								<div class="cellLeft">
									Лечащий врач<br />
									<span style="font-size: 80%">специалисты</span>
								</div>
								<div class="cellRight">
									<input type="text" size="50" name="searchdata4" id="search_client4" placeholder="Введите первые три буквы для поиска" value="" class="who4"  autocomplete="off">
									<ul id="search_result4" class="search_result4"></ul><br>
								</div>
							</div>
							-->
						
							<div id="errror"></div>
							<input type="button" class="b" value="Добавить" onclick="Ajax_add_client('.$_SESSION['id'].')">
						</form>
					</div>';	
				
			echo '
					</div>
				</div>
				
				<script type="text/javascript">
					sex_value = 0;
					$("input[name=sex]").change(function() {
						sex_value = $("input[name=sex]:checked").val();
					});

					$("#passport").on("keyup", function(e) { 
					 
						var $this = $(this); 
						var val = $this.val();

						if ((val.length >= 11) && !isNaN(val[val.length - 1])){
							document.getElementById("passportvidandata").focus();
						}
					});

					$("#passportvidandata").on("keyup", function(e) { 
					 
						var $this = $(this); 
						var val = $this.val(); 

						if ((val.length >= 10) && !isNaN(val[val.length - 1])){
							document.getElementById("passportvidankem").focus();
						}
					});

					jQuery(function($) {
						$.mask.definitions["~"]="[+-]";
						$("#polisdata").mask("99.99.9999");
						$("#passportvidandata").mask("99.99.9999");
						$("#telephone").mask("+7(999)999-9999");
						$("#telephoneo").mask("+7(999)999-9999");
						$("#passport").mask("9999 999999");
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