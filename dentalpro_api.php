<?php

//dentalpro_api.php
//Тест API DentalPro

    require_once 'header.php';
    require_once 'blocks_dom.php';

    if ($enter_ok){
            require_once 'header_tags.php';

            //if (($clients['see_all'] == 1) || ($clients['see_own'] == 1) || $god_mode){

//        if (isset($_POST['type'])){
            //$_POST['type'] = 5;



            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                            <!--<a href="fl_consolidated_report_admin.php?filial_id=&m=&y=" class="b">Выгрузка данных из DentalPro</a>-->
                        </div>
                        <h2 style="padding: 0;">Выгрузка данных из DentalPro</h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';

            echo '
                    </div>
                </div>
                <div id="doc_title">DentalPro API - Асмедика</div>';


            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';

            echo '
				<script type="text/javascript">
				
//                    $(document).ready(function() {
//                        DPloadZapisData(\'lm/appointments\');
//                    });

                    $(document).ready(function() {
                        //Постепенное выполнение шаг за шагом (#последовательное, #ждём)
//                        wait(function(runNext){
//    
//                            setTimeout(function(){
//                                //Загружаем запись
//                                DPloadZapisData(function(data) {
//                                    //processing the data
//                                    console.log(data);
//                                    $(\'#data\').html(data)
//                                    
//                                    
//                                    
//                                }, \'lm/appointments\');
//                
//                                runNext(data);
//                
//                            }, 100);
//                
//                        }).wait(function(){
//                
//                            setTimeout(function(){
//                
//                                console.log(\'Второй шаг\')
//                
//                            }, 1000);
//                
//                           
//                
//                        });
                            
                        wait(function(runNext){
                
                            setTimeout(function(){
                                //Загружаем запись
                                DPloadZapisData(function(res_data) {
//                                    console.log(res_data);
                                    //$(\'#data\').html(res_data)
                                    
                                    //Выводим полученные записи
                                    DPshowZapisData(res_data);
                                    
                                    runNext(/*res_data*/);
                                    
                                }, \'lm/appointments\', 0);
                
                                
                
                            }, 100);
                
                        }).wait(function(/*runNext, res_data*/){
                            //используем аргументы из предыдущего вызова
//                            console.log(\'Второй шаг\')
//                            console.log(res_data);
//                            $(\'#data\').html(res_data)

                            setTimeout(function(){
                                //Работаем с ID записей
                                $(".zapis_id").each(function() {
//                                    console.log("ID записи");
//                                    console.log($(this).attr("zapis_id"));
//                                    console.log("Пациент");
//                                    console.log($(this).find(".client_id").attr("client_id"));
//                                    console.log("Врач");
//                                    console.log($(this).find(".doctor_id").attr("doctor_id"));
                                    $(this).find(".client_id").html("loading...");
                                    $(this).find(".doctor_id").html("loading...");
                                    let cl = $(this).find(".client_id");
                                    let doc = $(this).find(".doctor_id");
                                    
                                    
                                    //setTimeout(function(){
                                        DPloadZapisData(function(res_data) {
//                                            console.log(res_data.data);
    //                                      $(\'#data\').html(res_data)
                                        
                                            //Выводим данные по пациентам 
    //                                      f(res_data);
                                          cl.html(res_data.data.surname + " " + res_data.data.name + " " + res_data.data.second_name);
                                        
                                            //runNext(/*res_data*/);
                                        
                                        }, \'i/client\', $(this).find(".client_id").attr("client_id"));
                                    //}, 100);
                                    
                                    //setTimeout(function(){
                                        DPloadZapisData(function(res_data) {
//                                            console.log(res_data.data[0]);
    //                                      $(\'#data\').html(res_data)
                                        
                                            //Выводим данные по пациентам 
    //                                      f(res_data);
                                          doc.html(res_data.data[0].lastname + " " + res_data.data[0].firstname + " " + res_data.data[0].midname);
                                        
                                            //runNext(/*res_data*/);
                                        
                                        }, \'lm/doctors\', $(this).find(".doctor_id").attr("doctor_id"));
                                    //}, 100);
                                        
                                });
                            }, 100);
                            
//                            setTimeout(function(){
//                                //Работаем с ID клиентов
//                                $(".client_id").each(function() {
//                                    //console.log($(this).attr("client_id"));
//                                    
//                                    
//                                });
//                            }, 100);
//                            
//                            setTimeout(function(){
//                                //Работаем с ID врачей
//                                $(".doctor_id").each(function() {
//                                    //console.log($(this).attr("doctor_id"));
//                                    
//                                    
//                                });
//                            }, 100);
                            
                            
                            
                            
                
//                            if (calcIDForTabel_arr.main_data.length > 0) {
//                
//                                if (calcIDForTabel_arr.main_data.length > 10){
//                                    alert("Рассчитать можно не более 10 РЛ за раз.");
//                                }else {
//                                    var rys = false;
//                
//                                    rys = confirm("Вы собираетесь перерасчитать выделенные РЛ. \n\nВы уверены?");
//                
//                                    if (rys) {
//                                        $.ajax({
//                                            url: "fl_reloadPercentsMarkedCalculates.php",
//                                            global: false,
//                                            type: "POST",
//                                            dataType: "JSON",
//                                            data: {
//                                                calcArr: calcIDForTabel_arr
//                                            },
//                                            cache: false,
//                                            beforeSend: function () {
//                                                //$(\'#errrror\').html("<div style=\'width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...</span></div>");
//                                            },
//                                            // действие, при ответе с сервера
//                                            success: function (res) {
//                                                //console.log(res);
//                
//                                                if (res.result == "success") {
//                                                    //console.log(res);
//                
//                                                    var tableArr = calcIDForTabel_arr.data.split(\'_\');
//                
//                                                    refreshOnlyThisTab(thisObj, tableArr[1],tableArr[2],tableArr[3]);
//                                                }
//                                            }
//                                        });
//                                    }
//                                }
//                            }
                        });
                            
                            
                            
                    });
				</script>';

	}else{
		header("location: enter.php");
	}

	require_once 'footer.php';

?>
	