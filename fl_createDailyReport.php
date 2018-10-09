<?php

//fl_createDailyReport.php
//Добавить ежедневный отчёт администратор

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){

            include_once 'DBWork.php';
            include_once 'functions.php';

            $filials_j = getAllFilials(false, false);
            //var_dump($filials_j);

            echo '
                <div id="status">
                    <header>
                        <h2>Добавить ежедневный отчёт</h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';
            echo '
                    
                            <div class="cellsBlock2">
                                <div class="cellLeft">Дата отчёта</div>
                                <div class="cellRight">
                                    <input type="text" id="datastart" name="datastart" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
                                                onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Z-отчёт, руб.</div>
                                <div class="cellRight">
                                    <input type="text" name="zreport" id="zreport" value="">
                                </div>
                            </div>';

            echo '				
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    Филиал
                                </div>
                            <div class="cellRight">';

            if (($finances['see_all'] == 1) || $god_mode){

                echo '
                            <select id="filial" name="filial">';

                foreach ($filials_j as $filial_item){

                    $selected = '';

                    if (isset($_SESSION['filial'])) {
                        if ($_SESSION['filial'] == $filial_item['id']) {
                            $selected = 'selected';
                        }
                    }
                    echo '<option value="'.$filial_item['id'].'" '.$selected.'>'.$filial_item['name'].'</option>';
                }

                echo '
                            </select>';
            }else{
                if (isset($_SESSION['filial'])) {
                    echo $filials_j[$_SESSION['filial']]['name'].'<input type="hidden" id="filial" name="filial" value="'.$_SESSION['filial'].'">';
                }
            }

            echo '
										</div>
									</div>
								</div>';


            //Вкладки для отчёта
            echo '
						<div id="tabs_w" style="font-family: Arial, sans-serif; font-size: 100% !important;">
							<ul style="border-top-left-radius: 0; border-top-right-radius: 0; border-bottom-left-radius: 0; border-bottom-right-radius: 0; ">
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-1" style="padding: 3px 10px; font-size: 12px;">Приём</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-2" style="padding: 3px 10px; font-size: 12px;">Терапия - Кариесология</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-3" style="padding: 3px 10px; font-size: 12px;">Хирургия</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-4" style="padding: 3px 10px; font-size: 12px;">Имплантация</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-5" style="padding: 3px 10px; font-size: 12px;">Пародонтология</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-6" style="padding: 3px 10px; font-size: 12px;">Ортопедия</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-7" style="padding: 3px 10px; font-size: 12px;">Ортопедия на имплантах</a></li>
							    <li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-8" style="padding: 3px 10px; font-size: 12px;">Ортодонтия</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-9" style="padding: 3px 10px; font-size: 12px;">Гигиена</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-10" style="padding: 3px 10px; font-size: 12px;">Дополнительно</a></li>
								<li style="border-top-left-radius: 0; border-top-right-radius: 0;"><a href="#tabs-11" style="padding: 3px 10px; font-size: 12px;">Дети</a></li>
							</ul>';

            echo '
							<div id="tabs-1">';
            echo '
							</div>';
            echo '
							<div id="tabs-2">';
            echo '
							</div>';
            echo '
							<div id="tabs-3">';
            echo '
							</div>';
            echo '
							<div id="tabs-4">';
            echo '
							</div>';
            echo '
							<div id="tabs-5">';
            echo '
							</div>';
            echo '
							<div id="tabs-6">';
            echo '
							</div>';
            echo '
							<div id="tabs-7">';
            echo '
							</div>';
            echo '
							<div id="tabs-8">';
            echo '
							</div>';
            echo '
							<div id="tabs-9">';
            echo '
							</div>';
            echo '
							<div id="tabs-10">';
            echo '
							</div>';
            echo '
							<div id="tabs-11">';
            echo '
							</div>';


            echo '
							
				        </div>';

















            echo '
                            <input type="button" class="b" value="Добавить" onclick="">';

            echo '
                    </div>
                </div>';
        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>