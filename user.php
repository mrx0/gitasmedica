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
							
            <!--		<div class="cellsBlock2">
                            <div class="cellLeft">Организация</div>
                            <div class="cellRight">';
			echo $org;
			echo '	
                            </div>
                        </div>-->
                                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Логин</div>
                            <div class="cellRight">'.$user[0]['login'].'</div>
                        </div>
                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Контакты</div>
                            <div class="cellRight">'.$user[0]['contacts'].'</div>
                        </div>
                        
                        <br><br><br>';

			echo '
                        <a href="scheduler_own.php?id='.$_GET['id'].'" class="b">График работы</a>';

			if ($zapis['see_own'] == 1){
					echo '
                        <a href="zapis_own.php?y='.$year.'&m='.$month.'&d='.$day.'&worker='.$_SESSION['id'].'" class="b">Запись сегодня</a>';
			}

            echo '
                        <a href="fl_my_tabels.php" class="b">Табели</a>';


			echo '
                        <div id="status_notes">';

            /*echo '
                        <div class="showHiddenDivs" style="cursor: pointer;">
                            <div style="color: #7D7D7D; margin: 10px;" id="showHideText">Показать всё</div>
                        </div>';*/
            echo '
                        <div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">
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