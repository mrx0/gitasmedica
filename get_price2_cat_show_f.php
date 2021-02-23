<?php

//get_sclad_cat_show_f.php
//получаем категории склада, чтобы их отобразить

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){
            if (isset($_POST['targetId'])){

                $rezult = '';
                $selected = '';
                $rezult_arr = array();

//                include_once 'DBWork.php';
                include_once('DBWorkPDO.php');
                include_once 'functions.php';

                //$msql_cnnct = ConnectToDB();
                $db = new DB();

                if ($_POST['targetId'] == 0){
                    $selected = 'selected';
                }

                $rezult .= '
                    <div style="margin-top: 20px;">
                        <span style="font-size:90%; color: #333; ">Категория, куда добавим</span><br>
						<select name="scladCategory" id="scladCategory" size="6" style="width: 250px;">
							<option value="0" '.$selected.'>Вне категории</option>';

                $rezult .= showTreePrice2(NUll, '', 'select', $_POST['targetId'], TRUE, 0, FALSE, 'spr_price2_category', 0, 0, $db);

                $rezult .= '	
                        </select>
                    </div>';

                echo json_encode(array('result' => 'success', 'data' => $rezult));

            }
        }
    }


?>
	