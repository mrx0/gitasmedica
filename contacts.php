<?php

//contacts.php
//Список сотрудников

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($workers['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			//var_dump($_SESSION);

            $filials_j = getAllFilials(false, false);
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Список сотрудников</h1>';

            $contacts = array();

            $who = '&who=5';
            $whose = 'Стоматологов ';
            $selected_stom = ' selected';
            $selected_cosm = ' ';
            $datatable = 'scheduler_stom';

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
                    $whose = 'Специалисты ';
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
                    $whose = 'Администраторы ';
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
                    $whose = 'Ассистенты ';
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
                }
            }else{
                $who = '';
                $whose = 'Все ';
                $selected_stom = ' selected';
                $selected_cosm = ' ';
                $datatable = 'scheduler_stom';
                $kabsForDoctor = 'stom';
                $type = 0;

                $stom_color = '';
                $cosm_color = '';
                $somat_color = '';
                $admin_color = '';
                $assist_color = '';
                $other_color = '';
                $all_color = 'background-color: #fff261;';
            }

            if ($_GET){

//                $query = "SELECT sw.*, sc.name AS category
//                FROM `journal_work_cat` jwcat
//                RIGHT JOIN (
//                  SELECT * FROM `spr_categories`
//                ) sc ON sc.id = jwcat.worker_id
//                RIGHT JOIN (
//                  SELECT * FROM `spr_workers` s_w WHERE s_w.permissions = '".$type."'
//                ) sw ON sw.id = jwcat.worker_id";


                $query = "SELECT sw.*, sc.name AS category
                FROM `spr_workers` sw  
                LEFT JOIN `journal_work_cat` jwcat ON sw.id = jwcat.worker_id
                LEFT JOIN `spr_categories` sc ON jwcat.category = sc.id
                WHERE sw.permissions = '".$type."'
                ORDER BY sw.full_name ASC";

                //var_dump($query);

            }else {
                //$contacts = SelDataFromDB('spr_workers', '', '');

                $query = "SELECT sw.*, sc.name AS category
                FROM `spr_workers` sw  
                LEFT JOIN `journal_work_cat` jwcat ON sw.id = jwcat.worker_id
                LEFT JOIN `spr_categories` sc ON jwcat.category = sc.id
                ORDER BY sw.full_name ASC";

            }

            $msql_cnnct = ConnectToDB ();


            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($contacts, $arr);
                }
            }else{
                //$sheduler_zapis = 0;
                //var_dump ($sheduler_zapis);
            }

            //var_dump($contacts);

			$arr_permissions = SelDataFromDB('spr_permissions', '', '');
			//var_dump ($arr_permissions);
			$arr_orgs = SelDataFromDB('spr_org', '', '');
			//var_dump ($arr_orgs);
			
			$fired_all = '';
			
			if (($workers['add_new'] == 1) || $god_mode){
				echo '
					<a href="add_worker.php" class="b">Добавить</a>
				</header>';			
			}


            echo '		    
                <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                    <a href="contacts.php" class="b" style="'.$all_color.'">Все</a>
                    <a href="?who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
                    <a href="?who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
                    <a href="?who=10" class="b" style="'.$somat_color.'">Специалисты</a>
                    <a href="?who=4" class="b" style="'.$admin_color.'">Администраторы</a>
                    <a href="?who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
                    <a href="?who=11" class="b" style="'.$other_color.'">Прочие</a>
                </li>';


			if ($contacts != 0){

				echo '
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold;">	
								<div class="cellFullName" style="text-align: center">
								    Полное имя';
                echo $block_fast_filter;
                echo '
								</div>
								<div class="cellOffice" style="text-align: center">Должность</div>
								<div class="cellName" style="text-align: center">Категория</div>
								<div class="cellName" style="text-align: center">Филиал</div>
								<div class="cellFullName" style="text-align: center">Специализация</div>
								
								<div class="cellText" style="text-align: center">Контакты</div>
								<div class="cellName" style="text-align: center">Логин</div>
								<div class="cellName" style="text-align: center">Пароль</div>
							</li>';

				for ($i = 0; $i < count($contacts); $i++) {
                    $specializations_str_rez = '';

					if ($contacts[$i]['permissions'] != '777'){
						$permissions = SearchInArray($arr_permissions, $contacts[$i]['permissions'], 'name');
						//var_dump($permissions);
						$org = SearchInArray($arr_orgs, $contacts[$i]['org'], 'name');
						//var_dump($org);
                        $specializations = workerSpecialization($contacts[$i]['id']);

						if ($contacts[$i]['fired'] != 1){
						
							echo '
								<li class="cellsBlock cellsBlockHover ', $contacts[$i]['fired'] == '1' ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'">
									<a href="user.php?id='.$contacts[$i]['id'].'" class="cellFullName ahref 4filter" id="4filter" ', $contacts[$i]['fired'] == '1' ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.$contacts[$i]['full_name'].'</a>
									<div class="cellOffice" style="text-align: right;">', $permissions != '0' ? $permissions : '-' ,'</div>									
									<div class="cellName" style="text-align: right;">';

							        //категория

                            echo $contacts[$i]['category'];

                            echo '
                                    </div>';

                            //Филиал
                            echo '
                                    <div class="cellName" style="text-align: right;">';

                            if ($contacts[$i]['filial_id'] != 0){
                                echo $filials_j[$contacts[$i]['filial_id']]['name'];
                            }

                            echo '
                                    </div>
									   
									<div class="cellFullName"  style="text-align: right;">';

                            if ($specializations != 0){
                                //var_dump($specializations_j);
                                foreach ($specializations as $data){
                                    $specializations_str_rez .= ''.$data['name'].' ';
                                }
                            }else{
                                $specializations_str_rez = '-';
                            }

                            echo $specializations_str_rez;


							echo '
                                    </div>
									
									<div class="cellText" >'.$contacts[$i]['contacts'].'</div>
									<div class="cellName" style="text-align: center;">'.$contacts[$i]['login'].'</div>';
							if (($workers['see_own'] == 1) &&
							(($contacts[$i]['permissions'] == 4) || ($contacts[$i]['permissions'] == 5) ||  ($contacts[$i]['permissions'] == 6) ||  ($contacts[$i]['permissions'] == 7) ||  ($contacts[$i]['permissions'] == 9)) || $god_mode){
								echo '
										<div class="cellName" style="text-align: center; ', $contacts[$i]['fired'] == '1' ? 'background-color: rgba(161,161,161,1);"' : '"' ,'>
											<div style="display:inline-block;">'.$contacts[$i]['password'].'</div> <div style="color: red; display: inline-block; cursor: pointer;" title="Сменить пароль" onclick=changePass('.$contacts[$i]['id'].')><i class="fa fa-key" aria-hidden="true"></i></div>
										</div>';
							}else{
								echo '
										<div class="cellName" style="text-align: center; ', $contacts[$i]['fired'] == '1' ? 'background-color: rgba(161,161,161,1);"' : '"' ,'>
											****
										</div>';
							}
							echo '
									</li>';
						}else{
						    //Уволенные
							$fired_all .= '
								<li class="cellsBlock cellsBlockHover" ';

                            if ($contacts[$i]['fired'] == '1')
                                $fired_all .= 'style="background-color: rgba(161,161,161,1);"';
                            else
                                $fired_all .= '';

                            $fired_all .= '>
							        <a href="user.php?id='.$contacts[$i]['id'].'" class="cellFullName ahref 4filter" id="4filter">
							            '.$contacts[$i]['full_name'].'
							        </a>
								    <div class="cellOffice" style="text-align: right;">';

                            if ($permissions != '0')
                                $fired_all .= $permissions;
                            else
                                $fired_all .= '-';

                            $fired_all .= '
                                    </div>';

                            //категория
                            $fired_all .= '
                                    <div class="cellName" style="text-align: right;">
                                        '.$contacts[$i]['category'].'
                                    </div>';

                            //Филиал
                            $fired_all .= '
                                    <div class="cellName" style="text-align: right;">';

                            if ($contacts[$i]['filial_id'] != 0){
                                $fired_all .= $filials_j[$contacts[$i]['filial_id']]['name'];
                            }

                            $fired_all .= '
                                    </div>';

                            //Специализации

                            if ($specializations != 0){
                                foreach ($specializations as $data){
                                    $specializations_str_rez .= $data['name'].' ';
                                }
                            }else{
                                $specializations_str_rez = '-';
                            }

                            $fired_all .= '
                                    <div class="cellFullName" style="text-align: right;">
                                        '.$specializations_str_rez.'
                                    </div>
                                    
                                    <div class="cellText">
                                        '.$contacts[$i]['contacts'].'
                                    </div>
                                    
                                    <div class="cellName" style="text-align: center;">
                                        '.$contacts[$i]['login'].'
                                    </div>';

							if ($god_mode){			
								$fired_all .= '<div class="cellName" style="text-align: center;">'.$contacts[$i]['password'].'</div>';
							}else{
								$fired_all .= '<div class="cellName" style="text-align: center;">****</div>';
							}

							$fired_all .= '
							    </li>';
						}
					}
				}

				//Выводим уволенных
				if ($fired_all != ''){
					if (($workers['see_own'] == 1) || $god_mode){
						if (true || $god_mode){
							echo $fired_all;
						}
					}
				}
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
			echo '
					</ul>
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>