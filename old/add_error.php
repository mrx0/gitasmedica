<?php

//add_error.php
//Добавить ошибку

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
			
			echo '
				<div id="status">
					<header>
						<span style= "color: red; font-size: 120%;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><br><br></span>
						<span style= "color: rgba(255, 16, 16, 0.7); text-shadow: 0px 1px 0px #999; font-size: 110%;">
							Добавлять посещения теперь необходимо через запись пациентов.<br><br>
						</span>
						<span style= "color: #777; font-size: 110%;">
							Найти запись пациента можно непосредственно через график работы врача.<br>
							В разделе "График" или в профиле самого врача.<br>
							А также в карточке пациента.<br><br>
							
							При условии наличия отметки администратором о том, что пациент пришёл,<br>
							справа появляется кнопка:<br><br>
							 - Для стоматологов - <b style= "color: rgba(255, 16, 16, 0.7);">Внести Осмотр/Зубную формулу</b><br>
							 - Для косметологов - <b style= "color: rgba(255, 16, 16, 0.7);">Внести посещение косм</b><br>
						</span>
					</header>';

			echo '
					<div id="data">
					</div>
				</div>';
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>