<?php

//stom_instruction.php
//Главная

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

	
		echo '
			<header style="margin-bottom: 5px;">
				<h1>Краткая инструкция для стоматологов по основным действиям</h1>';
			echo '
			</header>
		
				<div id="data">		

<span style="color: red; font-weight:bold;">Важно! Зубные отложения (ЗО).</span><br />
При первом выборе ЗО на ЗФ появляется красная отметка о том, что присутствуют ЗО.<br />
Если на зубе уже отмечены ЗО, то при следующем клике отметка меняет цвет на белый (Очищено)<br />
Если ошибочно была поставлена отметка ЗО, то, чтобы её удалить, необходимо либо полностью сбросить настройку зуба,<br />
либо отметить ЗО на данном зубе в третий раз.<br />
Кратко:<br />
1й клик на ЗО = отметка о присутствии ЗО<br />
2й клик на ЗО = ЗО очищено<br />
3й клик на ЗО = Полностью убрать отметки о ЗО<br />
<br />	
<br />	
				
<span style="color: red; font-weight:bold;">Важно! Не забываем отмечать в ЗФ галочкой "Чужой", <br />
если при осмотре обнаружены чужие коронки, импланты и т.д..</span>	<br /><br />		
	
<!--В браузере в адресной строке вводим адрес 88.201.171.90<br />-->
Заходим в программу<br />
Вводим логин и пароль<br />
Выбираем сверху раздел "Стоматология"<br />
Жмём "Добавить"<br />
Далее <br />
	либо:<br />
		Выбираем первую букву фамилии пациента<br />
		С помощью быстрого поиска находим пациента<br />
	либо: <br />
		Жмём "Поиск"<br />
		Вводим первые буквы ФИО (достаточно минимум трёх букв в каждом поле, либо в одном из них)<br />
Если пациента нет...<br />
Жмём "Добавить"<br />
Вводим полностью ФИО, дату рождения, пол, Контакты(телефон и т.д.), лечащего врача (чаще всего себя)<br />
Жмём "Добавить"<br />
Жмём "К списку пациентов"<br />
Ищем пациента одним из указанных выше способов<br />
Жмём на ФИО пациента, чтобы открыть его карточку<br />
Если в самом внизу отображается блок с символами "***". Значит карточка загрузилась. Иначе жмём появления этого блока.<br />
Жмём на кнопку "Стоматология"<br />
Разворачивается блок с текущим состоянием ЗФ (зубной формулы) на текущий момент, если данные по пациенту когда-либо вводились. Либо ничего, если пациента только что завели.<br />
Жмём "Добавить осмотр"<br />
В самом верху выбираем "Филиал" из списка<br />
ФИО пациента уже должно отобразиться в соответствующем поле<br />
На зубной формуле с помощью мыши отмечаем текущее состояние ЗФ<br />
Если необходимо создать напоминание, отмечаем галочкой соответствующий пункт<br />
В появившемся блоке выбираем из выпадающего списка то, о чем необходимо напомнить<br />
Выбираем время до какого момента необходимо напоминание<br />
Если необходимо, создаём направление к другому врачу<br />
В комментариях вводим информацию, которая не охватывается текущими функциями ЗФ, либо иное, что необходимо.<br />
Жмём "Добавить"<br />
В разделе/журнале "Стоматология" отобразится созданный осмотр/посещение на ряду с остальными.<br /><br /><br />

О напоминаниях.<br />
Для того, чтобы посмотреть все свои напоминания, необходимо зайти в свой профиль<br />
В правом верхнем углу жмем на свою Фамилию<br />
В самом низу должны отобразиться все напоминания, которые пользователь создавал<br />
Зеленым будут помечены закрытые/неактуальные напоминания<br />
Красным просроченные/незакрытые<br />
В левом крайнем столбце так же могут быть отметки оранжевым цветом и белым(оранжевый - один, два дня до срока, белый - больше 2х дней до срока)<br />
Также при приближении срока окончания напоминания сверху слева от фамилии пользователя во всех разделах будут гореть "оранжевые" и "красные" напоминания и их количество.<br />
В общем списке напоминаний доступны кнопки "ред."(редактирование) и "закр."(закрытие)<br />
При нажатии на них соответственно будет доступна возможность продлить срок окончания напоминания либо закрыть его.<br />

				
				</div>';
		
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>