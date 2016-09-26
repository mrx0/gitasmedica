<?php

//add_client.php
//Добавить клиента

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($clients['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			
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
						<form action=""add_client_f.php">
					
							<div class="cellsBlock2">
								<div class="cellLeft">Фамилия</div>
								<div class="cellRight">
									<input type="text" name="f" id="f" value="">
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Имя</div>
								<div class="cellRight">
									<input type="text" name="i" id="i" value="">
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Отчество</div>
								<div class="cellRight">
									<input type="text" name="o" id="o" value="">
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Дата рождения</div>
								<div class="cellRight">';
echo '<select name="sel_date" id="sel_date">';
$i = 1;
while ($i <= 31) {
    echo "<option value='" . $i . "'>$i</option>";
    $i++;
}
echo "</select>";
// Месяц
echo "<select name='sel_month' id='sel_month'>";
$month = array(
    "Январь",
    "Февраль",
    "Март",
    "Апрель",
    "Май",
    "Июнь",
    "Июль",
    "Август",
    "Сентябрь",
    "Октябрь",
    "Ноябрь",
    "Декабрь"
);
foreach ($month as $m => $n) {
    echo "<option value='" . ($m + 1) . "'>$n</option>";
}
echo "</select>";
// Год
echo "<select name='sel_year' id='sel_year'>";
$j = 1920;
while ($j <= 2020) {
    echo "<option value='" . $j . "'>$j</option>";
    $j++;
}
echo "</select>";

echo '
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Пол</div>
								<div class="cellRight">
									<input id="sex" name="sex" value="1" type="radio"> М
									<input id="sex" name="sex" value="2" type="radio"> Ж
								</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Контакты</div>
								<div class="cellRight"><textarea name="contacts" id="contacts" cols="35" rows="5"></textarea></div>
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
							
							</div>
							
							<input type=\'button\' class="b" value=\'Добавить\' onclick=\'
								ajax({
									url:"add_client_f.php",
									statbox:"status",
									method:"POST",
									data:
									{
										f:document.getElementById("f").value,
										i:document.getElementById("i").value,
										o:document.getElementById("o").value,
										
										contacts:document.getElementById("contacts").value,
										therapist:document.getElementById("search_client2").value,
										therapist2:document.getElementById("search_client4").value,
										sel_date:document.getElementById("sel_date").value,
										sel_month:document.getElementById("sel_month").value,
										sel_year:document.getElementById("sel_year").value,
										
										sex:sex_value,
										
										session_id:'.$_SESSION['id'].',
									},
									success:function(data){document.getElementById("status").innerHTML=data;}
								})\'
							>
						</form>';	
				
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