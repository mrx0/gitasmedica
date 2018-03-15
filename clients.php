<?php

//index.php
//Главная

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($clients['see_all'] == 1) || ($clients['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			$offices = SelDataFromDB('spr_filials', '', '');
			//var_dump ($offices);
			$filter = FALSE;
			$sw = '';
			$filter_rez = array();
			
			$deleted_clients = '';
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Список пациентов</h1>';
			if ($_GET){
				//var_dump($_GET);
				$filter_rez = array();
				if (!empty($_GET['filter']) && ($_GET['filter'] == 'yes')){
					$_GET['datastart'] = $_GET['sel_date_I'].'.'.$_GET['sel_month_I'].'.'.$_GET['sel_year_I'];
					//echo $_GET['datastart'].'<br />';
					$_GET['dataend'] = $_GET['sel_date_II'].'.'.$_GET['sel_month_II'].'.'.$_GET['sel_year_II'];
					//echo $_GET['dataend'];
					$_GET['datatable'] = 'spr_clients';
					$filter_rez = filterFunction ($_GET);
					$filter = TRUE;
				}elseif (!empty($_GET['alpha']) && ($_GET['alpha'] != '')){
					$sw .= $_GET['alpha'];
					$type = 'alpha';
				}else{
					$sw .= 'А';
					$type = 'alpha';

				}
				
			}else{
				$sw .= 'А';
				$type = 'alpha';
			}
			if ($filter){
				$sw = $filter_rez[1];
				//echo $sw;
				//var_dump ($filter_rez);
				echo $filter_rez[0];
				//echo $filter_rez[1];
				$clients_j = SelDataFromDB('spr_clients', $sw, 'filter');
			}else{
				//echo $type;
				$clients_j = SelDataFromDB('spr_clients', $sw, $type);
			}
			


			//$clients_j = SelDataFromDB('spr_clients', '', '');
			//var_dump ($clients_j);
			//$arr_permissions = SelDataFromDB('spr_permissions', '', '');
			//var_dump ($arr_permissions);
			//$arr_orgs = SelDataFromDB('spr_org', '', '');
			//var_dump ($arr_orgs);
			
			if (($clients['add_new'] == 1) || $god_mode){
				echo '
					<a href="client_add.php" class="b">Добавить</a>';
			}
			if (!$filter){
				echo '<button class="md-trigger b" data-modal="modal-11">Поиск</button>';
			}
			echo '
				</header>';
			
			DrawFilterOptions ('clients', $it, $cosm, $stom, $workers, $clients, $offices, $god_mode);

			echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 101;">';

            echo $block_fast_search_client;

            echo '
					</div>';
			echo '
					<br />
					<div>
						Сортировка по алфавиту<br />
						<a href="clients.php?alpha=А" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'А') || empty($_GET['alpha'])) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">А</a> 
						<a href="clients.php?alpha=Б" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Б')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Б</a>  
						<a href="clients.php?alpha=В" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'В')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">В</a>  
						<a href="clients.php?alpha=Г" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Г')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Г</a>  
						<a href="clients.php?alpha=Д" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Д')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Д</a>  
						<a href="clients.php?alpha=Е" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Е')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Е</a>  
						<a href="clients.php?alpha=Ё" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Ё')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Ё</a>  
						<a href="clients.php?alpha=Ж" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Ж')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Ж</a>  
						<a href="clients.php?alpha=З" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'З')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">З</a>  
						<a href="clients.php?alpha=И" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'И')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">И</a>  
						<a href="clients.php?alpha=Й" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Й')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Й</a>  
						<a href="clients.php?alpha=К" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'К')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">К</a>  
						<a href="clients.php?alpha=Л" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Л')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Л</a>
						<a href="clients.php?alpha=М" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'М')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">М</a>  
						<a href="clients.php?alpha=Н" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Н')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Н</a>  
						<a href="clients.php?alpha=О" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'О')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">О</a>  
						<a href="clients.php?alpha=П" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'П')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">П</a>  
						<a href="clients.php?alpha=Р" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Р')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Р</a>  
						<a href="clients.php?alpha=С" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'С')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">С</a>  
						<a href="clients.php?alpha=Т" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Т')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Т</a>  
						<a href="clients.php?alpha=У" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'У')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">У</a>  
						<a href="clients.php?alpha=Ф" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Ф')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Ф</a>  
						<a href="clients.php?alpha=Х" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Х')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Х</a>  
						<a href="clients.php?alpha=Ц" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Ц')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Ц</a>  
						<a href="clients.php?alpha=Ч" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Ч')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Ч</a>  
						<a href="clients.php?alpha=Ш" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Ш')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Ш</a>  
						<a href="clients.php?alpha=Щ" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Щ')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Щ</a>  
						<a href="clients.php?alpha=Ъ" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Ъ')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Ъ</a>  
						<a href="clients.php?alpha=Ы" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Ы')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Ы</a>  
						<a href="clients.php?alpha=Ь" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Ь')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Ь</a>  
						<a href="clients.php?alpha=Э" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Э')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Э</a>  
						<a href="clients.php?alpha=Ю" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Ю')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Ю</a>  
						<a href="clients.php?alpha=Я" class="', (!empty($_GET['alpha']) && ($_GET['alpha'] == 'Я')) ? 'AlphaSearchSel' : 'AlphaSearch' ,'">Я</a> 
					</div>
					';
			
			if ($clients_j != 0){
				echo '
				
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold;">	
								<div class="cellFullName" style="text-align: center">
                                    Полное имя';
                echo $block_fast_filter;
                echo '
								</div>';
				//if (($stom['add_own'] == 1) || $god_mode){
					/*echo '
								<div class="cellCosmAct" style="text-align: center" title="Добавить посещение Стоматолога">C</div>';*/
				//}
					echo '
								<div class="cellCosmAct" style="text-align: center" title="Страховое">Стр.</div>';
								
				/*if (($cosm['add_own'] == 1) || $god_mode){
					echo '
								<div class="cellCosmAct" style="text-align: center" title="Добавить посещение Косметолога">К</div>';
				}*/

				/*if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
					echo '
								<div class="cellCosmAct" style="text-align: center" title="История пациента (стоматология)">И</div>';
				}*/

				echo '
								<div class="cellCosmAct" style="text-align: center">Пол</div>
								<div class="cellCosmAct" style="text-align: center; width: 80px; min-width: 80px; max-width: 80px;">Карта</div>
								<div class="cellTime" style="text-align: center">Д. рож.</div>
								<div class="cellCosmAct" style="text-align: center">Лет</div>
								<div class="cellFullName" style="text-align: center">Контакты</div>
								<div class="cellText" style="text-align: center">Комментарий</div>
							</li>';

				for ($i = 0; $i < count($clients_j); $i++) { 
				//var_dump($clients_j);
				//var_dump($_SESSION['id']);
				//	if (isset($_GET['own_clients']) && ($_GET['own_clients'] == 'yes') && ($_SESSION['id'] == $clients_j[$i]['therapist'])){
				//		var_dump('мой');
				//	}
					if ($clients_j[$i]['status'] == 9){
						$bgcolor = 'background-color: rgba(161,161,161,1);';
					}else{
						$bgcolor = '';
					}

					if ($clients_j[$i]['status'] != 9){
						echo '
							<li class="cellsBlock cellsBlockHover" style="'.$bgcolor.'">
								<a href="client.php?id='.$clients_j[$i]['id'].'" class="cellFullName ahref 4filter" id="4filter">'.$clients_j[$i]['full_name'].'</a>';
								
						echo '
									<div class="cellCosmAct" style="text-align: center">';
						if (($clients_j[$i]['polis'] != '') && ($clients_j[$i]['insure'] != '')){
							echo '<img src="img/insured.png" title="Страховое">';
						}
						echo '
									</div>';
					}else{
						$deleted_clients .= '
							<li class="cellsBlock cellsBlockHover" style="'.$bgcolor.'">
								<a href="client.php?id='.$clients_j[$i]['id'].'" class="cellFullName ahref 4filter" id="4filter">'.$clients_j[$i]['full_name'].'</a>';
						$deleted_clients .= '
									<div class="cellCosmAct" style="text-align: center">';
						if (($clients_j[$i]['polis'] != '') && ($clients_j[$i]['insure'] != '')){
							echo '<img src="img/insured.png" title="Страховое">';
						}
						$deleted_clients .= '
									</div>';
					}						
					if ($clients_j[$i]['status'] != 9){
						/*if (($stom['add_own'] == 1) || $god_mode){
							echo '
										<div class="cellCosmAct" style="text-align: center"><a href="add_error.php"><img src="img/stom_add.png" title="Добавить посещение Стоматолога"></a></div>';
						}*/

						/*if (($cosm['add_own'] == 1) || $god_mode){
							echo '
										<div class="cellCosmAct" style="text-align: center"><a href="add_error.php"><img src="img/cosm_add.png" title="Добавить посещение Косметолога"></a></div>';
						}*/

						/*if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
							echo '
										<div class="cellCosmAct" style="text-align: center"><a href="stom_history.php?client='.$clients_j[$i]['id'].'"><img src="img/stom_hist.png" title="История пациента (стоматология)"></a></div>';
						}*/

					}else{
						$deleted_clients .= '
							<!--<div class="cellCosmAct" style="text-align: center"></div>-->
							<div class="cellCosmAct" style="text-align: center"></div>
							';
					}
					if ($clients_j[$i]['status'] != 9){
						echo '
									<div class="cellCosmAct" style="text-align: center">';
						if ($clients_j[$i]['sex'] != 0){
							if ($clients_j[$i]['sex'] == 1){
								echo 'М';
							}
							if ($clients_j[$i]['sex'] == 2){
								echo 'Ж';
							}
						}else{
							echo '-';
						}
						
						echo '
									</div>';
						echo '
									<div class="cellCosmAct" style="text-align: center; width: 80px; min-width: 80px; max-width: 80px;">'.$clients_j[$i]['card'].'</div>';
			
						echo '
									<div class="cellTime" style="text-align: center">';
						if ($clients_j[$i]['birthday2'] == '0000-00-00'){
							echo 'не указана';
						}else{
							echo date('d.m.Y', strtotime($clients_j[$i]['birthday2']));
						}
						echo '				
									</div>';
						echo '
									<div class="cellCosmAct" style="text-align: center">';
						if ($clients_j[$i]['birthday2'] == '0000-00-00'){
							echo '-';
						}else{
							echo '<b>'.getyeardiff(strtotime($clients_j[$i]['birthday2']), 0).'</b>';
						}
						echo '	
									</div>';
									
						echo '	
									<div class="cellFullName">'.$clients_j[$i]['telephone'];
						if ($clients_j[$i]['htelephone'] != ''){
							echo '
										дом. '.$clients_j[$i]['htelephone'].'';
						}
						if ($clients_j[$i]['telephoneo'] != ''){
							echo '
										тел.оп. '.$clients_j[$i]['telephoneo'].'';
						}
						if ($clients_j[$i]['htelephoneo'] != ''){
							echo '
										дом.тел.оп. '.$clients_j[$i]['htelephoneo'].'';
						}
						echo '
									</div>
									<div class="cellText">'.$clients_j[$i]['comment'].'</div>
								</li>';
								
								
					}else{
						$deleted_clients .= '
									<div class="cellCosmAct" style="text-align: center">';
						if ($clients_j[$i]['sex'] != 0){
							if ($clients_j[$i]['sex'] == 1){
								$deleted_clients .= 'М';
							}
							if ($clients_j[$i]['sex'] == 2){
								$deleted_clients .= 'Ж';
							}
						}else{
							$deleted_clients .= '-';
						}
						
						$deleted_clients .= '
									</div>';
						$deleted_clients .= '
									<div class="cellCosmAct" style="text-align: center; width: 60px; min-width: 60px; max-width: 60px;">'.$clients_j[$i]['card'].'</div>';
						$deleted_clients .= '
									<div class="cellTime" style="text-align: center">';

                        if ($clients_j[$i]['birthday2'] == '0000-00-00'){
							$deleted_clients .= 'не указана';
						}else{
							$deleted_clients .= date('d.m.Y', strtotime($clients_j[$i]['birthday2']));
						}

						$deleted_clients .= '
									</div>';
						$deleted_clients .= '
									<div class="cellCosmAct" style="text-align: center">';

                        if ($clients_j[$i]['birthday2'] == '0000-00-00'){
							$deleted_clients .= '-';
						}else{
							$deleted_clients .= '<b>'.getyeardiff(strtotime($clients_j[$i]['birthday2']), 0).'</b>';
						}

						$deleted_clients .= '
									</div>';
						$deleted_clients .= '
									<div class="cellFullName">'.$clients_j[$i]['telephone'];
						if ($clients_j[$i]['htelephone'] != ''){
							$deleted_clients .= '
										дом. '.$clients_j[$i]['htelephone'].'';
						}
						$deleted_clients .= '
									</div>
									<div class="cellText">'.$clients_j[$i]['comment'].'</div>
								</li>';
					}
				}
				
				echo $deleted_clients;
				
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
			echo '
					</ul>
					<div id="doc_title">Пациенты ['.$sw.'] - Асмедика</div>
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>