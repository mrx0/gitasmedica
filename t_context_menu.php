<?php

//t_context_menu.php
//Зубная карта меню

	function DrawTeethMapMenu($n_zuba, $surface, $menu){
		include_once 'DBWork.php';
		require 'tooth_status.php';
		require 'root_status.php';
		require 'surface_status.php';
		
		//var_dump($tooth_status);
		
		$m_menu = '';
		$t_menu = '';
		$r_menu = '';
		$s_menu = '';
		$first = '';
		
		
		foreach ($tooth_status as $key => $value){
			if (($key != '6') && ($key != '7')){
				if ($key != '3'){
					$t_menu .= "<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$n_zuba}','{$surface}') class='ahref'><img src='img/tooth_state/{$value['img']}' border='0' />{$value['descr']}</a><br />";
				}else{
					$t_menu .= "<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$n_zuba}','{$surface}') class='ahref'><img src='img/tooth_state/{$value['img']}' border='0' />{$value['descr']}</a><input type='checkbox' name='implant' value='1'><br />";
				}
			}
			
		}
		$t_menu .= "<img src='img/tooth_state/alien.png' border='0' />Чужой <input type='checkbox' name='alien' value='1'><br />";
		$t_menu .= "<a href='#' id='refresh' onclick=refreshTeeth(0,'{$n_zuba}','{$surface}') class='ahref'><img src='img/tooth_state/reset.png' border='0' />Сбросить</a><br />";
		
		foreach ($root_status as $key => $value){
			$r_menu .= "<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$n_zuba}','{$surface}') class='ahref'><img src='img/root_state/{$value['img']}' border='0' />{$value['descr']}</a><br />";
		}
		//$r_menu .= "<a href='#' id='refresh' onclick=refreshTeeth(0,'{$n_zuba}','{$surface}') class='ahref'><img src='img/root_state/0' border='0' />Сбросить</a><br />";
		
		
		foreach ($surface_status as $key => $value){
			$s_menu .= "<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$n_zuba}','{$surface}') class='ahref'><img src='img/surface_state/{$value['img']}' border='0' />{$value['descr']}</a><br />";
		}
		//$s_menu .= "<a href='#' id='refresh' onclick=refreshTeeth(0,'{$n_zuba}','{$surface}') class='ahref'><img src='img/surface_state/0' border='0' />Сбросить</a><br />";
		
		
		$actions_stomat = SelDataFromDB('actions_stomat', '', '');
		//var_dump ($actions_stomat);
		if ($actions_stomat != 0){
			for ($i = 0; $i < count($actions_stomat); $i++){
				$m_menu .= $actions_stomat[$i]['full_name']."<input type='checkbox' name='action{$actions_stomat[$i]['id']}' value='1'><br />";
			}
		}
		
		
		
		if ($menu == 't_menu'){
			echo $t_menu;
		}elseif($menu == 'r_menu'){
			echo $r_menu;
		}elseif($menu == 's_menu'){
			echo $s_menu;
		}elseif($menu == 'first'){
			$first = '';			
		}elseif($menu == 'm_menu'){
			echo $m_menu;			
		}
	}



?>