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
			//$org = SearchInArray($arr_orgs, $user[0]['org'], 'name');
			//var_dump($org);
            $category_j = SelDataFromDB('journal_work_cat', $_GET['id'], 'worker_id');
            //var_dump($category);
            if ($category_j != 0){
                $category = SelDataFromDB('spr_categories', $category_j[0]['category'], 'id');
            }
            //var_dump($category);

            $filials_j = getAllFilials(false, false, true);

            //Отметки по дополнительным опциям
            $spec_prikaz8_checked = '';
            $spec_oklad_checked = '';
            $spec_oklad_work_checked = '';

            $msql_cnnct = ConnectToDB ();

            $query = "SELECT * FROM `options_worker_spec` WHERE `worker_id`='{$_GET['id']}' LIMIT 1";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            $spec_prikaz8 = false;
            $spec_oklad = false;
            $spec_oklad_work = false;
            $work_6days = false;

            if ($number != 0){
                $arr = mysqli_fetch_assoc($res);
                if ($arr['prikaz8'] == 1){
                    $spec_prikaz8 = true;
                }
                if ($arr['oklad'] == 1){
                    $spec_oklad = true;
                }
                if ($arr['oklad_work'] == 1){
                    $spec_oklad_work = true;
                }
                if ($arr['work6days'] == 1){
                    $work_6days = true;
                }
            }

            CloseDB ($msql_cnnct);

			//операции со временем						
			$month = date('m');		
			$year = date('Y');
			$day = date("d");

            $specializations_str_rez = '';

			echo '
				<div id="status">
					<header>
                        <div class="nav">
                            <a href="contacts.php" class="b">Все сотрудники</a>
                        </div>
					
						<h2>Карточка пользователя';
			if (($workers['edit'] == 1) || $god_mode){
				echo '
									<a href="user_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
			}
			echo '
						</h2>
					</header>';
			if ($user[0]['status'] == '8'){
				echo '<span style="color:#EF172F;font-weight:bold;">УВОЛЕН</span>';
			}
			if ($user[0]['status'] == '6'){
				echo '<span style="color: rgb(213, 22, 239) ;font-weight:bold;">ДЕКРЕТ</span>';
			}

			echo '
					<div id="data">';

			echo '

                        <div class="cellsBlock2">
                            <div class="cellLeft">ФИО</div>
                            <div class="cellRight" style="font-weight: bold; font-size: 105%;">'.$user[0]['full_name'].'</div>
                        </div>
                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Логин</div>
                            <div class="cellRight">'.$user[0]['login'].'</div>
                        </div>

                        <div class="cellsBlock2">
                            <div class="cellLeft">Дата рождения</div>
                            <div class="cellRight">';
            if ($user[0]['birth'] == '0000-00-00'){
                echo 'не указана';
            }else{
                echo
                explode('-', $user[0]['birth'])[2].' '.$monthsName[explode('-', $user[0]['birth'])[1]]. '<br>
						<!--полных лет <b>'.getyeardiff(strtotime($user[0]['birth']), 0).'</b>-->';
            }
            echo
                            '</div>
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

            if (!empty($specializations)){
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
							
                		<div class="cellsBlock2">
                            <div class="cellLeft">Категория</div>
                            <div class="cellRight">';
			//var_dump($category_j);

			if ($category_j != 0){
                echo $category[0]['name'];
            }else{
			    echo 'не указано';
            }

			echo '	
                            </div>
                        </div>
                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Филиал</div>
                            <div class="cellRight">';

			if ($user[0]['filial_id'] != 0){
                echo  $filials_j[$user[0]['filial_id']]['name'];
            }else{
			    echo 'нет привязки';
            }

            echo '
                            </div>
                        </div>
                                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Контакты</div>
                            <div class="cellRight">'.$user[0]['contacts'].'</div>
                        </div>';

            echo '								
								
                        <div class="cellsBlock2">
                            <div class="cellLeft">Особые отметки</div>
                            <div class="cellRight">';

            if ($spec_prikaz8){
                echo '
                    <div>
                        Приказ №8
                    </div>';
            }

            if ($spec_oklad){
                echo '
                    <div>
                        Оклад
                    </div>';
            }

            if ($spec_oklad_work){
                echo '
                    <div>
                        Оклад+работа
                    </div>';
            }

            if ($work_6days){
                echo '
                    <div>
                        6 дней/нед.
                    </div>';
            }

            echo '
                            </div>
                        </div>';


            echo '            
                        <br><br><br>';

			echo '
                        <a href="scheduler_own.php?id='.$_GET['id'].'" class="b">График работы</a>';

			if ($zapis['see_own'] == 1){
					echo '
                        <a href="zapis_own.php?y='.$year.'&m='.$month.'&d='.$day.'&worker='.$_SESSION['id'].'" class="b">Запись сегодня</a>';
			}

            //if (($_SESSION['permissions'] == 5) || ($_SESSION['permissions'] == 6)) {
            //Если не администратор 20230203
            if (($_SESSION['permissions'] != 4) && ($_SESSION['permissions'] != 7) && ($_SESSION['permissions'] != 13) && ($_SESSION['permissions'] != 14) && ($_SESSION['permissions'] != 15)) {
                echo '
                        <a href="fl_my_tabels.php" class="b">Табели</a>';
            }

			echo '
                        <div id="status_notes">';

            /*echo '
                        <div class="showHiddenDivs" style="cursor: pointer;">
                            <div style="color: #7D7D7D; margin: 10px;" id="showHideText">Показать всё</div>
                        </div>';*/

            echo '
                        <div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">';
            if (($_SESSION['permissions'] == 1) || ($_SESSION['permissions'] == 2) || ($_SESSION['permissions'] == 3) || ($_SESSION['permissions'] == 9) || ($_SESSION['permissions'] == 12) || ($_SESSION['permissions'] == 11) || $god_mode){
                echo '<a href="notes_removes.php" class="b ahref">Все незакрытые</a>';
            }
            echo '                
                            <ul>
                                <li><a href="#tabs-1">Напоминания</a></li>
                                <li><a href="#tabs-2">Направления</a></li>
                            </ul>';
            echo '
                            <div id="tabs-1">
                                <div id="notes"></div>
                            </div>';

            echo '
                            <div id="tabs-2">
                                <div id="removes"></div>
                            </div>';

			echo '
				        </div>';
				

			echo '
                    </div>';
			echo '
				</div>';
				
			echo '
				<script>

                    $(document).ready(function() {
                        //Получаем, показываем направления
                        getNotesfunc ('.$_GET['id'].');
                        getRemovesfunc ('.$_GET['id'].');
				    });
				
					/*$(".showHiddenDivs").click(function () {
						$(".hiddenDivs").each(function(){

							if($(this).css("display") == "none"){
								$(this).css("display", "table");
								$("#showHideText").html("Скрыть закрытые");
							}else{
								$(this).css("display", "none");
								$("#showHideText").html("Показать всё");
							}
						})
					});*/
					
					/*$(function() {
						$(\'#SelectFilial\').change(function(){
							//alert($(this).val());
							ajax({
								url:"Change_user_session_filial.php",
								//statbox:"status_notes",
								method:"POST",
								data:
								{
									data: $(this).val()
								},
								success:function(data){
									//document.getElementById("status_notes").innerHTML=data;
									//alert("Ok");
									location.reload();
								}
							})
						});
					});*/
					


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