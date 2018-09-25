<?php

//permissions.php
//Для require

    if (isset($_SESSION['permissions'])) {
        if ($_SESSION['permissions'] == '777') {
            $god_mode = TRUE;
        } else {
            //Получили список прав
            //$permissions_j = SelDataFromDB('spr_permissions', $_SESSION['permissions'], 'id');
            //var_dump($permissions_j);

            $permissions_j = array();

            $msql_cnnct = ConnectToDB();

            $query = "SELECT * FROM `spr_permissions` WHERE `id`='{$_SESSION['permissions']}';";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

            $number = mysqli_num_rows($res);

            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {
                    array_push($permissions_j, $arr);
                }
            }
        }

        if (!$god_mode) {
            if (!empty($permissions_j)) {
                /*foreach ($permissions_j[0] as $name => $data){
                    var_dump($name);
                }*/

                $it = json_decode($permissions_j[0]['it'], true);
                //var_dump($it);
                $cosm = json_decode($permissions_j[0]['cosm'], true);
                $stom = json_decode($permissions_j[0]['stom'], true);
                $clients = json_decode($permissions_j[0]['clients'], true);
                $workers = json_decode($permissions_j[0]['workers'], true);
                $offices = json_decode($permissions_j[0]['offices'], true);
                $soft = json_decode($permissions_j[0]['soft'], true);
                $scheduler = json_decode($permissions_j[0]['scheduler'], true);
                $zapis = json_decode($permissions_j[0]['zapis'], true);
                $report = json_decode($permissions_j[0]['report'], true);
                $spravka = json_decode($permissions_j[0]['spravka'], true);
                $finances = json_decode($permissions_j[0]['finances'], true);
                $items = json_decode($permissions_j[0]['items'], true);
                $invoice = json_decode($permissions_j[0]['invoice'], true);
                $ticket = json_decode($permissions_j[0]['ticket'], true);
                //var_dump($spravka);
            }
        } else {
            //Видимость
            $it['see_all'] = 0;
            $it['see_own'] = 0;
            $cosm['see_all'] = 0;
            $cosm['see_own'] = 0;
            $stom['see_all'] = 0;
            $stom['see_own'] = 0;
            $workers['see_all'] = 0;
            $workers['see_own'] = 0;
            $clients['see_all'] = 0;
            $clients['see_own'] = 0;
            $offices['see_all'] = 0;
            $offices['see_own'] = 0;
            $soft['see_all'] = 0;
            $soft['see_own'] = 0;
            $scheduler['see_all'] = 0;
            $scheduler['see_own'] = 0;
            $zapis['see_all'] = 0;
            $zapis['see_own'] = 0;
            $report['see_all'] = 0;
            $report['see_own'] = 0;
            $spravka['see_all'] = 0;
            $spravka['see_own'] = 0;
            $finances['see_all'] = 0;
            $finances['see_own'] = 0;
            $items['see_all'] = 0;
            $items['see_own'] = 0;
            $invoice['see_all'] = 0;
            $invoice['see_own'] = 0;
            $ticket['see_all'] = 0;
            $ticket['see_own'] = 0;
            //
            $it['add_new'] = 0;
            $it['add_own'] = 0;
            $cosm['add_new'] = 0;
            $cosm['add_own'] = 0;
            $stom['add_new'] = 0;
            $stom['add_own'] = 0;
            $workers['add_new'] = 0;
            $workers['add_own'] = 0;
            $clients['add_new'] = 0;
            $clients['add_own'] = 0;
            $offices['add_new'] = 0;
            $offices['add_own'] = 0;
            $soft['add_new'] = 0;
            $soft['add_own'] = 0;
            $scheduler['add_new'] = 0;
            $scheduler['add_own'] = 0;
            $zapis['add_new'] = 0;
            $zapis['add_own'] = 0;
            $report['add_new'] = 0;
            $report['add_own'] = 0;
            $spravka['add_new'] = 0;
            $spravka['add_own'] = 0;
            $finances['add_new'] = 0;
            $finances['add_own'] = 0;
            $items['add_new'] = 0;
            $items['add_own'] = 0;
            $invoice['add_new'] = 0;
            $invoice['add_own'] = 0;
            $ticket['add_new'] = 0;
            $ticket['add_own'] = 0;
            //
            $it['edit'] = 0;
            $cosm['edit'] = 0;
            $stom['edit'] = 0;
            $workers['edit'] = 0;
            $clients['edit'] = 0;
            $offices['edit'] = 0;
            $soft['edit'] = 0;
            $scheduler['edit'] = 0;
            $zapis['edit'] = 0;
            $report['edit'] = 0;
            $spravka['edit'] = 0;
            $finances['edit'] = 0;
            $items['edit'] = 0;
            $invoice['edit'] = 0;
            $ticket['edit'] = 0;
            //
            $it['close'] = 0;
            $cosm['close'] = 0;
            $stom['close'] = 0;
            $workers['close'] = 0;
            $clients['close'] = 0;
            $offices['close'] = 0;
            $soft['close'] = 0;
            $scheduler['close'] = 0;
            $zapis['close'] = 0;
            $report['close'] = 0;
            $spravka['close'] = 0;
            $finances['close'] = 0;
            $items['close'] = 0;
            $invoice['close'] = 0;
            $ticket['close'] = 0;
            //
            $it['reopen'] = 0;
            $cosm['reopen'] = 0;
            $stom['reopen'] = 0;
            $workers['reopen'] = 0;
            $clients['reopen'] = 0;
            $offices['reopen'] = 0;
            $soft['reopen'] = 0;
            $scheduler['reopen'] = 0;
            $zapis['reopen'] = 0;
            $report['reopen'] = 0;
            $spravka['reopen'] = 0;
            $finances['reopen'] = 0;
            $items['reopen'] = 0;
            $invoice['reopen'] = 0;
            $ticket['reopen'] = 0;
            //
            $it['add_worker'] = 0;
            $cosm['add_worker'] = 0;
            $stom['add_worker'] = 0;
            $workers['add_worker'] = 0;
            $clients['add_worker'] = 0;
            $offices['add_worker'] = 0;
            $soft['add_worker'] = 0;
            $scheduler['add_worker'] = 0;
            $zapis['add_worker'] = 0;
            $report['add_worker'] = 0;
            $spravka['add_worker'] = 0;
            $finances['add_worker'] = 0;
            $items['add_worker'] = 0;
            $invoice['add_worker'] = 0;
            $ticket['add_worker'] = 0;
            //

        }
    }

?>