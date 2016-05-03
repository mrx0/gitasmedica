<?php

//it.php
//IT

	require_once 'header.php';

	if ($enter_ok){
		if (($it['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			$offices = SelDataFromDB('spr_office', '', '');
			//var_dump ($offices);
			$filter = FALSE;
			$sw = '';
			$filter_rez = array();
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Задачи Системных администраторов</h1>';
			if ($_GET){
				//var_dump($_GET);
				$filter_rez = array();
				if (!empty($_GET['filter']) && ($_GET['filter'] == 'yes')){
					$_GET['datatable'] = 'journal_it';
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
				$journal = SelDataFromDB('journal_it', $sw, 'filter');
			}else{
				$journal = SelDataFromDB('journal_it', $sw, $type);
			}
			//var_dump ($journal);

			if (($it['add_new'] == 1) || $god_mode){
				echo '
						<a href="add_task.php" class="b">Добавить</a>';
			}
			
			if (!$filter){
				echo '<button class="md-trigger b" data-modal="modal-11">Фильтр</button>';
			}
			
			if (($it['add_new'] == 1) || $god_mode){
				echo '<a href="report_it.php" class="b">Отчет</a>';
			}
			
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
								<div class="cellPriority" style="text-align: center"></div>
								<a href="it.php?sort_added=1" class="cellTime ahref" style="text-align: center">Добавлено</a>
								<a href="it.php?sort_filial=1" class="cellOffice ahref" style="text-align: center">Филиал</a>
								<div class="cellText" style="text-align: center">Описание проблемы</div>
								<div class="cellName" style="text-align: center">Автор</div>
								<div class="cellName" style="text-align: center">Исполнитель</div>
								<div class="cellTime" style="text-align: center">Закрыто</div>
							</li>';

				for ($i = 0; $i < count($journal); $i++) { 
					if ($journal[$i]['priority'] == 1){
						$priority_color = '#FFFF3D';
					}elseif ($journal[$i]['priority'] == 2){
						$priority_color = '#FF9900';
					}elseif ($journal[$i]['priority'] == 3){
						$priority_color = '#E70F2F';
					}
					if ($journal[$i]['office'] == 99){
						$office = 'Во всех';
					}else{
						$offices = SelDataFromDB('spr_office', $journal[$i]['office'], 'offices');
						//var_dump ($offices);
						$office = $offices[0]['name'];
					}
					$worker = SelDataFromDB('spr_workers', $journal[$i]['worker'], 'worker_id');
					//var_dump ($worker);
					$author = SelDataFromDB('spr_workers', $journal[$i]['create_person'], 'worker_id');
					//
					if ($journal[$i]['end_time'] == 0){
						$ended = 'Нет';
						$background_style = '';
						$background_style2 = '
							background: rgba(231,55,71, 0.9);
							color:#fff;
							';
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
								<div class="cellPriority" style="background-color:'.$priority_color.'"></div>
								<div class="cellTime">'.date('d.m.y H:i', $journal[$i]['create_time']).'</div>
								<div class="cellOffice" style="text-align: center;"><a href="" class="ahref">'.$office.'</a></div>
								<a href="task.php?id='.$journal[$i]['id'].'" class="ahref cellText" style="'.$background_style.'"><b>#'.$journal[$i]['id'].'</b> '.$journal[$i]['description'].'</a>
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