<?php

//dp_api_show_zapis_f.php
//Тест API DentalPro функция для вывода таблицы с посещениями на страницу

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            $rezult = '';
            $rezult_arr = array();

            require 'config_dentalpro_api.php';
            include_once('DBWorkPDO.php');

            if (!isset($_POST['zapis_data'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $i = 1;

                $rezult_arr = $_POST['zapis_data'];


                $rezult .= '
                <div style="margin: 2px 6px 3px;">
                    <span style="font-size: 80%; color: rgb(0, 172, 237);">Всего записей: <i>' . $rezult_arr['total'] . '</i></span><br>
                    <span style="font-size: 80%; color: rgb(0, 172, 237);">Будет загружено: <b>' . count($rezult_arr['data']) . '</b></span></a>
                    <div id="zapis_data_status">
                        <div  style="width: 350px; height: 32px; padding: 10px; text-align: left; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);">
                            <img src="img/wait.gif" style="float:left;">
                                <span style="float: right;  font-size: 80%;">
                                    <i>Ожидание загрузки данных</i><br>
                                    <b id="zapis_data_status_dop" style="font-size: 120%;"></b>
                                </span>
                        </div>
                    </div>
                </div>';

//                $rezult .= 'Всего записей: ' . $rezult_arr['total'] . '<br>';
//                $rezult .= 'Будет показано: ' . count($rezult_arr['data']) . '<br>';

                $rezult .= '
                    <table width="100%" style="border: 1px solid #BEBEBE; margin:5px;" id="zapis_table">';

                $rezult .= '
                        <tr class="cellsBlock cellsBlockHover" style="font-weight:bold;">';
                $rezult .= '
                            <td class="" style="width: 20px; border: 1px solid #BEBEBE; text-align: center;">-</td>';
                $rezult .= '
                            <td class="cellFullName" style="border: 1px solid #BEBEBE;">Пациент</td>';
                $rezult .= '
                            <td class="cellFullName" style="border: 1px solid #BEBEBE;">Врач</td>';
                $rezult .= '
                            <td class="cellFullName" style="border: 1px solid #BEBEBE;">Начало - Конец</td>';
                $rezult .= '
                            <td class="cellFullName" style="border: 1px solid #BEBEBE;">Статус посещения</td>';
                $rezult .= '
                        </tr>';

                foreach ($rezult_arr['data'] as $zapis_val) { !//!!![22-Mar-2023 13:48:44 UTC] PHP Warning:  Invalid argument supplied for foreach() in C:\wamp\www\dp_api_show_zapis_f.php on line 44
                    $rezult .= '
                        <tr class="cellsBlock cellsBlockHover zapis_id" zapis_id="'. $zapis_val['id'] .'" style="">';
                    $rezult .= '
                            <td class="" style="width: 20px; border: 1px solid #BEBEBE; text-align: center;"><a href="https://asstom.dental-pro.online/cbase/detail.html?id='.$zapis_val['client_id'].'" class="ahref" style="text-align: center; color: #2ebdbd;" target="_blank" rel="nofollow noopener">'.$i.'</a></td>';
                    $rezult .= '
                            <td class="cellFullName client_id" client_id="'. $zapis_val['client_id'] .'" client_data_'.$zapis_val['client_id'].'="" style="border: 1px solid #BEBEBE;"></td>';
                    $rezult .= '
                            <td class="cellFullName doctor_id" doctor_id="'. $zapis_val['doctor_id'] .'" style="border: 1px solid #BEBEBE;"></td>';
                    $rezult .= '
                            <td class="cellFullName">' . date('H:i', strtotime($zapis_val['start_time'])) . ' - ' . date('H:i', strtotime($zapis_val['finish_time'])) . '</td>';
                    //Статус посещения
                    $visited_status = '<span style="color: #DF314D">не посещено</span>';
                    if ($zapis_val['visited'] == 'true'){
                        $visited_status = '<span style="color: #2EB703">посещено</span>';
                    }
                    $rezult .= '
                            <td class="cellFullName" style="border: 1px solid #BEBEBE;">' . $visited_status . '</td>';
                    $rezult .= '
                        </td>';

                    $i++;
                }

                $rezult .= '
                    </table>';

                    //echo json_encode(array('result' => 'success', 'data' => $rezult));


                echo json_encode(array('result' => 'success', 'data' => $rezult));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>'));
        }
    }

?>
	