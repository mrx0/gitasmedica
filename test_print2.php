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

        $fio = '<u>'.'ФИО ПАЦИЕНТА'.'</u>';
        $fio_str_left = '';
        $fio_str_right = '';
        $bdate_str = '<u>'.'00.00.0000'.'</u>';
        $age_str = '<u>'.'00'.'</u>';
        $sex_str = '<u>'.'?'.'</u>';

        $card = '';

        $telephone = '__(____)__________';
        $htelephone = '__(____)__________';
        $telephoneo = '__(____)__________';
        $htelephoneo = '__(____)__________';

        $email = '';
        $email_str_right = '';

        $passport = ' серия _________ № _________________';
        $passportvidandata = '«___» ___________ _____ г.';
        $passportvidankem = '___________________________________________________________________________';

        $passporto = ' серия _________ № _________________';
        $passportvidandatao = '«___» ___________ _____ г.';
        $passportvidankemo = '___________________________________________________________________________';

        $address = '_____________________________________________________________________';
        $addresso = '_____________________________________________________________________';

        if (isset($_GET['client_id'])){

            $msql_cnnct = ConnectToDB ();

            //Для лога соберем сначала то, что было в записи.
            $query = "SELECT * FROM `spr_clients` WHERE `id`='{$_GET['client_id']}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                $arr = mysqli_fetch_assoc($res);
//                var_dump($arr);

                $fio = mb_strtoupper($arr['full_name'], "UTF-8");
                $bdate_str = '<u>'.date('d.m.Y', strtotime($arr['birthday2'])).'</u>';
                $age_str = '<u>'.getyeardiff(strtotime($arr['birthday2']), 0).'</u>';
                if ($arr['sex'] == 1){
                    $sex_str = '<u>'.'М'.'</u>';
                }
                if ($arr['sex'] == 2){
                    $sex_str = '<u>'.'Ж'.'</u>';
                }

                $card = $arr['card'];

                //Телефон
                if (mb_strlen($arr['telephone']) > 0) {
                    $telephone = '<u>'.$arr['telephone'].'</u>';
                }
                if (mb_strlen($arr['htelephone']) > 0) {
                    $htelephone = '<u>'.$arr['htelephone'].'</u>';
                }
                //Если есть данные опекуна, переделаем телефон
                if (mb_strlen($arr['telephoneo']) > 0){
                    $telephoneo = '<u>'.$arr['telephoneo'].'</u>';
                }
                if (mb_strlen($arr['htelephoneo']) > 0){
                    $htelephoneo = '<u>'.$arr['htelephoneo'].'</u>';
                }

                //E-mail
                if ($arr['email'] != NULL){
                    $email = $arr['email'];
                }

                //Паспорт
                if (mb_strlen($arr['passport']) > 0){
                    $passport = ' серия '.explode(' ', $arr['passport'])[0].' № '.explode(' ', $arr['passport'])[1].'';
                }
                if (mb_strlen($arr['passportvidandata']) > 0){
                    $passportvidandata = $arr['passportvidandata'];
                }
                if (mb_strlen($arr['passportvidankem']) > 0){
                    $passportvidankem = $arr['passportvidankem'];
                }

                //Адрес
                if (mb_strlen($arr['address']) > 0){
                    $address = $arr['address'];
                }
            }

        }

        //Соберём данные, присвоим значения переменным
        //ФИО
        $diff_symbols_left = 71 - mb_strlen($fio, 'UTF-8');
        $diff_symbols_right = 50 - mb_strlen($fio, 'UTF-8');
        $fio_str_left = str_repeat("_", intval($diff_symbols_left/2)).'<u>'.$fio.'</u>'.str_repeat("_", $diff_symbols_left - intval($diff_symbols_left/2));
        $fio_str_right = str_repeat("_", intval($diff_symbols_right/2)).'<u>'.$fio.'</u>'.str_repeat("_", $diff_symbols_right - intval($diff_symbols_right/2));

        $diff_symbols_right = 75 - mb_strlen($email, 'UTF-8');
        $email_str_right = '<u>'.$email.'</u>'.str_repeat("_", $diff_symbols_right);



        echo '
                            <div style="position: relative; /*border: 1px solid #CCC;*/ width: 49%; height: 200mm; float: left; vertical-align: top; font-family: \'Times New Roman\', Georgia, Times, serif; font-size: 12px">
                                <div style="margin-top: 7px; font-weight: bold; text-align: center;">6. Приложения и дополнительные соглашения</div>
                                <div style="margin-top: 5px; text-align: center;">6.1. С указанными ниже документами ознакомлен, согласен и обязуюсь соблюдать.</div> 
                                <div style="margin-top: 5px;">
                                    <table style="text-align: center; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 15px; border: 1px solid #111; vertical-align: middle; padding: 2px 4px;"></td>
                                            <td style="width: 50%; border: 1px solid #111; vertical-align: middle; padding: 2px 4px;"></td>
                                            <td style="border: 1px solid #111; vertical-align: middle; padding: 2px 4px;">ФИО Пациент</td>
                                            <td style="border: 1px solid #111; vertical-align: middle; padding: 2px 4px;">Подпись</td>
                                        </tr>
                                       <tr>
                                            <td style="width: 15px; vertical-align: middle; padding: 2px 4px; border: 1px solid #111;">1</td>
                                            <td style="vertical-align: middle; padding: 2px 4px; border: 1px solid #111; text-align: left;">Правила внутреннего распорядка для пациентов</td>
                                            <td style="vertical-align: middle; padding: 2px 4px; border: 1px solid #111;"></td>
                                            <td style="vertical-align: middle; padding: 2px 4px; border: 1px solid #111;"></td>
                                       </tr>
                                       <tr>
                                            <td style="width: 15px; vertical-align: middle; padding: 2px 4px; border: 1px solid #111;">2</td>
                                            <td style="vertical-align: middle; padding: 2px 4px; border: 1px solid #111; text-align: left;">Прейскурант на платные стоматологические услуги</td>
                                            <td style="vertical-align: middle; padding: 2px 4px; border: 1px solid #111;"></td>
                                            <td style="vertical-align: middle; padding: 2px 4px; border: 1px solid #111;"></td>
                                       </tr>                                       <tr>
                                            <td style="width: 15px; vertical-align: middle; padding: 2px 4px; border: 1px solid #111;">3</td>
                                            <td style="vertical-align: middle; padding: 2px 4px; border: 1px solid #111; text-align: left;">Положение о предоставлении гарантий на стоматологические услуги</td>
                                            <td style="vertical-align: middle; padding: 2px 4px; border: 1px solid #111;"></td>
                                            <td style="vertical-align: middle; padding: 2px 4px; border: 1px solid #111;"></td>
                                       </tr>
                                   </table>
                                </div>
                                <div style="margin-top: 7px; font-weight: bold; text-align: center;">7. Реквизиты и подписи сторон</div>
                                <div style="margin-top: 5px; font-weight: bold;">Пациент</div>
                                <div style="margin-top: 1px;">Ф.И.О. '.$fio_str_left.'</div>										
                                <div style="margin-top: 1px;">Место жительства: '.$address.'</div>
                                <div style="margin-top: 1px;">Паспорт: '.$passport.'</div>
                                <div style="margin-top: 1px;">Кем выдан: '.$passportvidankem.'</div>
                                <div style="margin-top: 1px;">Дата выдачи: '.$passportvidandata.'</div>
                                <div style="margin-top: 1px;">Телефон моб.: '.$telephone.' / дом.: '.$htelephone.'</div>
                                <div style="margin-top: 1px;">Место работы _________________________ Должность ______________________________________</div>
                                <div style="margin-top: 1px;">___________________________ /__________________________________________________________</div>
                                <div style="margin-top: -2px; font-size: 9px;">
                                    <div style="width: 30%; display: inline-block; text-align: center;">подпись</div><div style="width: 50%; display: inline-block; text-align: center;">расшифровка подписи</div>
                                </div> 
                                <div style="margin-top: 2px; clear: both;">От имени пациента. Законный представитель пациента:</div>
                                <div style="margin-top: 3px;">___________________________ /__________________________________________________________</div>
                                <div style="margin-top: -2px; font-size: 9px;">
                                    <div style="width: 30%; display: inline-block; text-align: center;">подпись</div><div style="width: 50%; display: inline-block; text-align: center;">Ф.И.О., разборчиво</div>
                                </div>
                                <div style="margin-top: 3px; clear: both;">Паспорт: '.$passporto.'</div>
                                <div style="margin-top: 1px;">Кем выдан: '.$passportvidankemo.'</div>
                                <div style="margin-top: 1px;">Дата выдачи: '.$passportvidandatao.'</div>
                                <div style="margin-top: 1px;">Место жительства: '.$addresso.'</div>
                                <div style="margin-top: 1px;">Место работы _________________________ Должность ______________________________________</div>
                                <div style="margin-top: 1px;">Телефон моб.: '.$telephoneo.' / дом.: '.$htelephoneo.'</div>
                                <div style="margin-top: 3px;">Я  даю  свое  согласие   на  отправку   мне   медицинских  документов  и  прочей  медицинской  информации о состоянии моего здоровья  на  e-mail: '.$email.'</div>
                                <div style="margin-top: 3px;">Экземпляр договора на руки получил(а).</div>
                                <div style="margin-top: 1px;">___________________________ /__________________________________________________________</div>
                                <div style="margin-top: -2px; font-size: 9px;">
                                    <div style="width: 30%; display: inline-block; text-align: center;">подпись</div><div style="width: 50%; display: inline-block; text-align: center;">расшифровка подписи</div>
                                </div>
                                <div style="margin-top: 7px;"><span style="font-weight: bold;">Исполнитель:</span> ООО “Приор”</div>
                                <div style="margin-top: 0px; font-size: 85%;">
                                    Зарегистрировано Регистрационной палатой Администрации Санкт-Петербурга 15.01.2001,<br>
                                    регистрационный номер 229902, ОГРН  1037811001337<br> 
                                    Юридический адрес: 191014, Санкт – Петербург, Литейный пр., 59, лит.А, пом. 14-Н<br>
                                    Место осуществления медицинской деятельности: Санкт-Петербург, Гражданский пр., д. 114,<br>
                                    корп.1, лит. А, часть пом. 53Н<br>
                                    Лицензия на осуществление медицинской деятельности №78-01-004424 от 21 февраля 2014 г.,<br>
                                    выдана Комитетом по здравоохранению Санкт-Петербурга: 191023,<br> 
                                    Санкт-Петербург, Малая Садовая ул., д. 1, тел. (812) 6355564<br>
                                    ИНН: 7805196817 /КПП: 784101001  Р/с  40702810501001204723<br>
                                    Филиал Северо-Западный ПАО Банка «ФК Открытие» БИК 044030795<br>
                                    к/с 30101810540300000795
                                </div>
                                <div style="margin-top: 10px;">
                                    <div style="display: inline-block; width: 50%; font-size: 12px;">
                                        <div>Директор</div>
                                        <div>________________ /Денисов А.Н./ </div>
                                        <div style="margin-top: -2px; font-size: 9px;">
                                            <div style="width: 30%; display: inline-block; text-align: center;">м.п.</div>
                                        </div>
                                    </div>
                                </div>
                                <div style="position: absolute; bottom: -5px; left: 50%; text-align: center;  font-size: 10px;">-24-</div>
                            </div>
                            
                            <div style="position: relative; /*border: 1px solid #CCC;*/ width: 49%; height: 200mm; float: right; vertical-align: top; font-family: \'Times New Roman\', Georgia, Times, serif; font-size: 12px">
                                <div style="font-size: 8px;">
                                    <table style="text-align: center; border-top: 1px solid #111; border-bottom: 1px solid #111; width: 100%;">
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
                                <div style="text-align: center; margin-top: 30px; font-weight: bold;">Медицинская карта стоматологического больного</div>
                                <div style="text-align: center; margin-top: 15px; font-weight: bold;">№ '.$card.'</div>
                                <div style="margin-top: 10px;">________________________________________________________________________________</div>
                                <div style="margin-top: 10px;">Фамилия, имя, отчество '.$fio_str_right.'</div>
                                <div style="margin-top: 10px;">________________________________________________________________________________</div>
                                <div style="margin-top: 10px;">Год рождения _____'.$bdate_str.'______ полных лет '.$age_str.'__________________ пол (м.ж.) ___'.$sex_str.'__</div>
                                <div style="margin-top: 10px;"> Адрес __________________________________________________________________________</div>
                                <div style="margin-top: 10px;">________________________________________________________________________________</div>
                                <div style="margin-top: 10px;">E-mail __'.$email_str_right.'</div>
                                <div style="margin-top: 10px;">Профессия ______________________________________________________________________</div>									
                                <div style="margin-top: 10px;">Диагноз ________________________________________________________________________</div> 									
                                <div style="margin-top: 10px;">Жалобы ________________________________________________________________________</div>
                                <div style="margin-top: 10px;">________________________________________________________________________________</div>
                                <div style="margin-top: 10px;">________________________________________________________________________________</div>
                                <div style="margin-top: 10px;">________________________________________________________________________________</div>
                                <div style="margin-top: 10px;">________________________________________________________________________________</div>
                                <div style="margin-top: 10px;">________________________________________________________________________________</div>
                                <div style="margin-top: 10px;">Перенесенные и сопутствующие заболевания _________________________________________</div>
                                <div style="margin-top: 10px;">________________________________________________________________________________</div>
                                <div style="margin-top: 10px;">________________________________________________Онкосмотр_______________________</div>
                                <div style="margin-top: 10px;">________________________________________________Гепатит__________________________</div>
                                <div style="margin-top: 10px;">Развитие настоящего заболевания ___________________________________________________</div>
                                <div style="margin-top: 10px;">________________________________________________________________________________</div>
                                <div style="margin-top: 10px;">_____________________________________________________ТВС_______________________</div>
                                <div style="margin-top: 10px;">_____________________________________________________Вен.заболев.________________</div>
                                <div style="margin-top: 10px;">_________________________________________________________«___» ___________ 20__ г.</div>
                                <div style="margin-top: 10px;">
                                    <div style="display: inline-block; width: 50%; font-size: 12px;"></div>
                                    <div style="display: inline-block; font-size: 12px;">Лечащий врач _______________________</div>
                                </div>
                                <div style="position: absolute; bottom: -5px; left: 50%; text-align: center;  font-size: 10px;">-1-</div>
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