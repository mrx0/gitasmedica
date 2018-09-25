<?php

//fl_my_tabels.php
//Табели сотрудников

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        include_once 'DBWork.php';
        include_once 'functions.php';

		//var_dump($_SESSION);
		//var_dump(date("m"));

        $filials_j = getAllFilials(true, true);
        //var_dump($filials_j);

        echo '
            <div id="tabs_w">
                <ul>';
        foreach ($filials_j as $filials_j_data) {
            if ($filials_j_data['id'] != 11) {
                echo '
                    <li id="filial_' . $filials_j_data['id'].'">
                        <a href="#tabs-' . $filials_j_data['id'] . '">
                            '.$filials_j_data['name2'].'
                            <div class="notes_count_div">
                                <div id="tabs_notes2_' . $_SESSION['permissions'] . '_' . $_SESSION['id'] . '_' . $filials_j_data['id'] . '" class="notes_count3" style="display: none;">
                                    <i class="fa fa-exclamation-circle" aria-hidden="true" title=""></i>
                                </div>
                            </div>
                        </a>
                    </li>';
            }
        }

        echo '
                </ul>';

        foreach ($filials_j as $filials_j_data) {
            if ($filials_j_data['id'] != 11) {
                echo '
                <div id="tabs-'.$filials_j_data['id'].'">';
                echo '
                <h1>' . $filials_j_data['name'] . '</h1>
                <div id="'.$_SESSION['permissions'] . '_' . $_SESSION['id'] . '_' . $filials_j_data['id'].'_tabels" class="tableTabels" style="background-color: rgba(210, 255, 167, 0.64);">
                    <!--<div style="width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);"><img src="img/wait.gif" style="float:left;"><span style="float: right;  font-size: 90%;"> обработка...</span></div>-->
                </div>
                <div id="'.$_SESSION['permissions'] . '_' . $_SESSION['id'] . '_' . $filials_j_data['id'].'_calcs" class="tableDataNPaidCalcs" style="width: 80%; background-color: rgba(251, 170, 170, 0.18);">
                </div>';
                echo '
                </div>';
            }
        }

        echo '
            </div>';

        echo '

            <script type="text/javascript">

                $( "#tabs_w" ).tabs();

                $(document).ready(function() {

                    var ids = "0_0_0";
                    var ids_arr = {};
                    var permission = 0;
                    var worker = 0;
                    var office = 0;
                    
				    //Необработанные расчеты
				    $(".tableDataNPaidCalcs").each(function() {
                        //console.log($(this).attr("id"));
                        
                        var thisObj = $(this);

                        ids = $(this).attr("id");
                        ids_arr = ids.split("_");
                        //console.log(ids_arr);
                         
                        permission = ids_arr[0];
                        worker = ids_arr[1];
                        office = ids_arr[2];
                        
                        var certData = {
                            permission: permission,
                            worker: worker,
                            office: office,
                            month: "'.date("m").'",
                            year: "'.date("Y").'",
                            own_tabel: false
                        };
                        
                        
                        getCalculatesfunc (thisObj, certData);
                    });
                    
                    //Табели
                    $(".tableTabels").each(function() {
                        //console.log($(this).attr("id"));

                        var thisObj = $(this);

                        ids = $(this).attr("id");
                        ids_arr = ids.split("_");
                        //console.log(ids_arr);

                        permission = ids_arr[0];
                        worker = ids_arr[1];
                        office = ids_arr[2];

                        var certData = {
                            permission: permission,
                            worker: worker,
                            office: office,
                            month: "'.date("m").'",
                            year: "'.date("Y").'",
                            own_tabel: true
                        };
                        
                        getTabelsfunc (thisObj, certData);
                     });
                });
                
		    </script>';


	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>