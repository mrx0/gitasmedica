<?php

//add_client.php
//Добавить клиента

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($clients['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			$orgs = SelDataFromDB('spr_org', '', '');
			$permissions = SelDataFromDB('spr_permissions', '', '');
			
			echo '
				<div id="status">
					<header>
						<h2>Добавить пациента</h2>
						Заполните поля
					</header>';

			echo '
					<div id="data">';
			echo '
						<div id="errrror"></div>';
			echo '
						<form action=""add_client_f.php">
					
							<div class="cellsBlock2">
								<div class="cellLeft">Фамилия</div>
								<div class="cellRight">
									<input type="text" name="f" id="f" value="">
									<label id="fname_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Имя</div>
								<div class="cellRight">
									<input type="text" name="i" id="i" value="">
									<label id="iname_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Отчество</div>
								<div class="cellRight">
									<input type="text" name="o" id="o" value="">
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
								<div class="cellLeft">Контакты</div>
								<div class="cellRight"><textarea name="contacts" id="contacts" cols="35" rows="5"></textarea></div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Номер карты</div>
								<div class="cellRight">
									<input type="text" name="card" id="card" value="">
								</div>
							</div>
							
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">
									Лечащий врач<br />
									<span style="font-size: 70%">стоматология</span>
								</div>
								<div class="cellRight">
									<input type="text" size="50" name="searchdata2" id="search_client2" placeholder="Введите первые три буквы для поиска" value="" class="who2"  autocomplete="off">
									<ul id="search_result2" class="search_result2"></ul><br />
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">
									Лечащий врач<br />
									<span style="font-size: 70%">косметология</span>
								</div>
								<div class="cellRight">
									<input type="text" size="50" name="searchdata4" id="search_client4" placeholder="Введите первые три буквы для поиска" value="" class="who4"  autocomplete="off">
									<ul id="search_result4" class="search_result4"></ul><br />
								</div>
							</div>
						
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
				</script>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>