<?php

//create_installment.php
//Создание рассрочки на основе наряда (неоплаченного)

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            /*!!!Тест PDO*/
            include_once('DBWorkPDO.php');

            require 'variables.php';

            //переменная для просроченных
            $allPayed = true;

            if ($_GET){
                if (isset($_GET['client_id'])){

                    $client_j = SelDataFromDB('spr_clients', $_GET['client_id'], 'id');

                    if ($client_j != 0){


                        echo '
                            <div id="status">
								<header>
								    <h2>Создание рассрочки на основе наряда</h2>
								</header>';

                        echo '
                                <ul style="margin-left: 6px; margin-bottom: 10px;">
                                    <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                        Контрагент: '.WriteSearchUser('spr_clients',   $client_j[0]['id'], 'user_full', true).'
                                    </li> 
                                    <!--<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                        <a href="pay_blank_pdf_qr.php?client_id='. $client_j[0]['id'].'" class="ahref" style="text-align: center;" target="_blank" rel="nofollow noopener" title="Выписать счет на оплату">
                                            Выписать счет на оплату в банке
                                            <i class="fa fa-file-text" style="font-size: 140%; color: rgb(74, 148, 70); /*float: right;*/" aria-hidden="true"></i>
                                        </a>
                                    </li>--> 
                                    
                                </ul>';
                        echo '
                                <div id="data">';

                        echo '
                                    <div>';

                        //Баланс контрагента
                        include_once 'ffun.php';
                        $client_balance = json_decode(calculateBalance ($client_j[0]['id']), true);
                        //var_dump("2 - ".(microtime(true) - $script_start));
                        //Долг контрагента
                        $client_debt = json_decode(calculateDebt ($client_j[0]['id']), true);

//                        echo '
//                                        <ul id="balance" style="padding: 0 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
//                                            <li style="font-size: 85%; color: #7D7D7D; /*margin-top: 10px;*/">
//                                                Всего внесено:
//                                            </li>
//                                            <li style="margin-bottom: 5px; font-size: 90%; font-weight: bold;">
//                                                '.$client_balance['summ'].' руб.
//                                            </li>
//                                        </ul>
//
//                                        <ul id="balance" style="padding: 0 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
//                                            <li style="font-size: 85%; color: #7D7D7D; /*margin-top: 10px;*/">
//                                                Всего возвращено пациенту:
//                                            </li>
//                                            <li style="margin-bottom: 5px; font-size: 90%; font-weight: bold; color: red;">
//                                                '.$client_balance['withdraw'].' руб.
//                                            </li>
//                                        </ul>';
//                        echo '
//                                    </div>';
                        echo '
                                    <div>';
                        echo '
                                        <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                Доступный остаток средств:
                                            </li>
                                            <li class="calculateOrder" style="font-size: 110%; font-weight: bold;">
                                                <div class="availableBalance" id="availableBalance"  draggable="true" ondragstart="return dragStart(event)" style="display: inline;">'.($client_balance['summ'] - $client_balance['debited'] - $client_balance['withdraw'] + $client_balance['refund']).'</div><div style="display: inline;"> руб.</div>
                                            </li>
                                        </ul>
                        
                                        <ul id="balance" style="padding: 5px; margin: 0 5px 10px; display: inline-block; vertical-align: top; /*border: 1px outset #AAA;*/">
                                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                Общий долг составляет:
                                            </li>
                                            <li class="calculateInvoice" style="font-size: 110%; font-weight: bold;">
                                                 '.$client_debt['summ'].' руб.
                                            </li>
                                          
                                         </ul>';

                        echo '
                                    </div>';
                        echo '
                                    <div>';

                        //  Выписанные наряды
                        $arr = array();
                        $invoice_j = array();

                        $invoice_j_start = 0;
                        $invoice_j_count = 30;

                        //var_dump("3.5 - ".(microtime(true) - $script_start));
                        $db = new DB();

                        $args = [
                            'client_id' => $client_j[0]['id']
                        ];

                        echo '
                                        <ul id="invoices" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: table; vertical-align: top; border: 1px outset #AAA;">
                                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Незакрытые наряды</li>';

                        $query = "SELECT * FROM `journal_invoice` WHERE `client_id`=:client_id AND `summ`<>`paid` ORDER BY `create_time` DESC";
                        //$query = "SELECT * FROM `journal_invoice` WHERE `client_id`='".$client_j[0]['id']."' ORDER BY `create_time` DESC LIMIT $invoice_j_start, $invoice_j_count";

//                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//                        $number = mysqli_num_rows($res);
//                        if ($number != 0){
//                            while ($arr = mysqli_fetch_assoc($res)){
//                                array_push($invoice_j, $arr);
//                            }
//                        }

                        $invoice_j = $db::getRows($query, $args);

                        $rezultInvoices = showInvoiceDivRezult($invoice_j, false, false, true, true, true, false);

                        echo $rezultInvoices['data'];

                        echo '
                                        </ul>';



                        echo '
                                        <ul id="" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: table; vertical-align: top; border: 1px outset #AAA;">
                                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px;">
                                                Выберите наряд, на который хотите открыть рассрочку:<br>
                                                (сумма рассрочки будет рассчитана из неоплаченного остатка)
                                            </li>';

                        foreach ($invoice_j as $data){
                            echo '
                                        <div style="display: block; margin: 5px; border-bottom: 1px dotted #C5C5EC">
                                            <input name="invoice4installment" value="'.$data['id'].'" type="radio" installment_summ="'.($data['summ'] - $data['paid']).'"><b> #'.$data['id'].'</b> - Осталось внести: <span class="calculateInvoice">'.($data['summ'] - $data['paid']).'</span> руб.
                                        </div>';
                        }


                        echo '
								        </ul>';

                        echo '
                                        <ul id="installment_options" style="padding: 5px; margin-left: 6px; margin: 10px 5px; display: none; vertical-align: top; border: 1px outset #AAA;">
                                            <li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px;">
                                                Настройки рассрочки
                                            </li>
                                            <div style="margin: -5px 5px 5px;">
                                                Сумма рассрочки: <span id="installment_summ" class="calculateOrder"></span> руб.
                                            </div>
                                            <div style="margin: 5px;">
                                                Выберите срок:  <input type="number" size="5" name="installment_months" id="installment_months" min="1" max="12" value="3"> мес.
                                            </div>
                                                ';

                        echo '
                                            <div id="installment_calculate">
                                            </div>
									       ';


                        echo '
                                            <div>
                                                <input type="button" class="b" value="Сохранить" onclick="showinstallmentAdd(\'add\')">
                                            </div>
									       ';

                        echo '
								        </ul>';



                        echo '
                                    </div>';


                            echo '
                                </div>';


                            echo '
                                <div id="doc_title">Создание рассрочки на основе наряда - '.WriteSearchUser('spr_clients',   $client_j[0]['id'], 'user', false).' - Асмедика</div>';


                            //echo '<script src="js/dds.js" type="text/javascript"></script>';
                            echo '
                                <script type="text/javascript">
                                    //!!! Пример click на radiobutton / клик на radiobutton
                                    // get list of radio buttons with name \'size\'
                                    var sz = document.getElementsByName(\'invoice4installment\');
                                    
                                    // loop through list
                                    for (var i=0, len=sz.length; i<len; i++) {
                                        sz[i].onclick = function() { // assign onclick handler function to each
//                                            console.log(this.value);
//                                            console.log(this.getAttribute("installment_summ"));
                                            // put clicked radio button\'s value in total field
                                            //this.form.elements.total.value = this.value;
                                            
                                            $("#installment_options").css({"display":"table"});
                                            $("#installment_summ").html(this.getAttribute("installment_summ"));
                                            
                                            installmentCalculate(this.getAttribute("installment_summ"), $("#installment_months").val());
                                        };
                                    }
                                    
//                                    $("body").on("change keyup input click", "#installment_months", function (e) {
//                                        //Если только цифры, delete, backspase
//                                        if (((e.keyCode >= 48) && (e.keyCode <= 57)) || ((e.keyCode >= 96) && (e.keyCode <= 105)) || ((e.keyCode == 8) || (e.keyCode == 46))) {
//                                            console.log(e.keyCode);
//                                        }
//                                    });
                                    
                                    
                                    $("#installment_months").on("change keyup input click", function() {
                                        //console.log($(this).val());
                                        
                                        let month_count = $(this).val();
                                        
                                        //Если не число
                                        if (isNaN(month_count)){
                                            $(this).val(3);
                                        }else{
                                            if (month_count < 0){
                                                $(this).val(1);
                                            }else{
                                                if (month_count > 12){
                                                    $(this).val(12);
                                                }else{
                                                    if (month_count == ""){
                                                        $(this).val(3);
                                                    }else{
                                                        if (month_count === undefined){
                                                            $(this).val(3);
                                                        }else{
                                                            //Всё норм с типами данных
                                                            //console.log("Всё норм с типами данных")
                                                            
                                                            //console.log(Math.ceil(Number($("#installment_summ").html())/month_count-1));
                                                            //console.log(Number($("#installment_summ").html()) - (Math.ceil(Number($("#installment_summ").html())/month_count-1)) * (month_count-1));
                                                            //console.log(Number($("#installment_summ").html()) % (month_count-1));
                                                            //console.log(12 % 5);
                                                            
                                                            installmentCalculate(Number($("#installment_summ").html()), month_count);
            
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        
                                        //calculateDailyReportSumm();
                                        
                                    });
                                    
                                </script>';


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