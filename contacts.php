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
			//$offices = SelDataFromDB('spr_filials', '', '');
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Список сотрудников</h1>';

			$contacts = SelDataFromDB('spr_workers', '', '');
			//var_dump ($contacts);
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
									<div class="cellOffice" ', $contacts[$i]['fired'] == '1' ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>', $permissions != '0' ? $permissions : '-' ,'</div>									
									   
									<div class="cellFullName" ', $contacts[$i]['fired'] == '1' ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>';

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
									
									<div class="cellText" ', $contacts[$i]['fired'] == '1' ? 'style="background-color: rgba(161,161,161,1);"' : '' ,'>'.$contacts[$i]['contacts'].'</div>
									<div class="cellName" style="text-align: center; ', $contacts[$i]['fired'] == '1' ? 'background-color: rgba(161,161,161,1);"' : '"' ,'>'.$contacts[$i]['login'].'</div>';
							if (($god_mode || ($workers['see_own'] == 1)) && 
							(($contacts[$i]['permissions'] == 4) || ($contacts[$i]['permissions'] == 5) ||  ($contacts[$i]['permissions'] == 6) ||  ($contacts[$i]['permissions'] == 7) ||  ($contacts[$i]['permissions'] == 9))){ 			
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
							$fired_all .= '
								<li class="cellsBlock cellsBlockHover ';
								if ($contacts[$i]['fired'] == '1') 
									$fired_all .= 'style="background-color: rgba(161,161,161,1);"'; 
								else 
									$fired_all .= ''; 
								$fired_all .= '">
									<a href="user.php?id='.$contacts[$i]['id'].'" class="cellFullName ahref 4filter" id="4filter" ';
								if ($contacts[$i]['fired'] == '1') 
									$fired_all .= 'style="background-color: rgba(161,161,161,1);"';
								else 
									$fired_all .= '';
								$fired_all .= '>'.$contacts[$i]['full_name'].'</a>
									<div class="cellOffice" ';
								if ($contacts[$i]['fired'] == '1') 
									$fired_all .= 'style="background-color: rgba(161,161,161,1);"';
								else 
									$fired_all .= '' ;
								$fired_all .= '>';
								if ($permissions != '0') 
									$fired_all .= $permissions;
								else
									$fired_all .= '-';
								$fired_all .= '</div>
                                    <div class="cellFullName" ';
                                if ($contacts[$i]['fired'] == '1')
                                    $fired_all .= 'style="background-color: rgba(161,161,161,1);"';
                                else
                                    $fired_all .= '' ;
                                $fired_all .= '>';

                                if ($specializations != 0){
                                    foreach ($specializations as $data){
                                        $specializations_str_rez .= $data['name'].' ';
                                    }
                                }else{
                                    $specializations_str_rez = '-';
                                }
    
                                $fired_all .= $specializations_str_rez;


                                $fired_all .= '</div>
                                    <div class="cellText" ';
                                
								if ($contacts[$i]['fired'] == '1') 
									$fired_all .= 'style="background-color: rgba(161,161,161,1);"';
								else
									$fired_all .= '';
								$fired_all .= '>'.$contacts[$i]['contacts'].'</div>
									<div class="cellName" style="text-align: center; ';
								if ($contacts[$i]['fired'] == '1') 
									$fired_all .= 'background-color: rgba(161,161,161,1);"';
								else 
									$fired_all .= '"';
								$fired_all .= '>'.$contacts[$i]['login'].'</div>';
							if ($god_mode){			
								$fired_all .= '
										<div class="cellName" style="text-align: center; ';
								if ($contacts[$i]['fired'] == '1') 
									$fired_all .= 'background-color: rgba(161,161,161,1);"';
								else
									$fired_all .= '"';
								$fired_all .= '>'.$contacts[$i]['password'].'</div>';
							}else{
								$fired_all .= '<div class="cellName" style="text-align: center; ';
								if ($contacts[$i]['fired'] == '1') 
									$fired_all .= 'background-color: rgba(161,161,161,1);"';
								else
									$fired_all .= '"';
								$fired_all .= '>****</div>';
							}
							$fired_all .= '
									</li>';
						}
					}
				}
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