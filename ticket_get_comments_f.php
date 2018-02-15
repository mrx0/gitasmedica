<?php

//ticket_get_comments_f.php
//Получаем комменты для тикета

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['ticket_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{
                include_once 'DBWork.php';
                include_once 'functions.php';

                $time = date('Y-m-d H:i:s', time());

                $comments = array();

                $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

                $query = "SELECT * FROM `journal_tickets_comments` WHERE `ticket_id` = '{$_POST['ticket_id']}' ORDER BY `create_time` ASC";

                $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($comments, $arr);
                    }
                }

                CloseDB ($msql_cnnct2);

                $data = '';
                $data_wrkrs_temp = array();
                $data_wrkrs_temp_query = array();
                $data_wrkrs = array();

                if (!empty($comments)){
                    foreach ($comments as $data_for_wrkrs){
                        if (!in_array(' `id`='.$data_for_wrkrs['create_person'].' ', $data_wrkrs_temp)){
                            array_push($data_wrkrs_temp, ' `id`='.$data_for_wrkrs['create_person'].' ');
                        }
                    }
                    if (!empty($data_wrkrs_temp)){
                        $msql_cnnct = ConnectToDB ('config_ticket');

                        $query = "SELECT `id`, `name` FROM `spr_workers` WHERE ";

                        $data_wrkrs_temp_query = implode(' OR ', $data_wrkrs_temp);

                        /*foreach ($data_wrkrs_temp as $data_wrkrs_temp_ids) {
                            $query .= "OR `id`='{$data_wrkrs_temp_ids}' ";
                        }*/

                        $query .= $data_wrkrs_temp_query;

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0) {
                            while ($arr = mysqli_fetch_assoc($res)) {
                                $data_wrkrs[$arr['id']] = $arr['name'];
                            }
                        }

                    }

                    foreach ($comments as $comment_data){

                        if ($comment_data['create_person'] != $_SESSION['id']){
                            $fl_l = "float: left;";
                            $fl_r = "";
                            $bckg_col = "";
                            $sender_show = $data_wrkrs[$comment_data['create_person']];
                        }else{
                            $fl_l = "";
                            $fl_r = "float: right;";
                            $bckg_col = "background: #DAE3ED;";
                            $sender_show = '';
                        }

                        //$data .= '<li>['.date('d.m.y H:i', strtotime($comment_data['create_time'])).'] <b>'.$data_wrkrs[$comment_data['create_person']].'</b> '.$comment_data['descr'].'</li>';
                        $data .= "
                        <div>
                            <div style='clear:both; position:relative;'>
                                <div style='".$fl_l." ".$fl_r."'>
                                    <div class='msg_container' style='".$bckg_col."".$fl_l." ".$fl_r."'>
                                        <div class='msg_container_sender' style='".$fl_l." ".$fl_r."'>".$sender_show."</div>
                                        <div class='msg_container_text' style='clear: both;".$fl_l." ".$fl_r."'>".htmlspecialchars_decode($comment_data['descr'])."</div>
                                        <div style='clear:both;'>
                                            <div class='msg_container_time'>".date('d.m.y H:i', strtotime($comment_data['create_time']))."</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }
                }

                echo json_encode(array('result' => 'success', 'data' => $data));
            }
        }
    }

?>