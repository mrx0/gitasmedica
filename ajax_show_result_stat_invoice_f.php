<?php

//ajax_show_result_stat_invoice_f.php
//

session_start();

if (empty($_SESSION['login']) || empty($_SESSION['id'])){
    header("location: enter.php");
}else{
    var_dump ($_POST);
    if ($_POST){
        $creatorExist = false;
        $workerExist = false;
        $clientExist = false;
        $queryDopExist = false;
        $queryDopExExist = false;
        $queryDopEx2Exist = false;
        $queryDopClientExist = false;
        $query = '';
        $queryDop = '';
        $queryDopEx = '';
        $queryDopEx2 = '';
        $queryDopClient = '';

        $dop = array();

        $edit_options = false;
        $upr_edit = false;
        $admin_edit = false;
        $stom_edit = false;
        $cosm_edit = false;
        $finance_edit = false;

        include_once 'DBWork.php';
        include_once 'functions.php';

        //разбираемся с правами
        $god_mode = FALSE;

        require_once 'permissions.php';

        //Дополнительные настройки, чтобы передать их дальше
        /*$dop['fullAll'] = $_POST['fullAll'];
        $dop['fullWOInvoice'] = $_POST['fullWOInvoice'];
        $dop['fullWOTask'] = $_POST['fullWOTask'];
        $dop['fullOk'] = $_POST['fullOk'];*/


        //Кто создал запись
        if ($_POST['creator'] != ''){
            include_once 'DBWork.php';
            $creatorSearch = SelDataFromDB ('spr_workers', $_POST['creator'], 'worker_full_name');

            if ($creatorSearch == 0){
                $creatorExist = false;
            }else{
                $creatorExist = true;
                $creator = $creatorSearch[0]['id'];
            }
        }else{
            $creatorExist = true;
            $creator = 0;
        }

        //К кому запись
        /*if ($_POST['worker'] != ''){
            include_once 'DBWork.php';
            $workerSearch = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');

            if ($workerSearch == 0){
                $workerExist = false;
            }else{
                $workerExist = true;
                $worker = $workerSearch[0]['id'];
            }
        }else{
            $workerExist = true;
            $worker = 0;
        }*/

        //Клиент
        if ($_POST['client'] != ''){
            include_once 'DBWork.php';
            $clientSearch = SelDataFromDB ('spr_clients', $_POST['client'], 'client_full_name');

            if ($clientSearch == 0){
                $clientExist = false;
            }else{
                $clientExist = true;
                $client = $clientSearch[0]['id'];
            }
        }else{
            $clientExist = true;
            $client = 0;
        }

        //if ($creatorExist && $workerExist) {
        if ($creatorExist) {
            if ($clientExist) {
                $query .= "SELECT * FROM `zapis`";

                /*require 'config.php';
                mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
                mysql_select_db($dbName) or die(mysql_error());
                mysql_query("SET NAMES 'utf8'");*/
                //$time = time();

                //Дата/время
                if ($_POST['all_time'] != 1) {
                    $queryDop .= "`create_time` BETWEEN '" . strtotime($_POST['datastart']) . "' AND '" . strtotime($_POST['dataend'] . " 23:59:59") . "'";
                    $queryDopExist = true;
                }

                //Кто создал запись
                if ($creator != 0) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= "`create_person` = '" . $creator . "'";
                    $queryDopExist = true;
                }

                //К кому запись
                /*if ($worker != 0) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= "`worker` = '" . $worker . "'";
                    $queryDopExist = true;
                }*/

                //Клиент
                if ($client != 0) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= "`patient` = '" . $client . "'";
                    $queryDopExist = true;
                }

                //Филиал
                if ($_POST['filial'] != 99) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= "`office` = '" . $_POST['filial'] . "'";
                    $queryDopExist = true;
                }

                //Все записи
                /*if ($_POST['zapisAll'] != 0) {
                    //ничего
                } else {
                    //Пришёл
                    if ($_POST['zapisArrive'] != 0) {
                        if ($queryDopExExist) {
                            $queryDopEx .= ' OR';
                        }
                        if ($_POST['zapisArrive'] == 1) {
                            $queryDopEx .= "`enter` = '1'";
                            $queryDopExExist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Не пришёл
                    if ($_POST['zapisNotArrive'] != 0) {
                        if ($queryDopExExist) {
                            $queryDopEx .= ' OR';
                        }
                        if ($_POST['zapisNotArrive'] == 1) {
                            $queryDopEx .= "`enter` = '9'";
                            $queryDopExExist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Не отмеченные
                    if ($_POST['zapisNull'] != 0) {
                        if ($queryDopExExist) {
                            $queryDopEx .= ' OR';
                        }
                        if ($_POST['zapisNull'] == 1) {
                            $queryDopEx .= "`enter` = '0'";
                            $queryDopExExist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Ошибочные
                    if ($_POST['zapisError'] != 0) {
                        if ($queryDopExExist) {
                            $queryDopEx .= ' OR';
                        }
                        if ($_POST['zapisError'] == 1) {
                            $queryDopEx .= "`enter` = '8'";
                            $queryDopExExist = true;
                        }
                        //$queryDopExExist = true;
                    }
                }*/

                //Тип
                /*if ($_POST['typeW'] != 0) {
                    if ($queryDopExist) {
                        $queryDop .= ' AND';
                    }
                    $queryDop .= "`type` = '" . $_POST['typeW'] . "'";
                    $queryDopExist = true;
                }*/


                //Первичный ночной страховой
                /*if ($_POST['statusAll'] != 0) {
                    //ничего
                } else {
                    //Первичные
                    if ($_POST['statusPervich'] != 0) {
                        if ($queryDopEx2Exist) {
                            $queryDopEx2 .= ' OR';
                        }
                        if ($_POST['statusPervich'] == 1) {
                            $queryDopEx2 .= "`pervich` = '1'";
                            $queryDopEx2Exist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Страховые
                    if ($_POST['statusInsure'] != 0) {
                        if ($queryDopEx2Exist) {
                            $queryDopEx2 .= ' OR';
                        }
                        if ($_POST['statusInsure'] == 1) {
                            $queryDopEx2 .= "`insured` = '1'";
                            $queryDopEx2Exist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Ночные
                    if ($_POST['statusNight'] != 0) {
                        if ($queryDopEx2Exist) {
                            $queryDopEx2 .= ' OR';
                        }
                        if ($_POST['statusNight'] == 1) {
                            $queryDopEx2 .= "`noch` = '1'";
                            $queryDopEx2Exist = true;
                        }
                        //$queryDopExExist = true;
                    }

                    //Все остальные
                    if ($_POST['statusAnother'] != 0) {
                        if ($queryDopEx2Exist) {
                            $queryDopEx2 .= ' OR';
                        }
                        if ($_POST['statusAnother'] == 1) {
                            $queryDopEx2 .= "`pervich` = '0' AND `insured` = '0' AND `noch` = '0'";
                            $queryDopEx2Exist = true;
                        }
                        //$queryDopExExist = true;
                    }
                }*/


                if ($queryDopExist) {
                    $query .= ' WHERE ' . $queryDop;

                    if ($queryDopExExist) {
                        $query .= ' AND (' . $queryDopEx . ')';
                    }

                    if ($queryDopEx2Exist) {
                        $query .= ' AND (' . $queryDopEx2 . ')';
                    }

                    /*if ($queryDopClientExist){
                        $queryDopClient = "SELECT `id` FROM `spr_clients` WHERE ".$queryDopClient;
                        if ($queryDopExist){
                            $query .= ' AND';
                        }
                        $query .= "`client` IN (".$queryDopClient.")";
                    }*/

                    $query = $query . " ORDER BY `create_time` DESC";

                    var_dump($query);

                    $msql_cnnct = ConnectToDB();

                    $arr = array();
                    $rez = array();

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($rez, $arr);
                        }
                        $journal = $rez;
                    } else {
                        $journal = 0;
                    }
                    //var_dump($journal);

                    //Выводим результат
                    if ($journal != 0) {
                        include_once 'functions.php';

                        // !!! **** тест с записью
                        include_once 'showZapisRezult2.php';

                        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode) {
                            $finance_edit = true;
                            $edit_options = true;
                        }

                        if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode) {
                            $stom_edit = true;
                            $edit_options = true;
                        }
                        if (($cosm['add_own'] == 1) || ($cosm['add_new'] == 1) || $god_mode) {
                            $cosm_edit = true;
                            $edit_options = true;
                        }

                        if (($zapis['add_own'] == 1) || ($zapis['add_new'] == 1) || $god_mode) {
                            $admin_edit = true;
                            $edit_options = true;
                        }

                        if (($scheduler['see_all'] == 1) || $god_mode) {
                            $upr_edit = true;
                            $edit_options = true;
                        }


                        echo showZapisRezult2($journal, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, true, false, $dop);


                        echo '
                                    <li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
                                        Всего<br>
                                        Посещений: ' . count($journal) . '<br>
                                    </li>';

                        echo '
                                        </ul>
                                    </div>';
                    } else {
                        echo '<span style="color: red;">Ничего не найдено</span>';
                    }

                } else {
                    echo '<span style="color: red;">Ожидается слишком большой результат выборки. Уточните запрос.</span>';
                }

                //var_dump($query);
                //var_dump($queryDopEx);
                //var_dump($queryDopClient);

                //mysql_close();
            }else {
                echo '<span style="color: red;">Не найден пациент.</span>';
            }
        }else{
            echo '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>';
        }
    }
}
?>