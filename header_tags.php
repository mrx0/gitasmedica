<?php

//header_tags.php
//Заголовок страниц сайта
	
	$god_mode = FALSE;
	
	$version = 'v 14.12.2016';
	
	echo'
		<!DOCTYPE html>
		<html>
		<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
			<meta name="description" content=""/>
			<meta name="keywords" content="" />
			<meta name="author" content="" />
			
			<title>Асмедика</title>
			
			<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
			<!-- Font Awesome -->
			<link rel="stylesheet" href="css/font-awesome.css">
			
			<link rel="stylesheet" href="css/style.css" type="text/css" />
			<!--<link rel="stylesheet" href="css/menu.css">-->
			<!--<link rel="stylesheet" type="text/css" href="css/default.css" />-->
			<link rel="stylesheet" type="text/css" href="css/component.css" />
			<link rel="stylesheet" type="text/css" href="css/ModalZakaz.css" />
			<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">	
			<link rel="stylesheet" type="text/css" href="css/pretty.css" />
			
			<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

			<link rel="stylesheet" href="css/calendar.css" type="text/css">
						
			<script type="text/javascript" src="js/dict.js"></script>
			<script type="text/javascript" src="js/common1.js"></script>

			<script src="js/chart.js" type="text/javascript"></script>

			<script src="js/tooth_status.js" type="text/javascript"></script>
			<script src="js/path2.js" type="text/javascript"></script>

			<!--<script type="text/javascript" src="js/jquery-1.4.3.min.js"></script>-->
			<!--<script type="text/javascript" src="js/jquery-1.11.3.js"></script>-->
			
			<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
			<script type="text/javascript" src="js/modernizr.custom.79639.js"></script> 
			
			<script type="text/javascript" src="js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
			<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
			<script type="text/javascript" src="jquery.liveFilter.js"></script>
			<script type="text/javascript" src="js/search.js"></script>
			<script type="text/javascript" src="js/search2.js"></script>

			<script type="text/javascript" src="js/search4.js"></script>
			
			<script type="text/javascript" src="js/search_fast_client.js"></script>
			
			<script type="text/javascript" src="js/jquery.maskedinput-1.2.2.js"></script>
			
			<!--<script src="js/jquery.js" type="text/javascript"></script>-->

			<script src="js/raphael.js" type="text/javascript"></script>
			<!--<script src="js/init.js" type="text/javascript"></script>-->

			<script src="js/modernizr.custom.js"></script>

			<script src="js/jquery.scrollUp.js?1.1"></script>

			<script src="js/jszakaz.js"></script>
			<!--<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>-->
			<!--<script src="js/jquery-ui.min-1.8.js"></script>-->
			<script src="js/jquery-ui.min.js"></script>

			<!--чтобы менюшка прилеплялась к верхней части окна-->
			<script>
			jQuery("document").ready(function($){
				 
				var nav = $(".sticky");
				 
				$(window).scroll(function () {
					if ($(this).scrollTop() > 136) {
						nav.addClass("f-sticky");
					} else {
						nav.removeClass("f-sticky");
					}
				});
			  
			});
			</script>

			<script type="text/javascript">
				$(function(){
					$(document).tooltip();
				});
			</script>
	
			<script type="text/javascript">
				$(document).ready(function(){
					$("a.photo").fancybox({
						transitionIn: \'elastic\',
						transitionOut: \'elastic\',
						speedIn: 500,
						speedOut: 500,
						hideOnOverlayClick: false,
						titlePosition: \'over\'
					});
				});
			</script>
			
			<script type="text/javascript">
				$(function(){
					$(\'#livefilter-list\').liveFilter(\'#livefilter-input\', \'li\', {
						filterChildSelector: \'#4filter\'
					});
				});
			</script>
			
			<script>
				$(function () {
					$.scrollUp({
						animation: \'slide\',
						activeOverlay: false,
						scrollText: \'Наверх\',
					});
				});
			</script>
			
			<script src="js/multiselect.js"></script>

			<script src="js/DrawTeethMapMenu.js"></script>

			<script type="text/javascript">
				function XmlHttp()
				{
				var xmlhttp;
				try{xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");}
				catch(e)
				{
				 try {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");} 
				 catch (E) {xmlhttp = false;}
				}
				if (!xmlhttp && typeof XMLHttpRequest!=\'undefined\')
				{
				 xmlhttp = new XMLHttpRequest();
				}
				  return xmlhttp;
				}
				 
				function ajax(param)
				{
								if (window.XMLHttpRequest) req = new XmlHttp();
								method=(!param.method ? "POST" : param.method.toUpperCase());
				 
								if(method=="GET")
								{
											   send=null;
											   param.url=param.url+"&ajax=true";
								}
								else
								{
									send="";
									for (var i in param.data) send+= i+"="+param.data[i]+"&";
									send=send+"ajax=true";
								}
				 
								req.open(method, param.url, true);
								if(param.statbox)
									document.getElementById(param.statbox).innerHTML = \'<img src="img/wait.gif"> обработка...\';
								
								req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
								req.send(send);
								req.onreadystatechange = function()
								{
									if (req.readyState == 4 && req.status == 200) //если ответ положительный
									{
										if(param.success)param.success(req.responseText);
									}
								}
							}
			</script>
			
			<!--для скрытых блоков старое-->				
			<script type="text/javascript">
				function switchDisplay(id){
					var el = document.getElementById(id);
					if (!!el)
						el.style.display = (el.style.display=="none") ? "" : "none";
					return false;
				}
			</script>

			<script type="text/javascript">
				$( document ).ready(typeres())
				function typeres() {
					$(\'.tabs\').hide();
					var etabs = document.getElementById("tabSelector");
					if (etabs != null){
						if (etabs.options[etabs.selectedIndex].value.indexOf("tabs-") != -1) {
							var tab = \'#tabs-\'+etabs.options[etabs.selectedIndex].value.substring(5);
							$(tab).fadeIn();
						}
					}
				}
			</script>

			<!--для печати-->	
			<style type="text/css" media="print">
			  div.no_print {display: none; }
			</style> 

		</head>
		<body>
		
		<!--<ul class="navigation">
			<li class="nav-item"><a href="index.php">Главная</a></li>
			<li class="nav-item"><a href="it.php">IT</a></li>
			<li class="nav-item"><a href="stomat.php">Стоматология</a></li>
			<li class="nav-item"><a href="cosmet.php">Косметология</a></li>
			<li class="nav-item"><a href="contacts.php">Сотрудники</a></li>
			<li class="nav-item"><a href="clients.php">Пациенты</a></li>
			<li class="nav-item"><a href="filials.php">Филиалы</a></li>
		</ul>

		<input type="checkbox" id="nav-trigger" class="nav-trigger" />
		<label for="nav-trigger"></label>

	<div class="site-wrap">-->
		<div class="no_print"> 
		<header class="h">
			<nav>
				<ul>';
	//Если в системе
	if ($enter_ok){
		include_once 'DBWork.php';
		if ($_SESSION['permissions'] == '777'){
			$god_mode = TRUE;
		}else{
			//Получили список прав
			$permissions = SelDataFromDB('spr_permissions', $_SESSION['permissions'], 'id');	
			//var_dump($permissions);
		}
		if (!$god_mode){
			if ($permissions != 0){
				$it = json_decode($permissions[0]['it'], true);
				//var_dump($it);
				$cosm = json_decode($permissions[0]['cosm'], true);
				$stom = json_decode($permissions[0]['stom'], true);
				$clients = json_decode($permissions[0]['clients'], true);
				$workers = json_decode($permissions[0]['workers'], true);
				$offices = json_decode($permissions[0]['offices'], true);
				$soft = json_decode($permissions[0]['soft'], true);
				$scheduler = json_decode($permissions[0]['scheduler'], true);
				$zapis = json_decode($permissions[0]['zapis'], true);
				$report = json_decode($permissions[0]['report'], true);
				$spravka = json_decode($permissions[0]['spravka'], true);
				$finances = json_decode($permissions[0]['finances'], true);
				$items = json_decode($permissions[0]['items'], true);
				//var_dump($spravka);
			}
		}else{
			//Видимость
			$it['see_all'] = 0;
			$it['see_own'] = 0;
			$cosm['see_all'] = 0;
			$cosm['see_own'] = 0;
			$stom['see_all'] = 0;
			$stom['see_own'] = 0;
			$workers['see_all'] = 0;
			$workers['see_own'] = 0;
			$clients['see_all'] = 0;
			$clients['see_own'] = 0;
			$offices['see_all'] = 0;
			$offices['see_own'] = 0;
			$soft['see_all'] = 0;
			$soft['see_own'] = 0;
			$scheduler['see_all'] = 0;
			$scheduler['see_own'] = 0;
			$zapis['see_all'] = 0;
			$zapis['see_own'] = 0;
			$report['see_all'] = 0;
			$report['see_own'] = 0;
			$spravka['see_all'] = 0;
			$spravka['see_own'] = 0;
			$finances['see_all'] = 0;
			$finances['see_own'] = 0;
			$items['see_all'] = 0;
			$items['see_own'] = 0;
			//
			$it['add_new'] = 0;
			$it['add_own'] = 0;
			$cosm['add_new'] = 0;
			$cosm['add_own'] = 0;
			$stom['add_new'] = 0;
			$stom['add_own'] = 0;
			$workers['add_new'] = 0;
			$workers['add_own'] = 0;
			$clients['add_new'] = 0;
			$clients['add_own'] = 0;
			$offices['add_new'] = 0;
			$offices['add_own'] = 0;
			$soft['add_new'] = 0;
			$soft['add_own'] = 0;
			$scheduler['add_new'] = 0;
			$scheduler['add_own'] = 0;
			$zapis['add_new'] = 0;
			$zapis['add_own'] = 0;
			$report['add_new'] = 0;
			$report['add_own'] = 0;
			$spravka['add_new'] = 0;
			$spravka['add_own'] = 0;
			$finances['add_new'] = 0;
			$finances['add_own'] = 0;
			$items['add_new'] = 0;
			$items['add_own'] = 0;
			//
			$it['edit'] = 0;
			$cosm['edit'] = 0;
			$stom['edit'] = 0;
			$workers['edit'] = 0;
			$clients['edit'] = 0;
			$offices['edit'] = 0;
			$soft['edit'] = 0;
			$scheduler['edit'] = 0;
			$zapis['edit'] = 0;
			$report['edit'] = 0;
			$spravka['edit'] = 0;
			$finances['edit'] = 0;
			$items['edit'] = 0;
			//
			$it['close'] = 0;
			$cosm['close'] = 0;
			$stom['close'] = 0;
			$workers['close'] = 0;
			$clients['close'] = 0;
			$offices['close'] = 0;
			$soft['close'] = 0;
			$scheduler['close'] = 0;
			$zapis['close'] = 0;
			$report['close'] = 0;
			$spravka['close'] = 0;
			$finances['close'] = 0;
			$items['close'] = 0;
			//
			$it['reopen'] = 0;
			$cosm['reopen'] = 0;
			$stom['reopen'] = 0;
			$workers['reopen'] = 0;
			$clients['reopen'] = 0;
			$offices['reopen'] = 0;
			$soft['reopen'] = 0;
			$scheduler['reopen'] = 0;
			$zapis['reopen'] = 0;
			$report['reopen'] = 0;
			$spravka['reopen'] = 0;
			$finances['reopen'] = 0;
			$items['reopen'] = 0;
			//
			$it['add_worker'] = 0;
			$cosm['add_worker'] = 0;
			$stom['add_worker'] = 0;
			$workers['add_worker'] = 0;
			$clients['add_worker'] = 0;
			$offices['add_worker'] = 0;
			$soft['add_worker'] = 0;
			$scheduler['add_worker'] = 0;
			$zapis['add_worker'] = 0;
			$report['add_worker'] = 0;
			$spravka['add_worker'] = 0;
			$finances['add_worker'] = 0;
			$items['add_worker'] = 0;
			//
			
		}
		echo '<a href="index.php">Главная<div style="font-size:60%">'.$version.'</div></a>';
		
		if (($it['see_all'] == 1) || ($it['see_own'] == 1) || $god_mode){
			echo '<li><a href="it.php" title="IT">IT</a></li>';
		}
		/*if (($soft['see_all'] == 1) || ($soft['see_own'] == 1) || $god_mode){
			echo '<li><a href="soft.php">Программа</a></li>';
		}*/
		if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
			echo '<li><a href="stomat.php" title="Стоматология">Стоматология</a></li>';
		}
		if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
			echo '<li><a href="cosmet.php" title="Косметология">Косметология</a></li>';
		}
		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			echo '<li><a href="scheduler.php" title="График">График</a></li>';
		}
		if (($report['see_all'] == 1) || ($report['see_own'] == 1) || $god_mode){
			echo '<li><a href="reports.php" title="Статистика и отчёты"><i class="fa fa-bar-chart"></i></a></li>';
		}
		if (($clients['see_all'] == 1) || ($clients['see_own'] == 1) || $god_mode){
			echo '<li><a href="clients.php" title="Пациенты">Пациенты</a></li>';
		}
		if (($spravka['see_all'] == 1) || ($spravka['see_own'] == 1) || $god_mode){
			echo '<li><a href="directory.php" title="Справочники">Справочники</a></li>';
		}
		if ($god_mode){
			echo '<li><a href="admin.php"><i class="fa fa-cogs"></i></a></li>';
		}
	}
	
	//echo '<li><a href="/other/Fortest/asm_journal/" style="background: rgba(0,255,255,0.7); color: #ff0000;">В ТЕСТ</a></li>';
	//echo '<li><a href="/other/Fortest/asm_journal/" style="background: rgba(0,255,255,0.7); color: #ff0000;">В ТЕСТ</a></li>';

	
	echo
				'</ul>
				<ul style="float:right;">';
	if (!$enter_ok){
		echo '
					<li><a href="enter.php" title="Вход"><i class="fa fa-power-off"></i></a></li>';
	}else{
		
		$alarm = 0;
		$warning = 0;
		$pre_warning = 0;
		$if_notes = '';
		$if_removes = '';
		
		if ($stom['see_own'] == 1){
			$notes = SelDataFromDB ('notes', $_SESSION['id'], 'create_person');
		}elseif (($stom['see_all'] == 1) || $god_mode){
			$notes = SelDataFromDB ('notes', 'dead_line', 'dead_line');
		}else{
			$notes = 0;
		}
		
		if ($notes != 0){

			for ($i = 0; $i < count($notes); $i++) {
				if ($notes[$i]['closed'] == 0){
					$dead_line_time = $notes[$i]['dead_line'] - time() ;
					if ($dead_line_time <= 0){
						$alarm++;
					}elseif (($dead_line_time > 0) && ($dead_line_time <= 2*24*60*60)){
						$warning++;
					}elseif (($dead_line_time > 2*24*60*60) && ($dead_line_time <= 3*24*60*60)){
						$pre_warning++;
					}
				}
			}

			
			$if_notes .= '
				<a href="user.php?id='.$_SESSION['id'].'">';
			if ($alarm !=0)
				$if_notes .= '<span style="background:#FF1F0F;" class="notes">'.$alarm.'</span>';
			if ($warning !=0)
					$if_notes .= '<span style="background:#FF9900;" class="notes">'.$warning.'</span>';
			if ($pre_warning!=0)
					$if_notes .= '<span style="background:#EFDF3F;" class="notes">'.$pre_warning.'</span>';
			
			$if_notes .= '</a>';
		}else{
			$if_notes = '';
		}

					
					
		//Перенаправления мои	
		$removesMy = SelDataFromDB ('removes_open', $_SESSION['id'], 'create_person');
		//... Ко мне
		$removesMe = SelDataFromDB ('removes_open', $_SESSION['id'], 'whom');
		if (($removesMy != 0) || ($removesMe != 0)){
			$if_removes .= '<a href="user.php?id='.$_SESSION['id'].'"><span class="removes">';
			if($removesMy != 0){

				$if_removes .= count($removesMy);
			}
			if($removesMe != 0){
				$if_removes .= ' ( '.count($removesMe).' )';
			}
			$if_removes .= '</span></a>';
		}else{
			$if_removes = '';
		}
		
		//Для автоматизации выбора филиала
		if (isset($_SESSION['filial']) && !empty($_SESSION['filial'])){
			$filial = array();
			$offices_j = SelDataFromDB('spr_office', $_SESSION['filial'], 'offices');
			//var_dump($offices_j['name']);
			$selected_fil = $offices_j[0]['name'];
		}else{
			$selected_fil = '-';
		}
		
		
		echo '
					<li>'.$if_removes.$if_notes.'<a href="user.php?id='.$_SESSION['id'].'" class="href_profile"><div style="font-size: 80%">['.$_SESSION['name'].']</div><div style="font-size: 65%; font-weight: normal;">'.$selected_fil.'</div></a><a href="exit.php" class="href_exit" title="Выход"><i class="fa fa-power-off"></i></a></li>';
		
	}
	echo '
				</ul>
			</nav>
		</header>
		</div> 
		<section id="main">
';

?>