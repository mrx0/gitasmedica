<?php
	
//test_change_or_add_summ_in_calcs.php
//добавим суммы в позиции РЛов, где они = 0

    require_once 'header.php';
	require_once 'header_tags.php';

	include_once 'DBWork.php';

    //!!! @@@
    include_once 'ffun.php';

    $result_j = array();

    $msql_cnnct = ConnectToDB ();

    $query = "SELECT * FROM `fl_journal_calculate_ex` WHERE `summ`='0' AND `guarantee`='0' ORDER BY `id` DESC LIMIT 10000";
    //$query = "SELECT * FROM `fl_journal_calculate_ex` WHERE `id`='167337'";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){
            array_push($result_j, $arr);
        }
        //var_dump($result_j);

        foreach ($result_j as $data_item){

            $pos_id = $data_item['id'];
            $price_id = $data_item['price_id'];
            $quantity = $data_item['quantity'];
            $insure = $data_item['insure'];
            $insure_approve = $data_item['insure_approve'];

            $itog_price = $price = $data_item['price'];

            $guarantee = $data_item['guarantee'];
            $spec_koeff = $data_item['spec_koeff'];
            $discount = $data_item['discount'];

            $percent_cats = $data_item['percent_cats'];
            $work_percent = $data_item['work_percent'];
            $material_percent = $data_item['material_percent'];
            $summ_special = $data_item['summ_special'];

            $itog_price_add = $itog_price;

            $pos_summ = 0;

            //Затраты на материалы
            $mat_cons_j_ex = array();

            $query = "SELECT * FROM `journal_inv_material_consumption_ex` WHERE `inv_pos_id`='{$pos_id}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                if ($number > 1){
                    var_dump('ПИЗДА!!!');
                }else{
                    while ($arr = mysqli_fetch_assoc($res)) {
                        if (!isset($mat_cons_j_ex['data'])){
                            $mat_cons_j_ex['data'] = array();
                        }

                        if (!isset($mat_cons_j_ex['data'][$arr['inv_pos_id']])){
                            $mat_cons_j_ex['data'][$arr['inv_pos_id']] = $arr['summ'];
                        }

//                        $mat_cons_j_ex['create_person'] = $arr['create_person'];
//                        $mat_cons_j_ex['create_time'] = $arr['create_time'];
//                        $mat_cons_j_ex['all_summ'] = $arr['all_summ'];
//                        $mat_cons_j_ex['descr'] = $arr['descr'];
//                        $mat_cons_j_ex['id'] = $arr['mc_id'];
                    }
                }
            }

            $price = $price * $quantity;

            $price = ($price - ($price * $discount / 100));

            if ($itog_price == 0) {
                $itog_price = $price;
            }

            if (!empty($mat_cons_j_ex['data'])) {
                if (isset($mat_cons_j_ex['data'][$pos_id])) {
                    $itog_price = $itog_price - $mat_cons_j_ex['data'][$pos_id];
                } else {
                }
            } else {
            }

            if ($itog_price < 0) $itog_price = 0;

            //Сумма одной позиции
            $pos_summ = calculateResult($itog_price, $work_percent, $material_percent, $summ_special);

            //Обновляем
            $query = "UPDATE `fl_journal_calculate_ex` SET `summ`='{$pos_summ}' WHERE `id`='{$data_item['id']}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            var_dump($data_item['calculate_id'].' -> '.$data_item['id'].' -> '.$pos_summ.' => OK');
        }
    }else{
        var_dump('Nothing to do');
    }
	require_once 'footer.php';
	
?>