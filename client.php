<?php

//client.php
//Карточка клиента

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';

		if ($_GET){
			//include_once 'DBWork.php';
            /*!!!Тест PDO*/
            include_once('DBWorkPDO.php');

			include_once 'functions.php';
			include_once 'tooth_status.php';
            include_once 'variables.php';


            $edit_options = false;
            $upr_edit = false;
            $admin_edit = false;
            $stom_edit = false;
            $cosm_edit = false;
            $finance_edit = false;

            //$msql_cnnct = ConnectToDB ();
            $db = new DB();

			//переменная для просроченных
			$allPayed = true;
			//переменная для именных серотификатов и прочего
			$haveСertName = false;

            //доступный остаток
            $dostOstatok = 0;

			$text_tooth_status = array(
				'up' => -9,
				'down' => 138,
				'left' => array (
					1 => 258,
					2 => 221,
					3 => 186,
					4 => 149,
					5 => 113,
					6 => 77,
					7 => 42,
					8 => 5,
				),
				'right' => array (
					1 => 311,
					2 => 350,
					3 => 386,
					4 => 422,
					5 => 459,
					6 => 495,
					7 => 529,
					8 => 566,
				),
			);

			//$client_j = SelDataFromDB('spr_clients', $_GET['id'], 'user');

            $args = [
                'client_id' => $_GET['id']
            ];

//			$query = "
//            SELECT s_c.*, j_pc.status AS call_status
//            FROM `spr_clients` s_c
//            LEFT JOIN `journal_phone_calling` j_pc ON j_pc.client_id = s_c.id
//            WHERE s_c.id = :client_id ORDER BY j_pc.call_time, j_pc.create_time DESC";

			$query = "
            SELECT s_c.*, /*j_inst.status AS installment_status,*/
            (SELECT j_pc.status FROM `journal_phone_calling` j_pc WHERE j_pc.client_id = s_c.id ORDER BY j_pc.call_time DESC, j_pc.create_time DESC LIMIT 1) AS call_status
            FROM `spr_clients` s_c 
            /*LEFT JOIN `journal_installments` j_inst ON j_inst.client_id = s_c.id*/
            WHERE s_c.id = :client_id";

           //Выбрать все
            $client_j = $db::getRow($query, $args);
//            var_dump($client_j);

			//!!!ДР по-новому надо сделать
            /*
SELECT
    `name`,
    `birth`,
    (YEAR(CURRENT_DATE)-YEAR(`birth`))-(RIGHT(CURRENT_DATE,5)<RIGHT(`birth`,5)
    ) AS `age`
FROM `users`
ORDER BY `name`;
            */

            //Есть рассрочки
            $have_installment = false;
            //Были рассрочки
            $was_installment = false;

            $query = "
            SELECT j_inst.status
            FROM `journal_installments` j_inst
            WHERE j_inst.client_id = :client_id AND `invoice_id` <> '0'";

            $installment_j = $db::getRows($query, $args);
//            var_dump($installment_j);

            foreach ($installment_j as $item){
                if ($item['status'] == 1){
                    $have_installment = true;
                }
                if ($item['status'] == 7){
                    $was_installment = true;
                }
            }
//            var_dump($have_installment);
//            var_dump($was_installment);


            //именные сертификаты, выданные пациенту
            $cert_name_j = array();

            $args = [
                'client_id' => $client_j['id']
            ];

            $query = "SELECT j_cn.* FROM `journal_cert_name` j_cn
                            WHERE j_cn.client_id = :client_id";

            //Выбрать все
            $cert_name_j = $db::getRows($query, $args);
            //var_dump($cert_name_j);

            if (!empty($cert_name_j)) {
                $haveСertName = true;
            }


			//var_dump($client_j);
            if (!empty($client_j)){
				echo '
					<script src="js/init.js" type="text/javascript"></script>
					<!--<script src="js/init2.js" type="text/javascript"></script>-->
					<div id="status">
						<header>
							<h2 style="padding-bottom: 5px;">
								Карточка пациента #'.$client_j['id'].'';

				if (($clients['edit'] == 1) || $god_mode){
					if ($client_j['status'] != 9){
						echo '
									<a href="client_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
					}
					if (($client_j['status'] == 9) && (($clients['close'] == 1) || $god_mode)){
						echo '
							<a href="#" onclick="Ajax_reopen_client('.$_SESSION['id'].', '.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
					}
				}
				if (($clients['close'] == 1) || $god_mode){
					if ($client_j['status'] != 9){
						echo '
									<a href="move_all.php?client='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Переместить"><i class="fa fa-external-link-square" aria-hidden="true"></i></a>';
						echo '
									<a href="client_del.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
					}

				}
                if (($finances['see_all'] != 0) || ($finances['see_own'] != 0) || $god_mode) {
                    if ($client_j['status'] != 9) {

                        //Нет отметки о рассрочке
                        //if ($client_j['installment_status'] == 0) {
                        if (!$have_installment && !$was_installment) {
                            echo '<span class="info"  style="display: inline; margin-left: 0px; font-size: 100%; padding: 2px 5px; /*cursor: pointer;*/" onclick="/*changeInstallmentStatus(' . $client_j['id'] . ', 0, true);*/" <i class="fa fa-database" aria-hidden="true" title="Нет рассрочек"></i></span>';
                        }
                        //Включена рассрочка
                        //if ($client_j['installment_status'] == 1) {
                        if ($have_installment) {
                            echo '
                                            <span class="info"  style="display: inline; color: red; margin-left: 0px; font-size: 100%; padding: 2px 5px; /*cursor: pointer;*/" onclick="/*changeInstallmentStatus(' . $client_j['id'] . ', 1, true);*/">
                                                <i class="fa fa-database" aria-hidden="true" title="Есть незакрытая рассрочка"></i>
        
                                                    <a href="stat_installments2.php" class="b4" style="font-size: 60%">Открытые рассрочки (новое)</a>
                                                    <a href="stat_installments.php" class="b4" style="font-size: 60%">Открытые рассрочки (старое)</a>
        
                                            </span>';
                        }
                        //Рассрочка закрыта
                        //if ($client_j['installment_status'] == 7) {
                        if (!$have_installment && $was_installment) {
                            echo '<span class="info"  style="display: inline; color: green; margin-left: 0px; font-size: 100%; padding: 2px 5px; /*cursor: pointer;*/" onclick="/*changeInstallmentStatus(' . $client_j['id'] . ', 7, true);*/"><i class="fa fa-database" aria-hidden="true" title="Рассрочка закрыта"></i></span>';
                        }
                    }
                }

                echo '
							</h2>';

                if ($client_j['status'] == 9){
                    echo '<i style="color:red;">Пациент удалён (заблокирован).</i><br>';
                }

                echo '
							Номер карты: '.$client_j['card'].'';

                if (($finances['see_all'] != 0) || ($finances['see_own'] != 0) || $god_mode) {
                    if ($client_j['status'] != 9) {
                        echo '<div style="font-size: 130%; margin-top: 5px;">';
                        echo '<a href="finance_account.php?client_id='.$client_j['id'].'" class="b" style="display: inline; margin-left: 0px; font-size: 70%; padding: 2px 5px;">Управление счётом</a>';
                        echo '<a href="zapis.php?client_id='.$client_j['id'].'" class="b" style="display: inline; margin-left: 0px; font-size: 70%; padding: 2px 5px;">Записать пациента</a>';
                        echo '<a href="cert_name_cell.php?client_id='.$client_j['id'].'" class="b" style="display: inline; margin-left: 0px; font-size: 70%; padding: 2px 5px;">Выдать именной серт-т</a>';
                        if (($_SESSION['permissions'] == 3) || ($_SESSION['id'] == 364) || $god_mode){
                            //var_dump($client_j['installment_status']);

                            //новая рассрочка
                            echo '
                                        <a href="create_installment.php?client_id='.$client_j['id'].'" class="b" style="display: inline; margin-left: 0px; font-size: 70%; padding: 2px 5px;">Создать рассрочку</a>';


                        }
                        echo '</div>';
                    }
                }


                echo '
                        </header>';

                echo '
                    <div style="margin-top: 7px; font-size: 70%; color: #777;">
                        <a href="test_print2.php?client_id=' . $client_j['id'] . '" class="ahref b2 no_print" target="_blank" rel="nofollow noopener">Мед.карта стом.(тест) </a>';

                echo '
                    </div>';

				echo '
                    <div style="margin-top: 7px; font-size: 70%; color: #777;">
                        *В карточке обязательно должны быть заполнены поля: номер карты, дата рождения, пол, адрес
                    </div>
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 101;">';

                echo $block_fast_search_client;

				echo '
					</div>';

				echo '
						<div id="data">';


				echo '

								<div class="cellsBlock2">
									<div class="cellLeft">ФИО</div>
									<div class="cellRight" style="font-weight: bolder; font-size: 105%;">'.$client_j['full_name'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Дата рождения</div>
									<div class="cellRight">';
                if ($client_j['birthday2'] == '0000-00-00'){
					echo 'не указана';
				}else{
					echo
						date('d.m.Y', strtotime($client_j['birthday2'])).'<br>
						полных лет <b>'.getyeardiff(strtotime($client_j['birthday2']), 0).'</b>';
				}
				echo '						
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Пол</div>
									<div class="cellRight">';
				if ($client_j['sex'] != 0){
					if ($client_j['sex'] == 1){
						echo 'М';
					}
					if ($client_j['sex'] == 2){
						echo 'Ж';
					}
				}else{
					echo 'не указан';
				}
				echo
									'</div>
								</div>';

				echo '
								<div class="cellsBlock2">
									<div class="cellLeft">
									    Телефон';
				echo '<span style="float: right; cursor: pointer;" onclick="contextMenuShow('.$client_j["id"].', 0, event, \'phone_call\')">';

                //Отметка о телефонном звонке
                if ($client_j['call_status'] == 8) {
                    echo '<i class="fa fa-phone-square" style="color: red; font-size: 120%;" title="Не звонить"></i>';
                }elseif($client_j['call_status'] == 6) {
                    echo '<i class="fa fa-phone-square" style="color: orange; font-size: 120%;" title="Не дозвонились"></i>';
                }elseif ($client_j['call_status'] == 7) {
                    echo '<i class="fa fa-phone-square" style="color: blue; font-size: 120%;" title="Записались"></i>';
                }elseif ($client_j['call_status'] == 5) {
                    echo '<i class="fa fa-phone-square" style="color: #b35bff; font-size: 120%;" title="Перезвонить"></i>';
                }elseif ($client_j['call_status'] == 4){
                    echo '<i class="fa fa-phone-square" style="color: #93021e; font-size: 140%;" title="Плохой отзыв"></i>';
                }elseif ($client_j['call_status'] == 3){
                    echo '<i class="fa fa-phone-square" style="color: #b1ffad; font-size: 140%; background-color: rgba(51, 51, 51, 0.1); box-shadow: -1px -2px 5px rgba(51, 51, 51, 0.2);" title="Хороший отзыв"></i>';
                }else{
                    echo '<i class="fa fa-phone-square" style="color: #dcdcdc; font-size: 120%;" title="Нет отметки"></i>';
                }

                echo '</span>';
                echo '
                                    </div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">мобильный</span><br>
											'.$client_j['telephone'].'
										</div>';
				if ($client_j['htelephone'] != ''){
					echo '
										<div>
											<span style="font-size: 80%; color: #AAA">домашний</span><br>
											'.$client_j['htelephone'].'
										</div>';
				}
				echo '
									</div>
								</div>';

                if ($client_j['no_sms'] == 1) {
                    echo '
								<div class="cellsBlock2">
									
									<div class="cellRight" style="color: red; font-size: 80%;">
                                        Отказ от рассылки';

                    echo '
									</div>
								</div>';
                }

                echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Email</div>
									<div class="cellRight">
                                        '.$client_j['email'].'';
                echo '
									</div>
								</div>';

                echo '
								<div class="cellsBlock2">
									<div class="cellLeft">ИНН</div>
									<div class="cellRight">
                                        '.$client_j['inn'].'';
                echo '
									</div>
								</div>';

				echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Паспорт</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 70%; color: #AAA">Серия номер</span><br>';
                if ($client_j['id'] == 3459){
                    if (($_SESSION['id'] == 270) || ($_SESSION['id'] == 1)){
                        echo $client_j['passport'];
                    }
                }else {
                    echo $client_j['passport'];
                }

                echo '
										</div>';
				if (($client_j['alienpassportser'] != NULL) && ($client_j['alienpassportnom'] != NULL)){
					echo '
										<div>
											<span style="font-size: 70%; color: #AAA">Серия номер (иностр.)</span><br>
											'.$client_j['alienpassportser'].'
											'.$client_j['alienpassportnom'].'
										</div>';
				}
				echo '
										<div>
											<span style="font-size: 70%; color: #AAA">Выдан когда</span><br>';
                if ($client_j['id'] == 3459){
                    if (($_SESSION['id'] == 270) || ($_SESSION['id'] == 1)){
                        echo $client_j['passportvidandata'];
                    }
                }else {
                    echo $client_j['passportvidandata'];
                }


				echo '
										</div>
										<div>
											<span style="font-size: 70%; color: #AAA">Кем</span><br>';
                if ($client_j['id'] == 3459){
                    if (($_SESSION['id'] == 270) || ($_SESSION['id'] == 1)){
                        echo $client_j['passportvidankem'];
                    }
                }else {
                    echo $client_j['passportvidankem'];
                }

                echo '
										</div>
									</div>
								</div>';

				echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Адрес</div>
									<div class="cellRight">';

                if ($client_j['id'] == 3459){
                    if (($_SESSION['id'] == 270) || ($_SESSION['id'] == 1)){
                        echo $client_j['address'];
                    }
                }else {
                    echo $client_j['address'];
                }

                echo '
									</div>
								</div>';
				if ($client_j['polis'] != ''){
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Полис</div>
									<div class="cellRight">
										<div>
											<span style="font-size: 80%; color: #AAA">Номер</span><br>
											'.$client_j['polis'].'
										</div>
										<div>
											<span style="font-size: 80%; color: #AAA">Дата</span><br>
											'.$client_j['polisdata'].'
										</div>';
					if ($client_j['insure'] == 0){
						$insure = 'не указана';
					}else{
						$insures_j = SelDataFromDB('spr_insure', $client_j['insure'], 'offices');
						if ($insures_j == 0){
							$insure = 'ошибка';
						}else{
							$insure = $insures_j[0]['name'];
						}
					}
					echo '
										<div>
											<span style="font-size: 80%; color: #AAA">Страховая компания</span><br>
											'.$insure.'
										</div>';
					echo '					
									</div>
								</div>';
				}

				if (($client_j['fo'] != '') || ($client_j['io'] != '')){
					echo '
							<div class="cellsBlock2" style="margin-top: 2px; margin-bottom: 0; display: block;">
								<div class="cellLeft" style="font-weight: bold; width: 500px; border-left: 1px ridge rgb(138 142 162); border-top: 1px ridge rgb(138 142 162); border-right: 1px ridge rgb(138 142 162);">
									Опекун
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft" style=" border-left: 1px ridge rgb(138 142 162); ">Фамилия</div>
								<div class="cellRight" style=" border-right: 1px ridge rgb(138 142 162);">
									'.$client_j['fo'].'
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft" style=" border-left: 1px ridge rgb(138 142 162); ">Имя</div>
								<div class="cellRight" style=" border-right: 1px ridge rgb(138 142 162);">
									'.$client_j['io'].'
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft" style=" border-left: 1px ridge rgb(138 142 162); ">Отчество</div>
								<div class="cellRight" style=" border-right: 1px ridge rgb(138 142 162);">
									'.$client_j['oo'].'
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft" style="border-bottom: 1px ridge rgb(138 142 162);  border-left: 1px ridge rgb(138 142 162);">Телефон</div>
								<div class="cellRight" style="border-bottom: 1px ridge rgb(138 142 162); border-right: 1px ridge rgb(138 142 162);">
									<div>
										<span style="font-size: 80%; color: #AAA">мобильный</span><br>
										'.$client_j['telephoneo'].'
									</div>';
					if ($client_j['htelephoneo'] != ''){
						echo '
									<div>
										<span style="font-size: 80%; color: #AAA">домашний</span><br>
										'.$client_j['htelephoneo'].'
									</div>';
					}
					echo '
								</div>
							</div>
							';
				}
				echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">Комментарий</div>
									<div class="cellRight">'.$client_j['comment'].'</div>
								</div>';

				if (TRUE){
				echo '				
								<div class="cellsBlock2">
									<div class="cellLeft">
										Лечащий врач<br />
										<span style="font-size: 70%">стоматология</span>
									</div>
									<div class="cellRight">'.WriteSearchUser('spr_workers',$client_j['therapist'], 'user', true).'</div>
								</div>';
				}
				if (TRUE){
				echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">
										Лечащий врач<br />
										<span style="font-size: 70%">косметология</span>
									</div>
									<div class="cellRight">'.WriteSearchUser('spr_workers',$client_j['therapist2'], 'user', true).'</div>
								</div>';
				}

				echo '
								<div class="cellsBlock2">
									<span style="font-size:80%;">';
				if (($client_j['create_time'] != 0) || ($client_j['create_person'] != 0)){
					echo '
										Добавлен: '.date('d.m.y H:i', $client_j['create_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $client_j['create_person'], 'user', true).'<br>';
				}else{
					echo 'Добавлен: не указано<br>';
				}
				if (($client_j['last_edit_time'] != 0) || ($client_j['last_edit_person'] != 0)){
					echo '
										Последний раз редактировался: '.date('d.m.y H:i', $client_j['last_edit_time']).'<br>
										Кем: '.WriteSearchUser('spr_workers', $client_j['last_edit_person'], 'user', true).'';
				}
				echo '
									</span>
								</div>';

				//Смотрим счёт (авансы/долги)
				//if (($finances['see_all'] != 0) || ($finances['see_own'] != 0) || $god_mode){

				    //Долги/авансы
                    //
                    //!!! @@@
                    //Баланс контрагента
                    include_once 'ffun.php';
                    $client_balance = json_decode(calculateBalance ($_GET['id']), true);
                    //Долг контрагента
                    $client_debt = json_decode(calculateDebt ($_GET['id']), true);

                    if ($client_debt['summ'] > 0){
                        $allPayed = false;
                    }

				//}
				if ($client_j['status'] != 9){
					//Вкладки
					echo '
						<div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">
							<ul>
								<li><a href="#tabs-1">Посещения (запись)</a></li>';

					if (($finances['see_all'] != 0) || ($finances['see_own'] != 0) || $god_mode   || ($_SESSION['id'] == 719)){
						echo '
								<li>
									<a href="#tabs-2">Счёт</a>';
						if (!$allPayed){
							echo '
									<div class="notes_count2" style="position: absolute; right: -2px; top: -4px;">
										<i class="fa fa-exclamation-circle" aria-hidden="true" title="Есть долги"></i>
									</div>';
						}
						echo '
								</li>';
					}

					if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
						echo '
								<li><a href="#tabs-3">Стоматология</a></li>';
					}

					if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
						echo '
								<li><a href="#tabs-4">Косметология</a></li>';
					}

                    echo '
								<li>
								    <a href="#tabs-5">Именные сертификаты</a>';
                    if ($haveСertName){
                        echo '
									<div class="notes_count2" style="position: absolute; right: -2px; top: -4px;">
										<i class="fa fa-exclamation-circle" aria-hidden="true" title="Есть именные сертификаты"></i>
									</div>';
                    }
					echo '
                                </li>';
					echo '
							</ul>';

					echo '
							<div id="tabs-1">';

					//Запись пациента (aka посещения) -->

                    if (!$allPayed) {
                        echo '
                                <div style="color: red; font-size: 13px;">
                                    <span style="font-size: 17px;"><i class="fa fa-exclamation-circle" aria-hidden="true" title="Есть долги"></i></span> У пациента есть долги.
                                </div>';
                    }

                    echo '
                                <div id="zapis"></div>';

					echo '
							</div>';

					//--> Запись пациента (aka посещения)


					//Счёт -->

					if (($finances['see_all'] != 0) || ($finances['see_own'] != 0) || $god_mode   || ($_SESSION['id'] == 719)){
						if ($client_j['status'] != 9){

							echo '
							<div id="tabs-2">';

							echo '
                                <div>';


                            //Если доступный остаток ОТРИЦАТЕЛЕН
                            $dostOstatok = $client_balance['summ'] - $client_balance['debited'] - $client_balance['withdraw'] + $client_balance['refund'];

                            //var_dump(json_decode($client_balance, true));
                            echo '
                                    <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                            Доступный остаток средств:
                                        </li>
                                        <li class="calculateOrder" style="font-size: 110%; font-weight: bold;">
                                             '.$dostOstatok.' руб.
                                        </li>
                                        <!--<li style="font-size: 85%; color: #7D7D7D; margin-top: 10px;">
                                            Всего внесено:
                                        </li>
                                        <li style="margin-bottom: 5px; font-size: 90%; font-weight: bold;">
                                            '.$client_balance['summ'].' руб.
                                        </li>-->
                                    </ul>
                            
                                    <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                            Общий долг составляет:
                                        </li>
                                        <li class="calculateInvoice" style="font-size: 110%; font-weight: bold;">
                                             '.$client_debt['summ'].' руб.
                                        </li>
                                      
                                     </ul>';

                            echo '
                                </div>';

                            echo '
                                <ul style=" margin-bottom: 5px;">
                                    <li style="font-size: 85%; color: #7D7D7D;">
                                        Ниже отображена <span style="color: red;">частичная</span> финансовая информация.<br>
                                        Подробная информация находится в разделе:
                                    </li>
                                    <li style="font-size: 85%; color: #7D7D7D;">
                                         <a href="finance_account.php?client_id='.$client_j['id'].'" class="b">Управление счётом</a>
                                    </li>
                                </ul>
                                <div id="giveMeYourMoney"></div>';




							echo '				
								<div class="cellsBlock2">
									<!--<a href="client_finance.php?client='.$client_j['id'].'" class="b">Долги/Авансы <i class="fa fa-rub"></i> (старое)</a><br>-->';

							/*if (!$allPayed)
								echo '<i style="color:red;">Есть не погашенное</i>';	*/

							echo '
								</div>';

							echo '
							</div>';
						}
					}

					//--> Счёт



						/*echo '
										<div class="cellsBlock2">';
						/*if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
							echo '
											<a href="#" id="showDiv1" class="b">Стоматология</a>';
						}
						if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
							echo '
											<a href="#" id="showDiv2" class="b">Косметология</a>';
						}
						echo '
										</div>';*/

					//Стоматология -->

					if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
						echo '
							<div id="tabs-3">';
						/*echo '
										<div id="div1">';*/
						if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || ($god_mode)){
							echo '	
								<!--<a href="add_error.php" class="b">Добавить осмотр</a>-->';
						}

						if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
							echo '	
								<a href="stom_history.php?client='.$client_j['id'].'" class="b">История</a>';
						}
						/*if (($clients['close'] == 1) || $god_mode){
							echo '
								<a href="stom_move.php?id='.$client_j['id'].'" class="b">Переместить</a>';
						}*/




						//Выберем из базы последнюю запись
						$t_f_data_db = array();

						/*require 'config.php';*/
						/*mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
						mysql_select_db($dbName) or die(mysql_error());
						mysql_query("SET NAMES 'utf8'");*/

						$time = time();

                        $args = [
                            'client_id' => $_GET['id']
                        ];

                        $query = "SELECT * FROM `journal_tooth_status` WHERE `client` = :client_id ORDER BY `create_time` DESC LIMIT 1";

                        //Выбрать все
                        $t_f_data_db = $db::getRows($query, $args);
                        //var_dump ($t_f_data_db);

//                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//						$number = mysqli_num_rows($res);
//						if ($number != 0){
//							while ($arr = mysqli_fetch_assoc($res)){
//								array_push($t_f_data_db, $arr);
//							}
//						}else
//							$t_f_data_db = 0;

						if (!empty($t_f_data_db)){
							//var_dump ($t_f_data_db);

							//echo '							<script src="js/init.js" type="text/javascript"></script>';
							//Выберем из базы первую запись
							$t_f_data_db_first = array();

							for ($z = 0; $z < count ($t_f_data_db); $z++){
								$dop = array();

                                $args = [
                                    'id' => $t_f_data_db[$z]['id']
                                ];

                                $query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = :id";

                                //Выбрать все
                                $dop = $db::getRows($query, $args);

								//ЗО и тд
//								$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = :id";
//
//                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//								$number = mysqli_num_rows($res);
//								if ($number != 0){
//									while ($arr = mysqli_fetch_assoc($res)){
//										array_push($dop, $arr);
//									}
//
//								}

								echo '
								<div class="cellsBlock3">';
								echo '
									<div class="cellLeft">
										<a href="task_stomat_inspection.php?id='.$t_f_data_db[$z]['id'].'" class="ahref">'.date('d.m.y H:i', $t_f_data_db[$z]['create_time']).'</a>
									</div>
									<div class="cellRight">';

								include_once 'teeth_map_db.php';
								include_once 't_surface_name.php';
								include_once 't_surface_status.php';

								include_once 'root_status.php';
								include_once 'surface_status.php';
								include_once 't_context_menu.php';

								$t_f_data = array();

								if ($z == 0){
									$n = '';
								}else{
									$n = $z;
								}

								$sw = 0;
								$stat_id = $t_f_data_db[$z]['id'];

								unset($t_f_data_db[$z]['id']);
								unset($t_f_data_db[$z]['create_time']);
								//echo "echo$sw";
								//var_dump ($surfaces);
								$t_f_data_temp_refresh = '';

								unset($t_f_data_db[$z]['id']);
								unset($t_f_data_db[$z]['office']);
								unset($t_f_data_db[$z]['client']);
								unset($t_f_data_db[$z]['create_time']);
								unset($t_f_data_db[$z]['create_person']);
								unset($t_f_data_db[$z]['last_edit_time']);
								unset($t_f_data_db[$z]['last_edit_person']);
								unset($t_f_data_db[$z]['worker']);
								unset($t_f_data_db[$z]['comment']);
								unset($t_f_data_db[$z]['zapis_date']);
								unset($t_f_data_db[$z]['zapis_id']);

								foreach ($t_f_data_db[$z] as $key => $value){
									//$t_f_data_temp_refresh .= $key.'+'.$value.':';


									//var_dump(json_decode($value, true));
									$surfaces_temp = explode(',', $value);
									//var_dump ($surfaces_temp);
									foreach ($surfaces_temp as $key1 => $value1){
										//$t_f_data[$key] = json_decode($value, true);
										///!!!Еба костыль
										if ($key1 < 13){
											$t_f_data[$key][$surfaces[$key1]] = $value1;
										}
									}
								}

								//var_dump ($t_f_data);
								if (!empty($dop[0])){
									//var_dump($dop[0]);
									unset($dop[0]['id']);
									//var_dump($dop[0]);
									foreach($dop[0] as $key => $value){
										//var_dump($value);
										if ($value != '0'){
											//var_dump($value);
											$dop_arr = json_decode($value, true);
											//var_dump($dop_arr);
											foreach ($dop_arr as $n_key => $n_value){
												if ($n_key == 'zo'){
													$t_f_data[$key]['zo'] = $n_value;
													//$t_f_data_draw[$key]['zo'] = $n_value;
												}
												if ($n_key == 'shinir'){
													$t_f_data[$key]['shinir'] = $n_value;
													//$t_f_data_draw[$key]['shinir'] = $n_value;
												}
												if ($n_key == 'podvizh'){
													$t_f_data[$key]['podvizh'] = $n_value;
													//$t_f_data_draw[$key]['podvizh'] = $n_value;
												}
												if ($n_key == 'retein'){
													$t_f_data[$key]['retein'] = $n_value;
													//$t_f_data_draw[$key]['retein'] = $n_value;
												}
												if ($n_key == 'skomplect'){
													$t_f_data[$key]['skomplect'] = $n_value;
													//$t_f_data_draw[$key]['skomplect'] = $n_value;
												}
											}
										}
									}
								}

								echo '
										<div class="map'.$n.' map_exist" id="map'.$n.'">
											<div class="text_in_map" style="left: 15px">8</div>
											<div class="text_in_map" style="left: 52px">7</div>
											<div class="text_in_map" style="left: 87px">6</div>
											<div class="text_in_map" style="left: 123px">5</div>
											<div class="text_in_map" style="left: 159px">4</div>
											<div class="text_in_map" style="left: 196px">3</div>
											<div class="text_in_map" style="left: 231px">2</div>
											<div class="text_in_map" style="left: 268px">1</div>
											
											<div class="text_in_map" style="left: 321px">1</div>
											<div class="text_in_map" style="left: 360px">2</div>
											<div class="text_in_map" style="left: 396px">3</div>
											<div class="text_in_map" style="left: 432px">4</div>
											<div class="text_in_map" style="left: 469px">5</div>
											<div class="text_in_map" style="left: 505px">6</div>
											<div class="text_in_map" style="left: 539px">7</div>
											<div class="text_in_map" style="left: 576px">8</div>';



								//var_dump ($teeth_map_temp);

								//!!!ТЕСТ ИНКЛУДА ОТРИСОВКИ ЗФ
								//require_once 'for32_teeth_map_svg.php';


								//$teeth_map_temp = SelDataFromDB('teeth_map', '', '');
								$teeth_map_temp = $teeth_map_db;
								foreach ($teeth_map_temp as $value){
									$teeth_map[mb_substr($value['tooth'], 0, 3)][mb_substr($value['tooth'], 3, strlen($value['tooth'])-3)]=$value['coord'];
								}
								//$teeth_map_d_temp = SelDataFromDB('teeth_map_d', '', '');
								$teeth_map_d_temp = $teeth_map_d_db;
								foreach ($teeth_map_d_temp as $value){
									$teeth_map_d[$value['tooth']]=$value['coord'];
								}
								//$teeth_map_pin_temp = SelDataFromDB('teeth_map_pin', '', '');
								$teeth_map_pin_temp = $teeth_map_pin_db;
								foreach ($teeth_map_pin_temp as $value){
									$teeth_map_pin[$value['tooth']]=$value['coord'];
								}

								for ($i=1; $i <= 4; $i++){
									for($j=1; $j <= 8; $j++){

										$DrawRoots = TRUE;
										$menu = 't_menu';
										if (isset($sw)){
											if ($sw == '1'){
												$t_status = 'yes';
											}else{
												$t_status = 'no';
											}
										}else{
											$t_status = 'yes';
										}
										//$t_status = 'yes';
										$color = "#fff";
										$color_stroke = '#74675C';
										$stroke_width = 1;
										$n_zuba = 't'.$i.$j;
										//echo $n_zuba.'<br />';
										if ($t_f_data[$i.$j]['alien'] == '1'){
											$color_stroke = '#F7273F';
											$stroke_width = 3;
										}

										foreach($teeth_map[$n_zuba] as $surface => $coordinates){

											$color = "#fff";
											//!!!! попытка с молочным зубом
											if ($t_f_data[$i.$j]['status'] == '19'){
												$color_stroke = '#FF9900';
											}
											$DrawMenu = TRUE;
											if (isset($t_f_data[$i.$j][$surface])){
											$s_stat = $t_f_data[$i.$j][$surface];
											}
											//!!! надо как-то получать статус в строку, чтоб писать в описании
											//t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);

											if ($t_f_data[$i.$j]['status'] == '3'){
												//штифт
												$surface = 'NONE';
												$color = "#9393FF";
												$color_stroke = '#5353FF';
												$coordinates = $teeth_map_pin[$n_zuba];
												$stroke_width = 1;

												echo '
													<div id="'.$n_zuba.$surface.'"
														status-path=\'
														"stroke": "'.$color_stroke.'", 
														"stroke-width": '.$stroke_width.', 
														"fill-opacity": "1"\' 
														class="mapArea'.$n.'" 
														t_status = "'.$t_status.'"
														data-path'.$n.'="'.$coordinates.'"
														fill-color'.$n.'=\'"fill": "'.$color.'"\'
														t_menu'.$n.' = "
																<div class=\'cellsBlock4\'>
																	<div class=\'cellLeft\'>
																		'.t_surface_name($n_zuba.$surface, 2).'<br />';

												echo DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');


												echo '
																	</div>
																</div>';
												echo
														'"
														t_menuA'.$n.' = "
																'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);

												//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');

												echo
														'"
													>
													</div>
												';
											}else{


												//Если надо рисовать корень, но в бд написано, что тут имплант
												if (($t_f_data[$i.$j]['pin'] == '1') && (mb_strstr($surface, 'root') != FALSE)){
													$DrawRoots = FALSE;
												}else{
													if  ((mb_strstr($surface, 'root') == TRUE) &&
														(($t_f_data[$i.$j]['status'] == '1') || ($t_f_data[$i.$j]['status'] == '2') ||
														($t_f_data[$i.$j]['status'] == '18') || ($t_f_data[$i.$j]['status'] == '19') ||
														($t_f_data[$i.$j]['status'] == '9'))){
														$DrawRoots = FALSE;
													}else{
														if (isset($t_f_data[$i.$j][$surface])){
															//echo $i.$j.'<br />';
															//var_dump ($t_f_data[$i.$j][$surface]);
															if  ((mb_strstr($surface, 'root') == TRUE) && ($t_f_data[$i.$j][$surface] != '0') && ($t_f_data[$i.$j][$surface] != '')){
																$color = $root_status[$t_f_data[$i.$j][$surface]]['color'];
															}
															$DrawRoots = TRUE;
														}
													}
												}
												//!!!!учим рисовать корни с коронками - начало  - кажется, это все говно. надо иначе
												/*if ($t_f_data[$i.$j]['status'] == '19'){
													$DrawRoots = TRUE;
												}*/
												if ((array_key_exists($t_f_data[$i.$j]['status'], $tooth_status)) && ($t_f_data[$i.$j]['status'] != '19')){
													//Если в массиве натыкаемся не на корни или если чужой, то корни не рисуем, а рисум кружок просто
													if ((($surface != 'root1') && ($surface != 'root2') && ($surface != 'root3')) || ($t_f_data[$i.$j]['alien'] == '1')){
														//без корней + коронки и всякая херня
														$surface = 'NONE';
														$color = $tooth_status[$t_f_data[$i.$j]['status']]['color'];
														$coordinates = $teeth_map_d[$n_zuba];
													}
												}else{
													//Если у какой-то из областей зуба есть статус в бд.
													if (isset($t_f_data[$i.$j][$surface])){
													if ($t_f_data[$i.$j][$surface] != '0'){
														if (array_key_exists($t_f_data[$i.$j][$surface], $root_status)){
															$color = $root_status[$t_f_data[$i.$j][$surface]]['color'];
														}elseif(array_key_exists($t_f_data[$i.$j][$surface], $surface_status)){
															$color = $surface_status[$t_f_data[$i.$j][$surface]]['color'];
														}else{
															$color = "#fff";
														}
													}
													}
												}

                                                //!Костыль для радикса(корень)/статус 34
                                                if ((($t_f_data[$i.$j]['root1'] == '34') || ($t_f_data[$i.$j]['root2'] == '34') || ($t_f_data[$i.$j]['root3'] == '34')) &&
                                                        (($t_f_data[$i.$j]['status'] != '1') && ($t_f_data[$i.$j]['status'] != '2') &&
                                                        ($t_f_data[$i.$j]['status'] != '18') && ($t_f_data[$i.$j]['status'] != '19') &&
                                                        ($t_f_data[$i.$j]['status'] != '9')))
                                                {
													$surface = 'NONE';
													$color = '#FF0000';
													$coordinates = $teeth_map_d[$n_zuba];
												}


												if (mb_strstr($surface, 'root') != FALSE){
													$menu = 'r_menu';
												}elseif((mb_strstr($surface, 'surface') != FALSE) || (mb_strstr($surface, 'top') != FALSE)){
													$menu = 's_menu';
												}else{
													$DrawMenu = FALSE;
												}

												if ($DrawRoots){
													echo '
														<div id="'.$n_zuba.$surface.'"
															status-path=\'
															"stroke": "'.$color_stroke.'", 
															"stroke-width": '.$stroke_width.', 
															"fill-opacity": "1"\' 
															class="mapArea'.$n.'" 
															t_status = "'.$t_status.'"
															data-path'.$n.'="'.$coordinates.'"
															fill-color'.$n.'=\'"fill": "'.$color.'"\'
															t_menu'.$n.' = "
																<div class=\'cellsBlock4\'>
																	<div class=\'cellLeft\'>
																		'.t_surface_name($n_zuba.'NONE', 1).'<br />';

													//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');
;
															echo '
																	</div>
																	<div class=\'cellRight\'>
																		'.t_surface_name($n_zuba.$surface, 0).'<br />';
													if ($DrawMenu){ /*DrawTeethMapMenu($key, $n_zuba, $surface, $menu)*/;}
													echo '
																	</div>
																</div>';
													echo
															'"
															t_menuA'.$n.' = "
																'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);

													//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');

													echo
															'"
															>
															</div>
															';
												}
											}
										}

										if ($t_f_data[$i.$j]['pin'] == '1'){
											//штифт
											$surface = 'NONE';
											$color = "#9393FF";
											$color_stroke = '#5353FF';
											$coordinates = $teeth_map_pin[$n_zuba];
											$stroke_width = 1;
											if ($t_f_data[$i.$j]['alien'] == '1'){
												$color_stroke = '#F7273F';
												$stroke_width = 3;
											}
											echo '
												<div id="'.$n_zuba.$surface.'"
													status-path=\'
													"stroke": "'.$color_stroke.'", 
													"stroke-width": '.$stroke_width.', 
													"fill-opacity": "1"\' 
													class="mapArea'.$n.'" 
													t_status = "'.$t_status.'"
													data-path'.$n.'="'.$coordinates.'"
													fill-color'.$n.'=\'"fill": "'.$color.'"\'
													t_menu'.$n.' = "
														<div class=\'cellsBlock4\'>
															<div class=\'cellLeft\'>
																'.t_surface_name($n_zuba.$surface, 2).'<br />';

											//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');
											echo '
															</div>
														</div>';
											echo
													'"
													t_menuA'.$n.' = "
																'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);

											//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');

											echo
													'"
													>
													</div>
													';
										}


										//Для ЗО и дополнительно
										if (isset($t_f_data[$i.$j]['zo'])){
											$surface = 'NONE';
											if ($t_f_data[$i.$j]['zo'] == '1'){
												$color = "#FF0000";
											}else{
												$color = "#FFF";
											}
											$color_stroke = '#5353FF';
											$coordinates = $teeth_map_zo_db[$i.$j];
											$stroke_width = 1;

											echo '
												<div id="'.$n_zuba.$surface.'"
													status-path=\'
													"stroke": "'.$color_stroke.'", 
													"stroke-width": '.$stroke_width.', 
													"fill-opacity": "1"\' 
													class="mapArea'.$n.'" 
													t_status = "'.$t_status.'"
													data-path="'.$coordinates.'"
													fill-color=\'"fill": "'.$color.'"\'
													t_menu = "'.$n_zuba.', '.$surface.', t_menu, true, '.$surface.', 2, false, \'\', \'\', false, \'\', \'\'"';
											echo
														'
													t_menuA = "
																'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);

											echo
													'"
													>
													</div>
													';
										}

										$text_status_div = '';
										$text_status_div_shinir = '';
										$text_status_div_podvizh = '';
										$text_status_div_retein = '';
										$text_status_div_skomplect = '';

										//Для Шинирования и дополнительно
										if (isset($t_f_data[$i.$j]['shinir'])){
											$text_status_div_shinir = 'ш';
											if ($i == 1){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											if ($i == 2){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 3){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 4){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['left'][$j];
											}
										}
										//Для Подвижности и дополнительно
										if (isset($t_f_data[$i.$j]['podvizh'])){
											$text_status_div_podvizh = 'A';
											if ($i == 1){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											if ($i == 2){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 3){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 4){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											$text_status_div .= '
												<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">';
										}
										//Для Ретейнер и дополнительно
										if (isset($t_f_data[$i.$j]['retein'])){
											$text_status_div_retein = 'р';
											if ($i == 1){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											if ($i == 2){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 3){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 4){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											$text_status_div .= '
												<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">';
										}
										//Для Сверхкомплекта и дополнительно
										if (isset($t_f_data[$i.$j]['skomplect'])){
											$text_status_div_skomplect = 'c';
											if ($i == 1){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											if ($i == 2){
												$top_tts = $text_tooth_status['up'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 3){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['right'][$j];
											}
											if ($i == 4){
												$top_tts = $text_tooth_status['down'];
												$left_tts = $text_tooth_status['left'][$j];
											}
											$text_status_div .= '
												<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">';
										}
										if ((isset($t_f_data[$i.$j]['shinir'])) || (isset($t_f_data[$i.$j]['podvizh'])) || (isset($t_f_data[$i.$j]['retein'])) || (isset($t_f_data[$i.$j]['skomplect']))){
											echo '<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">'.$text_status_div_shinir.''.$text_status_div_podvizh.''.$text_status_div_retein.''.$text_status_div_skomplect.'</div>';
										}

									}
								}



								echo '
										</div>
									</div>
								</div>';

							}


                            //Заявки
                            $tickets_arr = array();

                            $today = date('Y-m-d', time());
                            $today3daysplus = date('Y-m-d', strtotime('+3 days'));

                            //$filials_j = getAllFilials(false, false);

                            $show_option_str_for_paginator = '';

                            //Подключаемся к другой базе специально созданной для тикетов
                            $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

                            $query = "SELECT j_ticket.*, jticket_rm.status as read_status, j_tickets_worker.worker_id,
                            GROUP_CONCAT(DISTINCT j_tickets_filial.filial_id ORDER BY j_tickets_filial.filial_id ASC SEPARATOR \",\") AS filials
                            FROM `journal_tickets` j_ticket 
                            LEFT JOIN `journal_tickets_readmark` jticket_rm ON j_ticket.id = jticket_rm.ticket_id AND jticket_rm.create_person = '{$_SESSION['id']}'
                            LEFT JOIN `journal_tickets_workers` j_tickets_worker ON j_ticket.id = j_tickets_worker.ticket_id AND j_tickets_worker.worker_id = '{$_SESSION['id']}'
                            LEFT JOIN `journal_tickets_filial` j_tickets_filial ON j_tickets_filial.ticket_id = j_ticket.id 
                            WHERE j_ticket.id IN (SELECT `ticket_id` FROM `journal_ticket_associations` WHERE `ticket_id`= j_ticket.id AND `associate`='client.php' AND `association_id`='{$_GET['id']}')
                            AND j_ticket.status <> '9' AND j_ticket.status <> '1'
                            GROUP BY `id` ORDER BY /*`plan_date` ASC,*/ `id` DESC";

                            /*$res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0){
                                while ($arr = mysqli_fetch_assoc($res)){
                                    array_push($tickets_arr, $arr);
                                }
                            }*/
                            //var_dump($tickets_arr);

                            CloseDB ($msql_cnnct2);

                            if (!empty($tickets_arr)) {
                                echo '<div style="margin: 15px 0 0 -5px;">';
                                foreach ($tickets_arr as $j_tickets) {

                                    $ticket_style = 'ticketBlock';
                                    $expired_icon = '';

                                    //Если просрочен
                                    if ($j_tickets['plan_date'] != '0000-00-00') {
                                        //время истечения срока
                                        $pd = $j_tickets['plan_date'];
                                        //текущее
                                        $nd = $today;
                                        //сравнение не прошли ли сроки исполнения
                                        if (strtotime($pd) > strtotime($nd) + 2 * 24 * 60 * 60) {
                                            $expired = false;
                                        } else {
                                            if (strtotime($pd) < strtotime($nd)) {
                                                $expired = true;
                                                $ticket_style = 'ticketBlockexpired';
                                                $expired_icon = 'fa fa-exclamation-circle';
                                            } else {
                                                $expired = true;
                                                $ticket_style = 'ticketBlockexpired2';
                                                $expired_icon = 'fa fa-exclamation';
                                            }
                                        }
                                        /*var_dump(strtotime($nd));
                                        var_dump(strtotime($pd));
                                        var_dump(strtotime($pd)-strtotime($nd));
                                        var_dump(3*24*60*60);*/
                                        //var_dump(date('Y-m-d', time()));
                                        //var_dump(strtotime(date('Y-m-d', time())));
                                    } else {
                                        $expired = false;
                                    }
                                    //Если выполнен и закрыт
                                    if ($j_tickets['status'] == 1) {
                                        $ticket_done = true;
                                        $ticket_style = 'ticketBlockdone';
                                    } else {
                                        $ticket_done = false;
                                    }
                                    //Если удалён
                                    if ($j_tickets['status'] == 9) {
                                        $ticket_deleted = true;
                                        $ticket_style = 'ticketBlockdeleted';
                                    } else {
                                        $ticket_deleted = false;
                                    }
                                    //Если прочитано
                                    if ($j_tickets['read_status'] == 1) {
                                        //$readStateClass = 'display: none;';
                                        $newTopic = false;
                                    } else {
                                        $newTopic = true;
                                    }


                                    //Длина строки проверка, если больше, то сокращаем
                                    if (strlen($j_tickets['descr']) > 100) {
                                        $descr = mb_strimwidth($j_tickets['descr'], 0, 50, "...", 'utf-8');
                                    } else {
                                        $descr = $j_tickets['descr'];
                                    }

                                    echo '
                        <div class="' . $ticket_style . '" style="font-size: 95%;">
                            <div class="ticketBlockheader">
                                <div style="margin-left: 5px; text-align: left; float: left;">
                                    <span style=" color: rgb(29, 29, 29); font-size: 80%; font-weight: bold; margin-right: 3px;">#' . $j_tickets['id'] . '</span>';
                                    if (!$ticket_deleted) {
                                        if ($ticket_done) {
                                            echo '                                    
                                    <i class="fa fa-check" aria-hidden="true" style="color: green; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>
                                    <span style=" color: rgb(115, 112, 112); font-size: 80%;">' . date('d.m.Y', strtotime($j_tickets['fact_date'])) . '</span>';
                                        } else {
                                            if ($j_tickets['plan_date'] != '0000-00-00') {
                                                echo '
                                    <span style=" color: rgb(115, 112, 112); font-size: 80%;">до ' . date('d.m.Y', strtotime($j_tickets['plan_date'])) . '</span>';
                                                //<i class="fa fa-times" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>
                                            }
                                        }
                                        if (!$ticket_done && $expired) {
                                            echo '
                                    <i class="' . $expired_icon . '" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"  title=""></i>';
                                        }
                                    } else {
                                        echo '
                                    <span style=" color: rgb(115, 112, 112); font-size: 80%;">удалён</span>';
                                    }
                                    echo '
                                </div>
                                <div style="margin-right: 5px; text-align: right; float: right;">';
                                    if ($ticket_deleted) {
                                        echo '
                                    <i class="fa fa-trash" aria-hidden="true" style="color: rgba(244, 244, 244, 0.8); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Удалено"></i>
                                    <!--<i class="fa fa-reply" aria-hidden="true" style="color: rgb(167, 255, 0); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);"></i>-->';
                                    } else {
                                        if ($_SESSION['id'] == $j_tickets['worker_id']) {
                                            echo '                        
                                        <i class="fa fa-user" aria-hidden="true" style="color: rgba(124, 0, 255, 0.68); text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Вы исполнитель"></i>';
                                        }
                                        if ($newTopic) {
                                            echo '                        
                                        <i class="fa fa-bell" aria-hidden="true" style="color: red; text-shadow: 1px 1px rgba(52, 152, 219, 0.8);" title="Обновлено"></i>';
                                        }
                                    }

                                    echo '
                                </div>
                            </div>
                            <a href="ticket.php?id=' . $j_tickets['id'] . '&' . $show_option_str_for_paginator . '" class="ticketBlockmain ahref">
                                ' . $descr . '<br>
                                <span style="font-size: 80%; color: rgb(115, 112, 112);">нажмите, чтобы открыть</span>
                            </a><br>

                            <div class="ticketBlockfooter">
                                <!--создан ' . date('d.m.y H:i', strtotime($j_tickets['create_time'])) . '<br>-->
                                автор: <span style="color: rgb(51, 51, 51);">' . WriteSearchUser('spr_workers', $j_tickets['create_person'], 'user', false) . '</span><br>
                                <!--где создано: ', $j_tickets['filial_id'] == 0 ? 'не указано' : $filials_j[$j_tickets['filial_id']]['name'], '-->';
                                    if ($j_tickets['filials'] != NULL) {
                                        echo 'филиалы: ';
                                        $filials_arr_temp = explode(',', $j_tickets['filials']);

                                        if (!empty($filials_arr_temp)) {
                                            foreach ($filials_arr_temp as $f_id) {
                                                $bgColor_filialHere = '';
                                                if (isset($_SESSION['filial'])) {
                                                    if ($f_id == $_SESSION['filial']) {
                                                        $bgColor_filialHere = 'background-color: rgba(144,247,95, 1); border: 1px dotted rgba(65, 33, 222, 0.34);';
                                                    }
                                                }
                                                echo '<div style="display: inline-block; font-size: 80%; margin-right: 5px; color: rgb(59, 9, 111); ' . $bgColor_filialHere . '">' . $filials_j[$f_id]['name2'] . '</div>';
                                            }
                                        }

                                    }
                                    echo '                                
                            </div>
                        </div>';
                                }
                                echo '</div>';
                            }

							//$notes = SelDataFromDB ('notes', $client_j['id'], 'client');
							//include_once 'WriteNotes.php';

                            //Напоминания (стоматология)
                            $notes = array();

                            $args = [
                                'client_id' => $client_j['id'],
                                'type' => 5
                            ];

                            //$query = "SELECT * FROM `notes` WHERE `client`= :client_id ORDER BY `dead_line` ASC";

                            $query = "SELECT n.*, s_c.name, s_w.name AS w_name FROM `notes` n
                            RIGHT JOIN `spr_clients` s_c 
                            ON s_c.id = n.client  
                            RIGHT JOIN `spr_workers` s_w
                            ON s_w.id = n.create_person 
                            WHERE n.client = :client_id AND n.type = :type
                            ORDER BY n.dead_line DESC";

                            //Выбрать все
                            $notes = $db::getRows($query, $args);
                            //var_dump($notes);


                            //$query = "SELECT * FROM `notes` WHERE `client`='".$client_j['id']."' AND `closed` <> 1 ORDER BY `dead_line` ASC";
//                            $query = "SELECT * FROM `notes` WHERE `client`= :client_id ORDER BY `dead_line` ASC";
//
//                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                            $number = mysqli_num_rows($res);
//
//                            if ($number != 0){
//                                while ($arr = mysqli_fetch_assoc($res)){
//                                    array_push($notes, $arr);
//                                }
//                            }

                            //Направления
                            //!!! Привести к одному виду с напоминаниями получение данных
							//$removes = SelDataFromDB ('removes', $client_j['id'], 'client');

                            $removes = array();

                            $args = [
                                'client_id' => $client_j['id']
                            ];

                            //$query = "SELECT * FROM `removes` WHERE `client`= :client_id ORDER BY `create_time` DESC";

                            $query = "SELECT r.*, s_c.name, s_w.name AS w_name FROM `removes` r 
                            RIGHT JOIN `spr_clients` s_c 
                            ON s_c.id = r.client  
                            RIGHT JOIN `spr_workers` s_w
                            ON s_w.id = r.create_person 
                            WHERE r.client = :client_id
                            ORDER BY r.create_time DESC";

                            //Выбрать все
                            $removes = $db::getRows($query, $args);
                            //var_dump($removes);

							//Вывод всего
                            if (!empty($notes) || (!empty($removes))) {
                                echo 'Особые отметки<br>';

                                echo WriteNotes($notes, 0, true, $finances, 5);

                                if (!empty($removes)){
                                    echo WriteRemoves($removes, 0, 0, false, $finances);
                                }

                            }

						}else{
							echo '
									<div class="cellsBlock3">
										<div class="cellLeft">
											Не было посещений стоматолога
										</div>
									</div>';
						}


                        //Лаборатория
                        $laborder_j = SelDataFromDB ('journal_laborder', $client_j['id'], 'client');
                        //var_dump($laborder_j);

                        $labors_j = SelDataFromDB('spr_labor', '', '');
                        //var_dump ($labors_j);

                        $labors_jarr = array();

                        foreach ($labors_j as $labor_val){
                            $labors_jarr[$labor_val['id']] = $labor_val;
                        }
                        //var_dump ($labors_jrr);

                        if (TRUE) {
                            echo '
                            <ul style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: Auto; font-size: 95%; background-color: rgba(245, 245, 245, 0.9);">
								
								<li style="margin-bottom: 3px;">
                                    Заказы в лабораторию ';
                            if ($laborder_j == 0) {
                                echo '<span style="font-size: 90%; color: #7D7D7D; margin-bottom: 5px; color: red;">нет заказов</span>';
                            }

                            echo '
                                </li>
								<li style="margin-bottom: 10px;">
                                    <a href="lab_order_add.php?client_id=' . $client_j['id'] . '" class="b" style="font-size: 75%;">Добавить новый</a>
                                </li>';

							if ($laborder_j != 0) {
                                echo '
									<li class="cellsBlock" style="font-weight:bold; background-color:#FEFEFE;">
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Дата</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Врач</div>
										<div class="cellOffice" style="text-align: center; background-color:#FEFEFE;">Лаборатория</div>
										<div class="cellText" style="text-align: center; background-color:#FEFEFE;">Описание</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Статус</div>
									</li>';

                                foreach ($laborder_j as $lab_order_data){

                                    if ($lab_order_data['status'] == 1) {
                                        $back_color = 'background-color: rgba(119, 255, 135, 1);';
                                        $mark_enter = 'закрыт';
                                    } elseif ($lab_order_data['status'] == 5) {
                                        $back_color = 'background-color: rgba(183, 41, 240, 0.7);';
                                        $mark_enter = 'отменён';
                                    } elseif ($lab_order_data['status'] == 6) {
                                        $back_color = 'background-color: rgba(255, 102, 17, 0.7);';
                                        $mark_enter = 'отправлен в лаб.';
                                    } elseif ($lab_order_data['status'] == 7) {
                                        $back_color = 'background-color: rgba(47, 186, 239, 0.7);';
                                        $mark_enter = 'пришел из лаб.';
                                    } elseif ($lab_order_data['status'] == 8) {
                                        $back_color = 'background-color: rgba(137,0,81, .7);';
                                        $mark_enter = 'удалено';
                                    } else {
                                        //
                                        /*if ($ZapisHereQueryToday[$z]['office'] != $ZapisHereQueryToday[$z]['add_from']) {
                                            $back_color = 'background-color: rgb(119, 255, 250);';
                                            $mark_enter = 'подтвердить';
                                        } else {*/
                                            $back_color = 'background-color: rgba(255,255,0, .5);';
                                            $mark_enter = 'создан';
                                        //s}
                                    }


                                    echo '
                                    <li class="cellsBlock" style="font-weight:bold; background-color:#FEFEFE;">
										<a href="lab_order.php?id='.$lab_order_data['id'].'" class="cellName ahref" style="text-align: center; background-color:#FEFEFE;">
                                            '.date('d.m.y' ,strtotime($lab_order_data['create_time'])).'
										</a>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">
										    '.WriteSearchUser('spr_workers', $lab_order_data['worker_id'], 'user', true).'
										</div>
										<a href="labor.php?id='.$lab_order_data['labor_id'].'" class="cellOffice ahref" style="text-align: center; background-color:#FEFEFE;">
                                            '.$labors_jarr[$lab_order_data['labor_id']]['name'].'
										</a>
										<div class="cellText" style="text-align: left; background-color:#FEFEFE;">
										    '.$lab_order_data['descr'].'
										</div>
										<div class="cellName" style="text-align: center; '.$back_color.'">
										    '.$mark_enter.'
										</div>
									</li>';

                                }
							}
							echo '
                            </ul>';
                        }

						//mysql_close();




                        //Предварительные расчёты пациента
                        $invoice_j = array();

                        echo '
                                    <ul id="invoices" style="margin-left: 6px; margin-bottom: 10px;">					
                                        <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Предварительные расчёты пациента:</li>';

                        $args = [
                            'client_id' => $client_j['id']
                        ];

                        $query = "SELECT * FROM `journal_advanaced_invoice` WHERE `client_id`= :client_id AND `status` <> '9' ORDER BY `create_time`";

                        //Выбрать все
                        $invoice_j = $db::getRows($query, $args);

//                        $query = "SELECT * FROM `journal_advanaced_invoice` WHERE `client_id`='" . $client_j['id'] . "' AND `status` <> '9' ORDER BY `create_time`";
//
//                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//                        $number = mysqli_num_rows($res);
//                        if ($number != 0) {
//                            while ($arr = mysqli_fetch_assoc($res)) {
//                                array_push($invoice_j, $arr);
//                            }
//                        } else
                            $invoice_j = 0;
                        //var_dump ($invoice_j);

                        if (!empty($invoice_j)) {
                            //var_dump ($invoice_j);
                            echo '
                                            <li class="cellsBlock" style="width: auto; vertical-align: top;">';
                            foreach ($invoice_j as $invoice_item) {

                                echo '
                                                <div class="cellsBlockHover" style="display: inline-block;  vertical-align: top;">
                                                    <a href="invoice_advance.php?id=' . $invoice_item['id'] . '" class="cellName ahref" style="border-right: none;">
                                                        <b>Пред. расчёт #' . $invoice_item['id'] . '</b><br>
                                                        <span style="font-size: 85%; color: #999;">Автор: ' . WriteSearchUser('spr_workers', $invoice_item['create_person'], 'user', false) . '</span><br>
                                                        <span style="font-size: 85%; color: #999;">' . date('d.m.y H:i', strtotime($invoice_item['create_time'])) . '</span>
                                                    </a>
                                                    <div class="cellName" style="border-left: none;">
                                                        <div style="margin: 1px 0; padding: 1px 3px;">
                                                            <i>'.$invoice_item['comment'].'</i>
                                                        </div>
                                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                            Сумма:<br>
                                                            <span class="calculateInvoice" style="font-size: 13px">' . $invoice_item['summ'] . '</span> руб.
                                                        </div>';
                                if ($invoice_item['summins'] != 0) {
                                    echo '
                                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                            Страховка:<br>
                                                            <span class="calculateInsInvoice" style="font-size: 13px">' . $invoice_item['summins'] . '</span> руб.
                                                        </div>';
                                }
                                echo '
                                                    </div>';
                                echo '
                                                </div>';

                            }
                            echo '
                                            </li>';

                        } else {
                            echo '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 5px; color: red;">Нет нарядов</li>';
                        }

                        echo '
                                    </ul>';



						echo '
							</div>';

					}

					//--> Стоматология

					//Косметология  -->

					if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
						echo '
							<div id="tabs-4">';

						/*echo '
							<div id="div2">';*/

						if (($cosm['add_own'] == 1) || ($cosm['edit'] == 1) || $god_mode){
							echo '
								<!--<a href="add_error.php" class="b">Добавить посещение</a>-->
								<a href="add_kd.php?client='.$client_j['id'].'" class="b">Добавить КД</a>
								<a href="kd.php?client='.$client_j['id'].'" class="b">КД</a>
								<a href="etaps.php?client='.$client_j['id'].'" class="b">Исследования</a>';
							/*if (($clients['close'] == 1) || $god_mode){
								echo '
								<a href="cosm_move.php?id='.$client_j['id'].'" class="b">Переместить</a>';
							}*/
						}

                        //Напоминания (косметология)
                        $notes = array();

                        $args = [
                            'client_id' => $client_j['id'],
                            'type' => 6
                        ];

                        //$query = "SELECT * FROM `notes` WHERE `client`= :client_id ORDER BY `dead_line` ASC";

                        $query = "SELECT n.*, s_c.name, s_w.name AS w_name FROM `notes` n
                            RIGHT JOIN `spr_clients` s_c 
                            ON s_c.id = n.client  
                            RIGHT JOIN `spr_workers` s_w
                            ON s_w.id = n.create_person 
                            WHERE n.client = :client_id AND n.type = :type
                            ORDER BY n.dead_line DESC";

                        //Выбрать все
                        $notes = $db::getRows($query, $args);
                        //var_dump($notes);

                        //Вывод всего
                        if (!empty($notes)) {
                            echo '<br><br>Особые отметки<br>';

                            echo WriteNotes($notes, 0, true, $finances, 6);

                        }


						$cosmet_task = SelDataFromDB('journal_cosmet1', $_GET['id'], 'client_cosm_id');
						//var_dump ($cosmet_task);
						$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');

						if ($cosmet_task != 0){
							for ($i=0; $i < count($cosmet_task); $i++){
								//!а если нет офиса или работника??
								$worker = SelDataFromDB('spr_workers', $cosmet_task[$i]['worker'], 'worker_id');
								$offices = SelDataFromDB('spr_filials', $cosmet_task[$i]['office'], 'offices');
								echo '<br><br>
									<div class="cellsBlock3">
										<div class="cellLeft">
											<a href="task_cosmet.php?id='.$cosmet_task[$i]['id'].'" class="ahref">
												'.date('d.m.y H:i', $cosmet_task[$i]['create_time']).'
												<br />
												'.$worker[0]['name'].'
												<br />
												'.$offices[0]['name'].'
											</a>
										</div>';

								$decription = array();
								$decription_temp_arr = array();
								$decription_temp = '';

								/*!!!Лайфхак для посещений из-за переделки структуры бд*/
								foreach($cosmet_task[$i] as $key => $value){
									if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && ($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
										$decription_temp_arr[mb_substr($key, 1)] = $value;
									}
								}

                                //var_dump ($decription_temp_arr);

                                $decription = $decription_temp_arr;
                                /*$decription = array();
                                $decription = json_decode($cosmet_task[$i]['description'], true);
                                var_dump ($actions_cosmet);	*/

								echo '<div class="cellLeft">';

								for ($j = 1; $j <= count($actions_cosmet)-2; $j++) {
									$action = '';
									if (isset($decription[$j])){
										if ($decription[$j] != 0){
											$action = '<div style="margin: 2px; border: 1px solid #CCC; padding-left: 3px; background-color: '.$actions_cosmet[$j-1]['color'].'">'.$actions_cosmet[$j-1]['full_name'].'</div>';
										}else{
											$action = '';
										}
										echo $action;
									}else{
										echo '';
									}
								}

								echo '
										</div>
										<div class="cellRight">';
								//!!!!!!if ($SESSION_ID == )
								echo $cosmet_task[$i]['comment'];
								echo '
										</div>
									</div>';

								//echo ''.date('d.m.y H:i', $cosmet_task[$i]['create_time']).'<br />';
							}
						}else{
								echo '
									<div class="cellsBlock3">
										<div class="cellLeft">
											Не было посещений косметолога
										</div>
									</div>';
						}
						echo '
							</div>';
					}

					//--> Косметология

                    //Прочее (например именные сертификаты) -->
                    echo '
							<div id="tabs-5">';

                    //именные сертификаты, выданные пациенту
                    //Вывод всего
                    if (!empty($cert_name_j)){
                        echo 'Выданные сертификаты<br><br>';
                        for ($i = 0; $i < count($cert_name_j); $i++) {

                            $status = '';

                            if ($cert_name_j[$i]['status'] == 9) {
                                $back_color = 'background-color: rgba(161,161,161,1);';
                                $status = 'Удалён';
                            }elseif ($cert_name_j[$i]['status'] == 7){
                                $back_color = 'background-color: rgba(47, 186, 239, 0.7);';
                                $status = '<div style="font-size: 90%;">Выдан '.date('d.m.y H:i', strtotime($cert_name_j[$i]['cell_time'])).'</div>';

                                $expires_time_color = '';

                                $expirestime1weekminus = date('Y-m-d', strtotime(date('Y-m-d', strtotime($cert_name_j[$i]['expires_time'])).' -2 weeks'));
                                //var_dump($expirestime1weekminus);

//                                if (date('Y-m-d', time()) > $expirestime1weekminus) {
//                                    $expires_time_color = 'color: rgb(236 62 62);';
//                                }

//                                $status .= '
//                                    <div style="font-size: 85%; '.$expires_time_color.'"><b>Срок истекает: '.date('d.m.Y', strtotime($cert_name_j[$i]['expires_time'])).'</b></div>';
                            }elseif ($cert_name_j[$i]['status'] == 5){
                                //Закрыт
                                $back_color = 'background-color: rgba(119, 255, 135, 1);';
                                //$status = 'Закрыт '.date('d.m.y H:i', strtotime($cert_name_j[$i]['closed_time']));
                                $status = 'Использован '.date('d.m.y H:i', strtotime($cert_name_j[$i]['closed_time'])).'<br><i>В наряде: <a href="invoice.php?id=' . $cert_name_j[$i]['invoice_id'] . '" class="ahref" target="_blank" rel="nofollow noopener"><b>#' . $cert_name_j[$i]['invoice_id'] . '</b></a></i>';
                            }else{
                                $back_color = '';
                            }

                            $expired_color = '';
                            $expired_txt = '';

                            if (($cert_name_j[$i]['expires_time'] != '0000-00-00') && ($cert_name_j[$i]['status'] != 5)) {
                                //время истечения срока годности
                                $sd = $cert_name_j[$i]['expires_time'];
                                //текущее
                                $cd = date('Y-m-d', time());
                                /*var_dump(strtotime($sd));
                                var_dump(strtotime($cd));*/
                                //сравнение не прошла ли гарантия
                                if (strtotime($sd) > strtotime($cd)) {
                                    $expired_txt .= '';
                                } else {
                                    $expired_color = 'background-color: rgba(239,47,55, .7)';
                                    $back_color = 'background-color: rgba(255, 50, 25, 0.5)';
                                    $status = 'Истёк срок '.date('d.m.Y', strtotime($cert_name_j[$i]['expires_time']));
                                }

                            }


                            echo '
							<li class="cellsBlock3" style="'.$back_color.'">
								<div class="cellPriority" style=" margin-bottom: -1px;"></div>
								<a href="certificate_name.php?id='.$cert_name_j[$i]['id'].'" class="cellOffice ahref 4filter" style="text-align: left; font-weight: bold; width: 180px; min-width: 180px;" id="4filter">'.$cert_name_j[$i]['num'].'</a>
								<!--<div class="cellOffice" style="text-align: right">'.$cert_name_j[$i]['nominal'].' руб.</div>-->
								<!--<div class="cellOffice" style="text-align: right">';
                            //Очень странное условие, не помню, что тут должно было быть
                            //if (($cert_name_j[$i]['status'] == 7) && ($cert_name_j[$i]['status'] != '0000-00-00 00:00:00')) {
                            //Поменял его на это
                            if ($cert_name_j[$i]['status'] == 7) {
                                echo ($cert_name_j[$i]['nominal'] - $cert_name_j[$i]['debited']).' руб.';
                            }
                            echo '
                                 </div>-->';
                            echo '
								<div class="cellText" style="text-align: center;">'.$status.'';
//                    if ($cert_name_j[$i]['office_id'] != 0) {
//                        $offices_j = SelDataFromDB('spr_filials', $cert_name_j[$i]['office_id'], 'offices');
//                        if ($offices_j != 0) {
//                            echo '<div style="font-size: 90%;"><i>'.$offices_j[0]['name'].'</i></div>';
//                        }else {
//                            echo '-';
//                        }
//                    }
                            echo '
                                </div>';
                            echo '
							</li>';
                        }
                    }


                    echo '
							</div>';

                    //--> Прочее (например именные сертификаты)

					echo '
						</div>
					</div>
                    <!-- Подложка только одна -->
                    <div id="overlay"></div>';

			}

			echo '					
				<div id="doc_title">'.$client_j['full_name'].' - Карточка пациента - Асмедика</div>
				</div>
			</div>


			<script language="JavaScript" type="text/javascript">
			
                $(document).ready(function() {
                    //Получаем, показываем запись
                    getZapisfunc ('.$_GET['id'].');
                    getClientMoney ('.$_GET['id'].');
                });			
			
				 /*<![CDATA[*/
				 var s=[],s_timer=[];
				 function show(id,h,spd)
				 { 
					s[id]= s[id]==spd? -spd : spd;
					s_timer[id]=setTimeout(function() 
					{
						var obj=document.getElementById(id);
						if(obj.offsetHeight+s[id]>=h)
						{
							obj.style.height=h+"px";obj.style.overflow="auto";
						}
						else 
							if(obj.offsetHeight+s[id]<=0)
							{
								obj.style.height=0+"px";obj.style.display="none";
							}
							else 
							{
								obj.style.height=(obj.offsetHeight+s[id])+"px";
								obj.style.overflow="hidden";
								obj.style.display="block";
								setTimeout(arguments.callee, 10);
							}
					}, 10);
				 }
				 /*]]>*/
			 </script>';

			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}

	require_once 'footer.php';

?>