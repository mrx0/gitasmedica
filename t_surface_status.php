<?php

//t_surface_status.php
//

	function t_surface_status ($data, $data2){
		require 'tooth_status.php';
		require 'root_status.php';
		require 'surface_status.php';
		
		$rez = '';
		
		if (array_key_exists($data, $tooth_status)){
			$rez .= '<img src=\'img/tooth_state/'.$tooth_status[$data]['img'].'\' border=\'0\' />'.$tooth_status[$data]['descr'].'<br />';
		}
		
		if (array_key_exists($data2, $root_status)){
			$rez .= '<img src=\'img/root_state/'.$root_status[$data2]['img'].'\' border=\'0\' />'.$root_status[$data2]['descr'].'<br />';
		}elseif(array_key_exists($data2, $surface_status)){
			$rez .= '<img src=\'img/surface_state/'.$surface_status[$data2]['img'].'\' border=\'0\' />'.$surface_status[$data2]['descr'].'<br />';
		}else{
			//return '';
		}
		
		return $rez;
	}
		
?>