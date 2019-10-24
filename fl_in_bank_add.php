<?php

//fl_in_bank_add.php
//Добавить взнос в банк

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            //Опция доступа к филиалам конкретных сотрудников
            $optionsWF = getOptionsWorkerFilial($_SESSION['id']);
            //var_dump($optionsWF);

            if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){

                $filials_j = getAllFilials(true, true, true);
                //var_dump($filials_j);
                //Получили список прав
                //$permissions = SelDataFromDB('spr_permissions', '', '');
                //var_dump($permissions);

                //Дата
                //операции со временем
                if (isset($_GET['m'])){
                    $month = $_GET['m'];
                }else {
                    $month = date('m');
                }
                if (isset($_GET['y'])) {
                    $year = $_GET['y'];
                }else{
                    $year = date('Y');
                }
                if (isset($_GET['d'])) {
                    $day = $_GET['d'];
                }else{
                    $day = date("d");
                }
    //            var_dump($day);
    //            var_dump($month);
    //            var_dump($year);

                //Филиал
                if (isset($_GET['filial_id'])) {
                    $filial_id = $_GET['filial_id'];
                }else{
                    $filial_id = 15;
                }

                if (!$god_mode) {
                    if (!in_array($filial_id, $optionsWF[$_SESSION['id']])) {
                        $filial_id = $optionsWF[$_SESSION['id']][0];
                    }
                }

                echo '
                    <div id="status">
                        <header>
                            <div class="nav">
                                <a href="fl_consolidated_report_admin.php?filial_id='.$filial_id.'" class="b">Сводный отчёт по филиалу</a>
                            </div>
                            <h2>Добавить в банк</h2>
                            <a href="fl_in_bank_all.php?filial='.$filial_id.'" class="b">Смотреть все</a>
                        </header>';

                echo '
                        <div id="data">';

                echo '
                            <form action="announcing_add_f.php">';

                echo '
                            <div class="cellsBlock3">
                                <div class="cellLeft">Филиал</div>
                                <div class="cellRight">
                                    <select name="SelectFilial" id="SelectFilial">';

                if (!empty($filials_j)) {
                    foreach ($filials_j as $f_id => $filials_j_data) {

                        if (in_array($f_id, $optionsWF[$_SESSION['id']]) || $god_mode) {
                            $selected = '';
                            if ($filial_id == $f_id) {
                                $selected = 'selected';
                            }
                            echo "<option value='" . $f_id . "' $selected>" . $filials_j_data['name'] . "</option>";
                        }
                    }
                }

                echo '
                                    </select>
                                </div>
                            </div>';


                echo '
                            <div class="cellsBlock400px">
                                <div class="cellLeft" style="font-size: 90%;">
                                    Дата
                                </div>
                                <div class="cellRight">
                                    <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" value="'.$day.'.'.$month.'.'.$year.'" onfocus="this.select();_Calendar.lcs(this)"
                                                onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off">
                                    <!--<span class="button_tiny" style="font-size: 80%; cursor: pointer" onclick="iWantThisDate2(\'fl_createDailyReport.php?filial_id=' . $filial_id . '\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>-->
                                </div>
                            </div>';

                echo '
                            <div class="cellsBlock3">
                                <div class="cellLeft">
                                    Сумма руб.<br>
                                </div>
                                <div class="cellRight">
                                    <input type="text" size="30" name="summ" id="summ" value="" placeholder="" style="padding: 5px;">
                                    <label id="summ_error" class="error"></label>								
                                </div>
                            </div>';

                echo '
                            <div class="cellsBlock3">
                                <div class="cellLeft">Комментарий</div>
                                <div class="cellRight">
                                    <textarea name="comment" id="comment" cols="60" rows="10"></textarea>
                                </div>
                            </div>';


                echo '
                                <div id="errror"></div>
                                <input type="button" class="b" value="Добавить" onclick="fl_showAjaxAddInBank(\'add\')">
                            </form>';

                echo '
                        </div>
                    </div>';

            }else{
                echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
            }
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>