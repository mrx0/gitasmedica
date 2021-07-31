<?php

//abonement_type_add.php
//Добавить тип абонемента

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    include_once 'DBWork.php';

    if (($finances['see_all'] == 1) || $god_mode){

        echo '
			<div id="status">
				<header>
					<div class="nav">
						<a href="abonement_add.php" class="b">Добавить абонемент</a>
					</div>
					<h2>Добавить тип абонемента</h2>
					Заполните поля
				</header>';

        echo '
				<div id="data">';
        echo '
					<div id="errrror"></div>';
        echo '
                        <div class="cellsBlock2">
							<div class="cellLeft">Название</div>
							<div class="cellRight">
								<input type="text" name="name" id="name" value="">
								<label id="name_error" class="error"></label>
							</div>
						</div>
				
						<div class="cellsBlock2">
							<div class="cellLeft">минуты</div>
							<div class="cellRight">
								<input type="text" name="min_count" id="min_count" value="">
								<label id="min_count_error" class="error"></label>
							</div>
						</div>
						
						<div class="cellsBlock2">
							<div class="cellLeft">Срок действия (дней)</div>
							<div class="cellRight">
								<input type="text" name="exp_days" id="exp_days" value="">
								<label id="exp_days_error" class="error"></label>
							</div>
						</div>
						
						<div class="cellsBlock2">
							<div class="cellLeft">Стоимость (руб.)</div>
							<div class="cellRight">
								<input type="text" name="summ" id="summ" value="">
								<label id="summ_error" class="error"></label>
							</div>
						</div>
						
						<div id="errror"></div>                        
						<input type="button" class="b" value="Добавить" onclick="showAbonTypeAdd(0, \'add\')">';

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