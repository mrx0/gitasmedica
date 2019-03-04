<?php

//fl_edit_salary.php
//Изменение оклада сотрудника

    require_once 'header.php';

    if ($enter_ok){
        require_once 'header_tags.php';

        if (($finances['see_all'] == 1) || $god_mode){
            include_once 'DBWork.php';
            include_once 'functions.php';

            require 'variables.php';

            if ($_GET){
                if (isset($_GET['worker_id'])){

                    $worker_j = SelDataFromDB('spr_workers', $_GET['worker_id'], 'id');
                    //var_dump($worker_j);

                    if ($worker_j != 0){

                        echo '
                                <div id="status">
                                    <header>
                                        <div class="nav">
                                            <a href="fl_salaries.php" class="b">Оклады сотрудников</a>
                                        </div>
                                        <h1>Оклад сотрудника '.WriteSearchUser('spr_workers',   $worker_j[0]['id'], 'user_full', true).'</h1>
                                    </header>';

                        echo '
                                    <div id="data">
                                        <input type="hidden" id="worker_id" value="'.$worker_j[0]['id'].'">
                                        <input type="hidden" id="pass" value="fl_add_new_salary_f">';

                        $msql_cnnct = ConnectToDB ();

                        $salaries_j = array();

                        //$query = "SELECT * FROM `fl_spr_percents` ORDER BY `type`";
                        $query = "SELECT * FROM `fl_spr_salaries` WHERE `worker_id`='{$worker_j[0]['id']}' ORDER BY `date_from` DESC";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                array_push($salaries_j, $arr);
                            }

                            $currentSalary = $salaries_j[0];
                        }else{
                            $currentSalary['summ'] = 0;
                        }
                        //var_dump($salaries_j);
                        //var_dump(end($salaries_j));
                        //var_dump($currentSalary);

                        echo '
                                        <ul id="balance" style="padding: 0 5px; margin: 0 5px 20px; display: block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                            <li id="salaryText" style="font-size: 85%; color: #7D7D7D; margin: 10px 0;">
                                                Текущий оклад:
                                            </li>
                                            <li class="calculateOrder" style="font-size: 110%; font-weight: bold;">
                                                <div id="currentSalary" style="display: inline; cursor: pointer;" >'.number_format($currentSalary['summ'], 2, '.', '').'</div> <div id="textAfterSalary" style="display: inline;">руб. ', !empty($salaries_j) ? '<span style="font-size: 85%; font-weight: normal; color: #333; ">с '.date('d.m.Y', strtotime($currentSalary['date_from'])).'</span>' : '' ,'</div>
                                            </li>
                                            <div id="addSalaryDate" style="display: none;">
                                                <li id="" style="font-size: 85%; color: #7D7D7D; margin: 10px 0;">
                                                    Введите дату, с которой начнется применение новой суммы:
                                                </li>
                                                <li>
                                                    <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="text-align: inherit; color: rgb(30, 30, 30); font-size: 12px;" value="' . date('01.m.Y', time()) . '" onfocus="this.select();_Calendar.lcs(this)"  
                                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off"> 
                                                </li>
                                                <li style="margin-top: 20px;">
                                                    <div id="addSalaryOptions"></div>
                                                </li>
                                            </div>
                                        </ul>
                                        <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                Лог изменений:
                                            </li>
                                            <li style="font-size:80%; color: #555; margin: 10px 0 5px;">';
                        if (!empty($salaries_j)){
                            foreach ($salaries_j as $item){
                                echo '
                                    <li style="font-size: 80%;"><i class="fa fa-times" aria-hidden="true" style="cursor: pointer; color: red; font-size: 120%;"   title="Удалить" onclick="deleteThisSalary('.$item['id'].', \'worker\');"></i> <i style="font-size: 120%;">'.$item['summ'].' руб.</i> c '.date('d.m.y', strtotime($item['date_from'])).'  -   добавлено ['.date('d.m.y H:i', strtotime($item['create_time'])).'] <b>'.WriteSearchUser('spr_workers', $item['create_person'], 'user', true).'</b></li>';
                            }
                        }else{
                            echo '<span style="color:red">Ничего не указано</span>';
                        }
                        echo '                      
                                            </li>
                                        </ul>';

                        echo '
                                    </div>';





                        echo '
                                </div>';


                        echo '
                                </div>';


                        echo '
                                        <div id="doc_title">Оклад сотрудника '.WriteSearchUser('spr_workers',   $worker_j[0]['id'], 'user', false).' - Асмедика</div>';





                    }else{
                        echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                    }
                }else{
                    echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                }
            }else{
                echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
            }



        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
    }else{
        header("location: enter.php");
    }

    require_once 'footer.php';

?>