<?php

//individual_add.php
//Добавить занятие

    require_once 'header.php';

    if ($enter_ok){
        require_once 'header_tags.php';

        if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode) {
            echo '
            <script type="text/javascript" src="js/webtricks_demo_js_jquery.form.js"></script>
            ';

            include_once 'DBWork.php';
            include_once 'functions.php';

            $day = date("d");
            $month = date("m");
            $year = date("Y");

            //$offices = SelDataFromDB('spr_filials', '', '');
            $filials_j = getAllFilials(false, false, false);
            //Получили список прав
            //$permissions_j = SelDataFromDB('spr_permissions', '', '');
            //var_dump($permissions_j);
            //Получили список прав
            $permissions_j = getAllPermissions(false, true);

            //!!!Массив тех, кому видно заявку по умолчанию, потому надо будет вывести это в базу или в другой файл
            $permissionsWhoCanSee_arr = array(2, 3, 8, 9);

            //!!! для теста
            //unset($_SESSION['univer']);

            //Данные для подгрузки, если данные в сессии уже есть и не надо их снова добавлять
            $univer_exist_data = array();
            $univer_exist_data['file_data'] = '';

            //Если есть права, сразу создадим в сессии переменную для задания, если её нет
            //или берём оттуда данные, если она есть
            if (!isset($_SESSION['univer'])) {
                $_SESSION['univer'] = array();

                $task = array();
                $task['id'] = $task_id = 0;
                $task['file_data'] = $file_data = array();
                $task['theme'] = $task_theme = '';
                $task['descr'] = $task_descr = '';
                $task['workers'] = $task_workers = array();
                $task['workers_type'] = $task_workers_type = array();
                $task['filial'] = $task_filial = array();
                $task['status'] = $task_status = 0;

                $_SESSION['univer'] = $task;
            }else{
                $task_id = $_SESSION['univer']['id'];
                $file_data = $_SESSION['univer']['file_data'];
                $task_theme = $_SESSION['univer']['theme'];
                $task_descr = $_SESSION['univer']['descr'];
                $task_workers = $_SESSION['univer']['workers'];
                $task_workers_type = $_SESSION['univer']['workers_type'];
                $task_filial = $_SESSION['univer']['filial'];
                $task_status = $_SESSION['univer']['status'];

                //!!! переделать, тут всегда всё пусто.
                if (!empty($file_data)){
                    $univer_exist_data['file_data'] = '<div style="width: 60%; margin: 0 10px 10px; font-size: 85%; color: #333; border: 1px dashed rgb(255 0 0); padding: 7px;">Уже подгружен файл <b>'.$file_data['name'].'</b>. Загрузите новый файл и он будет заменён.</div>';
                }
            }

//            if (!isset($_SESSION['univer']['data'])) {
//                $_SESSION['univer']['items_data'] = array();
//            }
//            var_dump($_SESSION['univer']);
//            var_dump($task_id);

            echo '
				<div id="status">
					<header>
                         <div class="nav">
                            <a href="individuals.php" class="b">Индивидуальные занятия</a>
                        </div>
						<h2>Добавить занятие</h2>
						Заполните поля и нажмите <b>Сохранить</b>
					</header>';
            echo '
					<div id="data">';

            echo '
						<div class="cellsBlock3">
							<div class="cellLeft">Тема занятия/обсуждения</div>
							<div class="cellRight">
								<textarea name="descr" id="descr" cols="60" rows="10">'.$task_descr.'</textarea>
								<label id="descr_error" class="error"></label>
							</div>
						</div>';

            echo '
                        <div class="cellsBlock3">
                            <div class="cellLeft">
                                Для кого<!-- <br><span style="font-size: 80%; color: #555;">если необходимо указать конкретных сотрудников</span>--><br>
                            </div>
                            <div class="cellRight">
                                <input type="text" size="30" name="searchdata2" id="search_client2" placeholder="Введите ФИО" value="" class="who2"  autocomplete="off" style="width: 90%;">
                                <ul id="search_result2" class="search_result2"></ul><br />                              
                            </div>
                        </div>';

            echo '
                        <div class="cellsBlock3">
                            <div class="cellLeft">
                                Дата
                            </div>
                            <div class="cellRight">
                                <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="text-align: inherit; color: rgb(30, 30, 30); font-size: 12px;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)"  
                                    onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">                            
                            </div>
                        </div>';

            echo '
                        <div id="errror"></div>
                        <input type="button" class="b" value="Сохранить" onclick=Ajax_individual_add(\'add\')>
                        <!--<a href="univer_add_fin.php" class="b">Далее</a>-->
                        ';



            echo '
					</div>
				</div>';

            echo '	
			<!-- Подложка только одна / Вариант подложки для затемнения области вокруг элемента блока со своим стилем в CSS -->
			<div id="layer"></div>';

            echo '
             <div id="doc_title">Добавить ндивидуальное занятие - Асмедика</div>';

        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }

    }else{
        header("location: enter.php");
    }

require_once 'footer.php';

?>