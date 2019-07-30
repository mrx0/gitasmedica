<?php
	
//test_change_filial_material_consumption.php
//!!!! 2019-07-30 не доделал, забил, так как это в общих расходах есть вроде

    require_once 'header.php';
	require_once 'header_tags.php';

	include_once 'DBWork.php';

    $result_j = array();

    $msql_cnnct = ConnectToDB ();

    $query = "SELECT `id`, `invoice_id` FROM `journal_inv_material_consumption`";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){
            array_push($result_j, $arr);
        }
    }
    //var_dump($result_j);

    foreach ($result_j as $data_item){
//        $query = "UPDATE `journal_payment` SET `filial_id`='{$data_item['ji_filial_id']}' WHERE `id`='{$data_item['jp_id']}'";
//
//        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//        var_dump($data_item['jp_id'].' -> '.$data_item['ji_filial_id'].' ['.$data_item['ji_invoice_id'].'] OK');
    }

	require_once 'footer.php';
	
?>