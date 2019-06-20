<?php
	
//test_tabel_paidout_percent.php
//

    require_once 'header.php';
	require_once 'header_tags.php';

	include_once 'DBWork.php';

    $result_j = array();

    $msql_cnnct = ConnectToDB ();

    $query = "
            SELECT jp.filial_id, jp.summ, jcalc.invoice_id/*,
            GROUP_CONCAT(DISTINCT jcalcex.percent_cats ORDER BY jcalcex.percent_cats ASC SEPARATOR ',') AS percent_cats*/
            FROM `fl_journal_calculate` jcalc
            /*LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalc.id = jcalcex.calculate_id*/
            LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '988'
            LEFT JOIN `journal_payment` jp ON jp.invoice_id = jcalc.invoice_id
            WHERE jtabex.calculate_id = jcalc.id
            GROUP BY jcalc.invoice_id";

//    $query = "
//    SELECT jp.*
//    FROM `journal_payment` jp
//    INNER JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
//    INNER JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
//    INNER JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
//    WHERE jp.invoice_id = ji.id";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0){
        while ($arr = mysqli_fetch_assoc($res)){
            array_push($result_j, $arr);
        }
    }
    var_dump($result_j);



	require_once 'footer.php';
	
?>