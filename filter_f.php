<?php

//filter_f.php
//Функции для фильтра

	function filterFunction ($dataarray){
		//var_dump ($dataarray);
		$filter_rez = array();
		$sw = '';
		$ended_filter = FALSE;
		$worker_filter = FALSE;
		$filial_filter = FALSE;
		$priority_filter = FALSE;
		$pervich_filter = FALSE;
		$fio_filter = FALSE;
		$fio_f_filter = FALSE;
		$fio_i_filter = FALSE;
		$fio_o_filter = FALSE;
		$arr_temp = array();
		
		if (isset($dataarray['all_time'])){
			if ($dataarray['all_time'] == 1){
				$arr_temp = SelMINDataFromDB($dataarray['datatable'], 'create_time');
				$datastart = $arr_temp[0]['create_time'];
				$arr_temp = SelMAXDataFromDB($dataarray['datatable'], 'create_time');
				$dataend = $arr_temp[0]['create_time'];
			}else{
				$datastart = strtotime($dataarray['datastart']);
				$dataend = strtotime($dataarray['dataend'].' 23:59:59');
			}
		}else{
			$datastart = strtotime($dataarray['datastart']);
			$dataend = strtotime($dataarray['dataend'].' 23:59:59');
		}
		
		include_once 'DBWork.php';
		include_once 'filter.php';
		//$offices = SelDataFromDB('spr_office', '', '');
			
		$echo_filter = '';

		if (isset($dataarray['ended']) && (!empty($dataarray['ended']) || ($dataarray['ended'] == 0))){
			//$sw = $dataarray['ended'];
			//$type = 'ended_tasks';
			$echo_filter = 'Статус: ';
			
			if ($dataarray['ended'] == '0'){
				$echo_filter .= 'Все. ';
				$sw .= '';
			}elseif($dataarray['ended'] == '1'){
				$echo_filter .= 'Открытые. ';
				$sw .= "`end_time` = '0' ";
				$ended_filter = TRUE;
			}elseif($dataarray['ended'] == '2'){
				$echo_filter .= 'Закрытые. ';
				$sw .= "`end_time` <> '0' ";
				$ended_filter = TRUE;
			}
		}
		//echo '<br />'.$sw.'<br />';
		//Исполнитель
		if ((!empty($dataarray['searchdata2']) && ($dataarray['searchdata2']) !='' )){
			//$sw = $dataarray['ended'];
			//$type = 'ended_tasks';
			$workers = SelDataFromDB ('spr_workers', $dataarray['searchdata2'], 'worker_full_name');
			if ($workers != 0){
				if ($ended_filter){
					$sw .= 'AND '; 
				}
				$echo_filter .= 'Исполнитель: '.$dataarray['searchdata2'].'. ';
				$sw .= "`worker` = '{$workers[0]["id"]}' ";
				$worker_filter = TRUE;
			}
		}
		//echo '<br />'.$sw.'<br />';
		if  (!empty($dataarray['filial'])){
			//$sw = $dataarray['filial'];
			//$type = 'filial_tasks';
				if ($dataarray['filial'] == '99'){
				$echo_filter .= 'Во всех филиалах. ';
				$sw .= '';
			}else{
				$filial = SelDataFromDB('spr_office', $dataarray['filial'], 'offices');
				if ($filial !=0){
					if (($ended_filter) || ($worker_filter)){
						$sw .= 'AND '; 
					}
					$echo_filter .= 'Филиал: '.$filial[0]['name'].'. ';
					$sw .=  "`office` = '{$dataarray['filial']}' ";
					$filial_filter = TRUE;
				}else{
					$echo_filter .= 'Филиал unknown. ';
					$sw .=  '';
				}
			}
		}
		//echo '<br />'.$sw.'<br />';
		if  (!empty($dataarray['priority'])){
			//$sw = $dataarray['priority'];
			//$type = 'priority_tasks';
			
			if ($dataarray['priority'] == '0'){
				$echo_filter .= 'Приоритет: Любой. ';
				$sw .= '';
			}elseif($dataarray['priority'] == '1'){
				if (($ended_filter) || ($filial_filter) || ($worker_filter)){
					$sw .= 'AND '; 
				}
				$echo_filter .= 'Приоритет: Низкий. ';
				$sw .= "`priority` = '{$dataarray['priority']}' ";
				$priority_filter = TRUE;
			}elseif($dataarray['priority'] == '2'){
				if (($ended_filter) || ($filial_filter) || ($worker_filter)){
					$sw .= 'AND '; 
				}
				$echo_filter .= 'Приоритет: Средний. ';
				$sw .= "`priority` = '{$dataarray['priority']}' ";
				$priority_filter = TRUE;
			}elseif($dataarray['priority'] == '3'){
				if (($ended_filter) || ($filial_filter) || ($worker_filter)){
					$sw .= 'AND '; 
				}
				$echo_filter .= 'Приоритет: Высокий. ';
				$sw .= "`priority` = '{$dataarray['priority']}' ";
				$priority_filter = TRUE;
			}
		}
		if  (!empty($dataarray['pervich'])){
			//$sw = $dataarray['priority'];
			//$type = 'priority_tasks';
			
			if ($dataarray['pervich'] == '0'){
				$echo_filter .= 'Статуc: Все';
				//$sw .= '';
			}elseif($dataarray['pervich'] == '1'){
				$echo_filter .= 'Статуc: Только первичные. ';
				//$sw .= "`priority` = '{$dataarray['priority']}' ";
				$pervich_filter = TRUE;
			}elseif($dataarray['priority'] == '2'){
				$echo_filter .= 'Статуc: Только не первичные. ';
				$pervich_filter = TRUE;
			}
		}
		if (isset ($dataarray['f']) && ($dataarray['f'] != '') && (!empty($dataarray))){
			if (($ended_filter) || ($filial_filter) || ($priority_filter) || ($worker_filter)){
				$sw .= 'AND '; 
			}
			//$echo_filter .= 'Фамилия';
			$sw .= "`f` LIKE '%{$dataarray['f']}%' ";
			$fio_f_filter = TRUE;
		}
		if (isset ($dataarray['i']) && ($dataarray['i'] != '') && (!empty($dataarray))){
			if (($ended_filter) || ($filial_filter) || ($priority_filter) || ($fio_f_filter) || ($worker_filter)){
				$sw .= 'AND '; 
			}
			//$echo_filter .= 'Имя.';
			$sw .= "`i` LIKE '%{$dataarray['i']}%' ";
			$fio_i_filter = TRUE;
		}
		if (isset ($dataarray['o']) && ($dataarray['o'] != '') && (!empty($dataarray))){
			if (($ended_filter) || ($filial_filter) || ($priority_filter) || ($fio_f_filter) || ($fio_i_filter) || ($worker_filter)){
				$sw .= 'AND '; 
			}
			//$echo_filter .= 'Отчество.';
			$sw .= "`o` LIKE '%{$dataarray['o']}%' ";
			$fio_o_filter = TRUE;
		}
		//echo '<br />'.$sw.'<br />';
		
		if ($ended_filter || $worker_filter || $filial_filter || $priority_filter || $fio_f_filter || $fio_i_filter || $fio_o_filter){
			$sw .= 'AND ';
		}
		if (isset($dataarray['datatable'])){
			if ($dataarray['datatable'] == 'journal_it'){
				$sw .= "`create_time` BETWEEN '{$datastart}' AND '{$dataend}' ";
				$where = 'it';
				$filter_rez[0] = '<span style="font-size:85%;">Включен фильтр. '.$echo_filter.' С '.$dataarray['datastart'].' по '.$dataarray['dataend'].'. <a href="'.$where.'.php" class="ahref_sort">Сбросить</a></span><br />';
			}elseif ($dataarray['datatable'] == 'spr_clients'){
				$sw .= "`birthday` BETWEEN '{$datastart}' AND '{$dataend}' ";
				$where = 'clients';
				$filter_rez[0] = '<span style="font-size:85%;">Включен фильтр. '.$echo_filter.' Даты рождения с '.$dataarray['datastart'].' по '.$dataarray['dataend'].'. <a href="'.$where.'.php" class="ahref_sort">Сбросить</a></span><br />';
			}elseif ($dataarray['datatable'] == 'journal_cosmet1'){
				$sw .= "`create_time` BETWEEN '{$datastart}' AND '{$dataend}' ";
				$where = 'cosmet';
				$filter_rez[0] = '<span style="font-size:85%;">Включен фильтр. '.$echo_filter.' С '.$dataarray['datastart'].' по '.$dataarray['dataend'].'. <a href="'.$where.'.php" class="ahref_sort">Сбросить</a></span><br />';
			}elseif ($dataarray['datatable'] == 'journal_tooth_status'){
				$sw .= "`create_time` BETWEEN '{$datastart}' AND '{$dataend}' ";
				if (isset($dataarray['sw']) && $dataarray['sw'] == 'stat_stomat2'){
					$where = 'stat_stomat2';
				}else{
					$where = 'stomat';
				}
				$filter_rez[0] = '<span style="font-size:85%;">Включен фильтр. '.$echo_filter.' С '.$dataarray['datastart'].' по '.$dataarray['dataend'].'. <a href="'.$where.'.php" class="ahref_sort">Сбросить</a></span><br />';
			}else{
				$sw .= "`create_time` BETWEEN '{$datastart}' AND '{$dataend}' ";
			}
		}else{
			$sw .= "`create_time` BETWEEN '{$datastart}' AND '{$dataend}' ";
			//$filter_rez[0] = '<span style="font-size:85%;">Включен фильтр. '.$echo_filter.' Даты рождения с '.$dataarray['datastart'].' по '.$dataarray['dataend'].'. <a href="'.$where.'.php" class="ahref_sort">Сбросить</a></span><br />';

		}
		
		if ($pervich_filter) $filter_rez['pervich'] = true;
		$filter_rez[1] = $sw;
		
		return ($filter_rez);
	}

?>