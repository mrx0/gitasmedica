<?php 

//context_menu_show_f.php
//Контекстное меню

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		include_once 'DBWork.php';
        include_once 'functions.php';
		//разбираемся с правами
		$god_mode = FALSE;
		
		require_once 'permissions.php';
		
		if ($_POST){
			
			$data = '';
			
			if (!isset($_POST['mark'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				//Коэффициент общий
				if ($_POST['mark'] == 'spec_koeff'){
					$data = '
						<li><div onclick="spec_koeffInvoice(0)">нет (очистить)</div></li>'.
						'<li><div onclick="spec_koeffInvoice(\'k1\')">Ведущий 10%</div></li>'.
						'<li><div onclick="spec_koeffInvoice(\'k2\')">Главный 20%</div></li>'.
						'<li><div><input type="number" size="2" name="koeff" id="koeff" min="1" max="100" value="" class="mod"><div style="display: inline;" onclick="spec_koeffInvoice(document.getElementById(\'koeff\').value)"> Применить</div></div></li>';
				}
				//По гарантии общий
				if ($_POST['mark'] == 'guarantee'){
					$data = '
						<li><div onclick="guaranteeInvoice(0)">нет (очистить)</div></li>'.
						'<li><div onclick="guaranteeInvoice(1)">По гарантии</div></li>';
				}
				//Подарок общий
				if ($_POST['mark'] == 'gift'){
					$data = '
						<li><div onclick="giftInvoice(0)">нет (очистить)</div></li>'.
						'<li><div onclick="giftInvoice(1)">Подарок</div></li>';
				}
				//По гарантии и Подарок общий
				if ($_POST['mark'] == 'guaranteegift'){
					$data = '
						<li><div onclick="giftOrGiftInvoice(0)">нет (очистить)</div></li>'.
						'<li><div onclick="giftOrGiftInvoice(1)">По гарантии</div></li>'.
						'<li><div onclick="giftOrGiftInvoice(2)">Подарок</div></li>';
				}
				//Страховая общее
				if ($_POST['mark'] == 'insure'){
					
					$data .= '
						<li><div onclick="insureInvoice(0)">не страховой</div></li>';
					
					$insures_j = SelDataFromDB('spr_insure', '', '');
					
					if ($insures_j != 0){
						for ($i=0;$i<count($insures_j);$i++){
							$data .= '
								<li><div onclick="insureInvoice('.$insures_j[$i]['id'].')">'.$insures_j[$i]['name'].'</div></li>';
						}
					}
				}
				//Страховая согласовано общее
				if ($_POST['mark'] == 'insure_approve'){
					$data = '
						<li><div onclick="insureApproveInvoice(0)">нет (очистить)</div></li>'.
						'<li><div onclick="insureApproveInvoice(1)">Согласовано</div></li>';
				}	
				//Скидка акция общее
				if ($_POST['mark'] == 'discounts'){
					$data = '
						<li><div onclick="discountInvoice(0)">нет (очистить)</div></li>'.
						'<li>
						    <div onclick="discountInvoice(10)" style="display: inline;">10%</div>'.
                            '<div onclick="discountInvoice(15)" style="display: inline;">15%</div>'.
                            '<div onclick="discountInvoice(30)" style="display: inline;">30%</div>'.
                            '<div onclick="discountInvoice(50)" style="display: inline;">50%</div>
						</li>'.
						'<li><div><input type="number" size="2" name="discount" id="discount" min="1" max="100" value="" class="mod"><div style="display: inline;" onclick="discountInvoice($(\'#discount\').val())"> Применить</div></div></li>';
				}
				//Страховая согласовано позиция
				if ($_POST['mark'] == 'insure_approveItem'){
					$data = '
						<li><div onclick="insureApproveItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 0)">нет (очистить)</div></li>'.
						'<li><div onclick="insureApproveItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 1)">Согласовано</div></li>';
				}
				//Гарантия позиция
				/*if ($_POST['mark'] == 'guaranteeItem'){
					$data = '
						<li><div onclick="guaranteeItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 0)">нет (очистить)</div></li>'.
						'<li><div onclick="guaranteeItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 1)">По гарантии</div></li>';
				}
				//Подарок позиция
				if ($_POST['mark'] == 'giftItem'){
					$data = '
						<li><div onclick="giftItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 0)">нет (очистить)</div></li>'.
						'<li><div onclick="giftItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 1)">Подарок</div></li>';
				}*/
				//Гарантия+Подарок позиция
				if ($_POST['mark'] == 'guaranteeGiftItem'){
                    $data = '
						<li><div onclick="guaranteeGiftItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 0)">нет (очистить)</div></li>'.
                        '<li><div onclick="guaranteeGiftItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 1)">По гарантии</div></li>'.
						'<li><div onclick="guaranteeGiftItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 2)">Подарок</div></li>';
				}
				//Коэффициент позиция
				if ($_POST['mark'] == 'spec_koeffItem'){
					$data = '
						<li><div onclick="spec_koeffItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 0)">нет (очистить)</div></li>'.
						'<li><div onclick="spec_koeffItemInvoice('.$_POST['ind'].', '.$_POST['key'].', \'k1\')">Ведущий 10%</div></li>'.
						'<li><div onclick="spec_koeffItemInvoice('.$_POST['ind'].', '.$_POST['key'].', \'k2\')">Главный 20%</div></li>'.
						'<li><div><input type="number" size="2" name="koeff" id="koeff" min="1" max="100" value="" class="mod"><div style="display: inline;" onclick="spec_koeffItemInvoice('.$_POST['ind'].', '.$_POST['key'].', $(\'#koeff\').val())"> Применить</div></div></li>';
				}
                //Регуляция цены
				/*if ($_POST['mark'] == 'priceItem'){
					$data = '
						<li>***</li>';
				}*/
				//Скидки акции позиция
				if ($_POST['mark'] == 'discountItem'){
					$data = '
						<li><div onclick="discountItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 0)">нет (очистить)</div></li>'.
                        '<li>
                            <div onclick="discountItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 10)" style="display: inline;">10%</div>
                            <div onclick="discountItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 15)" style="display: inline;">15%</div>
                            <div onclick="discountItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 30)" style="display: inline;">30%</div>
                            <div onclick="discountItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 50)" style="display: inline;">50%</div>
                        </li>'.
						'<li><div><input type="number" size="2" name="discount" id="discount" min="1" max="100" value="" class="mod"><div style="display: inline;" onclick="discountItemInvoice('.$_POST['ind'].', '.$_POST['key'].', $(\'#discount\').val())"> Применить</div></div></li>';
				}
				//Страховка позиция
				if ($_POST['mark'] == 'insureItem'){
					
					$data .= '
						<li><div onclick="insureItemInvoice('.$_POST['ind'].', '.$_POST['key'].', 0)">не страховой</div></li>';
					
					$insures_j = SelDataFromDB('spr_insure', '', '');
					
					if ($insures_j != 0){
						for ($i=0;$i<count($insures_j);$i++){
							$data .= '
								<li><div onclick="insureItemInvoice('.$_POST['ind'].', '.$_POST['key'].', '.$insures_j[$i]['id'].')">'.$insures_j[$i]['name'].'</div></li>';
						}
					}
				}

				//Изменить прикреплённый филиал
				if ($_POST['mark'] == 'change_filial'){
					
					if (($stom['add_own'] == 1) || ($cosm['add_own'] == 1) || $god_mode || ($_SESSION['permissions'] == 3) || ($_SESSION['permissions'] == 9)){
						$data .= '
							<li><div onclick="changeUserFilial(0)">открепиться</div></li>';

						//Выбор филиала для сессии
						//$offices_j = SelDataFromDB('spr_filials', '', '');
                        $filials_j = getAllFilials(true, true);

						/*if ($filials_j != 0){
							for ($off = 0; $off < count($filials_j); $off++){
								if (isset($_SESSION['filial']) && !empty($_SESSION['filial']) && ($_SESSION['filial'] == $filials_j[$off]['id'])){
									$bg_col = 'background: rgba(83, 219, 185, 0.5) none repeat scroll 0% 0%;';
								}else{
									$bg_col = '';
								}
								$data .= '
									<li><div onclick="changeUserFilial('.$filials_j[$off]['id'].')" style="'.$bg_col.'">'.$filials_j[$off]['name'].'</div></li>';
							}
						}*/

                        foreach ($filials_j as $f_id => $filials_j_data) {
                            if (isset($_SESSION['filial']) && !empty($_SESSION['filial']) && ($_SESSION['filial'] == $filials_j_data['id'])){
                                $bg_col = 'background: rgba(83, 219, 185, 0.5) none repeat scroll 0% 0%;';
                            }else{
                                $bg_col = '';
                            }
                            $data .= '
									<li><div onclick="changeUserFilial('.$filials_j_data['id'].')" style="'.$bg_col.'">'.$filials_j_data['name2'].'</div></li>';
                        }

					}
				}

				//Изменить статус онлайн записи
				if ($_POST['mark'] == 'zapisOnlineStatusChange'){

					if (($zapis['add_new'] == 1) || $god_mode){
                        if ($_POST['key'] == 0){
                            $data .= '
                            <li><div onclick="changeOnlineZapisStatus('.$_POST['ind'].', 7)">Обработано</div></li>
                            <li><div onclick="changeOnlineZapisStatus('.$_POST['ind'].', 6)">Не доступен</div></li>';
                        }
                        if ($_POST['key'] == 6){
                            $data .= '
                            <li><div onclick="changeOnlineZapisStatus('.$_POST['ind'].', 7)">Обработано</div></li>
                            <li><div onclick="changeOnlineZapisStatus('.$_POST['ind'].', 0)">Сбросить статус</div></li>';
                        }
                        if ($_POST['key'] == 7){
                            $data .= '
                            <li><div onclick="changeOnlineZapisStatus('.$_POST['ind'].', 0)">Сбросить статус</div></li>';
                        }
					}
				}

				//Изменить категорию процентов
				if ($_POST['mark'] == 'percent_cats'){

				    //Категории процентов
                    $percents_j = SelDataFromDB('fl_spr_percents', $_POST['key'], 'type');

                    //Надо отсортировать по названию
                    $percent_cats_j_names = array();

                    //Определяющий массив из названий для сортировки
                    foreach ($percents_j as $key => $arr) {
                        array_push($percent_cats_j_names, $arr['name']);
                    }

                    //Сортируем по названию
                    array_multisort($percent_cats_j_names, SORT_LOCALE_STRING, $percents_j);

                    if ($percents_j != 0){
                        for ($i=0;$i<count($percents_j);$i++){
                            $data .= '
									<li><div onclick="fl_changePercentCat('.$percents_j[$i]['id'].')" style="">'.$percents_j[$i]['name'].'</div></li>';
                        }
                    }

				}

                //Скидки акции позиция
                if ($_POST['mark'] == 'lab_order_status'){
                    $data = '';
				    if ($_POST['key'] == 0){
                        $data .= '
                            <li><div onclick="labOrderStatusChange('.$_POST['ind'].', 6)">Отправлен в лабораторию</div></li>'.
                            '<li><div onclick="labOrderStatusChange('.$_POST['ind'].', 8)">Удалить заказ</div></li>';
                    }
                    if ($_POST['key'] == 7){
                        $data .= '
                            <li><div onclick="labOrderStatusChange('.$_POST['ind'].', 1)">Закрыть заказ</div></li>'.
                            '<li><div onclick="labOrderStatusChange('.$_POST['ind'].', 6)">Отправлен в лабораторию</div></li>';
                    }
                    if ($_POST['key'] == 6){
                        $data .= '
                            <li><div onclick="labOrderStatusChange('.$_POST['ind'].', 7)">Пришел из лаборатории</div></li>';
                    }
                    if ($_POST['key'] == 8){
                        $data .= '
                            <li><div onclick="labOrderStatusChange('.$_POST['ind'].', 0)">Восстановить</div></li>';
                    }
                    /*if (($_POST['key'] == 1) && ($god_mode || ($_SESSION['permissions'] == 3) || ($_SESSION['permissions'] == 9))){
                        $data .= '
                            <li><div onclick="labOrderStatusChange('.$_POST['ind'].', 4)">Удалить статус "Закрыт"</div></li>';
                    }*/
                    if (($_POST['key'] != 1) && ($_POST['key'] != 8) && ($_POST['key'] != 5) && ($god_mode || ($_SESSION['permissions'] == 3) || ($_SESSION['permissions'] == 9))){
                        $data .= '
                            <li><div onclick="labOrderStatusChange('.$_POST['ind'].', 5)">Отменить заказ</div></li>';
                    }
                    if (($_POST['key'] != 0) && ($god_mode || ($_SESSION['permissions'] == 3) || ($_SESSION['permissions'] == 9))){
                        $data .= '
                            <li><div onclick="labOrderStatusChange('.$_POST['ind'].', 2)">Удалить последний статус</div></li>';
                    }
                }

				//Для РЛ в табеле
				if ($_POST['mark'] == 'tabel_calc_options'){
                    $data .= '
                            <li><div onclick="fl_deleteCalculateFromTabel('.$_POST['ind'].', '.$_POST['key'].')">Удалить РЛ из табеля</div></li>';
				}

				//Для Вычетов в табеле
				if ($_POST['mark'] == 'tabel_deduction_options'){
                    $data .= '
                            <li><div onclick="fl_deleteDeductionFromTabel('.$_POST['ind'].', '.$_POST['key'].')">Удалить из табеля</div></li>';
				}

				//Для Надбавок в табеле
				if ($_POST['mark'] == 'tabel_surcharge_options'){
                    $data .= '
                            <li><div onclick="fl_deleteSurchargeFromTabel('.$_POST['ind'].', '.$_POST['key'].')">Удалить из табеля</div></li>';
				}

				//Настройка для записи
				if ($_POST['mark'] == 'zapis_options'){
				}
				//Настройка для молочных в наряде
				if ($_POST['mark'] == 'teeth_moloch'){
				}

				echo json_encode(array('result' => 'success', 'data' => $data));

			}
		}
	}
?>