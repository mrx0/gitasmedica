<?php

//invoice_advance_print.php
//Предварительный расчёт ПЕЧАТЬ

require_once 'header.php';

if ($enter_ok){

    include_once 'DBWork.php';
    include_once 'functions.php';

    require_once 'permissions.php';

    //if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || ($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode) {
    if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode){

        echo '

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
                    
                    <!--Для печати-->
                    <link rel="stylesheet" href="css/paper.css">
                    <style>@page { size: A4 }</style>
                    
                    <!--для печати-->	
                    <style type="text/css" media="print">
                      div.no_print {display: none; }
                      .never_print_it {display: none; }
                      #scrollUp {display: none; }
                    </style> 
        
                </head>';


        //$paper_format = 'A5 landscape';
        $paper_format = 'A4 landscape';

        if (isset($_GET['format'])){
            if ($_GET['format'] == 'A4') {
                $paper_format = 'A4';
            }
        }

        echo '

                <!-- Set "A5", "A4" or "A3" for class name -->
                <!-- Set also "landscape" if you need -->
                <body class="'.$paper_format.'" style="">
                
                    <!-- Each sheet element should have the class "sheet" -->
                    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
                    <section class="sheet padding-3mm" style="/*border: 1px dotted #0C0C0C*/">';

        echo '
                                <!-- Write HTML just like a web page -->
                        <article>
                                
                                   <!--<h2 style="padding: 0 0 7px; font-size: 3.5mm; color: #0C0C0C; text-align: center; font-weight: bold;">Предварительный расчёт #'.$_GET['id'].'</h2>-->';
        echo '
                            <div style="border: 1px solid #CCC; width: 49%; height: 204mm; float: left; vertical-align: top; font-family: Georgia, \'Times New Roman\', Times, serif; font-size: 12px">
                                <div>«__» __________ 20__ г.</div>
                                <div>Жалобы: ___________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>Объективно: _______________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>Диагноз:___________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>Лечение: __________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>Рекомендовано:_____________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                

Врач Явка “ ” 201 г.

Список рекомендаций получил(а) Подпись пациента


Министерство Здравоохранения РФ ООО «Приор» СПб, пр. Гражданский, д.114 Медицинская документация Форма №043/у Утверждена Минздравом СССР 04.10.80 №1030

Наименование учреждения

Вкладыш из медицинской карты стоматологического больного

№

Фамилия, имя, отчество возраст _______

Пол (м. ж.) Адрес

Контактный телефон_____________________________________________________

«______»_______________201 г. Лечащий врач

Карточка находится______________________________________________________

Министерство Здравоохранения РФ ООО «Приор» СПб, Гражданский пр, д.114 Медицинская документация Форма №043/у Утверждена Минздравом СССР 04.10.80 №1030

Наименование учреждения

Вкладыш из медицинской карты стоматологического больного

№

Фамилия, имя, отчество возраст _______

Пол (м. ж.) Адрес

Контактный телефон_____________________________________________________

«______»_______________201 г. Лечащий врач

Карточка находится______________________________________________________
                            
                            </div>
                            <div style="border: 1px solid #CCC; width: 49%; height: 204mm; float: right; vertical-align: top;">
                            
                            
                            </div>
        ';

        echo '           
                        </article>';
        echo '                
                    </section>
                  
                    <div class="no_print" style="position: fixed; top: 10px; right: 10px; border: 1px solid #0C0C0C; border-radius: 5px; padding: 5px 5px; background-color: #FFFFFF">
                        <!--<a href="?id='.$_GET['id'].'&format=A4" class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;">
                            A4
                        </a>
                        <a href="?id='.$_GET['id'].'&format=A5" class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;">
                            A5
                        </a>-->
                        <div class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;"
                        onclick="window.print();">
                            <i class="fa fa-print" aria-hidden="true"></i>
                        </div>
                    </div>
                
                </body>';


    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

echo '
	</html>';

?>