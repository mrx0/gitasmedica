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
                    <!--<style>@page { size: A4 landscape}</style>-->
                    
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
                                
                                   <!--<h2 style="padding: 0 0 7px; font-size: 3.5mm; color: #0C0C0C; text-align: center; font-weight: bold;">Предварительный расчёт #</h2>-->';
        echo '
                            <div style="/*border: 1px solid #CCC;*/ width: 49%; height: 200mm; float: left; vertical-align: top; font-family: Georgia, \'Times New Roman\', Times, serif; font-size: 12px">
                                <div style="margin-top: 20px;">«__» __________ 20__ г.</div>
                                <div style="margin-top: 15px;">Жалобы: </div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div style="margin-top: 15px;">Объективно: </div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div style="margin-top: 15px;">Диагноз:</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div style="margin-top: 15px;">Лечение: </div>
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
                                <div>___________________________________________________________</div>
                                <div style="margin-top: 15px;">Рекомендовано:</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div>___________________________________________________________</div>
                                <div style="margin-top: 20px;">
                                    <div style="display: inline-block; width: 50%;">Врач _________________</div>
                                    <div style="display: inline-block;">Явка&nbsp;&nbsp;&nbsp;&nbsp;«__» __________ 20__ г.</div>
                                </div>
                                <div style="clear: both; margin-top: 20px;">
                                    <div style="display: inline-block; width: 50%; font-size: 10px;">Список рекомендаций получил(а)</div>
                                    <div style="display: inline-block; font-size: 10px;">Подпись пациента ________________</div>
                                </div>
                            </div>
                            
                            <div style="/*border: 1px solid #CCC;*/ width: 49%; height: 200mm; float: right; vertical-align: top; font-family: Georgia, \'Times New Roman\', Times, serif; font-size: 12px">
                                <div style="font-size: 10px;">
                                    <table style="text-align: center; border-top: 1px solid #111; border-bottom: 1px solid #111;">
                                        <tr>
                                            <td width="30%" style="vertical-align: middle; padding: 2px 4px;">Министерство Здравоохранения<br>РФ</td>
                                            <td rowspan="2" style="vertical-align: middle; padding: 2px 4px; border-left: 1px solid #111; border-right: 1px solid #111;">ООО «Приор»<br>СПб, пр. Гражданский, д.114</td>
                                            <td rowspan="2" width="30%" style="vertical-align: middle; padding: 2px 4px;">Медицинская документация<br>Форма №043/у<br>Утверждена Минздравом СССР<br>04.10.80 №1030</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle; padding: 2px 4px; border-top: 1px solid #111;">Наименование учреждения</td>
                                        </tr>
                                    </table>
                                </div>
                                <div style="text-align: center; margin-top: 30px; font-weight: bold;">Вкладыш из медицинской карты стоматологического больного</div>
                                <div style="text-align: center; margin-top: 15px; font-weight: bold;">№_______________</div>
                                <div style="margin-top: 20px;">Фамилия, имя, отчество ___________________________________________</div>
                                <div style="margin-top: 10px; text-align: justify;">_________________________________________ возраст ______________</div>
                                <div style="margin-top: 10px; text-align: justify;">Пол (м. ж.)_______ Адрес _________________________________________</div>
                                <div style="margin-top: 10px;">_____________________________________________________________</div>
                                <div style="margin-top: 10px;">Контактный телефон _____________________________________________</div>
                                <div style="margin-top: 20px;">
                                    <div style="display: inline-block; width: 50%;">«__» __________ 20__ г.</div>
                                    <div style="display: inline-block;">Лечащий врач _________________</div>
                                </div>
                                <div style="margin-top: 20px;">Карточка находится ______________________________________________</div>
                            </div>';

        echo '           
                        </article>';
        echo '                
                    </section>
                  
                    <div class="no_print" style="position: fixed; top: 10px; right: 10px; border: 1px solid #0C0C0C; border-radius: 5px; padding: 5px 5px; background-color: #FFFFFF">
                        <!--<a href="?id=&format=A4" class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;">
                            A4
                        </a>
                        <a href="?id=&format=A5" class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;">
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