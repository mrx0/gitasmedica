<?php

//z
//Важный отчёт


    //!!!Сортировка - нигде не используется??
    function cmp($a, $b)
    {
        return sort($massive, SORT_STRING);
    }


	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			include_once 'ffun.php';

			$workers_j = array();

			$offices_j = SelDataFromDB('spr_filials', '', '');
            $permissions_j = SelDataFromDB('spr_permissions', '', '');

            $msql_cnnct = ConnectToDB ();

            if (!isset($_SESSION['fl_calcs_tabels'])){
                $_SESSION['fl_calcs_tabels'] = array();
            }

            //var_dump($_SESSION['fl_calcs_tabels']);

			if ($_POST){
			}else{

			    //Переменная для набора JS для tabs
                $tabs_rez_js = '';



				echo '
                    <div class="no_print"> 
					<header style="margin-bottom: 5px;">
						<h1>---</h1>
					</header>
					</div>';

				echo '
						<div id="data">';


                echo '
                    <div id="tabs_w">
                        <ul class="tabs">';

                //Для теста
                $permission['id'] = 5;
                $permission['name'] = $permissions_j[4]['name'];

                //вкладки по правам
                //foreach ($permissions_j as $permission){
                    if (($permission['id'] != 1) && ($permission['id'] != 2) && ($permission['id'] != 3) && ($permission['id'] != 8) && ($permission['id'] != 9)){
                        echo '
                            <li>
                                <a href="#tabs-' . $permission['id'] . '">
                                    ' . $permission['name'] . '
                                </a>
                            </li>';
                    }
                //}
                echo '
                        </ul>';

                //проходим по каждой из должностей
                //foreach ($permissions_j as $permission){
                    //Обнуляем массив
                    $workers_j = array();


                    if (($permission['id'] != 1) && ($permission['id'] != 2) && ($permission['id'] != 3) && ($permission['id'] != 8) && ($permission['id'] != 9)){

                        //Выберем всех сотрудников с такой должностью
                        $query = "SELECT * FROM `spr_workers` WHERE `permissions`='{$permission['id']}' AND `fired` = '0'";
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                $workers_j[$arr['name']] = $arr;
                            }
                        }

                        //Сортируем по имени
                        ksort($workers_j);

                        //содержимое по правам
                        echo '
                        <div id="tabs-'.$permission['id'].'" style="padding: 0;">';


                        //$tabs_rez_js .= '$( "#tabs_w'.$permission['id'].'" ).tabs();';
                        $tabs_rez_js .= '$( "#tabs_w'.$permission['id'].'" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );';
                        $tabs_rez_js .= '$( "#tabs_w'.$permission['id'].' li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );';


                        echo '
                            <div id="tabs_w'.$permission['id'].'" style="font-size: 100%;">
                                <ul class="tabs" style="font-size: 125%; height: 500px; overflow-y: scroll;">';

                        //вкладки по сотрудникам
                        foreach ($workers_j as $worker){

                            echo '
                                    <li>
                                        <a href="#tabs-' . $permission['id'] . '_' . $worker['id'] . '" onclick="$(\'input:checked\').prop(\'checked\', false); $(\'input\').parent().parent().parent().css({\'background-color\': \'\'}); ">
                                            ' . $worker['name'] . '
                                            <div  class="notes_count_div">
                                                <div id="tabs_notes2_' . $permission['id'] . '_' . $worker['id'].'" class="notes_count3" style="display: none;">
                                                    <i class="fa fa-exclamation-circle" aria-hidden="true" title=""></i>
                                                </div>
                                                <div id="tabs_notes_' . $permission['id'] . '_' . $worker['id'].'" class="notes_count2" style="display: none;">
                                                    <i class="fa fa-exclamation-circle" aria-hidden="true" title=""></i>
                                                </div>
                                            </div>
                                        </a>
                                    </li>';
                        }

                        echo '
                                </ul>';

                        //содержимое по сотрудникам
                        foreach ($workers_j as $worker){

                            echo '

                                <div id="tabs-' . $permission['id'] . '_' . $worker['id'] . '" style="width: auto; float: none; border: 1px solid rgba(228, 228, 228, 0.72); margin-left: 150px; font-size: 12px;">';

                            echo '<h2>' .$permission['name'].'</h2><h1>'.$worker['name'].'</h2>';

                            $tabs_rez_js .= '$( "#tabs_w'.$permission['id'].'_'.$worker['id'].'").tabs();';

                            echo '
                                    <div id="tabs_w'.$permission['id'].'_'.$worker['id'].'" style="font-size: 100%;">
                                        <ul class="tabs" style="font-size: 125%; float: left;">';

                            //закладки по офисам
                            foreach ($offices_j as $office){

                                if ($office['id'] != 11) {

                                    echo '
                                            <li class="tabs-' . $permission['id'] . '_' . $worker['id'] . '_' . $office['id'] . '" onclick="$(\'input:checked\').prop(\'checked\', false); $(\'input\').parent().parent().parent().css({\'background-color\': \'\'}); ">
                                                <a href="#tabs-' . $permission['id'] . '_' . $worker['id'] . '_' . $office['id'] . '">
                                                    ' . $office['name'] . '
                                                    <div class="notes_count_div">
                                                        <div id="tabs_notes2_' . $permission['id'] . '_' . $worker['id'] . '_' . $office['id'] . '" class="notes_count3" style="display: none;">
                                                            <i class="fa fa-exclamation-circle" aria-hidden="true" title=""></i>
                                                        </div>
                                                        <div id="tabs_notes_' . $permission['id'] . '_' . $worker['id'] . '_' . $office['id'] . '" class="notes_count2" style="display: none;">
                                                            <i class="fa fa-exclamation-circle" aria-hidden="true" title=""></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>';
                                }
                            }

                            echo '
                                        </ul>';

                            //содержимое по офисам
                            foreach ($offices_j as $office){

                                if ($office['id'] != 11) {
                                    echo '
                                        <div id="tabs-' . $permission['id'] . '_' . $worker['id'] . '_' . $office['id'] . '" style="position: relative; width: auto; float: none; border: 1px solid rgba(228, 228, 228, 0.72); font-size: 12px; margin-top: 65px;">';

                                    echo '
                                            <div class="tableTabels" id="'.$permission['id'] . '_' . $worker['id'] . '_' . $office['id'].'_tabels">
                                                <!--<div style="width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);"><img src="img/wait.gif" style="float:left;"><span style="float: right;  font-size: 90%;"> обработка...</span></div>-->
                                            </div>';

                                    echo '
                                            <div class="tableDataNPaidCalcs" id="'.$permission['id'] . '_' . $worker['id'] . '_' . $office['id'].'">
                                                <div style="width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);"><img src="img/wait.gif" style="float:left;"><span style="float: right;  font-size: 90%;"> обработка...</span></div>
                                            </div>';

                                    echo '
                                            <div style="position: absolute; cursor: pointer; top: 1px; right: 5px; font-size: 180%; color: #0C0C0C;" onclick="refreshOnlyThisTab($(this), '.$permission['id'] . ',' . $worker['id'] . ',' . $office['id'].');" title="Обновить">
                                                <i class="fa fa-refresh" aria-hidden="true"></i>
                                            </div>';

                                    echo '
                                        </div>';
                                }
                            }

                            echo '
                                    </div>';
                            echo '                
                                </div>';
                        }

                        echo '
                            </div>
                        </div>';

                    }
                //}

                echo '
                    </div>';



				echo '

				<script type="text/javascript">
				
				
				$( "#tabs_w" ).tabs();
				//$( "#tabs_ww" ).tabs();
				//$( "#tabs_w2" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
				'.$tabs_rez_js.'
				
				
				$(document).ready(function() {
				    //console.log(123);
				    
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
                            own_tabel: false
                        };
                        
                        
                        getTabelsfunc (thisObj, certData);
                    });
				});
				
                
				</script>';
			}
			//mysql_close();
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>