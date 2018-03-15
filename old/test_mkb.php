<?php

//test_mkb.php
//Временно использовалась

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
	
		include_once 'DBWork.php';
		include_once 'functions.php';
	
		//для МКБ
		function showTree3 ($level, $space, $type, $sel_id, $first, $last_level, $deleted, $dbtable, $insure_id, $dtype){
			
			$arr = array();
			$mkb_rez = array();
			$rez = array();
			
			$parent_str = '';
			//global $rez_str;
			$rez_str = '';
			
			$style_name = '';
			$color_array = array(
				'background-color: rgba(255, 236, 24, 0.5);',
				'background-color: rgba(103, 251, 66, 0.5);',
				'background-color: rgba(97, 227, 255, 0.5);',
			);
			$color_index = $last_level;
			
			/*$deleted_str = '';
			if ($deleted){
				//$deleted_str = 'AND `status` = 9';
			}else{
				//выбираем не удалённые
				$deleted_str = 'AND `status` <> 9';
			}*/
			
			//Если первый проход
			if ($first){
				require 'config.php';
			
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
			}
			
			//определяем уровень для запроса
			if ($level == NULL){
				$parent_str = '`parent_id` IS NULL';
			}else{
				$parent_str = '`parent_id` = '.$level;
			}
			
			//берем верхний уровень
			$query = "SELECT * FROM `$dbtable` WHERE ".$parent_str;
			
			$res = mysql_query($query) or die($query);
			$number = mysql_num_rows($res);
			if ($number != 0){
				while ($arr = mysql_fetch_assoc($res)){
					array_push($mkb_rez, $arr);
				}
			}else{
				$mkb_rez = 0;
			}
			//var_dump($mkb_rez[0]);
			
			if ($first){
				$rez_str .= '	
					<div style="margin: 10px 0 5px; font-size: 11px;">
						<span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>
					</div>';
				$rez_str .= '	
					<div style="width: 350px; height: 500px; overflow: scroll; border: 1px solid #CCC;">
						<ul class="ul-tree ul-drop" id="lasttree">';
			}
			
			if ($mkb_rez != 0){
				foreach ($mkb_rez as $mkb_rez_value){
					
					if ($mkb_rez_value['node_count'] > 0){
						$rez_str .= '	
							<li>
								<div class="drop" style="background-position: 0px 0px;"></div>
								<p class="drop"><b>'.$mkb_rez_value['code'].'</b> '.$mkb_rez_value['name'].'</p>';
								
						$rez_str .= '	
								<ul style="display: none;">';
								
						$rez_str .= showTree3($mkb_rez_value['id'], '', 'list', 0, FALSE, 0, FALSE, 'spr_mkb', 0, 0);
						
						$rez_str .= '	
								</ul>';
						$rez_str .= '	
							</li>';
								
					}else{
						$rez_str .= '	
								<li>
									<p onclick="checkMKBItem('.$mkb_rez_value['id'].');">'.$mkb_rez_value['name'].'</p>
								</li>';
					}
					
					//if ($type == 'list'){
						//echo $space.$value['name'].'<br>';
						
						//играемся с цветом	
						/*if ($value['level'] == 0) {
							$style_name = 'font-size: 130%;';
							$style_name .= $color_array[0];
							//$this_level = 0;
						}else{
							$style_name = 'font-size: 110%; font-style: oblique;';
							//$style_name .= 'background-color: rgba(97, 227, 255, 0.5)';
							if (isset($color_array[$color_index])){
								$style_name .= $color_array[$color_index];
							}else{
								$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
							}
						}*/
				}
			}
			
			if ($first){
				$rez_str .= '	
					</ul>
				</div>';

				mysql_close();
			}
			
			return $rez_str;
		}
		

		
		
		echo '
			<div id="data">';
			
		echo showTree3(NUll, '', 'list', 0, TRUE, 0, FALSE, 'spr_mkb', 0, 0);

		echo '
			</div>';

				//Прайс	
				/*echo '	
						<div style="margin: 10px 0 5px; font-size: 11px;">
							<span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action 	">раскрыть всё</span>
						</div>';
					
				echo '
						<div style="width: 350px; height: 500px; overflow: scroll; border: 1px solid #CCC;">
							<ul class="ul-tree ul-drop" id="lasttree">';

				showTree2(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelist_template', 0, 5);		
					
				echo '
							</ul>
						</div>
						';	*/
				
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>