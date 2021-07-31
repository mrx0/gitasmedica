<?php

//add_new_norma_hours.php
//Добавить персональную норму часов

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    include_once 'DBWork.php';

    echo '
			<div id="status">
				<header>
					<div class="nav">
						<a href="fl_normahours_personal.php" class="b">Персональные нормы часов</a>
					</div>
					<h2>Добавить персональную норму часов сотруднику</h2>
					Заполните поля
				</header>';

    if (($_SESSION['permissions'] == 3) || $god_mode) {
        echo '
                    <div id="data">';
        echo '
                        <div id="errrror"></div>';
        echo '
                        <form action="cert_add_f.php">
                    
                            <div class="cellsBlock2">
                                <div class="cellLeft">Сотрудник</div>
                                <div class="cellRight" id="worker_name">
                                    <input type="text" size="30" name="searchdata2" id="search_client2" placeholder="Введите ФИО сотрудника" value="" class="who2"  autocomplete="off" style="width: 90%;">
                                    <ul id="search_result2" class="search_result2"></ul><br />
                                    <label id="worker_error" class="error"></label>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Норма  часов</div>
                                <div class="cellRight">
                                    <input type="text" name="norma" id="norma" value="">
                                    <label id="norma_error" class="error"></label>
                                </div>
                            </div>
                            
                            <div id="errror"></div>                        
                            <input type="button" class="b" value="Добавить" onclick="showPersonalNormaHoursAdd(0, \'add\')">
                        </form>';

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