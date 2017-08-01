<?php

//specialization_add_f.php
//Функция для добавления новой специализации

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            if ($_POST['name'] == ''){

                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok"> Что-то не заполнено.</div>'));

            }else{
                include_once 'DBWork.php';
                include_once 'functions.php';

                //$name = trim($_POST['groupname']);

                $name = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['name']))));

                //Проверяем есть ли такая специализация
                $rezult = SelDataFromDB('spr_specialization', $name, 'name');
                //var_dump($rezult);

                if ($rezult == 0){
                    $specialization_id = WriteToDB_EditSpecialization ($name, $_SESSION['id']);

                    echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok"><a href="specialization.php?id=' . $specialization_id . '" class="ahref">Специализация</a> добавлена.</div>'));

                }else{

                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Такая специализация уже есть.</div>'));

                }
            }
        }else{
            //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok"> Что-то пошло не так.</div>'));
        }
    }
?>