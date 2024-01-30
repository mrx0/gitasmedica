<?php

//pricelistitem_edit.php
//Редактирование краточки товара

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($god_mode || $_SESSION['permissions'] == 3 || ($clients['add_own'] == 1)){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$items_j = SelDataFromDB('spr_pricelist_template', $_GET['id'], 'id');
				//var_dump($items_j);
				
				if ($items_j !=0){

                    $category_j = SelDataFromDB('fl_spr_percents', '', '');

					echo '
						<div id="status">
							<header>
								<div class="nav">
								    <a href="pricelist.php" class="b">Основной прайс</a>
							    </div>
								<h2>Редактировать <a href="pricelistitem.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
							</header>';

					echo '
							<div id="data">';
					echo '
								<div id="errror"></div>';
					echo '
								<form action="pricelistitem_edit_f.php" id="edit_form_id">
									
									<div class="cellsBlock2">
										<div class="cellLeft">Название</div>
										<div class="cellRight">
											<textarea name="pricelistitemname" id="pricelistitemname" style="width:90%; overflow:auto; height: 50px;">'.$items_j[0]['name'].'</textarea>
											<label id="pricelistitemname_error" class="error"></label>
										</div>
									</div>
									
									<div class="cellsBlock2">
										<div class="cellLeft">Код<br>страховых</div>
										<div class="cellRight">
											<input type="text" name="pricelistitemcode" id="pricelistitemcode" style="" value="'.$items_j[0]['code'].'">
											<label id="pricelistitemcode_error" class="error"></label>
										</div>
									</div>
									
									<div class="cellsBlock2" style="display: none;">
										<div class="cellLeft">Код МКБ</div>
										<div class="cellRight">
											<input type="text" name="pricelistitemcodemkb" id="pricelistitemcodemkb" style="" value="">
											<label id="pricelistitemcodemkb_error" class="error"></label>
										</div>
									</div>

                                    <div class="cellsBlock2" style="margin-bottom: 5px;">
                                        <div class="cellLeft" style="position: relative;">
                                            Код услуги
                                            <div class="notes_count" style="top: 0; right: 2px;">новый прайс</div>
                                        </div>
                                        <div class="cellRight">
                                            <input type="text" name="pricelistitemcode_u" id="pricelistitemcode_u" value="'.$items_j[0]['code_u'].'">
                                            <label id="pricelistitemcode_u_error" class="error"></label>
                                        </div>
                                    </div>
                                    <div class="cellsBlock2" style="margin-bottom: 5px;">
                                        <div class="cellLeft" style="position: relative;">
                                            Код услуги<br>по номенклатуре
                                            <div class="notes_count" style="top: 0; right: 2px;">новый прайс</div>
                                        </div>
                                        <div class="cellRight">
                                            <input type="text" name="pricelistitemcode_nom" id="pricelistitemcode_nom" value="'.$items_j[0]['code_nom'].'">
                                            <label id="pricelistitemcode_nom_error" class="error"></label>
                                        </div>
                                    </div>
                                    <div class="cellsBlock2">
										<div class="cellLeft">Расходный материал</div>
										<div class="cellRight">
											<input id="consumable" name="consumable" value="0" ', $items_j[0]['consumable'] == 0 ? 'checked': '',' type="radio"> Нет
											<input id="consumable" name="consumable" value="1" ', $items_j[0]['consumable'] == 1 ? 'checked': '',' type="radio"> Да
											<label id="consumable_error" class="error"></label>
										</div>
									</div
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">Категория</div>
                                        <div class="cellRight">
                                            <select name="category_id" id="category_id">';
                                        echo "<option value='0' selected>не указано</option>";
                    if ($category_j != 0){
                        for ($i=0; $i<count($category_j); $i++){
                            $selected = '';
                            if ($category_j[$i]['id'] == $items_j[0]['category']){
                                $selected = 'selected';
                            }

											echo "<option value='".$category_j[$i]['id']."' ".$selected.">".$category_j[$i]['name']."</option>";
                        }
                    }
                    echo '
                                            </select>
                                        </div>
                                    </div>
							
									<div class="cellsBlock2">
										<div class="cellLeft">Расположение</div>
										<div class="cellRight">';
					echo '
											<select name="group" id="group" size="6" style="width: 250px;">
												<option value="0">*</option>';
												
					$itemsingroups_j = SelDataFromDB('spr_itemsingroup', $_GET['id'], 'item');
					if ($itemsingroups_j != 0){
						$itemingroup = $itemsingroups_j[0]['group'];
					}else{
						$itemingroup = 0;
					}
					
					showTree(0, '', 'select', $itemingroup, TRUE, 0, FALSE, 'spr_pricelist_template', 0);
					echo '
											</select>';
					echo '
										</div>
									</div>
									<input type="button" class="b" value="Применить" onclick="Ajax_edit_pricelistitem('.$_GET['id'].', '.$_SESSION['id'].')">
								</form>
							</div>
						</div>
						
						
                        <script type="text/javascript">
                            consumable_value = '.$items_j[0]['consumable'].';
                            $("input[name=consumable]").change(function() {
                                consumable_value = $("input[name=consumable]:checked").val();
                            });
                        </script>
						
						
						';
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