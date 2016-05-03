<?php

//index.php
//Главная

	require_once 'header.php';

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_SESSION['id'] =='1'){
			include_once 'DBWork.php';
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Стоматология</h1>';
			
			echo '
						<a href="add_task_stomat.php" class="b">Добавить</a>

				</header>';
			if ($_SESSION['id'] = '1'){
				$user = SelDataFromDB('spr_workers', $_SESSION['id'], 'user');
				//var_dump ($user);
				echo 'Врач: '.$user[0]['name'];
				
				$journal = SelDataFromDB('journal_stomat', $_SESSION['id'], 'worker_stom_id');
			}else{
				//!
			}
			
			if ($journal != 0){
				echo '
					<p style="margin: 5px 0; padding: 2px;">
						Быстрый поиск: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
					</p>
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold;">	
								<div class="cellName" style="text-align: center">Дата</div>
								<div class="cellName" style="text-align: center">Пациент</div>
								<div class="cellName" style="text-align: center">Терапия</div>
								<div class="cellName" style="text-align: center">Хирургия</div>
								<div class="cellName" style="text-align: center">Ортопедия</div>
								<div class="cellName" style="text-align: center">Имплантация</div>
								<div class="cellName" style="text-align: center">Ортодонтия</div>
								<div class="cellName" style="text-align: center">Парад?</div>
								<div class="cellName" style="text-align: center">З.О.?</div>
								<div class="cellName" style="text-align: center">Сертификата</div>
								<div class="cellName" style="text-align: center">Под. лечение</div>
								<div class="cellText" style="text-align: center">Комментарий</div>
							</li>';
					for ($i = 0; $i < count($journal); $i++) {
					echo '
						<li class="cellsBlock">
								<div class="cellName" style="text-align: center">'.date('d.m.y H:i', $journal[$i]['create_time']).'</div>
								<div class="cellName" style="text-align: center">'.$journal[$i]['client'].'</div>
								<div class="cellName" style="text-align: center">14,13,44,45</div>
								<div class="cellName" style="text-align: center">24,21,11</div>
								<div class="cellName" style="text-align: center">(х).</div>
								<div class="cellName" style="text-align: center"></div>
								<div class="cellName" style="text-align: center"></div>
								<div class="cellName" style="text-align: center"></div>
								<div class="cellName" style="text-align: center">x</div>
								<div class="cellName" style="text-align: center"></div>
								<div class="cellName" style="text-align: center"></div>
								<div class="cellText" style="text-align: center">Комментарий</div>
						</li>';
				}
				echo '
						</ul>
					</div>';
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}
		
	require_once 'footer.php';

?>