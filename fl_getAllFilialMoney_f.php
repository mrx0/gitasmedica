<?php

//fl_getAllFilialMoney_f.php
//получаем выручку со всех филиалов (смотрим только закрытые работы)

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    //var_dump ($_POST);
    if ($_POST){

        include_once 'DBWork.php';
        include_once 'functions.php';

        if ($_POST['month'] < 10) {
            $month = '0'.$_POST['month'];
        }else{
            $month = $_POST['month'];
        }

        //$data_temp_arr = explode(".", $_POST['datastart']);
        $datastart = $_POST['year'].'-'.$month.'-01';

        //последний день мясяца (Дата)
        $d = new DateTime($datastart);
        //$day = $d->format('Y-m-t');

        //$data_temp_arr = explode(".", $_POST['dataend']);
        $dataend = $d->format('Y-m-t');

        //!!! Доделать тут, если несколько типов и т.д.
        //$query = "SELECT `summ`,`summins`, `office_id`, (SUM(`summ`)+ SUM(`summins`)) AS all_summ FROM `journal_invoice` WHERE `status`='5' AND `closed_time` BETWEEN '{$datastart}' AND '{$dataend}'";
        $query = "SELECT `summ`,`summins`, `office_id` FROM `journal_invoice` WHERE `status`='5' AND `closed_time` BETWEEN '{$datastart}' AND '{$dataend}'";
        //var_dump($query);

        $journal = array();

        $msql_cnnct = ConnectToDB();

        $arr = array();

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

        $number = mysqli_num_rows($res);

        if ($number != 0) {
            while ($arr = mysqli_fetch_assoc($res)) {

                if (!isset($journal[$arr['office_id']])){
                    $journal[$arr['office_id']] = array();
                }
                array_push($journal[$arr['office_id']], $arr);
            }
        }
        //var_dump($journal);

        //Делаем рассчеты
        //Выводим результат
        if (!empty($journal)) {

            $summ_arr = array();
            $all_summ = 0;

            foreach ($journal as $filial_id => $filial_journal){
                //var_dump($item);
                if (!isset($summ_arr[$filial_id])){
                    $summ_arr[$filial_id] = 0;
                }

                foreach ($filial_journal as $item){
                    $summ_arr[$filial_id] += $item['summ'] + $item['summins'];
                }
            }

            echo json_encode(array('result' => 'success', 'data' => $summ_arr, 'msg' => ''));

        } else {
            echo json_encode(array('result' => 'empty', 'data' => array(), 'msg' => '<div class="query_neok">Ничего не найдено</div>'));
        }
    }
}
?>