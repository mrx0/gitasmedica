<?php

//variables.php
//Глобальные переменные

	//Массив с месяцами
	$monthsName = array(
		'01' => 'Январь',
		'02' => 'Февраль',
		'03' => 'Март',
		'04' => 'Апрель',
		'05' => 'Май',
		'06' => 'Июнь',
		'07' => 'Июль',
		'08' => 'Август',
		'09' => 'Сентябрь',
		'10' => 'Октябрь',
		'11' => 'Ноябрь',
		'12' => 'Декабрь'
	);

	//Для напоминаний
    $for_notes = array(
        5 =>
            array (
                1 => 'Каласепт, Метапекс, Септомиксин (Эндосольф)',
                2 => 'Временная пломба',
                3 => 'Открытый зуб',
                4 => 'Депульпин',
                5 => 'Распломбирован под вкладку (вкладка)',
                6 => 'Имплантация (ФДМ ,  абатмент, временная коронка на импланте)',
                7 => 'Временная коронка',
                10 => 'Установлены брекеты',
                8 => 'Санированные пациенты ( поддерживающее лечение через 6 мес)',
                12 => 'Условно санированные пациенты',
                9 => 'Прочее',
                11 => 'Сертификат',
            ),
        6 =>
            array (
                13 => 'Биоревитализация',
                14 => 'Контурная пластика',
                15 => 'Ботокс',
                16 => 'Картон',
                17 => 'Уходовые процедуры',
                18 => 'Эпиляция',
                19 => 'Мезотерапия',
                12 => 'Прочее'
            )
    );


    $for_notes_colors = array (
        5 =>
            array (
                1 => '#ED1C24',
                2 => '#ED1C24',
                3 => '#ED1C24',
                4 => '#ED1C24',
                5 => '#00A2E8',
                6 => '#00A2E8',
                7 => '#ED1C24',
                10 => '#00A2E8',
                8 => '#FFF200',
                12 => '#FFF200',
                9 => '#A349A4',
                11 => '#22B14C',
            ),
        6 =>
            array (
                13 => '#ED1C24',
                14 => '#ED1C24',
                15 => '#ED1C24',
                16 => '#ED1C24',
                17 => '#00A2E8',
                18 => '#00A2E8',
                19 => '#ED1C24',
                12 => '#A349A4'
            )
    );

    //Массив с днями недели
    $dayWeek_arr = array(
        1 => 'Пн',
        2 => 'Вт',
        3 => 'Ср',
        4 => 'Чт',
        5 => 'Пт',
        6 => 'Сб',
        7 => 'Вс',
    );

    //Массив тех, кому видно заявку (тикет) по умолчанию
    //$permissionsWhoCanSee_arr = array(2, 3, 8, 9);

    //Массив обозначений времени приёма (по умолчанию)
    $zapis_times = array (
        0 => '0:00 - 0:30',
        30 => '0:30 - 1:00',
        60 => '1:00 - 1:30',
        90 => '1:30 - 2:00',
        120 => '2:00 - 2:30',
        150 => '2:30 - 3:00',
        180 => '3:00 - 3:30',
        210 => '3:30 - 4:00',
        240 => '4:00 - 4:30',
        270 => '4:30 - 5:00',
        300 => '5:00 - 5:30',
        330 => '5:30 - 6:00',
        360 => '6:00 - 6:30',
        390 => '6:30 - 7:00',
        420 => '7:00 - 7:30',
        450 => '7:30 - 8:00',
        480 => '8:00 - 8:30',
        510 => '8:30 - 9:00',
        540 => '9:00 - 9:30',
        570 => '9:30 - 10:00',
        600 => '10:00 - 10:30',
        630 => '10:30 - 11:00',
        660 => '11:00 - 11:30',
        690 => '11:30 - 12:00',
        720 => '12:00 - 12:30',
        750 => '12:30 - 13:00',
        780 => '13:00 - 13:30',
        810 => '13:30 - 14:00',
        840 => '14:00 - 14:30',
        870 => '14:30 - 15:00',
        900 => '15:00 - 15:30',
        930 => '15:30 - 16:00',
        960 => '16:00 - 16:30',
        990 => '16:30 - 17:00',
        1020 => '17:00 - 17:30',
        1050 => '17:30 - 18:00',
        1080 => '18:00 - 18:30',
        1110 => '18:30 - 19:00',
        1140 => '19:00 - 19:30',
        1170 => '19:30 - 20:00',
        1200 => '20:00 - 20:30',
        1230 => '20:30 - 21:00',
        1260 => '21:00 - 21:30',
        1290 => '21:30 - 22:00',
        1320 => '22:00 - 22:30',
        1350 => '22:30 - 23:00',
        1380 => '23:00 - 23:30',
        1410 => '23:30 - 00:00',
    );

    //Массив типов сотрудников, которые никуда не входят (не стоматологи, не косметологи, не администраторы, и тд)
    $workers_target_arr = [1, 9, 12];

    //Еденицы измерения
    $units =  array (
        'pc' => 'шт.',
        'gк' => 'г.',
        'ml' => 'мл.',
        'sh' => 'ш-ц.',
    );
	
?>