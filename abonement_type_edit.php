<?php

//abonement_type_add.php
//Редактируем тип абонемента

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    include_once 'DBWork.php';

    if (($finances['see_all'] == 1) || $god_mode){
        if ($_GET){
            include_once 'DBWork.php';

            $abonement_type_j = SelDataFromDB('spr_solar_abonements', $_GET['id'], 'id');
            //var_dump($abonement_type_j);

            if ($abonement_type_j != 0){

                echo '
                    <div id="status">
                        <header>
                            <div class="nav">
                                <a href="abonement_add.php" class="b">Добавить абонемент</a>
                            </div>
                            <h2>Редактировать тип абонемента</h2>
                        </header>';

                echo '
                        <div id="data">';
                echo '
                            <div id="errrror"></div>';
                echo '
                                <div class="cellsBlock2">
                                    <div class="cellLeft">Название</div>
                                    <div class="cellRight">
                                        <input type="text" name="name" id="name" value="'.$abonement_type_j[0]['name'].'">
                                        <label id="name_error" class="error"></label>
                                    </div>
                                </div>
                        
                                <div class="cellsBlock2">
                                    <div class="cellLeft">минуты</div>
                                    <div class="cellRight">
                                        <input type="text" name="min_count" id="min_count" value="'.$abonement_type_j[0]['min_count'].'">
                                        <label id="min_count_error" class="error"></label>
                                    </div>
                                </div>
						
						<div class="cellsBlock2">
							<div class="cellLeft">Срок действия (дней)</div>
							<div class="cellRight">
								<input type="text" name="exp_days" id="exp_days" value="'.$abonement_type_j[0]['exp_days'].'">
								<label id="exp_days_error" class="error"></label>
							</div>
						</div>
						
						<div class="cellsBlock2">
							<div class="cellLeft">Стоимость (руб.)</div>
							<div class="cellRight">
								<input type="text" name="summ" id="summ" value="'.$abonement_type_j[0]['summ'].'">
								<label id="summ_error" class="error"></label>
							</div>
						</div>
						
						<div id="errror"></div>';

                echo '<input type="button" class="b" value="Применить" onclick="showAbonTypeAdd('.$_GET['id'].', \'edit\')">';

                echo '
				    </div>
			    </div>';
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