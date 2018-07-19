<?php

//fl_tabel_print_all.php
//Вывод табеля на печать

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else {
    //var_dump ($_POST);

    if ($_POST) {

        if (!isset($_POST['worker_id']) || !isset($_POST['month']) || !isset($_POST['year']) || !isset($_POST['office'])) {
            //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
        } else {

            include_once 'DBWork.php';
            include_once 'functions.php';
            include_once 'ffun.php';
            include_once 'filter.php';
            include_once 'filter_f.php';

            require 'variables.php';

            $tabelsIDarr = array();

            $msql_cnnct = ConnectToDB2 ();
            if ($_POST['office'] == 0) {
                $query_dop = '';
            }else{
                $query_dop = "AND `office_id`='".$_POST['office']."'";
            }

            $query = "SELECT `id` FROM `fl_journal_tabels` WHERE `worker_id` = '{$_POST['worker_id']}' AND `month` = '{$_POST['month']}' AND `year` = '{$_POST['year']}' AND `status` <> '9' ".$query_dop."";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив
                    array_push($tabelsIDarr, $arr['id']);
                }
            }

            if (!empty($tabelsIDarr)){
                //echo json_encode(array('result' => 'success', 'data' => $tabelsIDarr));

                $result_str = '';

                foreach ($tabelsIDarr as $tabel_id){

                    $tabel_j = SelDataFromDB('fl_journal_tabels', $tabel_id, 'id');
                    //var_dump($tabel_j[0]);

                    if ($tabel_j != 0){

                        //!!! вынести это за цикл
                        $filials_j = getAllFilials(false, true);

                        //Смена/график !!! переделать ! нужно только количество
                        $rezultShed = array();
                        $nightSmena = 0;

                        $tabel_deductions_j = array();
                        $tabel_surcharges_j = array();
                        $tabel_surcharges_j = array();
                        $tabel_paidouts_j = array();

                        $query = "SELECT `id`, `day`, `smena`, `kab`, `worker` FROM `scheduler` WHERE `worker` = '{$tabel_j[0]['worker_id']}' AND `month` = '".(int)$tabel_j[0]['month']."' AND `year` = '{$tabel_j[0]['year']}' AND `filial`='{$tabel_j[0]['office_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                //Раскидываем в массив
                                array_push($rezultShed, $arr);
                                //Если ночная смена
                                if ($arr['smena'] == 3){
                                    $nightSmena++;
                                }
                            }
                        }
                        /*var_dump($query);
                        var_dump(count($rezultShed));
                        var_dump($rezultShed);*/

                        //Ночные смены
                        $nightSmenaCount = 0;
                        $nightSmenaPrice = 0;
                        $nightSmenaSumm = 0;

                        $query = "SELECT `price`, `count`, `summ` FROM `fl_journal_tabel_nightsmens` WHERE `tabel_id` = '{$tabel_j[0]['id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){

                            while ($arr = mysqli_fetch_assoc($res)){
                                //Раскидываем в массив
                                //array_push($rezultNightSmena, $arr);
                                $nightSmenaCount = $arr['count'];
                                $nightSmenaPrice = $arr['price'];
                                $nightSmenaSumm = $arr['summ'];
                            }
                            //var_dump($rezultNightSmena);

                        }

                        //Пустые смены
                        $emptySmenaCount = 0;
                        $emptySmenaPrice = 0;
                        $emptySmenaSumm = 0;

                        $query = "SELECT `price`, `count`, `summ` FROM `fl_journal_tabel_emptysmens` WHERE `tabel_id` = '{$tabel_j[0]['id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){

                            while ($arr = mysqli_fetch_assoc($res)){
                                //Раскидываем в массив
                                //array_push($rezultNightSmena, $arr);
                                $emptySmenaCount = $arr['count'];
                                $emptySmenaPrice = $arr['price'];
                                $emptySmenaSumm = $arr['summ'];
                            }
                            //var_dump($rezultNightSmena);

                        }

                        //Надбавки
                        //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."'";
                        $query = "SELECT * FROM `fl_journal_surcharges` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                if (!isset($tabel_surcharges_j[$arr['type']])){
                                    $tabel_surcharges_j[$arr['type']] = array();
                                    $tabel_surcharges_j[$arr['type']] = (int)$arr['summ'];
                                }else{
                                    $tabel_surcharges_j[$arr['type']] = $tabel_surcharges_j[$arr['type']] + $arr['summ'];
                                }
                                //array_push($tabel_surcharges_j[$arr['type']], $arr);
                            }
                        }

                        //Вычеты
                        //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."'";
                        $query = "SELECT * FROM `fl_journal_deductions` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                if (!isset($tabel_deductions_j[$arr['type']])){
                                    $tabel_deductions_j[$arr['type']] = array();
                                    $tabel_deductions_j[$arr['type']] = (int)$arr['summ'];
                                }else{
                                    $tabel_deductions_j[$arr['type']] = $tabel_deductions_j[$arr['type']] + $arr['summ'];
                                }
                                //array_push($tabel_surcharges_j[$arr['type']], $arr);
                            }
                        }

                        //Выплаты
                        //$query = "SELECT * FROM `fl_journal_tabels_ex` WHERE `tabel_id`='".$tabel_j[0]['id']."'";
                        $query = "SELECT * FROM `fl_journal_paidouts` WHERE `tabel_id`='".$tabel_j[0]['id']."';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                if (!isset($tabel_paidouts_j[$arr['type']])){
                                    $tabel_paidouts_j[$arr['type']] = array();
                                    $tabel_paidouts_j[$arr['type']] = (int)$arr['summ'];
                                }else{
                                    $tabel_paidouts_j[$arr['type']] = $tabel_paidouts_j[$arr['type']] + $arr['summ'];
                                }
                                //array_push($tabel_surcharges_j[$arr['type']], $arr);
                            }
                        }

                        //var_dump($tabel_surcharges_j);
                        //var_dump($tabel_deductions_j);
                        //var_dump($tabel_paidouts_j);

                        /*!!! временно скрыл отсюда ибо тут это лишнее
                        echo '
                            <div class="no_print">
                                <header style="margin-bottom: 5px;">';

                        echo '
                                </header>
                            </div>';
                        */

                        $tabel_summ = intval($tabel_j[0]['summ']);
                        if (isset($tabel_deductions_j[1])){
                            $tabel_summ = intval($tabel_j[0]['summ'] - $tabel_deductions_j[1]);
                        }
                        $tabel_deductions_j2 = 0;
                        if (isset($tabel_deductions_j[2])){
                            $tabel_deductions_j2 = $tabel_deductions_j[2];
                        }
                        $tabel_surcharges_j2 = 0;
                        if (isset($tabel_surcharges_j[2])){
                            $tabel_surcharges_j2 = $tabel_surcharges_j[2];
                        }
                        $tabel_deductions_j3 = 0;
                        if (isset($tabel_deductions_j[3])){
                            $tabel_deductions_j3 = $tabel_deductions_j[3];
                        }
                        $tabel_surcharges_j3 = 0;
                        if (isset($tabel_surcharges_j[3])){
                            $tabel_surcharges_j3 = $tabel_surcharges_j[3];
                        }
                        $tabel_deductions_j4 = 0;
                        if (isset($tabel_deductions_j[4])){
                            $tabel_deductions_j4 = $tabel_deductions_j[4];
                        }
                        $tabel_surcharges_j1 = 0;
                        if (isset($tabel_surcharges_j[1])){
                            $tabel_surcharges_j1 = $tabel_surcharges_j[1];
                        }
                        $tabel_deductions_j5 = 0;
                        if (isset($tabel_deductions_j[5])){
                            $tabel_deductions_j5 = $tabel_deductions_j[5];
                        }
                        $tabel_paidouts_j1 = 0;
                        if (isset($tabel_paidouts_j[1])){
                            $tabel_paidouts_j1 = $tabel_paidouts_j[1];
                        }
                        $tabel_paidouts_j2 = 0;
                        if (isset($tabel_paidouts_j[2])){
                            $tabel_paidouts_j2 = $tabel_paidouts_j[2];
                        }
                        $tabel_paidouts_j3 = 0;
                        if (isset($tabel_paidouts_j[3])){
                            $tabel_paidouts_j3 = $tabel_paidouts_j[3];
                        }
                        $tabel_paidouts_j4 = 0;
                        if (isset($tabel_paidouts_j[4])){
                            $tabel_paidouts_j4 = $tabel_paidouts_j[4];
                        }

                        //Пробуем вывести расчетный лист по табелю для печати
                        $result_str .= tabelPrintTemplate ($tabel_id, $monthsName[$tabel_j[0]['month']], $tabel_j[0]['year'], WriteSearchUser('spr_workers', $tabel_j[0]['worker_id'], 'user', false), $filials_j[$tabel_j[0]['office_id']]['name2'], count($rezultShed),
                            $tabel_summ, $tabel_deductions_j2, $tabel_surcharges_j2, $tabel_deductions_j3,
                            $tabel_surcharges_j3, $tabel_deductions_j4, $tabel_surcharges_j1,
                            $tabel_deductions_j5, $emptySmenaCount, $emptySmenaPrice, $emptySmenaSumm,
                            $tabel_paidouts_j1, $tabel_paidouts_j4, $tabel_paidouts_j2, $nightSmenaCount,
                            $nightSmenaPrice, $nightSmenaSumm, $tabel_paidouts_j3);


                        /*echo "
                            <script>
                                $(document).ready(function() {
                                    //console.log();

                                    var pay_plus = 0;
                                    var pay_minus = 0;
                                    var pay_plus_part = 0;
                                    var pay_minus_part = 0;

                                    wait(function(runNext){

                                        setTimeout(function(){

                                            $('.pay_plus_part1').each(function() {
                                                pay_plus_part += Number($(this).html());
                                                //console.log(Number($(this).html()));
                                            });
                                            //console.log(pay_plus_part);

                                            runNext(pay_plus_part);

                                        }, 100);

                                    }).wait(function(runNext, pay_plus_part){
                                        //используем аргументы из предыдущего вызова

                                        $('.pay_plus1').html(pay_plus_part);
                                        pay_plus += pay_plus_part;
                                        pay_plus_part = 0;

                                        setTimeout(function(){

                                            $('.pay_minus_part1').each(function() {
                                                pay_minus_part += Number($(this).html());
                                                //console.log(Number($(this).html()));
                                            });
                                            //console.log(pay_minus_part);

                                            runNext(pay_plus, pay_plus_part, pay_minus_part);

                                        }, 100);

                                    }).wait(function(runNext, pay_plus, pay_plus_part, pay_minus_part){
                                        //используем аргументы из предыдущего вызова

                                        $('.pay_minus1').html(pay_minus_part);
                                        pay_minus += pay_minus_part;
                                        pay_minus_part = 0;

                                        setTimeout(function(){

                                            $('.pay_plus_part2').each(function() {
                                                pay_plus_part += Number($(this).html());
                                                //console.log(Number($(this).html()));
                                            });
                                            //console.log(pay_plus_part);

                                            runNext(pay_plus, pay_minus, pay_plus_part, pay_minus_part);

                                        }, 100);

                                    }).wait(function(runNext, pay_plus, pay_minus, pay_plus_part, pay_minus_part){
                                        //используем аргументы из предыдущего вызова

                                        $('.pay_plus2').html(pay_plus_part);
                                        pay_plus += pay_plus_part;

                                        setTimeout(function(){

                                            $('.pay_minus_part2').each(function() {
                                                pay_minus_part += Number($(this).html());
                                                //console.log(Number($(this).html()));
                                            });
                                            //console.log(pay_plus_part);

                                            runNext(pay_plus, pay_minus, pay_plus_part, pay_minus_part);

                                        }, 100);

                                    }).wait(function(runNext, pay_plus, pay_minus, pay_plus_part, pay_minus_part){

                                        $('.pay_minus2').html(pay_minus_part);
                                        pay_minus += pay_minus_part;

                                        $('.pay_must').html(pay_plus - pay_minus);

                                    });

                                });
                            </script>";*/





                    }

                }

                echo json_encode(array('result' => 'success', 'data' => $result_str, 'tabel_ids' => json_encode($tabelsIDarr)));

            }else{
                echo json_encode(array('result' => 'empty', 'data' => '<div class="query_neok">Ничего не найдено</div>'));
            }

            CloseDB ($msql_cnnct);
        }
    }
}

?>