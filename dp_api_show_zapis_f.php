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

                $rezult_arr = $_POST['zapis_data'];

                $rezult .= 'Всего записей: ' . $rezult_arr['total'] . '<br>';
                $rezult .= 'Загружено: ' . count($rezult_arr['data']) . '<br>';

                $rezult .= '
                    <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';

                $rezult .= '
                        <li class="cellsBlock cellsBlockHover" style="font-weight:bold;">';
                $rezult .= '
                            <div class="cellFullName ahref">Пациент</div>';
                $rezult .= '
                            <div class="cellFullName ahref">Врач</div>';
                $rezult .= '
                            <div class="cellFullName ahref">Начало - Конец</div>';
                $rezult .= '
                            <div class="cellFullName ahref">Статус посещения</div>';
                $rezult .= '
                        </li>';

                foreach ($rezult_arr['data'] as $zapis_val) {
                    $rezult .= '
                        <li class="cellsBlock cellsBlockHover zapis_id" zapis_id="'. $zapis_val['id'] .'" style="">';
                    $rezult .= '
                            <div class="cellFullName ahref client_id" client_id="'. $zapis_val['client_id'] .'"></div>';
                    $rezult .= '
                            <div class="cellFullName ahref doctor_id" doctor_id="'. $zapis_val['doctor_id'] .'"></div>';
                    $rezult .= '
                            <div class="cellFullName ahref">' . date('H:i', strtotime($zapis_val['start_time'])) . ' - ' . date('H:i', strtotime($zapis_val['finish_time'])) . '</div>';
                    //Статус посещения
                    $visited_status = '<span style="color: #DF314D">не посещено</span>';
                    if ($zapis_val['visited'] == 'true'){
                        $visited_status = '<span style="color: #2EB703">посещено</span>';
                    }
                    $rezult .= '
                            <div class="cellFullName ahref">' . $visited_status . '</div>';
                    $rezult .= '
                        </li>';

                }

                $rezult .= '
                    </ul>';

                    //echo json_encode(array('result' => 'success', 'data' => $rezult));


                echo json_encode(array('result' => 'success', 'data' => $rezult));
            }
        }else{
            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Какая-то ошибка.</div>'));
        }
    }

?>
	