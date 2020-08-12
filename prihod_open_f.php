<?php 

//prihod_open_f.php
//Снять отметку о проведении приходной накладной, поставить статус 0

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){
			if (!isset($_POST['prihod_id'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{

                /*!!!Тест PDO*/
                include_once('DBWorkPDO.php');

                //$date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in'].""));
                $date_in = date('Y-m-d H:i:s', time());

                $date_in = date('Y-m-d H:i:s', time());

                $db = new DB();

                //Сначала надо проверить можно ли распроводить
                //собрать данные по позиции, прикинуть, не уйдёт ли кол-во в минус и так далее
                $args = [
                    'prihod_id' => $_POST['prihod_id']
                ];

                //Нужно выбрать сначала некоторые данные из приходной накладной, сравнить с наличием, а потом обновить количество
                $query = "
                SELECT scp_ex.*, scp.filial_id 
                FROM `sclad_prihod_ex` scp_ex
                RIGHT JOIN `sclad_prihod` scp
                ON scp.id = scp_ex.prihod_id
                WHERE scp_ex.prihod_id = :prihod_id";

                $prihod_data = $db::getRows($query, $args);
//                var_dump($prihod_data);

                //Соберём более четкий массив для сравнения, так как приходовать могли
                //одну позицию более одного раза в одной накладной.
                //маловероятно, но кто знает, лучше не рисковать
                $prihod_data_temp = [];

                foreach ($prihod_data as $temp_item){
                    if (!isset($prihod_data_temp[$temp_item['sclad_item_id']])){
                        $prihod_data_temp[$temp_item['sclad_item_id']] = array();
                    }
                    if (!isset($prihod_data_temp[$temp_item['sclad_item_id']][$temp_item['exp_garant_type']])){
                        $prihod_data_temp[$temp_item['sclad_item_id']][$temp_item['exp_garant_type']] = array();
                    }
                    if (!isset($prihod_data_temp[$temp_item['sclad_item_id']][$temp_item['exp_garant_type']][$temp_item['exp_garant_date']])){
                        $prihod_data_temp[$temp_item['sclad_item_id']][$temp_item['exp_garant_type']][$temp_item['exp_garant_date']] = 0;
                    }

                    $prihod_data_temp[$temp_item['sclad_item_id']][$temp_item['exp_garant_type']][$temp_item['exp_garant_date']] += $temp_item['quantity'];
                }

                //Маркер ошибки
                $error_marker = false;
                //Описание ошибок
                $error_descr = array(
                  1 => 'На складе нет позиции, которую вы собираетесь списать',
                  2 => 'На складе не хватает нужного количества для списания'
                );
                //Индекс ошибки
                $error_descr_ind = 0;
                //ID на котором произошла ошибка
                $error_position_id = 0;

                //Сюда запишем количества, которые будем в итоге списывать
                $quantity4minus_temp = [];

                //Выберем каждую позицию со склада
                foreach ($prihod_data as $prihod_item) {
                    $args = [
                        'filial_id' => $prihod_item['filial_id'],
                        'sclad_item_id' => $prihod_item['sclad_item_id'],
                        'exp_garant_type' => $prihod_item['exp_garant_type'],
                        'exp_garant_date' => $prihod_item['exp_garant_date']
                    ];

                    //Выбираем, смотрим, есть ли уже такое
                    $query = "
                    SELECT sc_av.*
                    FROM `sclad_availability` sc_av
                    WHERE sc_av.filial_id = :filial_id AND sc_av.sclad_item_id = :sclad_item_id AND sc_av.exp_garant_type = :exp_garant_type AND  sc_av.exp_garant_date = :exp_garant_date
                    LIMIT 1";

                    $available_data = $db::getRow($query, $args);

                    //Если нет на складе, выходим с ошибкой
                    if (empty($available_data)) {
                        $error_position_id = $prihod_item['sclad_item_id'];
                        $error_marker = true;
                        $error_descr_ind = 1;
                        break;
                    } else {
                        //Если позиция есть
                        //но количество не позволяет списывать, тоже выйдем с ошибкой
//                        if ($available_data['quantity'] < $prihod_item['quantity']) {
//                            $error_position_id = $prihod_item['sclad_item_id'];
//                            $error_marker = true;
//                            $error_descr_ind = 2;
//                            break;
//                        } else {
//                            //--
//                        }

                        if($available_data['quantity'] < $prihod_data_temp[$available_data['sclad_item_id']][$available_data['exp_garant_type']][$available_data['exp_garant_date']]){
                            $error_position_id = $prihod_item['sclad_item_id'];
                            $error_marker = true;
                            $error_descr_ind = 2;
                            break;
                        } else {
                            //Если всё хорошо
                            //Сюда запишем количества, которые будем в итоге списывать и откуда (вернее, сколько останется)
                            if (!isset($quantity4minus_temp[$available_data['id']])){
                                $quantity4minus_temp[$available_data['id']] = $available_data['quantity'] - $prihod_data_temp[$available_data['sclad_item_id']][$available_data['exp_garant_type']][$available_data['exp_garant_date']];
                            }
                        }

                    }
                }

                //Если ошибок нет, начинаем списание и потом меняем статус приходной на непроведённую
                if (!$error_marker){
                    foreach ($quantity4minus_temp as $id => $quantity) {
                        $args = [
                            'id' => $id,
                            'quantity' => $quantity
                        ];

                        //Обновить количество в наличии
                        $query = "UPDATE `sclad_availability` SET `quantity` = :quantity WHERE `id` = :id;";

                        $db::sql($query, $args);
                    }


                    //Обновляем статус на не приходованно
                    $args = [
                        'prihod_id' => $_POST['prihod_id']
                    ];
                    
                    $query = "UPDATE `sclad_prihod` SET `status` = '0' WHERE `id`= :prihod_id";

                    $db::sql($query, $args);

                    echo json_encode(array('result' => 'success', 'data' =>  $quantity4minus_temp));
                }else{
                //А если ошибка, выкинем её в ответе

                    echo json_encode(array('result' => 'error', 'data' => $error_descr[$error_descr_ind].'. Позиция: <a href="sclad_item.php?id='.$error_position_id.'" class="ahref"  target="_blank" rel="nofollow noopener">#'.$error_position_id.' <i class="fa fa-folder-open-o" aria-hidden="true" style="color: #000000; text-shadow: 1px 1px 1px #ffc90f;"></i></a>'));

                }






//                        $args['quantity'] = $prihod_item['quantity'];
//
//                        //Вставить запись прихода в БД:
//                        $query = "INSERT INTO `sclad_availability` (
//                            `filial_id`,
//                            `sclad_item_id`,
//                            `quantity`,
//                            `exp_garant_type`,
//                            `exp_garant_date`
//                            )
//                            VALUES (
//                            :filial_id,
//                            :sclad_item_id,
//                            :quantity,$error_position_id
//                            :exp_garant_type,
//                            :exp_garant_date
//                            )";
//
//                        $db::sql($query, $args);
//
//                    }else{
//                        $args = [
//                            'quantity' => $prihod_item['quantity'] + $data[0]['quantity']
//                        ];
//
//                        //Обновить запись прихода в БД:
//                        $query = "UPDATE `sclad_availability` SET `quantity` = :quantity WHERE `id`='{$data[0]['id']}';";
//
//                        $db::sql($query, $args);
//
//                    }



//                $query = "UPDATE `sclad_prihod` SET `status` = '0' WHERE `id`= :prihod_id";
//
//                $db::sql($query, $args);



                //!!! Добавить сюда обновление количества на складе!!!
                //Нужно выбрать сначала некоторые данные из приходной накладной, а потом обновлять


                //echo json_encode(array('result' => 'error', 'data' => $prihod_data_temp));

			}
		}
	}

?>