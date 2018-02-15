<?php

//fl_tabel.php
//Табель

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){
	
			include_once 'DBWork.php';
			include_once 'functions.php';

			include_once 'ffun.php';

            require 'variables.php';
			
			require 'config.php';

            $edit_options = false;
            $upr_edit = false;
            $admin_edit = false;
            $stom_edit = false;
            $cosm_edit = false;
            $finance_edit = false;

			//var_dump($_SESSION);
			//unset($_SESSION['invoice_data']);
			
			if ($_GET){
				if (isset($_GET['id'])){
					
					$tabel_j = SelDataFromDB('fl_journal_tabels', $_GET['id'], 'id');
					
					if ($tabel_j != 0){
						//var_dump($tabel_j);
						//array_push($_SESSION['invoice_data'], $_GET['client']);
						//$_SESSION['invoice_data'] = $_GET['client'];
                        //var_dump($calculate_j[0]['closed_time'] == 0);

                        $filials_j = getAllFilials(false, false);

						//$sheduler_zapis = array();
                        $tabel_ex_calculates_j = array();
						//$invoice_ex_j_mkb = array();

                        //$invoice_j = array();

						//$client_j = SelDataFromDB('spr_clients', $calculate_j[0]['client_id'], 'user');
						//var_dump($client_j);


                        echo '
                                <div id="status">
                                    <header>
                                        <div class="nav">
                                            <a href="fl_tabels.php" class="b">Важный отчёт</a>
                                        </div>
    
                                        <h2>Табель #'.$_GET['id'].'';

                        if (($finances['edit'] == 1) || $god_mode){
                            /*if ($calculate_j[0]['status'] != 9){
                                echo '
                                            <a href="invoice_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 100%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                            }*/
                            /*if (($calculate_j[0]['status'] == 9) && (($finances['close'] == 1) || $god_mode)){
                                echo '
                                    <a href="#" onclick="Ajax_reopen_tabel('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 100%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
                            }*/
                        }

                        /*if (($finances['close'] == 1) || $god_mode){
                            if ($tabel_j[0]['status'] != 9){
                                echo '
                                                    <span class="info" style="font-size: 100%; cursor: pointer;" title="Удалить" onclick="fl_deleteTabelItem('.$_GET['id'].');" ><i class="fa fa-trash-o" aria-hidden="true"></i></span>';
                            }
                        }*/

                        echo '			
                                        </h2>
                                        <div style="font-size: 90%;">
                                            <div style="color: #252525; font-weight: bold;">'.$monthsName[$tabel_j[0]['month']].' '.$tabel_j[0]['year'].'</div>
                                            <div>Сотрудник <b>'.WriteSearchUser('spr_workers', $tabel_j[0]['worker_id'], 'user_full', true).'</b></div>
                                            <div>Филиал <b>'.$filials_j[$tabel_j[0]['office_id']]['name'].'</b></div>
		        						</div>
                                        <div style="background-color: rgba(72, 218, 230, 0.16); border: 1px dotted #AAA; margin: 5px 0; padding: 1px 3px; ">
                                            Сумма: <span class="calculateOrder" style="font-size: 13px">' . $tabel_j[0]['summ'] . '</span> руб.
                                        </div>
		        					</header>';

                        echo '
                                    <div id="data">';
                        $summCalc = 0;

                        $msql_cnnct = ConnectToDB ();
						
						//$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."'";
                        $query = "SELECT jcalc.* FROM `fl_journal_calculate` jcalc WHERE jcalc.id IN (SELECT `calculate_id` FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."');";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

						$number = mysqli_num_rows($res);
						if ($number != 0){
							while ($arr = mysqli_fetch_assoc($res)){
								array_push($tabel_ex_calculates_j, $arr);
							}
						}else{
                            //$sheduler_zapis = 0;
                            //var_dump ($sheduler_zapis);
                        }

                        //var_dump($query);
                        //var_dump($tabel_ex_calculates_j);


                        $rezult = '';

                        foreach ($tabel_ex_calculates_j as $rezData){

                            //Наряды
                            $query = "SELECT `summ`, `summins` FROM `journal_invoice` WHERE `id`='{$rezData['invoice_id']}' LIMIT 1";

                            /*$query2 = "SELECT `summ` AS `summ`, `summins` AS `summins` FROM `journal_invoice` WHERE `id`='{$rezData['invoice_id']}'
                            UNION ALL (
                              SELECT `name` AS `name`, `full_name` AS `full_name` FROM `spr_clients` WHERE `id`='{$rezData['client_id']}'
                            )";*/


                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {
                                /*while ($arr = mysqli_fetch_assoc($res)) {
                                    array_push($rez, $arr);
                                }*/

                                $arr = mysqli_fetch_assoc($res);
                                $summ = $arr['summ'];
                                $summins = $arr['summins'];
                            }

                            $query = "SELECT `name`, `full_name` FROM `spr_clients` WHERE `id`='{$rezData['client_id']}' LIMIT 1";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {
                                /*while ($arr = mysqli_fetch_assoc($res)) {
                                    array_push($rez, $arr);
                                }*/

                                $arr = mysqli_fetch_assoc($res);
                                $name = $arr['name'];
                                $full_name = $arr['full_name'];
                            }


                            $rezult .=
                                '
                                <div class="cellsBlockHover" style=" border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block;">
                                    <div style="display: inline-block; width: 200px;">
                                        <div>
                                        <a href="fl_calculate.php?id='.$rezData['id'].'" class="ahref">
                                            <div>
                                                <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                    <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                </div>
                                                <div style="display: inline-block; vertical-align: middle; font-size: 90%;">
                                                    <b>#'.$rezData['id'].'</b> <span style="    color: rgb(115, 112, 112);">'.date('d.m.y H:i', strtotime($rezData['create_time'])).'</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                    Сумма расчёта: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">'.$rezData['summ'].'</span> руб.
                                                </div>
                                            </div>
                                            
                                        </a>
                                        </div>
                                        <div style="margin: 5px 0 0 3px; font-size: 80%;">
                                            <b>Наряд: <a href="invoice.php?id='.$rezData['invoice_id'].'" class="ahref">#'.$rezData['invoice_id'].'</a> - <a href="client.php?id='.$rezData['client_id'].'" class="ahref">'.$name.'</a><br>
                                            Сумма: '.$summ.' р. Страх.: '.$summins.' р.</b> <br>
                                            
                                        </div>
                                    </div>
                                    <div style="display: inline-block; vertical-align: top;">
                                        <div class="settings_text" style="border: 1px solid #CCC; padding: 3px; margin: 1px; width: 12px; text-align: center;"  onclick="contextMenuShow('.$tabel_j[0]['id'].', '.$rezData['id'].', event, \'tabel_calc_options\');">
                                            <i class="fa fa-caret-down"></i>
                                        </div>
                                    </div>
                                    <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                </div>';

                            $summCalc += $rezData['summ'];

                        }

                        echo '
                                <div style="border: 1px dotted #b3c0c8; display: inline-block; font-size: 12px; padding: 2px; margin-right: 10px; vertical-align: top;">
                                    <div style="font-size: 90%;  color: #555; margin-bottom: 10px; margin-left: 2px;">Расчётные листы</div>
                                    '.$rezult.'
                                </div>';

                        echo '	
						
					        </div>
					        
					        <div id="doc_title">Табель #'.$_GET['id'].' - Асмедика</div>
					        </div>';

					}else{
						echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
					}
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>