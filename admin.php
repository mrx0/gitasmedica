<?php

//admin.php
//Админка

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if ($god_mode || ($_SESSION['permissions'] == 3)){
			include_once 'DBWork.php';
			//$offices = SelDataFromDB('spr_filials', '', '');
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Настройки</h1>
				</header>		
				<div id="data">';

            include_once('DBWorkPDO.php');
            $db = new DB();

            //Копирование
            $query = "SELECT `value` FROM `settings` WHERE `option`='oncopy' LIMIT 1";

            //Выбрать все
            $oncopy = $db::getValue($query, []);
            //var_dump($oncopy);

            if ($oncopy == 'true'){
                $oncopy_str = '<span style="color: red;" onclick="changeSettings(\'oncopy\', \''.$oncopy.'\');">Копирование <b>ВКЛЮЧЕНО</b></span>';
            }else{
                $oncopy_str = '<span style="color: green;" onclick="changeSettings(\'oncopy\', \''.$oncopy.'\');"">Копирование <b>ВЫКЛЮЧЕНО</b></span>';
            }

            echo '<div class="b">'.$oncopy_str.'</div>';

            //Редактирование задним числом
            $query = "SELECT `value` FROM `settings` WHERE `option`='uncheckDailyReport' LIMIT 1";

            //Выбрать все
            $uncheck = $db::getValue($query, []);
            //var_dump($oncopy);

            if ($uncheck == 'true'){
                $uncheck_str = '<span style="color: red;" onclick="changeSettings(\'uncheckDailyReport\', \''.$uncheck.'\');">Отмена проверки сводного отчета <b>ВКЛЮЧЕНО</b></span>';
            }else{
                $uncheck_str = '<span style="color: green;" onclick="changeSettings(\'uncheckDailyReport\', \''.$uncheck.'\');"">Отмена проверки сводного отчета <b>ВЫКЛЮЧЕНО</b></span>';
            }

            echo '<div class="b">'.$uncheck_str.'</div>';

            if ($god_mode) {
                //echo '<a href="shed_temlates.php" class="b">Шаблоны графиков</a>';
                echo '<a href="settins.php" class="b">Настройки</a>';
                echo '<a href="logs.php" class="b">LOGS</a>';
                echo '<a href="wrights.php" class="b">Права</a>';
                echo '<a href="add_proizvcalendar.php" class="b">Добавить/обновить производственный календарь</a>';
                echo '<br>';
                echo '<a href="/sxd" class="b">SXD</a>';
                echo '<a href="/phpmyadmin" class="b">PHPMyAdmin</a>';
                echo '<br><br><a href="sql_requests.php" class="b" style="color: red;">sql_requests.php НЕ НАЖИМАЙ, СНАЧАЛА ПОСМОТРИ В КОД, ЧТО ОНО ДЕЛАЕТ И СДЕЛАЙ КОПИЮ БД ПЕРЕД</a>';
            }

				
			echo '			
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>