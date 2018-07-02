<?php

//user.php
//Карточка пользователя

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';
			
			$user = SelDataFromDB('spr_workers', $_GET['id'], 'user');
			//var_dump($user);
			$arr_orgs = SelDataFromDB('spr_org', '', '');
			//var_dump($orgs);
			$arr_permissions = SelDataFromDB('spr_permissions', '', '');
			//var_dump($permissions);
			$permissions = SearchInArray($arr_permissions, $user[0]['permissions'], 'name');
			//var_dump($permissions);
			$specializations = workerSpecialization($_GET['id']);
			//var_dump($specializations);
			$org = SearchInArray($arr_orgs, $user[0]['org'], 'name');
			//var_dump($org);
			
			//операции со временем						
			$month = date('m');		
			$year = date('Y');
			$day = date("d");

            $specializations_str_rez = '';

			echo '
				<div id="status">
					<header>
						<h2>Карточка пользователя';
			if (($workers['edit'] == 1) || $god_mode){
				echo '
									<a href="user_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
			}
			echo '
						</h2>
					</header>';
			if ($user[0]['fired'] == '1'){
				echo '<span style="color:#EF172F;font-weight:bold;">УВОЛЕН</span>';
			}

			echo '
					<div id="data">';

			echo '

							<div class="cellsBlock2">
								<div class="cellLeft">ФИО</div>
								<div class="cellRight">'.$user[0]['full_name'].'</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Должность</div>
								<div class="cellRight">';
			echo $permissions;
			echo '				
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Специализация</div>
								<div class="cellRight">';

            if ($specializations != 0){
                //var_dump($specializations_j);
                foreach ($specializations as $data){
                    $specializations_str_rez .= '<span class="tag">'.$data['name'].'</span>';
                }
            }else{
                $specializations_str_rez = 'не указано';
            }

            echo $specializations_str_rez;
			echo '				
								</div>
							</div>
							
			<!--				<div class="cellsBlock2">
								<div class="cellLeft">Организация</div>
								<div class="cellRight">';
			echo $org;
			echo '	
								</div>
							</div>
			-->				
							<div class="cellsBlock2">
								<div class="cellLeft">Логин</div>
								<div class="cellRight">'.$user[0]['login'].'</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Контакты</div>
								<div class="cellRight">'.$user[0]['contacts'].'</div>
							</div>
							<br /><br /><br />';
			if ($_GET['id'] == $_SESSION['id']){
				if (($stom['add_own'] == 1) || ($cosm['add_own'] == 1) || $god_mode || ($_SESSION['permissions'] == 3) || ($_SESSION['permissions'] == 9)){
					echo '
								<!--<div class="cellsBlock2">
									<div class="cellRight" style="font-size:70%;">Настройки.</div>
								</div>		
								<div class="cellsBlock2" style="font-size:80%;">
									<div class="cellRight">
										Филиал теперь можно выбрать/изменить в правом верхнем углу под ФИО пользователя';
					echo '
										<!--<form>
											<select name="SelectFilial" id="SelectFilial">
												<option value="0">Филиал не выбран</option>';
					//Выбор филиала для сессии
					$offices = SelDataFromDB('spr_filials', '', '');
					if ($offices != 0){
						if (isset($_SESSION['filial']) && !empty($_SESSION['filial'])){
							$selected_fil = $_SESSION['filial'];
						}else{
							$selected_fil = 0;
						}
						for ($off=0;$off<count($offices);$off++){
							echo "
												<option value='".$offices[$off]['id']."' ", $selected_fil == $offices[$off]['id'] ? "selected" : "" ,">".$offices[$off]['name']."</option>";
						}
					}
						echo '
											</select>
										</form>-->';
					echo '
										</div>
									</div>
									<br /><br /><br />';

				}
			}				

			echo '
									<a href="scheduler_own.php?id='.$_GET['id'].'" class="b">График работы</a>';
			
			if ($zapis['see_own'] == 1){
					echo '
								<a href="zapis_own.php?y='.$year.'&m='.$month.'&d='.$day.'&worker='.$_SESSION['id'].'" class="b">Запись сегодня</a>';
			}

            echo '
									<a href="fl_my_tabels.php" class="b">Табели</a>';


            if ($stom['see_own'] == 1){
				$notes = SelDataFromDB ('notes', $_SESSION['id'], 'create_person');
			}elseif (($stom['see_all'] == 1) || $god_mode){
				$notes = SelDataFromDB ('notes', 'dead_line', 'dead_line');
			}else{
				$notes = 0;
			}
			
			echo '<div id="status_notes">';
			if ($notes != 0){
				
				//var_dump ($notes);	

				/*$for_notes = array (
					1 => 'Каласепт, Метапекс, Септомиксин (Эндосольф)',
					2 => 'Временная пломба',
					3 => 'Открытый зуб',
					4 => 'Депульпин',
					5 => 'Распломбирован под вкладку (вкладка)',
					6 => 'Имплантация (ФДМ ,  абатмент, временная коронка на импланте)',
					7 => 'Временная коронка',
					8 => 'Санированные пациенты ( поддерживающее лечение через 6 мес)',
					9 => 'Прочее',					
					10 => 'Установлены брекеты',					
				);*/
				
				echo ' 
					<div class="showHiddenDivs" style="cursor: pointer;">
						<div style="color: #7D7D7D; margin: 10px;" id="showHideText">Показать всё</div>
					</div>';
				echo '
					<div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">
						<ul>
							<li><a href="#tabs-1">Напоминания</a></li>
							<li><a href="#tabs-2">Направления</a></li>
						</ul>';
				echo '
						<div id="tabs-1">';

				if ($stom['see_own'] == 1){
					echo 'Мои напоминания';
				}elseif (($stom['see_all'] == 1) || $god_mode){
					echo '<br><br>Все просроченные незакрытые напоминания';
				}
				echo '
							<ul class="live_filter" style="margin-left:6px;">
								<li class="cellsBlock" style="font-weight:bold;">	
									<div class="cellPriority" style="text-align: center"></div>
									<div class="cellTime" style="text-align: center">Срок</div>
									<div class="cellName" style="text-align: center">Пациент</div>
									<div class="cellName" style="text-align: center">Посещение</div>
									<div class="cellText" style="text-align: center">Описание</div>
									<div class="cellTime" style="text-align: center">Управление</div>
									<div class="cellTime" style="text-align: center">Создано</div>
									<div class="cellName" style="text-align: center">Автор</div>
									<div class="cellTime" style="text-align: center">Закрыто</div>
								</li>';
				for ($i = 0; $i < count($notes); $i++) {
					$dead_line_time = $notes[$i]['dead_line'] - time() ;
					if (($dead_line_time <= 0) && $notes[$i]['closed'] == 0){
						$priority_color = '#FF1F0F';
					}elseif ((($dead_line_time > 0) && ($dead_line_time <= 2*24*60*60)) && $notes[$i]['closed'] == 0){
						$priority_color = '#FF9900';
					}elseif ((($dead_line_time > 2*24*60*60) && ($dead_line_time <= 3*24*60*60)) && $notes[$i]['closed'] == 0){
						$priority_color = '#EFDF3F';
					}else{
						$priority_color = '#FFF';
					}

	
					if ($notes[$i]['closed'] == 0){
						$ended = 'Нет';
						$background_style = '';
						$background_style2 = '
							background: rgba(231,55,71, 0.9);
							color:#fff;
							';
						if ($dead_line_time <= 0){
							$background_style = '
								background: rgba(239,23,63, 0.5);
								background: -moz-linear-gradient(45deg, rgba(239,23,63, 1) 0%, rgba(231,55,39, 0.7) 33%, rgba(239,23,63, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
								background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(239,23,63, 0.4)), color-stop(33%,rgba(231,55,39, 0.7)), color-stop(71%,rgba(239,23,63, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
								background: -webkit-linear-gradient(45deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -o-linear-gradient(45deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -ms-linear-gradient(45deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: linear-gradient(-135deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								';
						}
						$showHiddenDivs = '';
					}else{
						$ended = 'Да';
						$background_style = '
							background: rgba(144,247,95, 0.5);
							background: -moz-linear-gradient(45deg, rgba(144,247,95, 1) 0%, rgba(55,215,119, 0.7) 33%, rgba(144,247,95, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
							background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(144,247,95, 0.4)), color-stop(33%,rgba(55,215,119, 0.7)), color-stop(71%,rgba(144,247,95, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
							background: -webkit-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
							background: -o-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
							background: -ms-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
							background: linear-gradient(-135deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
							';
						$background_style2 = '
							background: rgba(144,247,95, 0.5);
							';
						$showHiddenDivs = 'hiddenDivs';
					}
					echo '
							<li class="cellsBlock cellsBlockHover '.$showHiddenDivs.'">
									<div class="cellPriority" style="background-color:'.$priority_color.'"></div>
									<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $notes[$i]['dead_line']).'</div>
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_clients',$notes[$i]['client'], 'user', true).'</div>
									<a href="task_stomat_inspection.php?id='.$notes[$i]['task'].'" class="ahref cellName" style="text-align: center">#'.$notes[$i]['task'].'</a>
									<div class="cellText" style="'.$background_style.'">'.$for_notes[$notes[$i]['description']].'</div>
									<div class="cellTime" style="text-align: center">';
					if ($_SESSION['id'] == $notes[$i]['create_person']){
						echo '
										<a href="#" id="Change_notes_stomat" onclick="Change_notes_stomat('.$notes[$i]['id'].', '.$notes[$i]['description'].')">ред.</a>
										<a href="#" id="Close_notes_stomat" onclick="Close_notes_stomat('.$notes[$i]['id'].')">закр.</a>';
					}
					echo '
									</div>
									<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $notes[$i]['create_time']).'</div>
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$notes[$i]['create_person'], 'user', true).'</div>
									<div class="cellTime" style="text-align: center; '.$background_style2.'">'.$ended.'</div>
							</li>';
				}

			}else{
				//echo '<h1>Нечего показывать.</h1>';
			}

			echo '
					</ul>';
						
				echo '
						</div>';
				echo '
						<div id="tabs-2">';
			if ($_SESSION['id'] == $_GET['id']){					
				//Перенаправления мои	
				$removesMy = SelDataFromDB ('removes', $_SESSION['id'], 'create_person');
				//... Ко мне
				$removesMe = SelDataFromDB ('removes', $_SESSION['id'], 'whom');
			}else{
				if (($stom['see_all'] == 1) || $god_mode){
					//Перенаправления мои	
					$removesMy = SelDataFromDB ('removes', $_GET['id'], 'create_person');
					//... Ко мне
					$removesMe = SelDataFromDB ('removes', $_GET['id'], 'whom');
				}
			}
			if (($removesMy != 0) || ($removesMe != 0)){
				
				echo 'Направления';
				echo '
							<ul class="live_filter" style="margin-left:6px;">
								<li class="cellsBlock" style="font-weight:bold;">	
									<div class="cellName" style="text-align: center">К кому</div>
									<div class="cellName" style="text-align: center">Пациент</div>
									<div class="cellName" style="text-align: center">Посещение</div>
									<div class="cellText" style="text-align: center">Описание</div>
									<div class="cellTime" style="text-align: center">Управление</div>
									<div class="cellTime" style="text-align: center">Создано</div>
									<div class="cellName" style="text-align: center">Автор</div>
									<div class="cellTime" style="text-align: center">Закрыто</div>
								</li>';
								
				if($removesMy != 0){
					
					echo '<b>Мои</b>';
					for ($i = 0; $i < count($removesMy); $i++) {
						
						if ($removesMy[$i]['closed'] == 0){
							$ended = 'Нет';
							$background_style = '';
							$background_style2 = '
								background: rgba(231,55,71, 0.9);
								color:#fff;
								';

							$background_style = '
								background: rgba(255,255,71, 0.5);
								background: -moz-linear-gradient(45deg, rgba(255,255,71, 1) 0%, rgba(255,255,157, 0.7) 33%, rgba(255,255,71, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
								background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(255,255,71, 0.4)), color-stop(33%,rgba(255,255,157, 0.7)), color-stop(71%,rgba(255,255,71, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
								background: -webkit-linear-gradient(45deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -o-linear-gradient(45deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -ms-linear-gradient(45deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: linear-gradient(-135deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								';
							$showHiddenDivs = '';	
						}else{
							$ended = 'Да';
							$background_style = '
								background: rgba(144,247,95, 0.5);
								background: -moz-linear-gradient(45deg, rgba(144,247,95, 1) 0%, rgba(55,215,119, 0.7) 33%, rgba(144,247,95, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
								background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(144,247,95, 0.4)), color-stop(33%,rgba(55,215,119, 0.7)), color-stop(71%,rgba(144,247,95, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
								background: -webkit-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -o-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -ms-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: linear-gradient(-135deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								';
							$background_style2 = '
								background: rgba(144,247,95, 0.5);
								';
							$showHiddenDivs = 'hiddenDivs';
						}
						
						echo '
							<li class="cellsBlock cellsBlockHover '.$showHiddenDivs.'">
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removesMy[$i]['whom'], 'user', true).'</div>
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_clients',$removesMy[$i]['client'], 'user', true).'</div>
									<a href="task_stomat_inspection.php?id='.$removesMy[$i]['task'].'" class="ahref cellName" style="text-align: center">#'.$removesMy[$i]['task'].'</a>
									<div class="cellText" style="'.$background_style.'">'.$removesMy[$i]['description'].'</div>
									<div class="cellTime" style="text-align: center">';
						if ($_SESSION['id'] == $removesMy[$i]['create_person']){
							echo '
										<a href="#" id="Close_removes_stomat" onclick="Close_removes_stomat('.$removesMy[$i]['id'].')">закр.</a>';
						}
						echo '
									</div>
									<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $removesMy[$i]['create_time']).'</div>
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removesMy[$i]['create_person'], 'user', true).'</div>
									<div class="cellTime" style="text-align: center; '.$background_style2.'">'.$ended.'</div>
							</li>';
					}
				}
				
				if($removesMe != 0){
					echo '<b>Ко мне</b>';
					for ($i = 0; $i < count($removesMe); $i++) {
						
					
						if ($removesMe[$i]['closed'] == 0){
							$ended = 'Нет';
							$background_style = '';
							$background_style2 = '
								background: rgba(231,55,71, 0.9);
								color:#fff;
								';

							$background_style = '
								background: rgba(55,127,223, 0.5);
								background: -moz-linear-gradient(45deg, rgba(55,127,223, 1) 0%, rgba(151,223,255, 0.7) 33%, rgba(55,127,223, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
								background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(55,127,223, 0.4)), color-stop(33%,rgba(151,223,255, 0.7)), color-stop(71%,rgba(55,127,223, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
								background: -webkit-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -o-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -ms-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: linear-gradient(-135deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								';
							$showHiddenDivs = '';
						}else{
							$ended = 'Да';
							$background_style = '
								background: rgba(144,247,95, 0.5);
								background: -moz-linear-gradient(45deg, rgba(144,247,95, 1) 0%, rgba(55,215,119, 0.7) 33%, rgba(144,247,95, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
								background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(144,247,95, 0.4)), color-stop(33%,rgba(55,215,119, 0.7)), color-stop(71%,rgba(144,247,95, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
								background: -webkit-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -o-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -ms-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: linear-gradient(-135deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								';
							$background_style2 = '
								background: rgba(144,247,95, 0.5);
								';
							$showHiddenDivs = 'hiddenDivs';
						}
						
						echo '
							<li class="cellsBlock cellsBlockHover '.$showHiddenDivs.'">
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removesMe[$i]['whom'], 'user', true).'</div>
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_clients',$removesMe[$i]['client'], 'user', true).'</div>
									<a href="task_stomat_inspection.php?id='.$removesMe[$i]['task'].'" class="ahref cellName" style="text-align: center">#'.$removesMe[$i]['task'].'</a>
									<div class="cellText" style="'.$background_style.'">'.$removesMe[$i]['description'].'</div>
									<div class="cellTime" style="text-align: center">';
						if ($_SESSION['id'] == $removesMe[$i]['whom']){
							echo '
										<a href="#" id="Close_removes_stomat" onclick="Close_removes_stomat('.$removesMe[$i]['id'].')">закр.</a>';
						}
						echo '
									</div>
									<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $removesMe[$i]['create_time']).'</div>
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removesMe[$i]['create_person'], 'user', true).'</div>
									<div class="cellTime" style="text-align: center; '.$background_style2.'">'.$ended.'</div>
							</li>';
					}
				}
				
			}else{
				//echo '<h1>Нечего показывать.</h1>';
			}

			echo '
					</ul>';
				echo '
						</div>
					</div>';
				

					

					
					
					
			echo '</div>';
			echo '
					</div>
				</div>';
				
			echo '
				<script>
					$(".showHiddenDivs").click(function () {
						$(".hiddenDivs").each(function(){
							//alert($(this).css("display"));
							if($(this).css("display") == "none"){
								$(this).css("display", "table");
								document.getElementById("showHideText").innerHTML="Скрыть закрытые";
							}else{
								$(this).css("display", "none");
								document.getElementById("showHideText").innerHTML="Показать всё";
							}
						})
					});
					$(function() {
						$(\'#SelectFilial\').change(function(){
							//alert($(this).val());
							ajax({
								url:"Change_user_session_filial.php",
								//statbox:"status_notes",
								method:"POST",
								data:
								{
									data:$(this).val(),
									//type:type,
								},
								success:function(data){
									//document.getElementById("status_notes").innerHTML=data;
									//alert("Ok");
									location.reload();
								}
							})
						});
					});
					function Change_notes_stomat(id, type) {
						ajax({
							url:"Change_notes_stomat.php",
							statbox:"status_notes",
							method:"POST",
							data:
							{
								id:id,
								type:type,
							},
							success:function(data){
								document.getElementById("status_notes").innerHTML=data;
							}
						})
					}
					function Close_notes_stomat(id) {
						ajax({
							url:"Close_notes_stomat_f.php",
							statbox:"status_notes",
							method:"POST",
							data:
							{
								id:id,
							},
							success:function(data){
								document.getElementById("status_notes").innerHTML=data;
							}
						})
					}

				function Close_removes_stomat(id) {
						ajax({
							url:"Close_removes_stomat_f.php",
							statbox:"status_notes",
							method:"POST",
							data:
							{
								id:id,
							},
							success:function(data){
								document.getElementById("status_notes").innerHTML=data;
							}
						})
					}


					function Ajax_change_notes_stomat(id) {
						ajax({
							url:"Change_notes_stomat_f.php",
							statbox:"status_notes",
							method:"POST",
							data:
							{
								id:id,
								change_notes_months:document.getElementById("change_notes_months").value,
								change_notes_days:document.getElementById("change_notes_days").value,
								change_notes_type:document.getElementById("change_notes_type").value,
							},
							success:function(data){
								document.getElementById("status_notes").innerHTML=data;
							}
						})
					}

 				</script> 
			';
				
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>