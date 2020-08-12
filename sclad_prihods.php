<?php

//sclad_prihods.php
//Приходные накладные

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

        if (($items['see_all'] == 1) || ($items['see_own'] == 1) || $god_mode){
			//include_once 'DBWork.php';

            /*!!!Тест PDO*/
            include_once('DBWorkPDO.php');

            include_once 'functions.php';
			include_once 'ffun.php';
			include_once 'widget_calendar.php';
            require 'variables.php';

            $have_target_filial = false;
            $href_str = '';

            $filials_j = getAllFilials(true, false, false);
            //var_dump($filials_j);

            //$msql_cnnct = ConnectToDB ();

            //Дата
            //операции со временем
            $day = date('d');
            $month = date('m');
            $year = date('Y');

            //Или если мы смотрим другой месяц
            if (isset($_GET['m']) && isset($_GET['y'])) {
                $month = $_GET['m'];
                $year = $_GET['y'];
            }

            //Филиал
//            if (isset($_SESSION['filial'])) {
//                $filial_id = $_SESSION['filial'];
//                $have_target_filial = true;
//            } else {
            $filial_id = 15;
//            }

            //if (($finances['see_all'] == 1) || $god_mode) {
                if (isset($_GET['filial_id'])) {
                    $filial_id = $_GET['filial_id'];
//                    $have_target_filial = true;
                }
            //}

            $status = 0;

            if (isset($_GET['status'])) {
                $status = $_GET['status'];
            }

            $dop = 'filial_id='.$filial_id.'&status='.$status;

            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                            <a href="sclad.php" class="b">Склад</a>
                        </div>
                        <h2 style="">Приходные накладные</h2>
                    </header>';

            echo '
                    <div id="data" style="margin-top: 5px;">';
            echo '				
                        <div id="errrror"></div>';


            echo '<div class="no_print">';
            echo widget_calendar ($month, $year, 'sclad_prihods.php', $dop);
            echo '</div>';

            //Выбор филиала
            echo '
                        <div style="font-size: 90%; margin: 10px 0; display: inline-block;">
                            Филиал:

                            <select name="SelectFilial" id="SelectFilial">
							    <option value="0" selected>Все</option>';
            if (!empty($filials_j)){
                foreach($filials_j as $f_id => $filial_item){
                    $selected = '';
                    if ($f_id == $filial_id){
                        $selected = 'selected';
                    }
                    echo "<option value='".$f_id."' $selected>".$filial_item['name']."</option>";
                }
            }
            echo '
                            </select>


                        </div>
                        <div style="font-size: 90%; display: inline-block; background: #f1f1f1; padding: 3px;">
                            Только не проведённые <input type="checkbox" id="prihod_status" value="1" ', $status == 1? 'checked' : '' ,'>
                        </div>
                        <div style="font-size: 90%; display: inline-block;">
                            <span id="acceptScladPrihodsSettings" class="button_tiny" style="font-size: 100%; cursor: pointer" onclick=""><i class="fa fa-check-square" style=" color: green;"></i> Применить</span>
                        </div>
                        ';

            $db = new DB();

            $args = [
                'month' => $month,
                'year' => $year
            ];

            $query_dop = '';

            if ($filial_id != 0) {
                $query_dop .= 'AND s_p.filial_id = :filial_id';

                $args['filial_id'] = $filial_id;
            }

            if ($status == 1) {
                $query_dop .= 'AND s_p.status = :status';

                $args['status'] = 0;
            }


            //Выбрать все категории prihod_status
            $query = "
            SELECT s_p.*
            FROM `sclad_prihod` s_p
            WHERE MONTH(s_p.prihod_time) = :month AND YEAR(s_p.prihod_time) = :year ".$query_dop."
            ORDER BY s_p.prihod_time DESC, s_p.create_time DESC";
            //var_dump($query);

            $prihods_j = $db::getRows($query, $args);
            //var_dump($prihods_j);

            if (!empty($prihods_j)){

                echo '
                <table style="border: 1px solid #BFBCB5; margin: 5px; font-size: 90%;">';

                echo '
                    <tr style="text-align: center;">
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>№</i></td>  
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>Дата</i></td>  
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>Филиал/Склад</i></td>
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>Поставщик</i></td>
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>Сумма</i></td>
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%"><i>Дата создания<br>Автор</i></td>
                        <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; font-size: 100%"><i>Статус</i></td>
                    </tr>';

                foreach ($prihods_j as $prihod_item){

                    echo '
                        <tr class="cellsBlockHover">    
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px;">
                                <a href="sclad_prihod.php?id='.$prihod_item['id'].'" class="ahref">#'.$prihod_item['id'].'</a>
                            </td>
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px;">'.date('d.m.Y', strtotime($prihod_item['prihod_time'])).'</td>
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px;">'.$filials_j[$prihod_item['filial_id']]['name2'].'</td>
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px;"><!--'.$prihod_item['id'].'-->'.$prihod_item['provider_name'].'</td>
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px;">'.number_format($prihod_item['summ']/100, 2, '.', '').' руб.</td>
                            <td style="border: 1px solid #BFBCB5; padding: 2px 5px; font-size: 80%; text-align: right">
                                '.date('d.m.Y', strtotime($prihod_item['create_time']."")).'<br>
                                '.WriteSearchUser('spr_workers', $prihod_item['create_person'], 'user', true).'
                            </td>
                            <td style="outline: 1px solid #BFBCB5; padding: 2px 5px; text-align: center; ">';

                    if ($prihod_item['status'] == 7){
                        //echo 'проведён';
                    }else{
                        echo '<span style="color: red;"><i>не проведён</i></span>';
                    }

                    echo '            
                            </td>
                        </tr>';
                }



                echo '</table>';
            }else{
                echo '<br><span style="color: red;">Ничего не найдено</span>';
            }

            echo '
                    </div>
                </div>
                <div id="doc_title">Приходные накладные - Асмедика</div>';

            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';


            echo '
					<script>
					
					    $(\'#acceptScladPrihodsSettings\').on(\'click\', function(data){
                            let prihod_status = 0;

                            if ($(\'#prihod_status\').prop("checked")){
                                prihod_status = 1;
                            }
                            //console.log(prihod_status);
                            
                            let filial_id = $(\'#SelectFilial\').val();
                            //console.log(filial_id);
                            
                            let get_data_str = "";

                            let params = window
                                .location
                                .search
                                .replace("?","")
                                .split("&")
                                .reduce(
                                    function(p,e){
                                        let a = e.split(\'=\');
                                        p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                                        return p;
                                    },
                                    {}
                                );
                            //console.log(params);
                                            
                            for (let key in params) {
//                                        console.log(key.indexOf("filial_id"));
                                if (key.length > 0){
                                    if ((key.indexOf("filial_id") == -1) &&((key.indexOf("status") == -1))){
                                        get_data_str = get_data_str + "&" + key + "=" + params[key];
                                    }
                                }
                            }
                            //console.log(get_data_str);
                                    
                            window.location.href = "sclad_prihods.php?filial_id=" + filial_id + "&status=" + prihod_status + get_data_str;
                            
                        })
					</script>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>