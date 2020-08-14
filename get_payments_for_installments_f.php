<?php

//get_payments_for_installments_f.php
//получаем месяцы с платежами, начиная с даты начала рассрочки

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){
            if (isset($_POST['client_id']) && isset($_POST['date_in'])){

                //include_once 'DBWork.php';
                /*!!!Тест PDO*/
                include_once('DBWorkPDO.php');

                include_once 'functions.php';

                require 'variables.php';

                $rezult = '';

                $db = new DB();

                $query = "SELECT j_o.*
                FROM `journal_order` j_o
                /*LEFT JOIN `sclad_availability` sav 
                ON sav.sclad_item_id = sit.id*/
                WHERE j_o.client_id = :client_id
                AND j_o.date_in BETWEEN :date_in AND :date_now
                ORDER BY j_o.date_in
                ";

                $args = [
                    'client_id' => $_POST['client_id'],
                    'date_in' => $_POST['date_in'].' 00:00:00',
                    'date_now' =>date('Y-m-d H:i:s', time())
                ];

                $data = $db::getRows($query, $args);
                //var_dump($data);

                if (!empty($data)){
                    $data_arr = array();

                    foreach ($data as $item) {
                        //Получим месяц из даты
                        $input = $item['date_in'];
                        $date = DateTime::createFromFormat('Y-m-d H:i:s', $input);
                        //var_dump($date->format('m'));


                        if (!isset($data_arr[$date->format('Y')])){
                            $data_arr[$date->format('Y')] = array();
                        }
                        if (!isset($data_arr[$date->format('Y')][$date->format('m')])){
                            $data_arr[$date->format('Y')][$date->format('m')] = 0;
                        }
                        $data_arr[$date->format('Y')][$date->format('m')] += $item['summ'];
                    }
                    //var_dump($data_arr);

                    //вернуть все даты между двумя датами в массиве
                    $period = new DatePeriod(
                        new DateTime($_POST['date_in']),
                        new DateInterval('P1M'),
                        new DateTime(date('Y-m-d', time()))
                    );

                    foreach ($period as $key => $value) {
                        //var_dump($value->format( "Y-m" ));

                        if (isset($data_arr[$value->format( "Y" )])){
                            if (isset($data_arr[$value->format( "Y" )][$value->format( "m" )])){

                                $rezult .= '<div style="display: table-cell; width: 83px; min-width: 83px; border: 1px solid #BFBCB5; background: lawngreen; padding: 10px;">';

                                $rezult .= '<div style="margin-bottom: 5px;">'.$monthsName[$value->format( "m" )].' ';
                                $rezult .= $value->format( "Y" );
                                $rezult .= '</div>';
                                $rezult .= '<div>';
                                $rezult .= '<span style="font-weight: bold">'.$data_arr[$value->format( "Y" )][$value->format( "m" )].'</span>';
                                $rezult .= '</div>';

                                $rezult .= '</div>';

                            }else{
                                $rezult .= '<div style="display: table-cell; width: 83px; min-width: 83px; border: 1px solid #BFBCB5; background: #ff7777; padding: 10px;">';

                                $rezult .= '<div style="margin-bottom: 5px;">'.$monthsName[$value->format( "m" )].' ';
                                $rezult .= $value->format( "Y" );
                                $rezult .= '</div>';
                                $rezult .= '<div>';
                                $rezult .= 0;
                                $rezult .= '</div>';

                                $rezult .= '</div>';
                            }
                        }else{
                            $rezult .= '<div style="display: table-cell; width: 83px; min-width: 83px; border: 1px solid #BFBCB5; background: #ff7777; padding: 10px;">';

                            $rezult .= '<div style="margin-bottom: 5px;">'.$monthsName[$value->format( "m" )].' ';
                            $rezult .= $value->format( "Y" );
                            $rezult .= '</div>';
                            $rezult .= '<div>';
                            $rezult .= 0;
                            $rezult .= '</div>';

                            $rezult .= '</div>';
                        }
                    }

                }
                //echo $rezult;



                echo json_encode(array('result' => 'success', 'data' => $rezult));

            }
        }
    }


?>
	