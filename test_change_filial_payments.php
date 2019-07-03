<?php
	
//test_change_filial_payments.php
//

    require_once 'header.php';
	require_once 'header_tags.php';

	include_once 'DBWork.php';

    $result_j = array();

    $msql_cnnct = ConnectToDB ();

    $query = "
        SELECT jp.id AS jp_id, jp.filial_id AS jp_filial_id, jp.invoice_id AS jp_invoice_id, ji.id AS ji_invoice_id, ji.office_id AS ji_filial_id FROM `journal_payment` jp
        LEFT JOIN `journal_invoice` ji ON ji.id = jp.invoice_id
        WHERE jp.filial_id ='0' AND jp.create_time > '2018-12-31'";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){
            array_push($result_j, $arr);
        }
    }
    //var_dump($result_j);

    foreach ($result_j as $data_item){
        $query = "UPDATE `journal_payment` SET `filial_id`='{$data_item['ji_filial_id']}' WHERE `id`='{$data_item['jp_id']}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        var_dump($data_item['jp_id'].' -> '.$data_item['ji_filial_id'].' ['.$data_item['ji_invoice_id'].'] OK');
    }

	require_once 'footer.php';
	
?>