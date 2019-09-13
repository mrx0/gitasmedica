<?php

//fl_give_out_cash_add.php
//Добавить выдачу из кассы

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';

            $invoice_id = 0;

            if (isset($_GET['invoice_id'])){
                $invoice_id = $_GET['invoice_id'];
            }

            //Дата
            //операции со временем
            $day = date('d');
            $month = date('m');
            $year = date('Y');

            //Или если мы смотрим другой месяц
            if (isset($_GET['m']) && isset($_GET['y']) && isset($_GET['d'])) {
                $day = $_GET['d'];
                $month = $_GET['m'];
                $year = $_GET['y'];
            }

            //Филиал
            //Филиал
            if (isset($_SESSION['filial'])) {
                $current_filial = $_SESSION['filial'];
            } else {
                $current_filial = 15;
            }

            if (($finances['see_all'] == 1) || $god_mode) {
                if (isset($_GET['filial_id'])) {
                    $current_filial = $_GET['filial_id'];
                }
            }

//            if (isset($_GET['filial_id'])) {
//                $current_filial  = $_GET['filial_id'];
//            }else{
//                if (isset($_SESSION['filial'])) {
//                    $current_filial  = $_SESSION['filial'];
//                }else{
//                    $current_filial = 15;
//                }
//            }

            echo '
            <div id="status">
                <header>
                    <div class="nav">
                        <a href="stat_cashbox.php" class="b">Касса</a>
                        <a href="giveout_cash.php?filial_id=' . $current_filial . '&d=' . $day . '&m=' . $month . '&y=' . $year.'" class="b">Расходные ордеры</a>
                        <a href="fl_consolidated_report_admin.php" class="b">Сводный отчёт</a>
                    </div>
                    <h2>Новый расходный ордер</h2>
                    <ul style="margin-left: 6px; margin-bottom: 10px;">
                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                            
                        </li>';
            //Календарик
            echo '
                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                            <span style="color: rgb(125, 125, 125);">
                                Дата внесения: <input type="text" id="date_in" name="date_in" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.$day.'.'.$month.'.'.$year.'" onfocus="this.select();_Calendar.lcs(this)" 
                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
                            </span>
                        </li>';
            echo '
                    </ul>   
                </header>';

            echo '
                <div id="data">';

            echo '
                    <div class="cellsBlock2">
                        <div class="cellRight">
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                    Сумма (руб.) <label id="summ_error" class="error"></label>
                                </li>
                                <li style="margin-bottom: 5px;">
                                    <input type="text" size="15" name="summ" id="summ" placeholder="Введите сумму" value="" class="who2"  autocomplete="off">
                                </li>
                            </ul>
                        </div>
                    </div>';

            $give_out_cash_types_j = SelDataFromDB('spr_cashout_types', '', '');
            //var_dump($give_out_cash_j);

            echo '		
                    <div class="cellsBlock2">
                        <div class="cellRight">
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                    Тип <label id="type_error" class="error">
                                </li>
                                <li style="font-size: 90%; margin-bottom: 5px;">';

            echo '
                                    <select name="type" id="type">';
            echo '
                                        <option value="0" selected>Прочее</option>';
            if ($give_out_cash_types_j != 0){
                for ($i=0; $i<count($give_out_cash_types_j); $i++){
                    echo "<option value='".$give_out_cash_types_j[$i]['id']."'>".$give_out_cash_types_j[$i]['name']."</option>";

                }
            }

            echo '
                                    </select>';
            echo '
                                </li>
                            </ul>
                        </div>';


            echo '
                   
                        <div id="additional_info_block" class="cellRight">
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                    Описание <!--если выбрано "Прочее"-->
                                </li>
                                <li style="font-size: 90%; margin-bottom: 5px;">
                                    <textarea name="additional_info" id="additional_info" cols="35" rows="2"></textarea>
                                </li>
                            </ul>
                        </div>
                    </div>';

            echo '		
                    <div class="cellsBlock2">
                        <div class="cellRight">
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                    Филиал <label id="filial_error" class="error">
                                </li>
                                <li style="font-size: 90%; margin-bottom: 5px;">';

            if (($finances['see_all'] == 1) || $god_mode){

//                if (!isset($_SESSION['filial'])){
//                    $current_filial = 15;
//                }else{
//                    $current_filial = $_SESSION['filial'];
//                }

                $offices_j = SelDataFromDB('spr_filials', '', '');

                echo '
                                    <select name="filial" id="filial">';
                if ($offices_j != 0){
                    for ($i=0;$i<count($offices_j);$i++){
                        echo "<option value='".$offices_j[$i]['id']."' ", $current_filial == $offices_j[$i]['id'] ? "selected" : "" ,">".$offices_j[$i]['name']."</option>";
                    }
                }
                echo '
                                    </select>';
            }else{
                $offices_j = SelDataFromDB('spr_filials', $_SESSION['filial'], 'offices');
                if ($offices_j != 0) {
                    echo $offices_j[0]['name'].'
                    <input type="hidden" id="filial" name="filial" value="'.$_SESSION['filial'].'">';
                }
            }
            echo '
                                </li>
                            </ul>
                        </div>
                    </div>';

            echo '
                    <div class="cellsBlock2">
                        <div class="cellRight">
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                    Комментарий
                                </li>
                                <li style="font-size: 90%; margin-bottom: 5px;">
                                    <textarea name="comment" id="comment" cols="35" rows="2"></textarea>
                                </li>
                            </ul>
                        </div>
                    </div>';

            echo '
                    <div>
                        <div id="errror"></div>
                        <input type="button" class="b" value="Сохранить" onclick="showGiveOutCashAdd(\'add\')">
                    </div>
                </div>

                
            </div>
            
            <div id="doc_title">Новый расходный ордер - Асмедика</div>
            
            <!-- Подложка только одна -->
            <div id="overlay"></div>';

            //Скрипты которые грузят данные при загрузке
            echo '
                <script>
                    $(document).ready(function() {
                        $(\'#type\').change(function(){
                            //console.log($(\'#type\').val());
                            
                            if ($(\'#type\').val() != 0){
                                $(\'#additional_info_block\').hide();
                                $(\'#additional_info_block\').removeClass(\'cellRight\');
                            }else{
                                $(\'#additional_info_block\').show();
                                $(\'#additional_info_block\').addClass(\'cellRight\');
                            }
                        });
                    })
                </script>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>