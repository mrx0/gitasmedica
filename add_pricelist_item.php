<?php

//add_pricelist_item.php
//Добавить услугу

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			//операции со временем						
			$day = date('d');		
			$month = date('m');		
			$year = date('Y');
			
			if (isset($_GET['addinid'])){
				$selected = $_GET['addinid'];
			}else{
				$selected = 0;
			}

            $category_j = SelDataFromDB('fl_spr_percents', '', '');

			echo '
				<div id="status">
					<header>
						<h2>Добавить новую позицию</h2>
					</header>';
			if ($selected != 0){
				echo '<i style="color: blue;">Позиция будет добавлена в группу: '.$selected.'</i><br>';
			}
			
			echo '
					<a href="pricelist.php" class="b">В прайс</a><br>
					Заполните поля';
			

			

			echo '
					<div id="data">';
			echo '
						<div id="errror"></div>';
			echo '
						<form action="add_pricelistitem_f.php" style="font-size: 90%;" class="input_form">
					
							<div class="cellsBlock2" style="margin-bottom: 5px;">
								<div class="cellLeft">Код</div>
								<div class="cellRight">
									<input type="text" name="pricecode" id="pricecode" value="">
									<label id="pricecode_error" class="error"></label>
								</div>
							</div>
							<div class="cellsBlock2" style="margin-bottom: 5px;">
								<div class="cellLeft">Название</div>
								<div class="cellRight">
									<textarea name="pricename" id="pricename" style="width:90%; overflow:auto; height: 50px;"></textarea>
									<label id="pricename_error" class="error"></label>
								</div>
							</div>
                            <div class="cellsBlock2">
                                <div class="cellLeft">Категория</div>
                                <div class="cellRight">
                                    <select name="category_id" id="category_id">';
                                        echo "<option value='0' selected>не указано</option>";
            if ($category_j != 0){
                for ($i=0; $i<count($category_j); $i++){
                    echo "<option value='".$category_j[$i]['id']."'>".$category_j[$i]['name']."</option>";
                }
            }
            echo '
                                            </select>
                                        </div>
                                    </div>
							<div class="cellsBlock2">
								<div class="cellLeft">Цена</div>
								<div class="cellRight">
									<input type="text" name="price" id="price" value="0"  style="width: 50px;"> руб.
									<label id="price_error" class="error"></label>
								</div>
							</div>
							<div class="cellsBlock2">
								<div class="cellLeft">Цена2</div>
								<div class="cellRight">
									<input type="text" name="price2" id="price2" value="0"  style="width: 50px;"> руб.
									<label id="price2_error" class="error"></label>
								</div>
							</div>
							<div class="cellsBlock2">
								<div class="cellLeft">Цена3</div>
								<div class="cellRight">
									<input type="text" name="price3" id="price3" value="0"  style="width: 50px;"> руб.
									<label id="price3_error" class="error"></label>
								</div>
							</div>
							';
					//Календарик	
					echo '
	
								<div class="cellsBlock2">
									<div class="cellLeft">С какого числа применять цену:</div>
									<div class="cellRight">
										<input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
										onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
									</div>
								</div>';
					echo '			
								<div class="cellsBlock2">
									<div class="cellLeft">Расположение</div>
									<div class="cellRight">';
					echo '
										<select name="group" id="group" size="6" style="width: 250px;">
											<option value="0" ',$selected == 0 ? 'selected' : '','>*</option>';
											showTree(0, '', 'select', $selected, TRUE, 0, FALSE, 'spr_pricelist_template', 0);
					echo '	
										</select>';
					echo '	
									</div>
								</div>';
				echo '
							<input type="button" class="b" value="Добавить" onclick="Ajax_add_priceitem('.$_SESSION['id'].')">
						</form>
					</div>';	
				
			echo '
					</div>
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>