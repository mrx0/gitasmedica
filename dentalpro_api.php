<?php

//dentalpro_api.php
//Тест API DentalPro

    require_once 'header.php';
    require_once 'blocks_dom.php';

    if ($enter_ok){
            require_once 'header_tags.php';

            //if (($clients['see_all'] == 1) || ($clients['see_own'] == 1) || $god_mode){

//        if (isset($_POST['type'])){
            //$_POST['type'] = 5;



            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                            <!--<a href="fl_consolidated_report_admin.php?filial_id=&m=&y=" class="b">Выгрузка данных из DentalPro</a>-->
                        </div>
                        <h2 style="padding: 0;">Выгрузка данных из DentalPro</h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';

            echo '
                    </div>
                </div>
                <div id="doc_title">DentalPro API - Асмедика</div>';


            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';

            echo '
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
	