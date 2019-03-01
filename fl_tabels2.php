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
		//var_dump($_SESSION);

		if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			include_once 'ffun.php';
            include_once 'widget_calendar.php';

            $dop = '';
            $dopWho = '';
            $dopDate = '';
            $dopFilial = '';
            //$di = 0;

            //тип график (космет/стомат/...)
            $who = '&who=4';
            $whose = 'Администраторов ';
            $selected_stom = ' selected';
            $selected_cosm = ' ';
            $datatable = 'scheduler_admin';

            //тип (космет/стомат/...)
            if (isset($_GET['who'])){
                if ($_GET['who'] == 5){
                    $who = '&who=5';
                    $whose = 'Стоматологи ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_stom';
                    $kabsForDoctor = 'stom';
                    $type = 5;

                    $stom_color = 'background-color: #fff261;';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 6){
                    $who = '&who=6';
                    $whose = 'Косметологи ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_cosm';
                    $kabsForDoctor = 'cosm';
                    $type = 6;

                    $stom_color = '';
                    $cosm_color = 'background-color: #fff261;';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 10){
                    $who = '&who=10';
                    $whose = 'Специалистов ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';
                    $type = 10;


                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = 'background-color: #fff261;';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 4){
                    $who = '&who=4';
                    $whose = 'Администраторов ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 4;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = 'background-color: #fff261;';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 7){
                    $who = '&who=7';
                    $whose = 'Ассистенты ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 7;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = 'background-color: #fff261;';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 11){
                    $who = '&who=11';
                    $whose = 'Прочее ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 11;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = 'background-color: #fff261;';
                    $all_color = '';
                }else{
                    $who = '&who=4';
                    $whose = 'Администраторов ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_admin';
                    $kabsForDoctor = 'admin';
                    $type = 4;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = 'background-color: #fff261;';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }
            }else{
//                $who = '';
//                $whose = 'Все ';
//                $selected_stom = ' selected';
//                $selected_cosm = ' ';
//                $datatable = 'scheduler_stom';
//                $kabsForDoctor = 'stom';
//                $type = 0;
//
//                $stom_color = '';
//                $cosm_color = '';
//                $somat_color = '';
//                $admin_color = '';
//                $assist_color = '';
//                $other_color = '';
//                $all_color = 'background-color: #fff261;';

                $who = '&who=4';
                $whose = 'Администраторов ';
                $selected_stom = ' selected';
                $selected_cosm = ' ';
                $datatable = 'scheduler_admin';
                $kabsForDoctor = 'admin';
                $type = 4;

                $stom_color = '';
                $cosm_color = '';
                $somat_color = '';
                $admin_color = 'background-color: #fff261;';
                $assist_color = '';
                $other_color = '';
                $all_color = '';
            }

            if (isset($_GET['m']) && isset($_GET['y'])){
                //операции со временем
                $month = $_GET['m'];
                $year = $_GET['y'];
            }else{
                //операции со временем
                $month = date('m');
                $year = date('Y');
            }

            //Сегодняшняя дата
            $day = date("d");
            $cur_month = date("m");
            $cur_year = date("Y");

            foreach ($_GET as $key => $value){
                if (($key == 'd') || ($key == 'm') || ($key == 'y'))
                    $dopDate  .= '&'.$key.'='.$value;
                if ($key == 'filial'){
                    $dopFilial .= '&'.$key.'='.$value;
                    $dop .= '&'.$key.'='.$value;
                }
                if ($key == 'who'){
                    $dopWho .= '&'.$key.'='.$value;
                    $dop .= '&'.$key.'='.$value;
                }
            }

            $today = date("Y-m-d");





			$workers_j = array();

			//$offices_j = SelDataFromDB('spr_filials', '', '');
            //$permissions_j = SelDataFromDB('spr_permissions', '', '');
            $filials_j = getAllFilials(false, true, true);
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
				echo '
                    <div class="no_print"> 
					<header style="margin-bottom: 5px;">
						<h1>Важный отчёт</h1>';
                echo '
                        <div>
						    <!--<a href="fl_tabel_print_choice.php" class="b4">Печать пачки</a>-->
						</div>';
                echo '    
					</header>
					</div>';

				echo '
                    <div id="data" style="margin: 10px 0 0;">
                        <ul style="margin-left: 6px; margin-bottom: 20px;">
                            <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                            <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                <a href="fl_tabels.php?who=5" class="b" style="">Стоматологи</a>
                                <a href="fl_tabels.php?who=6" class="b" style="">Косметологи</a>
                                <a href="fl_tabels.php?who=10" class="b" style="">Специалисты</a>
                                <a href="fl_tabels2.php?who=4" class="b" style="'.$admin_color.'">Администраторы</a>
                                <a href="fl_tabels2.php?who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
                            </li>';




                //Соберем массив сотрудников
                $workers_j = array();

                //Выберем всех сотрудников с такой должностью
                //$query = "SELECT * FROM `spr_workers` WHERE `permissions`='{$type}' AND `status` <> '8'";

                $query = "SELECT sw.*, sc.name AS cat_name, sc.id AS cat_id
                FROM `spr_workers` sw  
                LEFT JOIN `journal_work_cat` jwcat ON sw.id = jwcat.worker_id
                LEFT JOIN `spr_categories` sc ON jwcat.category = sc.id
                WHERE sw.permissions = '".$type."'  AND sw.status <> '8'
                ORDER BY sw.full_name ASC";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        $workers_j[$arr['name']] = $arr;
                    }
                }

                //Сортируем по имени
                ksort($workers_j);


                echo '<div class="no_print">';
                echo widget_calendar ($month, $year, 'fl_tabels2.php', $dop);
                echo '</div>';

                echo '
                        </ul>';


                //Процент с выручки для этого типа
                $revenue_percent_j = array();

                $arr = array();
                $rez = array();

                $query = "SELECT * FROM `fl_spr_revenue_percent` WHERE `permission` = '{$type}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        if (!isset($revenue_percent_j[$arr['filial_id']])){
                            $revenue_percent_j[$arr['filial_id']] = array();
                        }
                        if (!isset($revenue_percent_j[$arr['filial_id']][$arr['category']])){
                            $revenue_percent_j[$arr['filial_id']][$arr['category']] = array();
                        }
                        $revenue_percent_j[$arr['filial_id']][$arr['category']] = $arr;
                    }
                }
                //var_dump($revenue_percent_j);

                //Получаем нормы смен для этого типа
                $arr = array();
                $normaSmen = array();

                $query = "SELECT * FROM `fl_spr_normasmen` WHERE `type` = '$type'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        //Раскидываем в массив
                        $normaSmen[$arr['month']] = $arr['count'];
                    }
                }

                //Соберём часы за месяц отовсюду для этого типа
                $arr = array();
                $hours_j = array();

                $query = "SELECT * FROM `fl_journal_scheduler_report` WHERE `type` = '$type' AND `month` = '$month' AND `year` = '$year'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        //Раскидываем в массив
                        if (!isset($hours_j[$arr['worker_id']])) {
                            $hours_j[$arr['worker_id']] = array();
                        }
                        if (!isset($hours_j[$arr['worker_id']][$arr['filial_id']])) {
                            $hours_j[$arr['worker_id']][$arr['filial_id']] = 0;
                        }
                        //array_push($hours_j, $arr);
                        $hours_j[$arr['worker_id']][$arr['filial_id']] += $arr['hours'];

                    }
                }
                //var_dump($hours_j);

                //Календарная сетка
                echo '
                        <table style="border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                            <tr class="<!--sticky f-sticky-->">
                                <td style="width: 260px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><i>ФИО</i></td>
                                <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><i>Категория</i></b></td>
                                <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><i>Прикреплён</i></b></td>
                                <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><i>Часы</i><br><span style="color: rgb(158, 158, 158); font-size: 80%;">всего/ норма/ %</span></td>
                                <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><i>% от выручки</i></td>
                                <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><i>Выручка</i></td>
                                <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><i>Итого к выплате</i></td>
                                ';
                echo '
                            </tr>';

                if (!empty($workers_j)) {
                    foreach ($workers_j as $worker_data) {
                        //var_dump($worker_data);

                        $bgColor = '';
                        //Если в декрете, выделим
                        if ($worker_data['status'] == 6){
                            $bgColor = 'background-color: rgba(213, 22, 239, 0.13)';
                        }

                        //var_dump($worker_data);
                        $haveFilial = true;
                        $haveCategory = true;
                        $worker_сategory_id = 0;
                        $worker_filial_id = 0;
                        $worker_revenue_percent = 0;

                        echo '
                                <tr class="cellsBlockHover workerItem" worker_id="'.$worker_data['id'].'" style="'.$bgColor.'">
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>'.$worker_data['full_name'].'</b></td>
                                    <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">';

                        //Категория
                        if (($worker_data['cat_id'] != NUll) && ($worker_data['cat_name'] != NUll)) {
                            echo $worker_data['cat_name'];
                            $worker_сategory_id = $worker_data['cat_id'];
                        }else{
                            echo '<span style="color: rgb(243, 0, 0);">не указано</span>';
                            $haveCategory =false;
                        }

                        echo '
                                    </td>
                                    <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">';

                        //Если есть привязка к филиалу
                        if ($worker_data['filial_id'] != 0){
                            echo $filials_j[$worker_data['filial_id']]['name2'];
                            $worker_filial_id = $worker_data['filial_id'];
                        }else{
                            echo '<span style="color: rgb(243, 0, 0);">не прикреплен</span>';
                            $haveFilial =false;
                        }
                        echo '
                                    </td>
                                    <td style="width: 120px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">';

                        //Смены часы
                        if (isset($hours_j[$worker_data['id']])){
                            $w_hours = array_sum($hours_j[$worker_data['id']]);
                            $w_normaSmen = $normaSmen[(int)$month]*12;
                            $w_percentHours = number_format($w_hours * 100 / $w_normaSmen, 2, ',', '');
                            echo '
                                        <div style="margin-bottom: 15px;">'.$w_hours.'/ '.$w_normaSmen.'/ '.$w_percentHours.'</div>';

                            //Нарисуем табличку со всеми филиалами
                            echo '<table style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; margin:5px; font-size: 80%;">';
                            foreach ($hours_j[$worker_data['id']] as $filial_id => $hours_data){
                                echo '<tr><td>'.$filials_j[$filial_id]['name2'].'</td><td style="text-align: right; width: 39px;">'.$hours_data.'</td></tr>';
                            }
                            echo '</table>';

                        }else{
                            echo '<span style="color: rgb(243, 0, 0);">не работал</span>';
                        }

                        echo '
                                    </td>
                                    <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; font-size: 110%;">';

                        //% от выручки
                        if ($haveCategory && $haveFilial){
                            echo $revenue_percent_j[$worker_filial_id][$worker_сategory_id]['value'];
                            $worker_revenue_percent = $revenue_percent_j[$worker_filial_id][$worker_сategory_id]['value'];
                        }else{
                            echo '<span style="color: rgb(243, 0, 0);">0.00</span>';
                        }
                        echo '
                                    </td>
                                    <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">';

                        //Выручка

                        echo '
                                    </td>
                                    <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">';

                        //Итого

                        echo '
                                    </td>                                    
                                </tr>';
                    }
                }
                echo '
                        </table>';
                echo '
                    </div>';

                echo '
		            <div id="doc_title">Важный отчёт - Асмедика</div>';

				echo '

				<script type="text/javascript">

				$(document).ready(function() {
				    //console.log(123);
				    
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