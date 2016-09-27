<?php

//index.php
//Главная

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		include_once 'DBWork.php';
		include_once 'functions.php';
		$offices = SelDataFromDB('spr_office', '', '');

		echo '
			<header style="margin-bottom: 5px;">
				<h1>Главная</h1>';
			echo '
			</header>
		
				<div id="data">';
				
					echo '<h2><a href="stom_instruction.php">Краткая инструкция для стоматологов по основным действиям <span style="color:red;">(обновлено 07.04.2016)</span></a></h2>';
					
					
					echo '<h3>История изменений и обновлений.</h3>';
					
					echo '<br><b>27.09.2016</b><br><br>';
					echo '
						- Открыт доступ в программу администраторам<br>
						- Администраторы могут вносить в базу пациентов, редактировать их данные<br>
						- В ЗФ исправлена ошибка со статусом санации, возникающая, <br>
						когда на жевательную поверхность добавлялся статус, например:<br>
						кариес, периодонтит и т.д., а затем на тут же поверхность в следующей ЗФ<br> 
						ставился статус пломба, и т.д.<br>
						Если вы обнаружили такую ошибку, то просто отредактируйте ЗФ, <br>сначала, убрав статус пломбы, а затем снова установите и сохраните.<br>
						<br>';				

					echo '<br><b>16.08.2016</b><br><br>';
					echo '
						- Добавлены статусы: "ретейнер", "сверхкомплект"<br>
						- При выборе в ЗФ статусов "удалён" и "остутствует" удаляются все остальные статусы с зуба<br>
						<br>';				

					echo '<br><b>03.08.2016</b><br><br>';
					echo '
						- После добавления пациента теперь появляются кнопки для добавления посещений стоматологов и косметологов<br>
						<br>';				

						
					echo '<br><b>03.07.2016</b><br><br>';
					echo '
						- В ЗФ добавлены статусы "Шинирование" и "Подвижность"<br>
						- Отображаться новые статусы будут в виде маленьких буков ("ш" и "А") над I и II и под III IV четвертями<br>
						- Исправлены не которые ошибки<br>
						- Исправлена ошибка, когда ЗО не убирались при сбросе всех статусов зуба<br>
						Примечание: при нестабильной работе и ошибках, просьба сообщать сразу<br>
						<br>';				

					
					echo '<br><b>21.06.2016</b><br><br>';
					echo '
						- В ЗФ добавлен статус "Герметизация" (только для жевательной поверхности)<br>
						- При редактировании посещения теперь статусы "ночной", "страховой", "первичный" тоже доступны<br>
						- Изменена скорость работы меню ЗФ в лучшую сторону.<br>
						(В связи с этим временно в меню не пишется номер зуба и названия поверхностей)<br>
						- Санация теперь по некоторым параметрам не учитывает пациентов возрасте до 14 лет.<br>
						- Исправлены некоторые ошибки при загрузке и заполнении фоотснимков КД у косметологов.<br>
						
						<br>';				

					
					echo '<br><b>05.06.2016</b><br><br>';
					echo '
						- 
						- В журнале стоматологов иконкой будут отмечаться страховые посещения<bВ посещениях стоматологов добавлена галочка "страховой" для страховых посещений<br>
						<br>';
					
					echo '<br><b>03.06.2016</b><br><br>';
					echo '
						- Включено определение санации (ТЕСТ).<br>
						Прим.: В санации временно не учитываются ЗО<br>
						- В тестовом режиме включено отображение "санирован/не санирован" в <b><i>посещениях</i></b> пациентов (не в карточках).<br>
						- В журнале "Стоматология" добавлен столбец с изображением зуба. Зелёный фон = санирован. Красный фон = не санирован.<br>
						При нажатии на изображение зуба открывается окно с кратким описанием посещения: ЗФ, пациент, врач, комментарий врача.<br>
						- Раздел о "пропавшей первичке" доступен теперь и стоматологам по их пациентам<br>
						В этом разделе показаны пациенты, которые приходили только 1 раз на осмотр и, не санированные, больше не пришли в клинику.<br>
						Отображаются пациенты за 3 месяца, полгода, год.<br>
						И это только те пациенты, которые были в клинике от 2х месяцев назад и больше.<br>
						- Частично убраны ошибки в оранжевых прямоугольниках.<br>
						Вместо этих ошибок теперь появляется уведомление о том, что необходимо отредактировать ЗФ и указаны зубы, для которых необходимо сделать изменения.<br>
						После редактирования, уведомление должно исчезнуть и ошибок возникать больше не должно.<br>
						<br>';
					
					echo '<br><b>02.06.2016</b><br><br>';
					echo '
						- Для стоматологов вернулся рабочий модуль отфильтрации по пациентам в разделе стоматология.<br>
						- Тестовый модуль для статистики стоматологии по "пропавшей первичке".<br>
						<br>';
					
					echo '<br><b>30.05.2016</b><br><br>';
					echo '
						- Исправил быстрый поиск пациента.<br>
						<br>';	
						
					echo '<br><b>28.05.2016</b><br><br>';
					echo '
						<span style="color:red;">Обновление сильно коснулось ЗФ, возможны ошибки и неточности. Сообщайте сразу.</span><br><br>
					
						- У управляющих появилась возможность редактировать ФИО и данные пациентов<br>
						- Исправлены некоторые ошибки в ЗФ(множественный выбор статуса "Сбросить" и другие)<br>
						- Статус в ЗФ "Корень" переименован в "Корень/радикс"<br>
						- При выборе ЗФ на корне статуса "Корень/радикс" зуб отображается полностью красным без корня<br>
						- У всех стоматологов теперь галочка "первичный" при добавлении посещения по умолчанию отключена<br>
						- В посещениях стоматологов добавлена галочка "ночной" для ночных посещений<br>
						- В журнале стоматологов теперь иконками будут отмечаться первичные и ночные посещения<br>
						- Оптимизирован быстрый поиск пациентов (теперь ищет сначала по фамилии)
						- ЗО на ЗФ теперь сбрасываются при сбросе состояния всего зуба на "здоровый"<br>
						- Исправлены некоторые ошибки<br>
						- Исправлены неверные/ удалены лишние/ добавлены новые ссылки переходов после завершения некоторых действий<br>
						- В ЗФ теперь множественный выбор статуса "Чужой" работает более корректно<br>
						Сначала выбираем статус для нескольких зубов (прим.: "Коронка"), затем выбираем для необходимых зубов статус "Чужой"<br>
						
						<br>
						
						
						';	
						
						
					echo '<br><b>16.05.2016</b><br><br>';
					echo '
						- Раздел ПО переименован в Программа<br>
						- В разделе Программа добавлена возможность добававлять на рассмотрение<br>
						свои идеи и предложения. Режим тестовый.<br>
						- У задач в разделе Программа есть несколько статусов, определяющих<br>
						их судьбу. Менять статусы могут только управляющие.<br>
						- В ЗФ теперь если Мост, то корни зуба не показываются<br>
						<br>
						
						
						';
					echo '<br><b>02.05.2016</b><br><br>';
					echo '
						- При создании посещения стоматолога, оно автоматическми помечается<br>
						как первичное. Изменить это можно, отключив галочку в самом низу.<br>
						- Измение статуса первичное/не первичное доступно в редактировании.<br>
						
						
						';
					echo '<br><b>22.04.2016</b><br><br>';
					echo '
						- Добавлена возможность редактирования и добавления напоминаний<br>
						- Добавлена возможность редактирования и добавления направлений<br>
						Сделать это можно теперь во время редактирования самого посещения.<br>
						Примечание: доступно только стоматологам.<br>
						
						
						';
						
											
					echo '<br><b>07.04.2016</b><br><br>';
					echo '
						- В ЗФ добавлен функционал ЗО<br>
						- Добавления в инструкцию стоматологов по поводу ЗО<br>
						- Множественный выбор для ЗО<br>
						- Теперь в ЗФ, если, к примеру, вы выбрали коронку и вам нужно её убрать,<br>
						вы можете, не сбрасывая настройки зуба, снова выбрать коронку на этом зубе и она пропадёт.<br>
						И так почти со всеми статусами. Не распространяется на множественный выбор.<br>
						- В окне множественного выбора добавлен пункт "+ имплант".<br>
						Для тех случаев, когда необходимо выделить несколько зубов,<br>
						на которых есть коронки с имплантами<br>
						
						
						';
						
						
						
					echo '<br><b>01.04.2016</b><br><br>';
					echo '
						- В списке пациентов добавлен быстрый поиск<br>
						- В карточке каждого пациента так же доступен быстрый поиск пациента<br>
						- В истории каждого пациента так же доступен быстрый поиск пациента.<br>
						Прим. по быстрому поиску: Чтобы попасть в карточку пациента из результата поиска необходим два раза кликнуть на нужного пациента.<br>
						- Посещения теперь можно добавлять сразу из списка пациентов, нажав нужную иконку напротив нужного пациента:<br>
						Для стоматологов: <img src="img/stom_add.png"><br>
						Для косметологов: <img src="img/cosm_add.png"><br>
						Для просмотра истории (стоматология): <img src="img/stom_hist.png"><br>
						- Управляющим доступна функция редактирования посещений<br>
						- Управляющим доступны комментарии в историях пациентов<br>
						- Каждый пользователь в своём профиле теперь может выбрать филиал по умолчанию.<br>
						Это значит, что если необходимо заполнить несколько посещений с одного филиала, то необходимо в профиле выбрать филиал,<br>
						и тогда не придётся каждый раз выбирать его при добавлении посещений.<br>
						- В Отчёте IT список теперь группируется по филиалам<br>
						
						';
					echo '<br><b>12.03.2016</b><br><br>';
					echo '
						- Добавлены статусы "Пульпит" и "Периодонтит" (отметить можно на Окклюзионных поверхностях)<br>
						- Клиновидный деффект теперь можно отметить только на вестибулярной поверхности<br>
						- Исправлены некоторые зависания и ошибки<br>
						- В графике при добавлении врача в смену удалено лишнее диалоговое окно<br>
						
						';
						
					echo '<br><b>09.03.2016</b><br><br>';
					echo '
						- В тестовом режиме включен График Работ<br>
						- В тестовом режиме включена предварительная запись пациентов<br>
						- Где были разделены окклюзионные поверхности на две, теперь осталась визуально только одна<br>
						- Добалена нумерация зубов<br>
						- Добавлен статус "Винир" для вестибулярной поверхности<br>
						- Добавлен статус "Клиновидный дефект" для поверхностей<br>
						- Добавлен статус "Ретенция" для всего зуба<br>
						- Статус "Кариес" для корня переименован в "Корень"<br>
						- Статус "Коронка" <b>для поверхностей</b> изъят из использования<br>
						- В напоминания добален новый пункт "Брекеты" (для устанавливаемых нашими специалистами)<br>
						
						';
						
					echo '<br><b>16.02.2016</b><br><br>';
					echo '
						- Исправление некоторых ошибок<br>
						';
						
					echo '<br><b>13.02.2016</b><br><br>';
					echo '
						- Исправлена ошибка с редактированием лечащего врача,<br>если пациент наблюдается и у стоматолога и у косметолога<br>
						';
						
					echo '<br><b>1.02.2016</b><br><br>';
					echo '
						- Работает режим редактирования посещений в стоматологии<br>
						- Управляющие видят перенаправления врачей в профилях самих врачей<br>
						- В журнал стоматологии добавлены фильтры для управляющих<br>
						';
						
					echo '<br><b>25.01.2016</b><br><br>';
					echo '
						- Включен тестовый вариант перенаправлений.<br>
						Теперь если создано направление, то справа вверху виднеется синяя отметка,<br>
						цифрами отмечено количество направлений, в скобках указано количество направленных к Вам.<br>
						В вашем профиле указана подробная информация.<br>
						- Исправлено отображение названий поверхностей для 3 и 4 четверти ЗФ.<br>
						- Исправлена ошибка, при которой после выбора имплантанта на 36 зубе, происходило искажение всей ЗФ<br>
						- "Вставка" переименована во "Вкладку"<br>
						- Теперь при выборе статуса для всего зуба и для корня того же зуба в отдельности,<br>статус корня будет отображаться как и должен.<br>
						';
						
					echo '<br><b>21.01.2016</b><br><br>';
					echo '
						- Готова <b>пробная</b> система выделения в зубной формуле нескольких сегментов.<br>
						Работает только для статусов общих для всего зуба (коронка, имплант...)<br>
						Для выделения нескольких сегментов нажмите <img src="img/list.jpg"><br>
						';
					
					echo '<br><b>19.01.2016</b><br><br>';
					echo '
						- Ускорена работа зубной формулы<br>
						- В ближайшее время будет готова система выделения в зубной формуле нескольких сегментов
						- Исправлены некоторые недочёты и ошибки<br>
						';
					
					echo '<br><b>28.12.2015</b><br><br>';
					echo '
						- Заработал более расширенный поиск в косметологии<br>
						- Сверху в общем списке разделов добавлена кнопка "Начало"<br>
						- Стоматологам открыт доступ для глобального теста<br>
						- Написана небольшая инструкция по основным действиям для стоматологам. <br>Ссылка находится в разделе "Начало"<br>
						- Исправлены некоторые недочёты и ошибки<br>
						';
					
					echo '<br><b>15.12.2015</b><br><br>';
					echo '
						- В косметологии снова должна заработать функция, чтобы заголовок не уезжал при пролистывании. Проверяйте.<br>
						- При добавлении посещения теперь, если не заполнен филиал или пациент, будет выдана ошибка и внесённые изменения не будут сброшены. Тестируйте.<br>
						';
					echo '<br><b>04.12.2015</b><br><br>';
					echo '
						- Тест зубной формулы для стоматологов.<br>
						- Тест внесения осмотров для стоматологов.<br>
						- Список пациентов дополнен информацией о ДР и Поле.<br>
						- Искать пациентов теперь можно по алфавиту. По умолчанию открываются пациенты, чьи фамилии начинаются с буквы "А".<br>
						';
					echo '<br><b>27.11.2015</b><br><br>';
					echo '
						- В списке пациентов заработал фильтр по дате рождения. Тестируйте.<br>
						- Косметологи теперь могут добавлять посещение из карточки пациента.<br>
						То есть ищем в списке пациентов нужного нам, открываем его карточку, жмём внизу "кнопку косметология", появляется кнопка "добавить посещение".<br>
						При этом в посещении уже будет выбран пациент.<br>
						';
				
			echo '		
				</div>';
		
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>