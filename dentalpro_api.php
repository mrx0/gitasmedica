<?php

//dentalpro_api.php
//Тест API DentalPro

    require_once 'header.php';
    require_once 'blocks_dom.php';

    if ($enter_ok){
        require_once 'header_tags.php';
        include_once 'functions.php';

        require 'variables.php';

            //if (($clients['see_all'] == 1) || ($clients['see_own'] == 1) || $god_mode){

//        if (isset($_POST['type'])){
            //$_POST['type'] = 5;

            //операции со временем
            $day = date('d');
            $month = date('m');
            $year = date('Y');

            if (isset($_GET['d']) && isset($_GET['m']) && isset($_GET['y'])){
                //операции со временем
                $day = $_GET['d'];
                $month = $_GET['m'];
                $year = $_GET['y'];
            }

            if (!isset($day) || $day < 1 || $day > 31)
                $day = date("d");
            if (!isset($month) || $month < 1 || $month > 12)
                $month = date("m");
            if (!isset($year) || $year < 2010 || $year > 2037)
                $year = date("Y");

            //Приводим месяц к виду 01 02 09 ...
            $month = dateTransformation ($month);


            echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <!--<a href="fl_consolidated_report_admin.php?filial_id=&m=&y=" class="b">Выгрузка данных из DentalPro</a>-->
                        </div>
                        <h2>Выгрузка данных из DentalPro</h2>
                    </header>';


        //Календарик
        echo '
	
                    <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: left; margin-bottom: 10px;">
                        <div style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="?" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></div>
                        <div>
                            <span style="color: rgb(125, 125, 125);">
                                Изменить дату:
                                <a href="dentalpro_api.php?d='.explode('.', date('d.m.Y', strtotime('-1 days', gmmktime(0, 0, 0, $month, $day, $year))))[0].'&m='.explode('.', date('d.m.Y', strtotime('-1 days', gmmktime(0, 0, 0, $month, $day, $year))))[1].'&y='.explode('.', date('d.m.Y', strtotime('-1 days', gmmktime(0, 0, 0, $month, $day, $year))))[2].'" class="b4" title="Пред. день"><i class="fa fa-caret-left" aria-hidden="true"></i></a>
                                <a href="dentalpro_api.php?d='.explode('.', date('d.m.Y', strtotime('+1 days', gmmktime(0, 0, 0, $month, $day, $year))))[0].'&m='.explode('.', date('d.m.Y', strtotime('+1 days', gmmktime(0, 0, 0, $month, $day, $year))))[1].'&y='.explode('.', date('d.m.Y', strtotime('+1 days', gmmktime(0, 0, 0, $month, $day, $year))))[2].'" class="b4" title="След. день"><i class="fa fa-caret-right" aria-hidden="true"></i></a>
                                <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date(dateTransformation($day).'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
                                    onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off"> 
                                <span class="button_tiny" style="font-size: 100%; cursor: pointer" onclick="iWantThisDate2(\'dentalpro_api.php?\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
                            </span>
                        </div>
                    </li>';


            echo '
                    <div id="data" style="position: relative;">
                        <div id="errrror"></div>
                        
                    </div>
                   
                </div>
                <div id="doc_title">DentalPro API - Асмедика</div>';



            echo '
                <script type="text/javascript" src="js/jquery.tablesorter.js"></script>
				<script type="text/javascript">
				

                    $(document).ready(function() {

//                        loadAllDataFromAPI_DP();
                        loadAllDataFromAPI_DP2();
                      

                    });
                    
                    
  
                                
				</script>';

	}else{
		header("location: enter.php");
	}

	require_once 'footer.php';

?>
	