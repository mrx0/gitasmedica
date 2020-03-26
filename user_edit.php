<?php

//user_edit.php
//Редактирование пользователя

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($workers['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
			    $user = SelDataFromDB('spr_workers', $_GET['id'], 'user');
			    //var_dump($user);
                //$arr_orgs = SelDataFromDB('spr_org', '', '');
                //var_dump($orgs);
                $arr_permissions = SelDataFromDB('spr_permissions', '', '');
                //var_dump($arr_permissions);
                $permissions = SearchInArray($arr_permissions, $user[0]['permissions'], 'name');
                //var_dump($permissions);
                $specializations = workerSpecialization($_GET['id']);
                //var_dump($specializations);

                //$specialization_j = SelDataFromDB('spr_specialization', '', '');
                //var_dump($specialization_j);
                //$org = SearchInArray($arr_orgs, $user[0]['org'], 'name');
                //var_dump($org);

                $category = SelDataFromDB('journal_work_cat', $_GET['id'], 'worker_id');
                //var_dump($category);
                $filials_j = getAllFilials(false, false, true);

                //Отметки по дополнительным опциям
                $spec_prikaz8_checked = '';
                $spec_oklad_checked = '';

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT * FROM `options_worker_spec` WHERE `worker_id`='{$_GET['id']} LIMIT 1'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    $arr = mysqli_fetch_assoc($res);
                    if ($arr['prikaz8'] == 1){
                        $spec_prikaz8_checked = 'checked';
                    }
                    if ($arr['oklad'] == 1){
                        $spec_oklad_checked = 'checked';
                    }
                }

                CloseDB ($msql_cnnct);

                if ($user !=0){
                    echo '
                        <div id="status">
                            <header>
                                <h2>Редактировать карточку пользователя</h2>
                            </header>';

				    echo '
						    <div id="data">';

				    echo '
                                <form action="user_edit_f.php">
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">ФИО';

                    //if ($god_mode || ($workers['edit'] == 1)){
                        echo '    <a href="user_edit_fio.php?id='.$_GET['id'].'"><i class="fa fa-cog" title="Редактировать ФИО"></i></a>';
                    //}

				    echo '
                                        </div>
                                        <div class="cellRight"><a href="user.php?id='.$_GET['id'].'" class="ahref">'.$user[0]['full_name'].'</a></div>
                                    </div>
                                    
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">Дата рождения</div>
                                        <div class="cellRight">';

                    //var_dump(explode('-', $user[0]['birth'])[1]);

			echo selectDate (explode('-', $user[0]['birth'])[2], explode('-', $user[0]['birth'])[1], explode('-', $user[0]['birth'])[0]);

			echo '	
                                            <label id="sel_date_error" class="error"></label>
                                            <label id="sel_month_error" class="error"></label>
                                            <label id="sel_year_error" class="error"></label>
                                        </div>
                                    </div>
								
						            <!--<div class="cellsBlock2">
									    <div class="cellLeft">Организация</div>
									    <div class="cellRight">	
                                            <select name="org" id="org">
                                                <option value="0">Выбери</option>';
//                    for ($i=0;$i<count($arr_orgs);$i++){
//                        if ($arr_orgs[$i]['name'] == $org){
//                            $slctd = 'selected';
//                        }else{
//                            $slctd = '1';
//                        }
//                        echo "<option value='".$arr_orgs[$i]['id']."' $slctd>".$arr_orgs[$i]['name']."</option>";
//                    }

                    echo '
										    </select>
									    </div>
								    </div>-->	
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">Должность</div>
                                        <div class="cellRight">';
                    //var_dump($permissions);
                    //var_dump($user[0]['permissions']);

                    if ((($user[0]['permissions'] != 1) && ($user[0]['permissions'] != 2) && ($user[0]['permissions'] != 3) && ($user[0]['permissions'] != 8)) || ($god_mode)){
                        echo '
                                            <select name="permissions" id="permissions">
                                                <option value="0">Выберите должность</option>';
                        for ($i=0;$i<count($arr_permissions);$i++){
                            if ((($arr_permissions[$i]['id'] != 1) && ($arr_permissions[$i]['id'] != 2) && ($arr_permissions[$i]['id'] != 3) && ($arr_permissions[$i]['id'] != 8)) || ($god_mode)){
                                if ($user[0]['permissions'] == $arr_permissions[$i]['id']){
                                    $slctd = 'selected';
                                }else{
                                    $slctd = '';
                                }
                                echo "<option value='".$arr_permissions[$i]['id']."' $slctd>".$arr_permissions[$i]['name']."</option>";
                            }
                        }
						echo "</select>";
                    }else{
                        echo $permissions.'<input type="hidden" id="permissions" name="permissions" value="'.$user[0]['permissions'].'">';
                    }

                    echo '
										
									    </div>
                                    </div>
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">Специализация</div>
                                        <div id="specializations" class="cellRight">';
                    //var_dump($user[0]['permissions']);

                    if ($user[0]['permissions'] != 0) {

                        $specialization_j = SelDataFromDB('spr_specialization', $user[0]['permissions'], 'permission');
                        //var_dump($specialization_j);

                        //if (!empty($specializations)){
                            $specializations_temp = array();

                            //Преобразуем массив чтоб id стали ключами
                            if ($specializations != 0) {
                                foreach ($specializations as $data) {
                                    $specializations_temp[$data['id']] = $data;
                                }
                            }

                            if (!empty($specialization_j)) {
                                foreach ($specialization_j as $data) {
                                    $chckd = '';
                                    if (!empty($specializations_temp)) {
                                        if (isset($specializations_temp[$data['id']])) {
                                            $chckd = 'checked';
                                        }
                                    }

                                    echo '<input type="checkbox" name="specializations[]" value="' . $data['id'] . '" ' . $chckd . '> ' . $data['name'] . '<br>';
                                }
                            }
                        //}
                    }

										echo '
                                        </div>
                                    </div>
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">Категория</div>
                                        <div id="categories" class="cellRight">';
                    //var_dump($user[0]['permissions']);

                    if ($user[0]['permissions'] != 0) {

                        $categories_j = SelDataFromDB('spr_categories', $user[0]['permissions'], 'permission');
//                        var_dump($categories_j);
//                        var_dump($category);

                        if ($categories_j != 0) {

                            echo '<select name="SelectCategory" id="SelectCategory">';
                            echo "<option value=''>Выберите категорию</option>";

                            foreach ($categories_j as $data) {
                                $slctd = '';
                                if ($category[0]['category'] == $data['id']) {
                                    $slctd = 'selected';
                                }


                                echo "<option value='".$data['id']."' ".$slctd.">".$data['name']."</option>";
                            }
                            echo '</select>';
                        }
                    }
                    echo '
									</div>
								</div>';

                    echo '
                                <div class="cellsBlock2">
                                    <div class="cellLeft">Филиал</div>
                                    <div class="cellRight">';

                    echo '
                                        <select name="SelectFilial" id="SelectFilial">
                                            <option value="0">нет привязки</option>';

                    foreach ($filials_j as $filial_item) {

                        $selected = '';

                        if ($user[0]['filial_id'] == $filial_item['id']) {
                            $selected = 'selected';
                        }

                        echo '
                                <option value="' . $filial_item['id'] . '" ' . $selected . '>' . $filial_item['name'] . '</option>';
                    }

                    echo '
                                        </select>
                                    </div>
								</div>';
				    echo '								
								
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">
										<textarea name="contacts" id="contacts" cols="35" rows="5">'.$user[0]['contacts'].'</textarea>
									</div>
								</div>';

                    echo '								
								
								<div class="cellsBlock2">
									<div class="cellLeft">Особые отметки</div>
									<div class="cellRight">
									    <div>
                                            <input type="checkbox" name="spec_prikaz8" value="1" '.$spec_prikaz8_checked.'> Приказ №8
										</div>
										<div>
										    <input type="checkbox" name="spec_oklad" value="1" '.$spec_oklad_checked.'> Оклад
										</div>
									</div>
								</div>';

                    $selected0 = '';
                    $selected8 = '';
                    $selected6 = '';

                    if ($user[0]['status'] == 0){
                        $selected0 = 'selected';
                    }
                    if ($user[0]['status'] == 8){
                        $selected8 = 'selected';
                    }
                    if ($user[0]['status'] == 6){
                        $selected6 = 'selected';
                    }

				    echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Статус</div>
									<div class="cellRight">
                                        <select name="w_status" id="w_status">
                                            <option value="0" '.$selected0.'>Работает</option>
                                            <option value="8"'.$selected8.'>Уволен</option>
                                            <option value="6"'.$selected6.'>Декрет</option>
                                        </select>';


//				if ($user[0]['fired'] == '1'){
//					$chkd = 'checked';
//				}else{
//					$chkd = '';
//				}
				echo '
										<!--<input type="checkbox" name="fired" id="fired" value="1" $chkd>-->
									</div>
								</div>
											<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
											<!--<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">-->
											<input type=\'button\' class="b" value="Применить" onclick=Ajax_user_edit('.$_GET['id'].')>
										</form>';	

						echo '
						
								</div>
							</div>

                            <!-- Подложка только одна -->
                            <div id="overlay"></div>';
							
			    //Фунция JS для выбора специализации и категории
                echo '
					<script>
					
						$(function() {
							$("#permissions").change(function(){
							    //console.log($("#permissions").val());
							    
							    blockWhileWaiting (true);
							    
							    var permission = $("#permissions").val();
							    
							    var link = "get_specializations.php";

                                var reqData = {
                                    permission: permission
                                };
                
                                $.ajax({
                                    url: link,
                                    global: false,
                                    type: "POST",
                                    dataType: "JSON",
                                    data: reqData,
                                    cache: false,
                                    beforeSend: function () {
                                        //$(\'#errrror\').html("<div style=\'width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...</span></div>");
                                    },
                                    success: function (res) {
                                        //console.log (res);
                                        
                                        $("#specializations").html(res.data);

                                        var link = "get_categories.php";
                        
                                        $.ajax({
                                            url: link,
                                            global: false,
                                            type: "POST",
                                            dataType: "JSON",
                                            data: reqData,
                                            cache: false,
                                            beforeSend: function () {
                                                //$(\'#errrror\').html("<div style=\'width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...</span></div>");
                                            },
                                            success: function (res) {
                                                //console.log (res);
                                                
                                                $("#categories").html(res.data);

                                            }
                                        
                                        });  
                                        
                                        
                                        blockWhileWaiting (false);
                                    }
							    
							    });    
                            })
                        })
						
					</script>';
							
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>