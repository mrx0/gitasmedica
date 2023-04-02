<?php

//fl_remark_to_employee_add.php
//Добавить замечание сотруднику

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';

            $filials_j = getAllFilials(false, false, false);
            //var_dump($filials_j);

//            $invoice_id = 0;
//
//            if (isset($_GET['invoice_id'])){
//                $invoice_id = $_GET['invoice_id'];
//            }

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
                $current_filial = 16;
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
            //var_dump($current_filial);

            echo '
            <div id="status">
                <header>
                    <div class="nav">
                        <a href="remarks_to_employees.php" class="b">Все замечания</a>
                        <!--<a href="giveout_cash_all.php?filial_id=' . $current_filial . '&d=' . $day . '&m=' . $month . '&y=' . $year.'" class="b">Расходные ордеры</a>
                        <a href="fl_consolidated_report_admin.php" class="b">Сводный отчёт</a>-->
                    </div>
                    <h2>Новое замечание сотруднику</h2>
                </header>';

            echo '
                <div id="data">';

            echo '
                    <div class="cellsBlock2">
                        <div class="cellRight" style="font-size: 85%; color: #7D7D7D; padding-left: 11px;">
                            Введите дату: 
                            <input type="text" id="date_in" name="date_in" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.$day.'.'.$month.'.'.$year.'" onfocus="this.select();_Calendar.lcs(this)" 
                                onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
                        </div>
                    </div>';

            echo '
                    <div class="cellsBlock2">
                        <div class="cellRight">
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                    Укажите сотрудника
                                </li>  
                                <li style="font-size: 110%; margin-bottom: 5px;">                      
                                    <input type="text" size="30" name="searchdata4" id="search_client4" placeholder="Минимум три буквы для поиска" value="" class="who4" autocomplete="off">
                                    <ul id="search_result4" class="search_result4"></ul>
                                </li>
                        </div>
                    </div>';

            echo '
                    <div class="cellsBlock2">
                        <div class="cellRight">
                            <ul style="margin-left: 6px; margin-bottom: 10px;">
                                <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                    Текст замечания
                                </li>
                                <li style="font-size: 90%; margin-bottom: 5px;">
                                    <textarea name="comment" id="comment" cols="50" rows="10"></textarea>
                                </li>
                            </ul>
                        </div>
                    </div>';

            echo '
                    <div>
                        <div id="errror"></div>
                        <input type="button" class="b" value="Сохранить" onclick="Ajax_remarkToEmployee_add(\'add\')">
                    </div>
                </div>

                
            </div>
            
            <div id="doc_title">Новое замечание сотруднику - Асмедика</div>
            
            <!-- Подложка только одна -->
            <div id="overlay"></div>';

            //Скрипты которые грузят данные при загрузке
            echo '
                <script>
//                    $(document).ready(function() {
//                        $(\'#type\').change(function(){
//                            //console.log($(\'#type\').val());
//                            
//                            if ($(\'#type\').val() != 0){
//                                $(\'#additional_info_block\').hide();
//                                $(\'#additional_info_block\').removeClass(\'cellRight\');
//                            }else{
//                                $(\'#additional_info_block\').show();
//                                $(\'#additional_info_block\').addClass(\'cellRight\');
//                            }
//                        });
//                    })
                </script>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>