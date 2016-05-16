<?php

//soft.php
//ПО

//2016 05 16 изменить БД для предложений
//ALTER TABLE `journal_soft` ADD `inwork` INT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `worker` ;

	require_once 'header.php';

	if ($enter_ok){
		if (($soft['see_all'] == 1) || ($soft['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'filter.php';
			include_once 'filter_f.php';

			$filter = FALSE;
			$sw = '';
			$filter_rez = array();
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Программная часть</h1>';
			if ($_GET){
				$filter_rez = array();
				if (!empty($_GET['filter']) && ($_GET['filter'] == 'yes')){
					$filter_rez = filterFunction ($_GET);
					$filter = TRUE;
				}else{
					if  (!empty($_GET['sort_added'])){
					//Для сортировки по времени добавления
						$sw .= '';
						$type = 'sort_added';
					}elseif  (!empty($_GET['sort_filial'])){
					//Для сортировки по филиалу добавления
						$sw .= '';
						$type = 'sort_filial';
					}else{
						$sw .= '';
						$type = '';
					}
				}
				
			}else{
				$sw .= '';
				$type = '';
			}
			if ($filter){
				$sw = $filter_rez[1];
				//echo $sw;
				//var_dump ($filter_rez);
				echo $filter_rez[0];
				//echo $filter_rez[1];
				$journal = SelDataFromDB('journal_soft', $sw, 'filter');
			}else{
				if (($soft['see_all'] == 1) || $god_mode){
					$journal = SelDataFromDB('journal_soft', $sw, $type);
				}elseif ($soft['see_own'] == 1){
					$journal = SelDataFromDB('journal_soft', $_SESSION['id'], 'see_own');
				}
			}
			//var_dump ($journal);

			if (($soft['add_own'] == 1) || $god_mode){
				echo '
						<a href="add_task_soft.php" class="b">Добавить</a>';
			}
			
			/*if (!$filter){
				echo '<button class="md-trigger b" data-modal="modal-11">Фильтр</button>';
			}*/
			
			echo '
				</header>';
			
			DrawFilterOptions ('it', $it, $cosm, $stom, $workers, $clients, $offices, $god_mode);
			if ($journal != 0){
						
			

				echo '
					<!--<p style="margin: 5px 0; padding: 2px;">
						Быстрый поиск: 
						<input type="text" class="filter" name="livefilter" value="" placeholder="Поиск"/>
					</p>-->
					<div id="data">
						<ul class="live_filter" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold;">	
								<div class="cellName" style="text-align: center">Раздел</div>
								<a href="soft.php?sort_added=1" class="cellTime ahref" style="text-align: center">Добавлено</a>
								<div class="cellText" style="text-align: center">Описание</div>
								<div class="cellName" style="text-align: center">Автор</div>
								<div class="cellName" style="text-align: center">Исполнитель</div>
								<div class="cellTime" style="text-align: center">Статус</div>
							</li>';

				for ($i = 0; $i < count($journal); $i++) { 
					if ($journal[$i]['priority'] == 1){
						$razdel = 'Косметология';
					}elseif ($journal[$i]['priority'] == 2){
						$razdel = 'Стоматология';
					}elseif ($journal[$i]['priority'] == 3){
						$razdel = 'Другое';
					}else{
						$razdel = 'unknown';
					}

					$worker = SelDataFromDB('spr_workers', $journal[$i]['worker'], 'worker_id');
					//var_dump ($worker);
					$author = SelDataFromDB('spr_workers', $journal[$i]['create_person'], 'worker_id');
					//
					if ($journal[$i]['end_time'] == 0){
						if ($journal[$i]['inwork'] == 0){
							$ended = 'Рассмотрение';
							$background_style = '';
							$background_style2 = '
								background: rgba(231,55,71, 0.9);
								color:#fff;
								';
						}
						if ($journal[$i]['inwork'] == 1){
							$ended = 'В работе';
							$background_style = '
								background: rgba(231,175,55, 0.8);
								color:#fff;
								';
							$background_style2 = '
								background: rgba(231,175,55, 0.8);
								color:#fff;
								';
						}
						if ($journal[$i]['inwork'] == 2){
							$ended = 'Отказ';
							$background_style = '
								background: rgba(185,145,255, 0.9);
								color:#fff;
								';
							$background_style2 = '
								background: rgba(185,145,255, 0.9);
								color:#fff;
								';
						}
					}else{
						$ended = date('d.m.y H:i', $journal[$i]['end_time']);
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
					}
					echo '
							<li class="cellsBlock cellsBlockHover">
								<div class="cellName">'.$razdel.'</div>
								<div class="cellTime">'.date('d.m.y H:i', $journal[$i]['create_time']).'</div>
								<a href="task_soft.php?id='.$journal[$i]['id'].'" class="ahref cellText" style="'.$background_style.'">'.$journal[$i]['description'].'</a>
								<div class="cellName" style="text-align: center;">'.$author[0]['name'].'</div>
								<div class="cellName" style="text-align: center;">'.$worker[0]['name'].'</div>
								<div class="cellTime" style="text-align: center; '.$background_style2.'">'.$ended.'</div>
							</li>';
				}
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}

			echo '
					</ul>
				</div>
				<script type="text/javascript">

					function DropDown(el) {
						this.dd = el;
						this.initEvents();
					}
					DropDown.prototype = {
						initEvents : function() {
							var obj = this;

							obj.dd.on(\'click\', function(event){
								$(this).toggleClass(\'active\');
								event.stopPropagation();
							});	
						}
					}

					$(function() {

						var dd = new DropDown( $(\'#dd\') );

						$(document).click(function() {
							// all dropdowns
							$(\'.wrapper-dropdown-2\').removeClass(\'active\');
						});

					});

					function DropDown(el) {
						this.dd2 = el;
						this.initEvents();
					}
					DropDown.prototype = {
						initEvents : function() {
							var obj = this;

							obj.dd2.on(\'click\', function(event){
								$(this).toggleClass(\'active\');
								event.stopPropagation();
							});	
						}
					}

					$(function() {

						var dd2 = new DropDown( $(\'#dd2\') );

						$(document).click(function() {
							// all dropdowns
							$(\'.wrapper-dropdown-2\').removeClass(\'active\');
						});

					});

				</script>
				
				';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>