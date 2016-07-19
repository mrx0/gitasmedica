<?php

//filter.php
//

	function DrawFilterOptions($sw, $it, $cosm, $stom, $workers, $clients, $offices, $god_mode){
		include_once 'DBWork.php';
		
		$offices = SelDataFromDB('spr_office', '', '');
		//print_r (pathinfo(__FILE__));
		echo '

		
			<style type="text/css">
				div.ZakazDemo { padding: 10px !important; width: 300px;}
				.ui-widget{font-size: 0.6em !important;}
			</style>';
		
		echo '
		
			<div class="md-modal md-effect-11" id="modal-11">
				<div class="md-content">
					<h3>Фильтр</h3>
					<div>';
		echo '		
						<form name="cl_form" action="'.$sw.'.php" method="GET" id="form">	
							<input type="hidden" name="filter" value="yes">
							<!--<input type="hidden" name="template" id="type" value="5">-->';
		if (($sw == 'it') || ($sw == 'cosmet') || ($sw == 'stomat') || ($sw == 'stat_stomat2') ){
			echo '					
							<div class="filterBlock">
								<div class="filtercellLeft">
									Выберите период
								</div>
								<div class="filtercellRight">
									С <input name="datastart" class="date" value="'.date("01.m.Y").'"> &bull;По <input name="dataend" class="date" value="'.date("d.m.Y").'">
									<!--<input type="text" name="duration" id="duration" onchange="calc(this.value);" style="border:0; color:#f6931f; font-weight:bold; width:120px;" readonly />
									<div id="slider-range-max"></div>
									<input type="hidden" name="period" value="неделя" readonly="readonly" id="period">-->
									<br />';
			if ($sw == 'it'){
				echo '						
									<span style="font-size:80%;">За всё время <input type="checkbox" name="all_time" value="1" checked></span>';
			}
			if  (($sw == 'cosmet') || ($sw == 'stomat') || ($sw == 'stat_stomat2')){
				echo '						
									<span style="font-size:80%;">За всё время <input type="checkbox" name="all_time" value="1"></span>';
			}
			echo '
								</div>
							</div>';
		}

		if (($it['see_all'] == 1) || ($cosm['see_all'] == 1) || ($clients['see_all'] == 1) || $god_mode ){
			echo '
							<div class="filterBlock">
								<div class="filtercellLeft">';
			if ($sw == 'it'){		
				echo '
									Исполнитель';
			}
			if (($sw == 'cosmet') || ($sw == 'stomat') || ($sw == 'stat_stomat2')){
				echo '
									Врач';
			}
			if ($sw == 'clients'){
				echo '
									Фамилия';
			}
			echo '				</div>
								<div class="filtercellRight">';
			if (($sw == 'it') || ($sw == 'cosmet') || ($sw == 'stomat') || ($sw == 'stat_stomat2')){
				echo '	
									<input type="text" size="35" name="searchdata2" id="search_client2" placeholder="Введите первые три буквы для поиска" value="" class="who2" autocomplete="off">
									<ul id="search_result2" class="search_result2"></ul><br />';
			}
			if ($sw == 'clients'){
				echo '	
									<input type="text" size="35" name="f" placeholder="" value="" autocomplete="off">';
			}
			echo '	
								</div>
							</div>';
							
			if ($sw == 'clients'){
			echo '
							<div class="filterBlock">
								<div class="filtercellLeft">
									Имя
								</div>
								<div class="filtercellRight">
									<input type="text" size="35" name="i" placeholder="" value="" autocomplete="off">
								</div>
							</div>
							<div class="filterBlock">
								<div class="filtercellLeft">
									Отчество
								</div>
								<div class="filtercellRight">
									<input type="text" size="35" name="o" placeholder="" value="" autocomplete="off">
								</div>
							</div>';
			}
		}
		if ($sw == 'it'){
			if (($it['see_all'] == 1) ||  $god_mode){
				echo '
							<div class="filterBlock">
								<div class="filtercellLeft">
									Статус
								</div>
								<div class="filtercellRight">
									<div class="wrapper-demo">
										<select id="dd2" class="wrapper-dropdown-2 b2" tabindex="1" name="ended">
											<ul class="dropdown">
												<li><option value="0" selected>Все</option></li>
												<li><option value="1">Открытые</option></li>
												<li><option value="2">Закрытые</option></li>
											</ul>
										</select>
									</div>
								</div>
							</div>';
			}
		}
		if (($sw == 'it') || ($sw == 'cosmet') || ($sw == 'stomat') || ($sw == 'stat_stomat2')){
			echo '
							<div class="filterBlock">
								<div class="filtercellLeft">
									Филиал
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
		}
		if ($sw == 'it'){
			if (($it['see_all'] == 1) ||  $god_mode){	
				echo '
							<div class="filterBlock">
								<div class="filtercellLeft">
									Приоритет
								</div>
								<div class="filtercellRight">
									<div class="wrapper-demo">
										<select id="dd2" class="wrapper-dropdown-2 b2" tabindex="3" name="priority">
											<ul class="dropdown">
												<li><option value="3">Высокий</option></li>
												<li><option value="2">Средний</option></li>
												<li><option value="1">Низкий</option></li>
												<li><option value="0" selected>Все</option></li>
											</ul>
										</select>
									</div>
								</div>
							</div>';
			}
		}
		if ($sw == 'stomat'){
			if (($stom['see_all'] == 1) || $god_mode){
				echo '
							<div class="filterBlock">
								<div class="filtercellLeft">
									Статус
								</div>
								<div class="filtercellRight">
									<div class="wrapper-demo">
										<select id="dd2" class="wrapper-dropdown-2 b2" tabindex="3" name="pervich">
											<ul class="dropdown">
												<li><option value="0" selected>Все</option></li>
												<li><option value="1">Только первичные</option></li>
											
											</ul>
										</select>
									</div>
								</div>
							</div>';
			}
		}
		if ($sw == 'clients'){
			echo '					
							<div class="filterBlock">
								<div class="filtercellLeft">
									Дата рождения
								</div>
								<div class="filtercellRight" style="text-align:right">
									<!--С <input name="datastart" class="date" value="'.date("01.01.1920").'"> &bull;По <input name="dataend" class="date" value="'.date("d.m.Y").'">-->
									<!--<input type="text" name="duration" id="duration" onchange="calc(this.value);" style="border:0; color:#f6931f; font-weight:bold; width:120px;" readonly />
									<div id="slider-range-max"></div>
									<input type="hidden" name="period" value="неделя" readonly="readonly" id="period">-->';
									
echo 'С <select name="sel_date_I" id="sel_date_I">';
$i = 1;
while ($i <= 31) {
    echo "<option value='" . $i . "'>$i</option>";
    $i++;
}
echo "</select>";
// Месяц
echo "<select name='sel_month_I' id='sel_month_I'>";
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
echo "<select name='sel_year_I' id='sel_year_I'>";
$j = 1920;
while ($j <= 2020) {
    echo "<option value='" . $j . "'>$j</option>";
    $j++;
}
echo "</select><br />";




echo 'По <select name="sel_date_II" id="sel_date_II">';
$i = 1;
while ($i <= 31) {
    echo "<option value='" . $i . "'>$i</option>";
    $i++;
}
echo "</select>";
// Месяц
echo "<select name='sel_month_II' id='sel_month_II'>";
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
echo "<select name='sel_year_II' id='sel_year_II'>";
$j = 1920;
while ($j <= 2020) {
	if ($j == 2020){
		$slctd = 'selected';
	}else{
		$slctd = '';
	}
    echo "<option value='" . $j . "' $slctd>$j</option>";
    $j++;
}
echo "</select>";

			echo '									
								</div>
							</div>';
		}
		
			echo '
							<!--<input type="submit" value="Применить">-->
							
						</form>
						
						<button  type="submit" form="form" formaction="'.$sw.'.php" formmethod="GET" class="b" style="float:left;">Применить</button>
						<button class="md-close b">Закрыть</button>							
					</div>
				</div>
			</div>';
	}
	
?>