<?php

//fl_tabels.php
//Важный отчёт

    //!!!Сортировка - нигде не используется??
    function cmp($a, $b)
    {
        return sort($massive, SORT_STRING);
    }


	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION['fl_calcs_tabels']);

		if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			include_once 'ffun.php';

            include_once 'variables.php';

            unset($_SESSION['fl_calcs_tabels2']);

            //тип (космет/стомат/...)
            if (isset($_GET['who'])) {
                $getWho = returnGetWho($_GET['who'], 5, array(5,6,10,4,7,13,14,15,11));
            }else{
                $getWho = returnGetWho(5, 5, array(5,6,10,4,7,13,14,15,11));
            }
            //var_dump($getWho);

            $who = $getWho['who'];
            $whose = $getWho['whose'];
            $selected_stom = $getWho['selected_stom'];
            $selected_cosm = $getWho['selected_cosm'];
            $datatable = $getWho['datatable'];
            $kabsForDoctor = $getWho['kabsForDoctor'];
            $type = $getWho['type'];

            $stom_color = $getWho['stom_color'];
            $cosm_color = $getWho['cosm_color'];
            $somat_color = $getWho['somat_color'];
            $admin_color = $getWho['admin_color'];
            $assist_color = $getWho['assist_color'];
            $sanit_color = $getWho['sanit_color'];
            $ubor_color = $getWho['ubor_color'];
            $dvornik_color = $getWho['dvornik_color'];
            $other_color = $getWho['other_color'];
            $all_color = $getWho['all_color'];


			$workers_j = array();

			//$offices_j = SelDataFromDB('spr_filials', '', '');
            //$permissions_j = SelDataFromDB('spr_permissions', '', '');
            $filials_j = getAllFilials(true, true, true);
            //var_dump($filials_j);

            //Получили список прав
            $permissions_j = getAllPermissions(false, true);
            //var_dump($permissions_j);

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
						<div class="nav">
						    <a href="fl_tabels2.php" class="b4">Отчёт по часам</a>
						    <a href="fl_tabels_check.php" class="b4">Проверка табелей</a>
						    <a href="fl_tabels_simple_pay.php" class="b">Проверка табелей 2</a>
                        </div>

						<h1>Важный отчёт</h1>';
                echo '
                        <div>
						    <a href="fl_tabel_print_choice.php?type='.$type.'" class="b4">Печать пачки</a>
						</div>';
                echo '    
					</header>
					<div id="errror"></div>
					</div>';

				echo '
						<div id="data" style="margin: 10px 0 0;">
						    <ul style="margin-left: 6px; margin-bottom: 20px;">
						        <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                                <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                    <a href="fl_tabels.php?who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
                                    <a href="fl_tabels.php?who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
                                    <a href="fl_tabels.php?who=10" class="b" style="'.$somat_color.'">Специалисты</a>
                                    <a href="fl_tabels.php?who=4" class="b" style="'.$admin_color.'">Администраторы</a>
                                    <a href="fl_tabels.php?who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
								    <a href="fl_tabels.php?who=13" class="b" style="'.$sanit_color.'">Санитарки</a>
                                    <a href="fl_tabels.php?who=14" class="b" style="'.$ubor_color.'">Уборщицы</a>
                                    <a href="fl_tabels.php?who=15" class="b" style="'.$dvornik_color.'">Дворники</a>
                                    <a href="fl_tabels.php?who=11" class="b" style="'.$other_color.'">Прочие</a>
								    <!--<a href="fl_tabels_noch.php?who=5" class="b" style="">Ночь стом.</a>
								    <a href="fl_tabels_noch.php?who=7" class="b" style="">Ночь ассист.</a>-->';

                if (in_array($_SESSION['permissions'], $workers_target_arr) || ($_SESSION['id'] == 270) || $god_mode) {
                    echo '
                                <a href="fl_tabels999.php?who=999" class="b" style="">Другие</a>';
                }
                echo '
                                </li>
						    </ul>';


                echo '
                    <div id="tabs_w">
                        <ul class="tabs">';

                //Для теста
                /*$permission['id'] = $type;
                $permission['name'] = $permissions_j[4]['name'];*/

                //вкладки по правам
                //foreach ($permissions_j as $permission){
                    /*if (($permission['id'] != 1) && ($permission['id'] != 2) && ($permission['id'] != 3) && ($permission['id'] != 8) && ($permission['id'] != 9)){
                        echo '
                            <li>
                                <a href="#tabs-' . $permission['id'] . '">
                                    ' . $permission['name'] . '
                                </a>
                            </li>';
                    }*/
                //}
                echo '
                        </ul>';

                //проходим по каждой из должностей
                //foreach ($permissions_j as $permission){
                    //Обнуляем массив
                    $workers_j = array();


                    //if (($permission['id'] != 1) && ($permission['id'] != 2) && ($permission['id'] != 3) && ($permission['id'] != 8) && ($permission['id'] != 9)){

                        //Выберем всех сотрудников с такой должностью + спец отметки
                        $query = "
                        SELECT s_w.*, opt_w_s.oklad, opt_w_s.prikaz8 FROM `spr_workers` s_w 
                          LEFT JOIN `options_worker_spec` opt_w_s ON opt_w_s.worker_id = s_w.id
                        WHERE s_w.permissions='{$type}' AND s_w.status <> '8'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                $workers_j[$arr['name']] = $arr;
                            }
                        }
                        //var_dump($query);

                        //Сортируем по имени
                        ksort($workers_j);

                        //содержимое по правам
                        echo '
                        <div id="tabs-'.$type.'" style="padding: 0;">';


                        //$tabs_rez_js .= '$( "#tabs_w'.$permission['id'].'" ).tabs();';
                        $tabs_rez_js .= '$( "#tabs_w'.$type.'" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );';
                        $tabs_rez_js .= '$( "#tabs_w'.$type.' li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );';


                        echo '
                            <div id="tabs_w'.$type.'" style="font-size: 100%;">
                                <ul class="tabs" style="font-size: 125%; height: 500px; overflow-y: scroll;">';

                        //вкладки по сотрудникам
                        foreach ($workers_j as $worker){

                            echo '
                                    <li>
                                        <a href="#tabs-' . $type . '_' . $worker['id'] . '" onclick="clearAllChecked(); hideAllErrors();">
                                            ' . $worker['name'] . '
                                            <div  class="notes_count_div">
                                                <div id="tabs_notes2_' . $type . '_' . $worker['id'].'" class="notes_count3" style="display: none;">
                                                    <i class="fa fa-exclamation-circle" aria-hidden="true" title=""></i>
                                                </div>
                                                <div id="tabs_notes_' . $type . '_' . $worker['id'].'" class="notes_count2" style="display: none;">
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

                                <div id="tabs-' . $type . '_' . $worker['id'] . '" class="workerDataAllFilials" workerData="' . $type . '_' . $worker['id'] . '" style="width: auto; float: none; border: 1px solid rgba(228, 228, 228, 0.72); margin-left: 150px; font-size: 12px;">';

                            echo '<h2>' .$permissions_j[$type]['name'].'</h2><h1>'.$worker['name'].'</h2>';

                            $tabs_rez_js .= '$( "#tabs_w'.$type.'_'.$worker['id'].'").tabs({collapsible: true, active: false});';

                            echo '
                                    <div id="tabs_w'.$type.'_'.$worker['id'].'" style="font-size: 100%;">
                                        <ul class="tabs" style="font-size: 125%; float: left;">';

                            //закладки по офисам
                            foreach ($filials_j as $office){

                                if ($office['id'] != 11) {

                                    echo '
                                            <li class="tabs-' . $type . '_' . $worker['id'] . '_' . $office['id'] . '" onclick="clearAllChecked(); hideAllErrors();">
                                                <a href="#tabs-' . $type . '_' . $worker['id'] . '_' . $office['id'] . '">
                                                    ' . $office['name2'] . '
                                                    <div class="notes_count_div">
                                                        <div id="tabs_notes2_' . $type . '_' . $worker['id'] . '_' . $office['id'] . '" class="notes_count3" style="display: none;" filial_id="' . $office['id'] . '">
                                                            <i class="fa fa-exclamation-circle" aria-hidden="true" title=""></i>
                                                        </div>
                                                        <div id="tabs_notes_' . $type . '_' . $worker['id'] . '_' . $office['id'] . '" class="notes_count2" style="display: none;" filial_id="' . $office['id'] . '">
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
                            foreach ($filials_j as $office){

                                if ($office['id'] != 11) {

                                    echo '
                                        <div id="tabs-' . $type . '_' . $worker['id'] . '_' . $office['id'] . '" style="position: relative; width: auto; float: none; border: 1px solid rgba(228, 228, 228, 0.72); font-size: 12px; margin-top: 65px;">';

                                    echo '
                                            <div class="tableTabels" style="background-color: rgba(210, 255, 167, 0.64)" id="'.$type . '_' . $worker['id'] . '_' . $office['id'].'_tabels">
                                                <!--<div style="width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);"><img src="img/wait.gif" style="float:left;"><span style="float: right;  font-size: 90%;"> обработка...</span></div>-->
                                            </div>';

                                    echo '
                                            <div class="tableDataNPaidCalcs" style="width: 665px; background-color: rgba(251, 170, 170, 0.18);" id="'.$type . '_' . $worker['id'] . '_' . $office['id'].'">
                                                <!--<div style=\'width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...<br>загрузка<br>расч. листов</span></div>-->
                                                Нет данных по необработанным расчетным листам
                                            </div>';

                                    echo '
                                            <div id="refreshID_'.$type.'_'.$worker['id'].'_'.$office['id'].'" style="position: absolute; cursor: pointer; top: -30px; right: 5px; font-size: 180%; color: #0C0C0C;" onclick="refreshOnlyThisTab($(this), '.$type . ',' . $worker['id'] . ',' . $office['id'].'); fl_addCalcsIDsINSessionForTabel([], 0, 0, 0, 0);" title="Обновить эту вкладку">
                                                <span style="font-size: 50%;">Обновить эту вкладку</span> <i class="fa fa-refresh" aria-hidden="true"></i>
                                            </div>';

                                    echo '
                                            <div id="" style="position: absolute; top: -25px; right: 160px; cursor: pointer;">
                                               <span class="b4" onclick="fl_addNewClearTabelIN(true, ' . $type . ', ' . $worker['id'] . ', ' . $office['id'] . ');">Пустой табель +</span>
                                            </div>';

                                    echo '
                                            <div id="" style="position: absolute; top: -22px; left: 5px; font-size: 130%; color: rgb(0, 123, 182)">
                                               '.$office['name'].'
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

                    //}
                //}

                echo '
                    </div>
                    <!-- Подложка только одна -->
                    <div id="overlay"></div>';

                echo '
		            <div id="doc_title">Важный отчёт - Асмедика</div>';

				echo '

				<script type="text/javascript">
				
				
				$( "#tabs_w" ).tabs();
				
				//$( "#tabs_ww" ).tabs();
				//$( "#tabs_w2" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
				'.$tabs_rez_js.'
				
				
				$(document).ready(function() {
				    //console.log(123);
				    
				    //blockWhileWaiting (true);
				    
				    var ids = "0_0_0";
				    var ids_arr = {};
				    var permission = 0;
				    var worker = 0;
				    var office = 0;


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
                        
                        var reqData = {
                            permission: permission,
                            worker: worker,
                            office: office,
                            month: "'.date("m").'",
                            year: "'.date("Y").'",
                            own_tabel: false
                        };
                        
                        
                        getTabelsfunc (thisObj, reqData);
                    });

				    //Необработанные расчеты
				    /*$(".tableDataNPaidCalcs").each(function() {
                        //console.log($(this).attr("id"));
                        
                        var thisObj = $(this);

                        ids = $(this).attr("id");
                        ids_arr = ids.split("_");
                        //console.log(ids_arr);
                         
                        permission = ids_arr[0];
                        worker = ids_arr[1];
                        office = ids_arr[2];showRefundAdd
                        
                        var reqData = {
                            permission: permission,
                            worker: worker,
                            office: office,
                            month: "'.date("m").'",
                            year: "'.date("Y").'",
                            own_tabel: false
                        };
                        
                        
                        getCalculatesfunc (thisObj, reqData);
                    });*/
                    
                    $(".workerDataAllFilials").each(function(){
                        //console.log($(this).attr("id"));
                    
                        //var thisObj = $(this);

                        ids = $(this).attr("workerData");
                        ids_arr = ids.split("_");
                        //console.log(ids_arr);
                         
                        permission = ids_arr[0];
                        worker = ids_arr[1];
                        
                        var reqData = {
                            permission: permission,
                            worker: worker,
                            month: "'.date("m").'",
                            year: "'.date("Y").'",
                            own_tabel: false
                        };
                        
                        
                        getCalculatesfunc2 (reqData);
                        
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