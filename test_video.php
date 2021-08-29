<?php

//test_video.php
//Тест с видео

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        echo '

        
<style type="text/css">

/*#my-video {
    width: 100%;
}*/
 
.buffered {
    height: 20px;
    position: relative;
    background: #555;
    width: 300px;
}
 
#buffered-amount {
    display: block;
    height: 100%;
    background-color: #FFEB3B;
    width: 0;
}
 
.progress {
    margin-top: -20px;
    height: 20px;  
    position: relative;
    width: 300px;
}
 
#progress-amount {
    display: block;
    height: 100%;
    background-color: #595;
    width: 0;
}
</style>';

        echo '
        
<video id="my-video" width="640" height="480" controls poster="" preload="metadata">
   <!--<source src="video/nubex.ogv" type=\'video/ogg; codecs="theora, vorbis"\'>
   <source src="video/nubex.mp4" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>-->
   <source src="video2/01.mp4" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>
   <!--<source src="video/Сергей Мельников Кураторы лечения в стоматологии-master.m3u8" type="application/x-mpegURL">-->
   <!--<source src="video/nubex.webm" type=\'video/webm; codecs="vp8, vorbis"\'>-->
   Ваш браузер не поддерживает тег video.
</video>

<div class="buffered">
    <span id="buffered-amount"></span>
</div>
<div class="progress">
    <span id="progress-amount"></span>
</div>
';

        echo '
<script type="text/javascript">
window.onload = function(){
    var video = document.getElementById(\'my-video\');
 
    video.addEventListener(\'progress\', function() {
        var duration =  video.duration;
        if (duration > 0) {
            for (var i = 0; i < video.buffered.length; i++) {
                if (video.buffered.start(video.buffered.length - 1 - i) < video.currentTime) {
                    document.getElementById("buffered-amount").style.width = (video.buffered.end(video.buffered.length - 1 - i) / duration) * 100 + "%";
                    break;
                }
            }
        }
    });
 
    video.addEventListener(\'timeupdate\', function() {
        var duration =  video.duration;
        if (duration > 0) {
            document.getElementById(\'progress-amount\').style.width = ((video.currentTime / duration)*100) + "%";
        }
    });
}
</script>
        ';

//		if (($spravka['see_all'] == 1) || $god_mode){
//			if ($_GET){
//				include_once 'DBWork.php';
//				include_once 'functions.php';
//
//				$cert_j = SelDataFromDB('journal_cert', $_GET['id'], 'id');
//				//var_dump($cert_j);
//
//				if ($cert_j != 0){
//					echo '
//						<div id="status">
//							<header>
//                                <div class="nav">
//                                    <a href="certificates.php" class="b">Сертификаты</a>
//                                </div>
//								<h2>
//									Карточка сертификата';
//
//					if (($spravka['edit'] == 1) || $god_mode){
//						if ($cert_j[0]['status'] != 9){
//							echo '
//										<a href="cert_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
//						}
//						if (($cert_j[0]['status'] == 9) && (($spravka['close'] == 1) || $god_mode)){
//							echo '
//								<a href="#" onclick="Ajax_reopen_cert('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
//						}
//					}
//					if (($spravka['close'] == 1) || $god_mode){
//						if ($cert_j[0]['status'] != 9){
//							echo '
//										<a href="cert_del.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
//						}
//					}
//
//					echo '
//								</h2>
//								<div id="errror"></div>';
//
//					if ($cert_j[0]['status'] == 9){
//						echo '<i style="color:red;">Сертификат удален (заблокирован).</i><br>';
//					}
//
//                    echo '
//										<div class="cellsBlock2" style="margin-bottom: 10px;">
//											<span style="font-size:80%;  color: #555;">';
//
//                    if (($cert_j[0]['create_time'] != 0) || ($cert_j[0]['create_person'] != 0)){
//                        echo '
//													Добавлен: '.date('d.m.y H:i' ,strtotime($cert_j[0]['create_time'])).'<br>
//													Автор: '.WriteSearchUser('spr_workers', $cert_j[0]['create_person'], 'user', true).'<br>';
//                    }else{
//                        echo 'Добавлен: не указано<br>';
//                    }
//                    if (($cert_j[0]['last_edit_time'] != 0) || ($cert_j[0]['last_edit_person'] != 0)){
//                        echo '
//													Последний раз редактировался: '.date('d.m.y H:i' ,strtotime($cert_j[0]['last_edit_time'])).'<br>
//													Кем: '.WriteSearchUser('spr_workers', $cert_j[0]['last_edit_person'], 'user', true).'';
//                    }
//                    echo '
//											</span>
//										</div>';
//
//					echo '
//							</header>';
//
//					echo '
//							<div id="data">';
//					echo '
//								<div class="cellsBlock2">
//									<div class="cellLeft">Номер</div>
//									<div class="cellRight">'.$cert_j[0]['num'].'</div>
//								</div>
//
//								<div class="cellsBlock2">
//									<div class="cellLeft">Номинал</div>
//									<div class="cellRight">'.$cert_j[0]['nominal'].' руб.</div>
//								</div>
//  								<div class="cellsBlock2">
//									<div class="cellLeft">Продан</div>';
//					if (($cert_j[0]['cell_time'] == '0000-00-00 00:00:00') && ($cert_j[0]['status'] != 7)){
//					    echo '
//                                    <div class="cellRight">нет</div>';
//                    }else {
//					    echo '
//					        <div class="cellRight" style="background-color: rgba(47, 186, 239, 0.7);">'
//                                . date('d.m.y H:i', strtotime($cert_j[0]['cell_time'])) . ' за ' . $cert_j[0]['cell_price'] . ' руб.<br>';
//                        if ($cert_j[0]['office_id'] != 0){
//                            $offices_j = SelDataFromDB('spr_filials', $cert_j[0]['office_id'], 'offices');
//                            if ($offices_j != 0) {
//                                echo '<span style="font-size: 70%;">'.$offices_j[0]['name'].'</span>';
//                            }
//                        }else{
//                            echo '-';
//                        }
//                        //Удалить продажу
//                        if (($finances['see_all'] == 1) || $god_mode) {
//                            echo '<div style="float: right; cursor: pointer;" onclick="Ajax_cert_celling_del('.$_GET['id'].');" title="Отменить продажу"><i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 130%;"></i></div>';
//                        }else{
//                            //Если не закрыт
//                            if ($cert_j[0]['status'] != 5) {
////                                var_dump(date('Y-m-d', time()));
////                                var_dump(date('Y-m-d', strtotime($cert_j[0]['cell_time'])));
//
//                                //Если дата текущая
//                                if (date('Y-m-d', time()) == date('Y-m-d', strtotime($cert_j[0]['cell_time']))){
//                                    //Если совпадает филиал
//                                    if ($_SESSION['filial'] == $cert_j[0]['office_id']){
//                                        echo '<div style="float: right; cursor: pointer;" onclick="Ajax_cert_celling_del('.$_GET['id'].');" title="Отменить продажу"><i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 130%;"></i></div>';
//                                    }
//                                }
//                            }
//                        }
//
//					    echo '
//                            </div>';
//                    }
//                    echo '
//								</div>
//           					    <div class="cellsBlock2">
//									<div class="cellLeft">Потрачено</div>
//									<div class="cellRight">'.$cert_j[0]['debited'].' руб.  ';
//					if ($cert_j[0]['nominal']-$cert_j[0]['debited'] != 0) {
//                        echo '
//					                    <span style="font-weight: bold; font-size: 80%;">(осталось ' . ($cert_j[0]['nominal'] - $cert_j[0]['debited']) . ' руб.)</span>';
//                    }
//                    echo '
//                                    </div>
//								</div>';
//					if ($cert_j[0]['status'] == 5) {
//                        echo '
//           					    <div class="cellsBlock2">
//									<div class="cellLeft">Закрыт (полностью потрачен)</div>
//									<div class="cellRight" style="background-color: rgba(119, 255, 135, 1);">' . date('d.m.y H:i', strtotime($cert_j[0]['closed_time'])) . '</div>
//								</div>';
//                    }
//
//                    if ($cert_j[0]['status'] != 5) {
//                        $expired_color = '';
//
//                        if ($cert_j[0]['expires_time'] != '0000-00-00') {
//                            //время истечения срока годности
//                            $sd = $cert_j[0]['expires_time'];
//                            //текущее
//                            $cd = date('Y-m-d', time());
//                            //сравнение не прошла ли гарантия
//                            /*var_dump(strtotime($sd));
//                            var_dump(strtotime($cd)); */
//                            //var_dump($cert_j[0]['status']);
//                            if (strtotime($sd) > strtotime($cd)) {
//                                echo '
//                                <div class="cellsBlock2">
//                                    <div class="cellLeft">Срок истечёт</div>
//                                    <div class="cellRight">
//                                        ' . date('d.m.Y', strtotime($cert_j[0]['expires_time'])) . '
//                                    ';
//                            } else {
//                                echo '
//                                <div class="cellsBlock2">
//                                    <div class="cellLeft">Истёк срок</div>
//                                    <div class="cellRight" style="background-color: rgba(239,47,55, .7);">' . date('d.m.y', strtotime($cert_j[0]['expires_time']));
//                                //                            if ((($finances['see_all'] == 1) || $god_mode) && ($cert_j[0]['status'] != 5) && ($cert_j[0]['status'] != 9)) {
//                                //                                echo '
//                                //                                    <div style="float: right;">
//                                //                                        <span style="font-size: 80%;">Изменить срок <i class="fa fa-calendar" aria-hidden="true"></i></span><br>
//                                //                                        <input type="text" id="dataCertEnd" name="dataCertEnd" class="dateс" value="'.date('d.m.Y', strtotime($cert_j[0]['expires_time'])).'" onfocus="this.select();_Calendar.lcs(this)"
//                                //                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
//                                //                                        <i class="fa fa-check" aria-hidden="true" style="color: green; cursor: pointer;" title="Применить" onclick="Ajax_change_expiresTime(\'cert\','.$_GET['id'].');"></i>
//                                //                                    </div>';
//                                //                            }
//                                //                            echo '
//                                //                                </div>
//                                //                            </div>';
//                            }
//
//                            if ((($finances['see_all'] == 1) || $god_mode) && ($cert_j[0]['status'] != 5) && ($cert_j[0]['status'] != 9)) {
//                                echo '
//                                    <div style="float: right;">
//                                        <span style="font-size: 80%;">Изменить срок <i class="fa fa-calendar" aria-hidden="true"></i></span><br>
//                                        <input type="text" id="dataCertEnd" name="dataCertEnd" class="dateс" value="' . date('d.m.Y', strtotime($cert_j[0]['expires_time'])) . '" onfocus="this.select();_Calendar.lcs(this)"
//                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
//                                        <i class="fa fa-check" aria-hidden="true" style="color: green; cursor: pointer;" title="Применить" onclick="Ajax_change_expiresTime(\'cert\',' . $_GET['id'] . ');"></i>
//                                    </div>';
//                            }
//
//                            echo '
//                                    </div>
//                                </div>';
//                        }
//                    }
//
//					//Если не удалён
//                    if ($cert_j[0]['status'] != 9){
//                        //Если ещё не продан
//                        if (($cert_j[0]['status'] != 7) && ($cert_j[0]['cell_time'] == '0000-00-00 00:00:00')){
//                                echo '
//                                <a href="cert_cell.php?id=' . $cert_j[0]['id'] . '" class="b">Продать сертификат</a>';
//                        }else{
//                            //Если ничего не потрачено с него
//                            if ($cert_j[0]['debited'] != 0) {
//
//                            }
//                        }
//                    }
//
//                    //Список кому использовали
//                    $certPayList = array();
//
//                    $msql_cnnct = ConnectToDB ();
//
//                    $query = "SELECT * FROM `journal_payment` WHERE `cert_id`='{$cert_j[0]['id']}'";
//
//                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//                    $number = mysqli_num_rows($res);
//
//                    if ($number != 0){
//                        while ($arr = mysqli_fetch_assoc($res)){
//                            array_push($certPayList, $arr);
//                        }
//
//                        if (!empty($certPayList)){
//                            //var_dump($certPayList);
//
//                            echo '
//								<ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: inline-block; vertical-align: top; border: 1px outset #AAA;">
//									<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Проведённые оплаты</li>';
//
//                            foreach ($certPayList as $certPayListData) {
//                                echo '<li class="cellsBlock" style="width: auto;">';
//
//
//                                echo '
//											<a href="invoice.php?id=' . $certPayListData['invoice_id'] . '" class="cellName ahref" style="position: relative;">
//												<b>Наряд #' . $certPayListData['invoice_id'] . '</b><br>
//											</a>
//											<div class="cellName">
//												<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
//													Сумма:<br>
//													<span class="calculateInvoice" style="font-size: 13px">' . $certPayListData['summ'] . '</span> руб.
//												</div>
//											</div>
//											<div class="cellName">
//											    Пациент:<br>
//											    '.WriteSearchUser('spr_clients', $certPayListData['client_id'], 'user', true).'
//											</div>';
//
//                                echo '</li>';
//                            }
//
//                            echo '
//								</ul>';
//                        }
//
//                    }
//
//                    echo '
//                    <div id="doc_title">Сертификат #'.$cert_j[0]['id'].'</div>';
//
//					echo '
//							</div>';
//
//				}else{
//					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
//				}
//
//			}else{
//				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
//			}
//		}else{
//			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
//		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>