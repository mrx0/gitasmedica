<?php

//removes_get_f.php
//Функция для выдачи направлений

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';

            //разбираемся с правами
            $god_mode = FALSE;

            require_once 'permissions.php';

            $removesMy = 0;
            $removesMe = 0;

            $rezult = '';

            if (!isset($_POST['worker_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {

                $msql_cnnct = ConnectToDB ();
                $removesMy = array();
                $removesMe = array();
                $number = 0;

                if (($_SESSION['id'] == $_POST['worker_id']) && !$god_mode && ($stom['see_all'] != 1)){
                    //Перенаправления мои
                    //$removesMy = SelDataFromDB ('removes', $_SESSION['id'], 'create_person');

                    $q = ' WHERE `'.$type.'` = '.$sw.' ORDER BY `create_time` DESC';


                    $query = 'SELECT * FROM `notes` WHERE `create_person` = '.$_SESSION['id'].' ORDER BY `create_time` DESC';

                    $query = "SELECT * FROM `notes` WHERE `create_person`='".$_SESSION['id']."' AND `closed` <> 1 ORDER BY `dead_line` ASC";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

                    //... Ко мне
                    //$removesMe = SelDataFromDB ('removes', $_SESSION['id'], 'whom');

                    $query = "SELECT * FROM `notes` WHERE `create_person`='".$_SESSION['id']."' AND `closed` <> 1 ORDER BY `dead_line` ASC";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

                }else{
                    if (($stom['see_all'] == 1) || $god_mode){
                        //Перенаправления мои
                        //$removesMy = SelDataFromDB ('removes',  $_POST['worker_id'], 'create_person');

                        $query = "SELECT * FROM `notes` WHERE `create_person`='".$_SESSION['id']."' AND `closed` <> 1 ORDER BY `dead_line` ASC";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        //... Ко мне
                        //$removesMe = SelDataFromDB ('removes',  $_POST['worker_id'], 'whom');

                        $query = "SELECT * FROM `notes` WHERE `create_person`='".$_SESSION['id']."' AND `closed` <> 1 ORDER BY `dead_line` ASC";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                    }
                }

                if (($removesMy != 0) || ($removesMe != 0)){

                    $rezult .= 'Направления';
                    $rezult .= '
							<ul class="live_filter" style="margin-left:6px;">
								<li class="cellsBlock" style="font-weight:bold;">	
									<div class="cellName" style="text-align: center">К кому</div>
									<div class="cellName" style="text-align: center">Пациент</div>
									<div class="cellName" style="text-align: center">Посещение</div>
									<div class="cellText" style="text-align: center">Описание</div>
									<div class="cellTime" style="text-align: center">Управление</div>
									<div class="cellTime" style="text-align: center">Создано</div>
									<div class="cellName" style="text-align: center">Автор</div>
									<div class="cellTime" style="text-align: center">Закрыто</div>
								</li>';

                    if($removesMy != 0){

                        $rezult .= '<b>Мои</b>';
                        for ($i = 0; $i < count($removesMy); $i++) {

                            if ($removesMy[$i]['closed'] == 0){
                                $ended = 'Нет';
                                $background_style = '';
                                $background_style2 = '
								background: rgba(231,55,71, 0.9);
								color:#fff;';

                                $background_style = '
								background: rgba(255,255,71, 0.5);
								background: -moz-linear-gradient(45deg, rgba(255,255,71, 1) 0%, rgba(255,255,157, 0.7) 33%, rgba(255,255,71, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
								background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(255,255,71, 0.4)), color-stop(33%,rgba(255,255,157, 0.7)), color-stop(71%,rgba(255,255,71, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
								background: -webkit-linear-gradient(45deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -o-linear-gradient(45deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -ms-linear-gradient(45deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: linear-gradient(-135deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);';
                                $showHiddenDivs = '';
                            }else{
                                $ended = 'Да';
                                $background_style = '
								background: rgba(144,247,95, 0.5);
								background: -moz-linear-gradient(45deg, rgba(144,247,95, 1) 0%, rgba(55,215,119, 0.7) 33%, rgba(144,247,95, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
								background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(144,247,95, 0.4)), color-stop(33%,rgba(55,215,119, 0.7)), color-stop(71%,rgba(144,247,95, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
								background: -webkit-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -o-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -ms-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: linear-gradient(-135deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);';
                                $background_style2 = 'background: rgba(144,247,95, 0.5);';
                                $showHiddenDivs = 'hiddenDivs';
                            }

                            $rezult .= '
							    <li class="cellsBlock cellsBlockHover '.$showHiddenDivs.'">
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removesMy[$i]['whom'], 'user', true).'</div>
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_clients',$removesMy[$i]['client'], 'user', true).'</div>
									<a href="task_stomat_inspection.php?id='.$removesMy[$i]['task'].'" class="ahref cellName" style="text-align: center">#'.$removesMy[$i]['task'].'</a>
									<div class="cellText" style="'.$background_style.'">'.$removesMy[$i]['description'].'</div>
									<div class="cellTime" style="text-align: center">';
                            if ($_SESSION['id'] == $removesMy[$i]['create_person']){
                                $rezult .= '
										<a href="#" id="Close_removes_stomat" onclick="Close_removes_stomat('.$removesMy[$i]['id'].', '.$_POST['worker_id'].')">закр.</a>';
                            }
                            $rezult .= '
									</div>
									<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $removesMy[$i]['create_time']).'</div>
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removesMy[$i]['create_person'], 'user', true).'</div>
									<div class="cellTime" style="text-align: center; '.$background_style2.'">'.$ended.'</div>
							</li>';
                        }
                    }

                    if($removesMe != 0){
                        $rezult .= '<b>Ко мне</b>';
                        for ($i = 0; $i < count($removesMe); $i++) {

                            if ($removesMe[$i]['closed'] == 0){
                                $ended = 'Нет';
                                $background_style = '';
                                $background_style2 = '
								background: rgba(231,55,71, 0.9);
								color:#fff;';

                                $background_style = '
								background: rgba(55,127,223, 0.5);
								background: -moz-linear-gradient(45deg, rgba(55,127,223, 1) 0%, rgba(151,223,255, 0.7) 33%, rgba(55,127,223, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
								background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(55,127,223, 0.4)), color-stop(33%,rgba(151,223,255, 0.7)), color-stop(71%,rgba(55,127,223, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
								background: -webkit-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -o-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -ms-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: linear-gradient(-135deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);';
                                $showHiddenDivs = '';
                            }else{
                                $ended = 'Да';
                                $background_style = '
								background: rgba(144,247,95, 0.5);
								background: -moz-linear-gradient(45deg, rgba(144,247,95, 1) 0%, rgba(55,215,119, 0.7) 33%, rgba(144,247,95, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
								background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(144,247,95, 0.4)), color-stop(33%,rgba(55,215,119, 0.7)), color-stop(71%,rgba(144,247,95, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
								background: -webkit-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -o-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: -ms-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
								background: linear-gradient(-135deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);';
                                $background_style2 = 'background: rgba(144,247,95, 0.5);';
                                $showHiddenDivs = 'hiddenDivs';
                            }

                            $rezult .= '
							    <li class="cellsBlock cellsBlockHover '.$showHiddenDivs.'">
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removesMe[$i]['whom'], 'user', true).'</div>
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_clients',$removesMe[$i]['client'], 'user', true).'</div>
									<a href="task_stomat_inspection.php?id='.$removesMe[$i]['task'].'" class="ahref cellName" style="text-align: center">#'.$removesMe[$i]['task'].'</a>
									<div class="cellText" style="'.$background_style.'">'.$removesMe[$i]['description'].'</div>
									<div class="cellTime" style="text-align: center">';
                            if ($_SESSION['id'] == $removesMe[$i]['whom']){
                                $rezult .= '
										<a href="#" id="Close_removes_stomat" onclick="Close_removes_stomat('.$removesMe[$i]['id'].', '.$_POST['worker_id'].')">закр.</a>';
                            }
                            $rezult .= '
									</div>
									<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $removesMe[$i]['create_time']).'</div>
									<div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removesMe[$i]['create_person'], 'user', true).'</div>
									<div class="cellTime" style="text-align: center; '.$background_style2.'">'.$ended.'</div>
							    </li>';
                        }
                    }else{
                        echo json_encode(array('result' => 'error', 'data' => 'Ошибка #12'));
                    }

                    $rezult .= '
					        </ul>';

                }else{
                    echo json_encode(array('result' => 'error', 'data' => 'Ошибка #11'));
                }

                echo json_encode(array('result' => 'success', 'data' => $rezult));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => 'Ошибка #13'));
        }
    }
?>