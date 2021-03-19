

    //Ждем ждём ожидание
    //Взято с Хабра https://habrahabr.ru/post/134823/
    //first — первая функция,которую нужно запустить
    wait = function(first){
        //класс для реализации вызова методов по цепочке #поочередный вызов
        return new (function(){
            var self = this;
            var callback = function(){
                var args;
                if(self.deferred.length) {
                    /* превращаем массив аргументов
                     в обычный массив */
                    args = [].slice.call(arguments);

                    /* делаем первым аргументом функции-обертки
                     коллбек вызова следующей функции */
                    args.unshift(callback);

                    //вызываем первую функцию в стеке функций
                    self.deferred[0].apply(self, args);

                    //удаляем запущенную функцию из стека
                    self.deferred.shift();
                }
            }
            this.deferred = []; //инициализируем стек вызываемых функций

            this.wait = function(run){
                //добавляем в стек запуска новую функцию
                this.deferred.push(run);

                //возвращаем this для вызова методов по цепочке
                return self;
            }

            first(callback); //запуск первой функции
        });
    }

    //Для добавления суммы в оплате наряда
	$('#addSummInPayment').click(function () {

		var lefttopay = Number(document.getElementById("leftToPay").innerHTML);
		var available = Number(document.getElementById("addSummInPayment").innerHTML);
		//console.log(lefttopay);
		//console.log(available);

		var rezult = 0;

		if (available >= lefttopay) {
            rezult = lefttopay;
		}else{
            //rezult = lefttopay - available;
            rezult = available;
        }

		document.getElementById("summ").value = rezult;

	});

    //Показываем блок с суммами и кнопками Для оплаты наряда
    function showPaymentAdd(mode, another_payer = false){
        // console.log(another_payer);

        let Summ = $("#summ").val();

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    summ:Summ
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    //$('#overlay').show();

                    //var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_payment_add(\'add\')">';

                    /*if (mode == 'edit'){
                        buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_payment_add(\'edit\')">';
                    }*/

                    if (mode == 'add'){
                       Ajax_payment_add('add', another_payer);
                    }

                    if (mode == 'edit'){
                        Ajax_payment_add('edit', another_payer);
                    }

                    // Создаем меню:
                    /*var menu = $('<div/>', {
                        class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
                    })
                        .appendTo('#overlay')
                        .append(
                            $('<div/>')
                                .css({
                                    "height": "100%",
                                    "border": "1px solid #AAA",
                                    "position": "relative",
                                })
                                .append('<span style="margin: 5px;"><i>Проверьте сумму и нажмите сохранить</i></span>')
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "width": "100%",
                                            "margin": "auto",
                                            "top": "-10px",
                                            "left": "0",
                                            "bottom": "0",
                                            "right": "0",
                                            "height": "50%",
                                        })
                                        .append('<div style="margin: 10px;">Сумма: <span class="calculateInvoice">'+Summ+'</span> руб.</div>')
                                )
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "bottom": "2px",
                                            "width": "100%",
                                        })
                                        .append(buttonsStr+
                                            '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                                        )
                                )
                        );

                    menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
                    */


                    // в случае ошибок в форме
                }else{
                    // перебираем массив с ошибками
                    for(var errorField in data.text_error){
                        // выводим текст ошибок
                        $('#'+errorField+'_error').html(data.text_error[errorField]);
                        // показываем текст ошибок
                        $('#'+errorField+'_error').show();
                        // обводим инпуты красным цветом
                        // $('#'+errorField).addClass('error_input');
                    }
                    document.getElementById("errror").innerHTML='<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>'
                }
            }
        })
    }

    function Ajax_payment_add_cert(mode){
        //console.log(mode);

        var payment_id = 0;

        var link = "payment_cert_add_f.php";

        if (mode == 'edit'){
            link = "payment_cert_edit_f.php";
            payment_id = document.getElementById("payment_id").value;
        }

        var Summ = $("#summ").html();
        //console.log(Summ);
        var invoice_id = $("#invoice_id").val();
        //console.log(invoice_id);

        var filial_id = $("#filial_id").val();

        var client_id = $("#client_id").val();
        //console.log(client_id);
        var date_in = $("#date_in").val();
        //console.log(date_in);

        //!!!тут сделано только для одного сертификата, если надо переделать, то тут
        var cert_id = $(".cert_pay").attr('cert_id');
        //console.log(cert_id);

        var comment = $("#comment").val();
        //console.log(comment);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    client_id: client_id,
                    invoice_id: invoice_id,
                    filial_id: filial_id,
                    cert_id: cert_id,
                    summ: Summ,
                    date_in: date_in,
                    comment: comment
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //!!! перенести вывод ошибки нормально, а то
                //$('#errror').html(res.data); не работает, которое ниже
                //Приходится смотреть через консоль
                console.log(res);

                $('.center_block').remove();
                $('#overlay').hide();

                if(res.result == "success"){
                    //$('#data').hide();
                    $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
                        /*'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Оплата наряда прошла успешно</li>'+*/
                        '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">'+res.data+'</li>'+
                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                        '<a href="finance_account.php?client_id='+client_id+'" class="b">Управление счётом</a>'+
                        '</li>'+
                        '</ul>');
                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }

    //!!! 2020-12-20 неверное описание. ф-цию пока не использую. Показавает блок для выбора именного сертификата, который мы потом добавим
    // function Ajax_payment_add_cert_name(mode){
    //     //console.log(mode);
    //
    //     let payment_id = 0;
    //
    //     let link = "payment_cert_add_f.php";
    //
    //     if (mode == 'edit'){
    //         link = "payment_cert_edit_f.php";
    //         payment_id = document.getElementById("payment_id").value;
    //     }
    //
    //     let Summ = $("#summ").html();
    //     //console.log(Summ);
    //     let invoice_id = $("#invoice_id").val();
    //     //console.log(invoice_id);
    //
    //     let filial_id = $("#filial_id").val();
    //
    //     let client_id = $("#client_id").val();
    //     //console.log(client_id);
    //     let date_in = $("#date_in").val();
    //     //console.log(date_in);
    //
    //     //!!!тут сделано только для одного сертификата, если надо переделать, то тут
    //     let cert_id = $(".cert_pay").attr('cert_id');
    //     //console.log(cert_id);
    //
    //     let comment = $("#comment").val();
    //     //console.log(comment);
    //
    //     $.ajax({
    //         url: link,
    //         global: false,
    //         type: "POST",
    //         dataType: "JSON",
    //         data:
    //             {
    //                 client_id: client_id,
    //                 invoice_id: invoice_id,
    //                 filial_id: filial_id,
    //                 cert_id: cert_id,
    //                 summ: Summ,
    //                 date_in: date_in,
    //                 comment: comment
    //             },
    //         cache: false,
    //         beforeSend: function() {
    //             //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
    //         },
    //         // действие, при ответе с сервера
    //         success: function(res){
    //             //!!! перенести вывод ошибки нормально, а то
    //             //$('#errror').html(res.data); не работает, которое ниже
    //             //Приходится смотреть через консоль
    //             console.log(res);
    //
    //             $('.center_block').remove();
    //             $('#overlay').hide();
    //
    //             if(res.result == "success"){
    //                 //$('#data').hide();
    //                 $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
    //                     /*'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Оплата наряда прошла успешно</li>'+*/
    //                     '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">'+res.data+'</li>'+
    //                     '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
    //                     '<a href="finance_account.php?client_id='+client_id+'" class="b">Управление счётом</a>'+
    //                     '</li>'+
    //                     '</ul>');
    //             }else{
    //                 $('#errror').html(res.data);
    //             }
    //         }
    //     });
    // }

    //Показываем блок с суммами и кнопками Для оплаты наряда сертификатом
    function showPaymentAddCert (mode){
        //console.log(mode);

        var Summ = $("#summ").html();

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    summ:Summ
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                if(data.result == 'success'){
                    //$('#overlay').show();

                    //var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_payment_add(\'add\')">';

                    /*if (mode == 'edit'){
                        buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_payment_add(\'edit\')">';
                    }*/

                    if (mode == 'add'){
                        Ajax_payment_add_cert('add');
                    }

                    if (mode == 'edit'){
                        Ajax_payment_add_cert('edit');
                    }

                    // в случае ошибок в форме
                }else{
                    // перебираем массив с ошибками
                    for(var errorField in data.text_error){
                        // выводим текст ошибок
                        $('#'+errorField+'_error').html(data.text_error[errorField]);
                        // показываем текст ошибок
                        $('#'+errorField+'_error').show();
                        // обводим инпуты красным цветом
                        // $('#'+errorField).addClass('error_input');
                    }
                    document.getElementById("errror").innerHTML='<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>'
                }
            }
        })
    }

    //!!! 2020-12-20 неверное описание. ф-цию пока не использую. Показываем блок с суммами и кнопками Для добавления именного сертификата
    // function showPaymentAddCertName (mode){
    //     //console.log(mode);
    //
    //     var Summ = $("#summ").html();
    //
    //     //проверка данных на валидность
    //     $.ajax({
    //         url:"ajax_test.php",
    //         global: false,
    //         type: "POST",
    //         dataType: "JSON",
    //         data:
    //             {
    //                 summ:Summ
    //             },
    //         cache: false,
    //         beforeSend: function() {
    //             //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
    //         },
    //         success:function(data){
    //             if(data.result == 'success'){
    //                 //$('#overlay').show();
    //
    //                 //var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_payment_add(\'add\')">';
    //
    //                 /*if (mode == 'edit'){
    //                     buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_payment_add(\'edit\')">';
    //                 }*/
    //
    //                 if (mode == 'add'){
    //                     Ajax_payment_add_cert_name('add');
    //                 }
    //
    //                 if (mode == 'edit'){
    //                     Ajax_payment_add_cert_name('edit');
    //                 }
    //
    //                 // в случае ошибок в форме
    //             }else{
    //                 // перебираем массив с ошибками
    //                 for(var errorField in data.text_error){
    //                     // выводим текст ошибок
    //                     $('#'+errorField+'_error').html(data.text_error[errorField]);
    //                     // показываем текст ошибок
    //                     $('#'+errorField+'_error').show();
    //                     // обводим инпуты красным цветом
    //                     // $('#'+errorField).addClass('error_input');
    //                 }
    //                 document.getElementById("errror").innerHTML='<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>'
    //             }
    //         }
    //     })
    // }

    //Добавляем/редактируем в базу оплату
    function Ajax_payment_add(mode, another_payer = false){
        //console.log(another_payer);

        let payment_id = 0;

        let link = "payment_add_f.php";

        if (mode == 'edit'){
            link = "payment_edit_f.php";
            payment_id = $("#payment_id").val();
        }

        let reqData = {
            client_id: $("#client_id").val(),
            invoice_id: $("#invoice_id").val(),
            filial_id: $("#filial_id").val(),
            summ: $("#summ").val(),
            date_in: $("#date_in").val(),
            comment: $("#comment").val()
        };

        if (another_payer){
            reqData.another_payer = true;
            reqData.another_payer_id = $("#new_payer_id").val();
        }

        // var Summ = $("#summ").val();
        // var invoice_id = $("#invoice_id").val();
        // var filial_id = $("#filial_id").val();
        //
        let client_id = $("#client_id").val();
        // var date_in = $("#date_in").val();
        // //console.log(date_in);
        //
        // var comment = $("#comment").val();

        //console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                console.log(res);   //!!! не убирай это, или сделай отображение ошибок


                $('.center_block').remove();
                $('#overlay').hide();

                if(res.result == "success"){
                    //$('#data').hide();
                    $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
                        /*'<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Оплата наряда прошла успешно</li>'+*/
                        '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">'+res.data+'</li>'+
                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                        '<a href="finance_account.php?client_id='+client_id+'" class="b">Управление счётом</a>'+
                        //'<a href="invoice.php?id='+invoice_id+'" class="b">Вернуться в наряд</a>'+
                        '</li>'+
                        '</ul>');
                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }

    //Добавляем/редактируем в базу выдачу именного сертификата
    function certificateNameCell(mode){

        let link = "cert_name_cell_f.php";

        let cert_id = $("#cert_id").val();
        let client_id = $("#client_id").val();

        if((client_id != 0) && (cert_id != 0)) {

            let reqData = {
                cert_id: cert_id,
                client_id: client_id
            };
            //console.log(reqData);

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    console.log(res);   //!!! не убирай это, или сделай отображение ошибок


                    // $('.center_block').remove();
                    // $('#overlay').hide();

                    if (res.result == "success") {
                        //$('#data').hide();
                        $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">' +
                            '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Сертификат выдан</li>' +
                            '</ul>');
                        setTimeout(function () {
                            window.location.replace('certificate_name.php?id=' + cert_id + '');
                            //console.log('client.php?id='+id);
                        }, 100);
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }



    //Выборка касса
    function Ajax_show_result_stat_cashbox(){

        var link = "ajax_show_result_cashbox_f.php";

        var summtype = $("input[name=summType]:checked").val();

        /*var zapisTypeAll = $("input[id=zapisTypeAll]:checked").val();
        if (zapisTypeAll === undefined){
            zapisTypeAll = 0;
        }
        var zapisTypeStom = $("input[id=zapisTypeStom]:checked").val();
        if (zapisTypeStom === undefined){
            zapisTypeStom = 0;
        }
        var zapisTypeCosm = $("input[id=zapisTypeCosm]:checked").val();
        if (zapisTypeCosm === undefined){
            zapisTypeCosm = 0;
        }*/

        var certificatesShow = $("input[id=certificatesShow]:checked").val();
        if (certificatesShow === undefined){
            certificatesShow = 0;
        }

        var reqData = {
            datastart: $("#datastart").val(),
            dataend: $("#dataend").val(),

            filial: $("#filial").val(),

            summtype: summtype,

            /*zapisTypeAll: zapisTypeAll,
             zapisTypeStom: zapisTypeStom,
             zapisTypeCosm: zapisTypeCosm,*/

            certificatesShow: certificatesShow
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            data: reqData,
            cache: false,
            beforeSend: function() {
                $('#qresult').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(data){
                $('#qresult').html(data);

                $( "#tabs_w" ).tabs();
            }
        })
    }
    //Удалить текущую проплату
    function deletePaymentItem(id, client_id, invoice_id){
        //console.log(id);

        var rys = false;

        rys = confirm("Удалить оплату?");

        if (rys) {

            $.ajax({
                url: "payment_del_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    client_id: client_id,
                    invoice_id: invoice_id,
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    if (res.result == "success") {
                        location.reload();
                        //console.log(res.data);
                    }
                    if (res.result == "error") {
                        alert(res.data);
                    }
                    //console.log(data.data);

                }
            });
        }
    }


    //Удалить затраты на материалы
    function fl_deleteMaterialConsumption(id, invoice_id){
        //console.log(id);

        var rys = false;

        rys = confirm("Вы собираетесь удалить затраты на материалы.\nЭто необратимое действие.\nРасчётный лист будет пересчитан.\nВы уверены?");

        if (rys) {

            $.ajax({
                url: "fl_delete_material_consumption_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    mat_cons_id: id,
                    invoice_id: invoice_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res.data2);
                    /*if(data.result == "success"){

                     }*/
                    //location.reload();
                    window.location.href = "invoice.php?id=" + invoice_id;
                }
            });
        }
    }

    //Сбросить проценты персональные на по умолчанию
    //function fl_changePersonalPercentCatdefault(workerID, catID, typeID){
    function fl_changePersonalPercentCatdefault(workerID){
        /*console.log(workerID);
        console.log(catID);
        console.log(typeID);*/

        var rys = false;

        rys = confirm("Сбросить на значения по умолчанию?");

        if (rys) {

            $.ajax({
                url: "fl_change_personal_percent_cat_default_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    worker_id: workerID,
                    //cat_id: catID,
                    //type: typeID,
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (data) {
                    if (data.result == "success") {
                        //console.log(data.data);
                        location.reload();
                    }
                }
            });
        }
    }

    //Перерасчёт расчёта
    function fl_reloadPercentsCalculate(workerID){

        var rys = false;

        /*var rys = confirm("Расчитать сумму заново?");

        if (rys) {

            $.ajax({
                url: "fl_reload_percents_calculate_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    worker_id: workerID,
                    //cat_id: catID,
                    //type: typeID,
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (data) {
                    if (data.result == "success") {
                        //console.log(data.data);
                        location.reload();
                    }
                }
            });
        }*/
    }

    //Для изменений в процентах персональных
    var changePersonalPercentCat_elems = document.getElementsByClassName("changePersonalPercentCat"), newInput;
    //console.log(elems);

    if (changePersonalPercentCat_elems.length > 0) {
        for (var i = 0; i < changePersonalPercentCat_elems.length; i++) {
            var el = changePersonalPercentCat_elems[i];
            el.addEventListener("click", function () {
                //var thisID = this.id;
                var workerID = this.getAttribute("worker_id");
                //console.log(this.getAttribute("worker_id"));
                var catID = this.getAttribute("cat_id");
                //console.log(this.getAttribute("cat_id"));
                var typeID = this.getAttribute("type_id");
                //console.log(this.getAttribute("type_id"));

                var thisVal = this.innerHTML;
                var newVal = thisVal;
                //console.log(this);
                //console.log(workerID);
                //console.log(catID);
                //console.log(typeID);
                //console.log(thisVal);
                //console.log(isNaN(thisVal));

                var inputs = this.getElementsByTagName("input");
                if (inputs.length > 0) return;
                if (!newInput) {

                    /*buttonDiv = document.createElement("div");
                    //buttonDiv.innerHTML = '<i class="fa fa-check" aria-hidden="true" title="Применить" style="margin-right: 4px;"></i> <i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>';
                    buttonDiv.innerHTML = '<i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>';
                    buttonDiv.style.position = "absolute";
                    buttonDiv.style.right = "-9px";
                    buttonDiv.style.top = "1px";
                    buttonDiv.style.fontSize = "12px";
                    buttonDiv.style.color = "green";
                    buttonDiv.style.border = "1px solid #BFBCB5";
                    buttonDiv.style.backgroundColor = "#FFF";
                    buttonDiv.style.padding = "0 6px";

                    buttonDiv.id = "changePersonalPercentCatdefault";*/

                    newInput = document.createElement("input");
                    newInput.type = "text";
                    newInput.maxLength = 5;
                    newInput.setAttribute("size", 20);
                    newInput.style.width = "40px";
                    newInput.addEventListener("blur", function () {
                        //console.log(newInput.parentNode.getAttribute("worker_id"));

                        workerID = newInput.parentNode.getAttribute("worker_id");
                        catID = newInput.parentNode.getAttribute("cat_id");
                        typeID = newInput.parentNode.getAttribute("type_id");

                        //Попытка обработать клика на кнопке для сброса на значения по умолчанию - провалилась, всегда сбрасывается на по умолчанию
                        //var changePersonalPercentCatdefault = document.getElementById("changePersonalPercentCatdefault");
                        //console.log(changePersonalPercentCatdefault.innerHTML);

                        //changePersonalPercentCatdefault.addEventListener("click", fl_changePersonalPercentCatdefault(workerID, catID, typeID), false);

                        //Новые данные
                        //if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (newInput.value > 100) || (isNaN(parseInt(newInput.value, 10)))) {
                        if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (isNaN(parseInt(newInput.value, 10)))) {
                            //newInput.parentNode.innerHTML = 0;
                            newInput.parentNode.innerHTML = thisVal;
                            newVal = thisVal;
                        } else {
                            newInput.parentNode.innerHTML = parseInt(newInput.value, 10);
                            newVal = parseInt(newInput.value, 10);
                        }
                        //console.log(this);
                        //console.log(workerID);

                        //console.log(thisVal == newVal);

                        if (Number(thisVal) != Number(newVal)) {

                            $.ajax({
                                url: "fl_change_personal_percent_cat_f.php",
                                global: false,
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    worker_id: workerID,
                                    cat_id: catID,
                                    type: typeID,
                                    val: newVal
                                },
                                cache: false,
                                beforeSend: function () {
                                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                                },
                                // действие, при ответе с сервера
                                success: function (res) {
                                    if (res.result == "success") {
                                        //console.log(data);
                                        $('#infoDiv').html(res.data);
                                        $('#infoDiv').show();
                                        setTimeout(function () {
                                            $('#infoDiv').hide('slow');
                                            $('#infoDiv').html();
                                        }, 1000);

                                        //location.reload();
                                    }

                                }
                            });
                        }
                    }, false);
                }

                //newInput.value = this.firstChild.innerHTML;
                newInput.value = thisVal;
                this.innerHTML = "";
                //this.appendChild(buttonDiv);
                this.appendChild(newInput);
                //newInput.innerHTML = ('<i class="fa fa-check" aria-hidden="true"></i>');
                newInput.focus();
                newInput.select();
            }.bind(el), false);
        }
    }


    //Сохраняем дефицитную сумму в указанный месяц
    function Ajax_PrevMonthDeficitAdd (filial_id){
        //console.log();

        var link = "fl_add_prev_month_filial_deficit_f.php";
        //console.log(link);

        var Data = {
            filial_id: filial_id,
            summ: Number($("#ostatokDeficit").html().replace(/\s{1,}/g, '')),
            month:  $("#iWantThisMonthh_prevdef").val(),
            year:  $("#iWantThisYearh_prevdef").val()
        };
        //console.log(Data);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: Data,
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == "success"){
                    location.reload();
                }else{
                    alert(res.data);
                    //$("#overlay").hide();
                    $('#errrror').html('<div class="query_neok">'+res.data+'</div>');
                }
            }
        });
    }

    //Удаляем дефицитную сумму тз указанного месяца
    function Ajax_PrevMonthDeficitDelete (filial_id, year, month){
        //console.log();

        let rys = false;

        rys = confirm("Вы хотите удалить дефицит. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            let link = "fl_delete_prev_month_filial_deficit_f.php";
            //console.log(link);

            let reqData = {
                filial_id: filial_id,
                year: year,
                month: month
            };
            //console.log(reqData);

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    // console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        alert(res.data);
                        //$("#overlay").hide();
                        $('#errrror').html('<div class="query_neok">' + res.data + '</div>');
                    }
                }
            });
        }
    }



    //Показываем промежуточный блок для добавления дефицита
    function showPrevMonthDeficitAdd (filial_id){

        $('#overlay').show();

        var deficit_summ = $("#ostatokDeficit").html();
        //console.log(deficit_summ);

        // console.log($("#iWantThisMonth").html());
        // console.log($("#iWantThisYear").html());
        var calendar_month = $("#iWantThisMonth").html();
        var calendar_year = $("#iWantThisYear").val();

        var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_PrevMonthDeficitAdd('+filial_id+')">';

        // Создаем меню:
        var menu = $('<div/>', {
            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        })
            .appendTo('#overlay')
            .append(
                $('<div/>')
                    .css({
                        "height": "100%",
                        "border": "1px solid #AAA",
                        "position": "relative"
                    })
                    .append('<span style="margin: 5px;"><i>Выберите в какой месяц необходимо добавить и нажмите сохранить</i></span>')
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "width": "100%",
                                "margin": "auto",
                                "top": "10px",
                                "left": "0",
                                "bottom": "0",
                                "right": "0",
                                "height": "50%"
                            })
                            .append('<select name="iWantThisMonth" id="iWantThisMonthh_prevdef" style="margin-right: 5px;">'+calendar_month+'</select>')
                            //.append('<select name="iWantThisYear" id="iWantThisYearh_prevdef">'+calendar_year+'</select>')
                            .append('<input name="iWantThisYear" id="iWantThisYearh_prevdef" type="number" value="'+calendar_year+'" min="2000" max="2030" size="4" style="width: 60px;">')
                            .append('<div style="margin: 10px;">Cумма: <span class="calculateInvoice">'+deficit_summ+'</span> руб.</div>')
                    )
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "bottom": "2px",
                                "width": "100%"
                            })
                            .append(buttonsStr+
                                '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                            )
                    )
            );


        menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

    }


    //Добавляем/редактируем в базу приход извне
    function  fl_Ajax_money_from_outside_add(moneyData){
        console.log(moneyData);

        let link = "fl_money_from_outside_add_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data: moneyData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
//                console.log(res.data);
                //$('#data').html(res)

                blockWhileWaiting (true);

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    location.reload();

                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу расходы на материалы
    function  fl_Ajax_material_cost_add(moneyData){
        //console.log(moneyData);

        let link = "fl_material_cost_add_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data: moneyData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
//                console.log(res.data);
                //$('#data').html(res)

                blockWhileWaiting (true);

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    //location.reload();
                    window.location.href = "material_costs_test.php?filial_id="+moneyData.filial_id+"&m="+moneyData.month+"&y="+moneyData.year;

                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Удаляем все денежные приходы извне на филиал в месяц
    function Ajax_MoneyFromOutsideDelete (filial_id, year, month){
        //console.log();

        let rys = false;

        rys = confirm("Вы хотите удалить ВСЕ приходы денег извне. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            let link = "fl_delete_money_from_outside_f.php";
            //console.log(link);

            let reqData = {
                filial_id: filial_id,
                year: year,
                month: month
            };
            //console.log(reqData);

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    // console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        alert(res.data);
                        //$("#overlay").hide();
                        $('#errrror').html('<div class="query_neok">' + res.data + '</div>');
                    }
                }
            });
        }
    }


    //Добавляем/редактируем в базу оплату солярия
    function fl_Ajax_solar_add(reqData){
        //console.log(reqData);

        var link = "fl_solar_add_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data: reqData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
                // console.log(res);
                // console.log(res.data);

                //$('#data').html(res)

                blockWhileWaiting (true);

                if(res.result == 'success') {
                    // console.log('success');

                    //$('#data').html(res.data);
                    //blockWhileWaiting (false);

                    document.location.href = "zapis_solar.php?filial_id=" + reqData.filial_id;
                }else{
                    //console.log('error');

                    blockWhileWaiting (false);
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу расход материалов для наряда
    function fl_Ajax_MaterialsConsumptionAdd(invoice_id, mode){

        var link = "fl_material_consumption_add_f.php";

        if (mode == 'edit'){
         link = "fl_material_consumption_edit_f.php";
        }

        var matConsData = {
            invoice_id:invoice_id,
            descr: $('#descr').val(),
            summ: $('#mat_cons_pos_summ_all').val()
        };

        var error_marker = false;

        var positionsArr = {};

        wait(function(runNext){

            setTimeout(function(){

                $(".materials_consumption_pos").each(function(){
                    //console.log($(this).attr("positionID"));
                    //console.log($(this).val());
                    //console.log($(this).parent().parent().find('.invoiceItemPriceItog').text());

                    var position_id = Number($(this).attr("positionID"));
                    var invoiceItemPriceItog = Number($(this).parent().parent().find('.invoiceItemPriceItog').text());
                    var materials_consumption_sum = Number($(this).val());

                    var checked_status = $(this).parent().find('.chkMatCons').prop("checked");
                    //console.log(checked_status);

                    if (checked_status) {

                        if (invoiceItemPriceItog < materials_consumption_sum) {
                            $('#errrror').html('<div class="query_neok">Расход не может быть больше стоимости позиции.</div>');
                            //console.log(position_id);

                            $('#overlay').hide();
                            $('.center_block').remove();

                            error_marker = true;

                            return false;
                        } else {
                            //console.log(position_id);

                            positionsArr[position_id] = {};
                            positionsArr[position_id]['mat_cons_sum'] = materials_consumption_sum;

                        }
                    }
                });

                runNext(positionsArr, error_marker);

            }, 1500);

        }).wait(function(runNext, positionsArr, error_marker){
            //используем аргументы из предыдущего вызова

            if (!error_marker) {
                //console.log(positionsArr)

                matConsData["positionsArr"] = positionsArr;

                $.ajax({
                    url: link,
                    global: false,
                    type: "POST",
                    dataType: "JSON",

                    data: matConsData,

                    cache: false,
                    beforeSend: function() {
                        $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                    },
                    // действие, при ответе с сервера
                    success:function(res){
                        //console.log(res.data);
                        /*$('#errrror').html(res);*/

                        if(res.result == 'success') {
                            //console.log('success');
                            //$('#data').html(res.data);

                            blockWhileWaiting (true);

                            document.location.href = "invoice.php?id="+invoice_id;
                        }else{
                            //console.log('error');
                            $('#overlay').hide();
                            $('.center_block').remove();

                            $('#errror').html(res.data);
                            //$('#errrror').html('');
                        }
                    }
                });

            }
        });
    }

    // Добавляем/редактируем в базу расход материалов для наряда
    function fl_showMaterialsConsumptionAdd(invoice_id, mode){
        //console.log(invoice_id);

        var Summ = $("#mat_cons_pos_summ_all").val();

        if (Summ > 0) {

            $('#overlay').show();


            /*var SummIns = 0;
             var SummInsBlock = '';*/

            /*if (invoice_type == 5){
             SummIns = $("#calculateInsInvoice").html();
             SummInsBlock = '<div>Страховка: <span class="calculateInsInvoice">'+SummIns+'</span> руб.</div>';
             }*/

            var buttonsStr = '<input type="button" class="b" value="Применить" onclick="$(this).prop(\'disabled\', true ); fl_Ajax_MaterialsConsumptionAdd('+invoice_id+', \'add\')">';


            if (mode == 'edit') {
                buttonsStr = '<input type="button" class="b" value="Применить" onclick="$(this).prop(\'disabled\', true ); fl_Ajax_MaterialsConsumptionAdd('+invoice_id+', \'edit\')">';
            }

            // Создаем меню:
            var menu = $('<div/>', {
                class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
            })
                .appendTo('#overlay')
                .append(
                    $('<div/>')
                        .css({
                            "height": "100%",
                            "border": "1px solid #AAA",
                            "position": "relative",
                        })
                        .append('<span style="margin: 5px;"><i>Проверьте сумму расходов на материалы.</i></span>')
                        .append('<br><br><span style="margin: 5px; color: red"><i>Внимание! Расчётный лист будет пересчитан.</i></span>')
                        .append(
                            $('<div/>')
                                .css({
                                    "position": "absolute",
                                    "width": "100%",
                                    "margin": "auto",
                                    "top": "25px",
                                    "left": "0",
                                    "bottom": "0",
                                    "right": "0",
                                    "height": "50%",
                                })
                                .append('<div style="margin: 15px;">Сумма: <span class="calculateInvoice">' + Summ + '</span> руб.</div>')
                        )
                        .append(
                            $('<div/>')
                                .css({
                                    "position": "absolute",
                                    "bottom": "2px",
                                    "width": "100%",
                                })
                                .append(buttonsStr +
                                    '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                                )
                        )
                );

            menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

        }



    }

    //Промежуточная функция для прихода денег извне
    function fl_showMoneyFromOutsideAdd(){

        //убираем ошибки
        hideAllErrors ();

        if ($('#SelectFilial').val() == 0){
            $("#errror").html('<div class="query_neok">Не выбран филиал</div>');
        }else {

            let moneyData = {
                month: $('#iWantThisMonth').val(),
                year: $('#iWantThisYear').val(),
                summ: $('#paidout_summ').val(),
                filial_id: $('#SelectFilial').val(),
                descr: $('#descr').val()
            };
            //console.log(paidoutData);

            //проверка данных на валидность
            $.ajax({
                url: "ajax_test.php",
                global: false,
                type: "POST",
                dataType: "JSON",

                data: {summ: $('#summ').val()},

                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (res) {
                    if (res.result == 'success') {

                        fl_Ajax_money_from_outside_add(moneyData);

                        // в случае ошибок в форме
                    } else {
                        // перебираем массив с ошибками
                        for (var errorField in res.text_error) {
                            // выводим текст ошибок
                            $('#' + errorField + '_error').html(res.text_error[errorField]);
                            // показываем текст ошибок
                            $('#' + errorField + '_error').show();
                            // обводим инпуты красным цветом
                            // $('#'+errorField).addClass('error_input');
                        }
                        $('#errror').html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
                    }
                }
            })
        }
    }

    //Промежуточная функция для Добавления расходов
    function fl_showMaterialCostAdd_test(){

        //убираем ошибки
        hideAllErrors ();

        if ($('#SelectFilial').val() == 0){
            $("#errror").html('<div class="query_neok">Не выбран филиал</div>');
        }else {
            if ($('#SelectCategory').val() == 0){
                $("#errror").html('<div class="query_neok">Не выбрана категория</div>');
            }else {

                let moneyData = {
                    month: $('#iWantThisMonth').val(),
                    year: $('#iWantThisYear').val(),
                    summ: $('#paidout_summ').val(),
                    filial_id: $('#SelectFilial').val(),
                    cat_id: $('#SelectCategory').val(),
                    descr: $('#descr').val()
                };
                //console.log(paidoutData);

                //проверка данных на валидность
                $.ajax({
                    url: "ajax_test.php",
                    global: false,
                    type: "POST",
                    dataType: "JSON",

                    data: {summ: $('#summ').val()},

                    cache: false,
                    beforeSend: function () {
                        //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                    },
                    success: function (res) {
                        if (res.result == 'success') {

                            fl_Ajax_material_cost_add(moneyData);

                            // в случае ошибок в форме
                        } else {
                            // перебираем массив с ошибками
                            for (var errorField in res.text_error) {
                                // выводим текст ошибок
                                $('#' + errorField + '_error').html(res.text_error[errorField]);
                                // показываем текст ошибок
                                $('#' + errorField + '_error').show();
                                // обводим инпуты красным цветом
                                // $('#'+errorField).addClass('error_input');
                            }
                            $('#errror').html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
                        }
                    }
                })
            }
        }
    }

    //Промежуточная функция для ввода оплаты за солярий
    function fl_showSolarAdd(filial_id){

        //убираем ошибки
        hideAllErrors ();

        //!!!тут сделано только для одного абонемента, если надо переделать, то тут
        var abon_id = $(".abon_pay").attr('abon_id');
        //console.log(abon_id);

        if (abon_id === undefined){
            abon_id = 0;
        }

        if ((Number($('#finPrice').val()) > 0) || (Number($('#realiz_summ').val()) > 0) || (abon_id != 0)){
            var reqData = {
                filial_id: filial_id,
                date_in: $('#iWantThisDate2').val(),
                /*device_type: $('#selectDeviceType').val(),*/
                min_count: $('#min_count').val(),
                discount: $('#discount').val(),
                summ_type: $('input[name=summ_type]:checked').val(),
                oneMinPrice: $('#oneMinPrice').html(),
                finPrice: $('#finPrice').val(),
                descr: $('#descr').val(),
                abon_id: abon_id,

                realiz_summ: $('#realiz_summ').val()

            };
            //console.log(reqData);

            fl_Ajax_solar_add(reqData);

        }else{
            $("#errror").html('<span style="color: red">Ошибка, что-то заполнено не так.</span>');
            $("#min_count_error").html('<span style="color: red">В этом поле ошибка</span>');
            $("#min_count_error").show();
        }
    }


    //
    function changeAllMaterials_consumption_pos() {

        var materials_consumption_pos_all_summ = 0;

        //Сумма оплачено
        var mat_paid_summ = Number($(".calculateInvoicePaid").html());
        //console.log(mat_paid_summ);

        $(".materials_consumption_pos").each(function() {

            var checked_status = $(this).parent().find('.chkMatCons').prop("checked");
            //console.log(checked_status);

            if (checked_status) {
                if (!isNaN(Number($(this).val()))) {
                    if (Number($(this).val()) > 0) {
                        $(this).val(Number($(this).val()));
                        materials_consumption_pos_all_summ += Number($(this).val());

                        // if (materials_consumption_pos_all_summ > mat_paid_summ){
                        //     alert("Сумма расходов не может превышать суммы оплаты.");
                        //     materials_consumption_pos_all_summ = mat_paid_summ;
                        //     $(".materials_consumption_pos_all").val(materials_consumption_pos_all_summ);
                        //     $("#matConsAccept").prop("disabled", true);
                        // }else{
                            $("#matConsAccept").prop("disabled", false);
                        // }

                    } else {
                        $(this).val(0);
                    }
                } else {
                    $(this).val(0);
                }
            }
        });

        $(".materials_consumption_pos_all").val(materials_consumption_pos_all_summ);

    }





    $(document).ready(function() {

        //Рабочий пример клика на элементе после подгрузки его в DOM
        $("body").on("click", ".radioBtnCalcs", function () {
            var checked_status = $(this).prop("checked");
            //console.log(checked_status);
            //console.log($(this).parent());

            $(".radioBtnCalcs").each(function() {
                $(this).parent().parent().parent().css({"background-color": ""});
            });

            if (checked_status) {
                $(this).parent().parent().parent().css({"background-color": "#83DB53"});
            } else {
                $(this).parent().parent().parent().css({"background-color": ""});
            }
        });

        //Для расчета затрат на материалы
        $("body").on("keyup change", ".materials_consumption_pos", function () {
            //console.log($(this).val());

            changeAllMaterials_consumption_pos ();

        });

        $("body").on("keyup change", ".materials_consumption_pos_all", function () {
            //console.log($(this).val());

            if (!isNaN(Number($(this).val()))) {
                //console.log($('input[type=checkbox]:checked').length);

                $(this).val(Number($(this).val()));

                //Сумма оплачено
                var mat_paid_summ = Number($(".calculateInvoicePaid").html());
                //console.log(mat_paid_summ);

                var mat_cons_pos_summ_all = Number($(this).val());

                // if (mat_cons_pos_summ_all > mat_paid_summ){
                //     alert("Сумма расходов не может превышать суммы оплаты.");
                //     mat_cons_pos_summ_all = mat_paid_summ;
                //     $(this).val(mat_cons_pos_summ_all);
                // }

                var chkBoxsCount = $('input[type=checkbox]:checked').length;

                var ostatok = mat_cons_pos_summ_all % chkBoxsCount;
                var mat_cons_pos_summ = Math.floor(mat_cons_pos_summ_all/chkBoxsCount);
                //console.log(mat_cons_pos_summ);

                var first_count = true;

                $(".materials_consumption_pos").each(function() {

                    var checked_status = $(this).parent().find('.chkMatCons').prop("checked");
                    //console.log(checked_status);

                    if (checked_status) {
                        if (first_count == true) {
                            $(this).val(mat_cons_pos_summ+ostatok);
                            first_count = false
                        }else{
                            $(this).val(mat_cons_pos_summ);
                        }
                    }else{
                        $(this).val(0);
                    }

                });

            }else{
                $(this).val(0);
            }

        });

        $("body").on("click", ".chkMatCons", function () {

            changeAllMaterials_consumption_pos ();

        });

    });

    //Получаем необработанные расчетные листы
    function getCalculatesfunc (thisObj, reqData){
        $.ajax({
            url:"fl_get_calculates_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data:reqData,

            cache: false,
            beforeSend: function() {
                thisObj.html("<div style='width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка<br>расч. листов</span></div>");
            },
            success:function(res){
                //console.log(res);
                //thisObj.html(res);

                if(res.result == 'success'){

                    ids = thisObj.attr("id");
                    ids_arr = ids.split("_");
                    //console.log(ids_arr);

                    permission = ids_arr[0];
                    worker = ids_arr[1];
                    office = ids_arr[2];

                    if (res.status == 1){
                        thisObj.html(res.data);

                        //Показываем оповещения на фио и филиале
                        //console.log("#tabs_notes_"+permission+"_"+worker+"_"+office);

                        $("#tabs_notes_"+permission+"_"+worker).css("display", "inline-block");
                        $("#tabs_notes_"+permission+"_"+worker+"_"+office).css("display", "inline-block");

                        thisObj.parent().find(".summCalcsNPaid").html(res.summCalc);

                    }else{
                        //$("#tabs_notes_"+permission+"_"+worker).css("display", "none");
                        $("#tabs_notes_"+permission+"_"+worker+"_"+office).css("display", "none");
                    }

                    if (res.status == 0){
                        thisObj.html("Нет данных по необработанным расчетным листам");

                        //Спрячем пустые вкладки, где нет данных

                        //console.log($(".tabs-"+permission+"_"+worker+"_"+office).css("display"));

                        //$(".tabs-"+permission+"_"+worker+"_"+office).hide();
                    }
                }

                if(res.result == 'error'){
                    thisObj.html(res.data);


                }

                //!!! тест. Разблокируем страницу, когда все загрузилось
                //blockWhileWaiting (false);
            }
        });
    }

    //Получаем необработанные расчетные листы v2.0
    function getCalculatesfunc2 (reqData){
        //  console.log(reqData);

        $.ajax({
            url:"fl_get_calculates2_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //thisObj.html("<div style='width: 120px; height: 32px; padding: 5px 10px 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка<br>расч. листов</span></div>");
            },
            success:function(res){
                // console.log(res);
                //console.log(reqData.worker);
                // if (reqData.worker == 492) {
                //     console.log(res.query);
                // }
                //$("#tabs-"+reqData.permission+"_"+reqData.worker).html(res);

                if(res.result == 'success'){
                    //$("#tabs-"+reqData.permission+"_"+reqData.worker).html(res);

                    //!!! Размер объекта JS
                    //console.log(Object.keys(res.data).length);

                    if (Object.keys(res.data).length > 0){

                        var data = res.data;

                        for(var filial_id in data){
                            //console.log(filial_id);
                            //console.log(data[filial_id]);
                            //console.log("#"+reqData.permission+"_"+reqData.worker+"_"+filial_id);

                            $("#"+reqData.permission+"_"+reqData.worker+"_"+filial_id).html(data[filial_id].data);

                            //Показываем оповещения на фио и филиале
                            $("#tabs_notes_"+reqData.permission+"_"+reqData.worker).css("display", "inline-block");
                            $("#tabs_notes_"+reqData.permission+"_"+reqData.worker+"_"+filial_id).css("display", "inline-block");

                        }
                    }

                }

/*                if(res.result == 'success'){

                    ids = thisObj.attr("id");
                    ids_arr = ids.split("_");
                    //console.log(ids_arr);

                    permission = ids_arr[0];
                    worker = ids_arr[1];
                    office = ids_arr[2];

                    if (res.status == 1){
                        thisObj.html(res.data);

                        //Показываем оповещения на фио и филиале
                        $("#tabs_notes_"+permission+"_"+worker).css("display", "inline-block");
                        $("#tabs_notes_"+permission+"_"+worker+"_"+office).css("display", "inline-block");

                        thisObj.parent().find(".summCalcsNPaid").html(res.summCalc);

                    }else{
                        $("#tabs_notes_"+permission+"_"+worker+"_"+office).css("display", "none");
                    }

                    if (res.status == 0){
                        thisObj.html("Нет данных по необработанным расчетным листам");

                        //Спрячем пустые вкладки, где нет данных

                        //console.log($(".tabs-"+permission+"_"+worker+"_"+office).css("display"));

                        //$(".tabs-"+permission+"_"+worker+"_"+office).hide();
                    }
                }

                if(res.result == 'error'){
                    thisObj.html(res.data);


                }*/

                //!!! тест. Разблокируем страницу, когда все загрузилось
                //blockWhileWaiting (false);
            }
        });
    }


    //!!!!тест разбор
    /*var openedWindow;
    function iOpenNewWindow(url, name, options){


        openedWindow = window.open(url, name, options);

        if (openedWindow.focus){
            openedWindow.focus();
        }

        WaitForCloseWindow(openedWindow);
    }

    function WaitForCloseWindow(openedWindow){
        if(!openedWindow.closed){
            setTimeout("WaitForCloseWindow(openedWindow)", 300);
        }else{
            alert(" Closed!");
        }
    }*/

    //Суммируем все поля в отчете
    function calculateDailyReportSumm(){

        var summNal = 0;
        var summBeznal = 0;
        var summ = 0;

        //Готовые суммы из отчёта "Касса" нал
        $(".allSummNal").each(function(){
            summNal += Number($(this).html());
            summ += Number($(this).html());
        });
        //Готовые суммы из отчёта "Касса" безнал
        $(".allSummBeznal").each(function(){
            summBeznal += Number($(this).html());
            summ += Number($(this).html());
        });

        //Суммы ручной ввод  из отчёта "Касса" нал
        $(".allSummInputNal").each(function(){
            summNal += Number($(this).val());
            summ += Number($(this).val());
        });
        //Суммы ручной ввод  из отчёта "Касса" безнал
        $(".allSummInputBeznal").each(function(){
            summBeznal += Number($(this).val());
            summ += Number($(this).val());
        });

        //Общая сумма по кассе до вычета расходов, должно совпасть с Z-отчетом
        $("#allsummKassa").html(number_format(summ, 2, '.', ' '));

        //summ = summ - $(".summMinus").val();
        summ = summ - $(".summMinus").html();

        $("#SummNal").html(number_format(summNal, 2, '.', ' '));
        $("#SummBeznal").html(number_format(summBeznal, 2, '.', ' '));

        //Остаток наличных по кассе минус расходы
        summNal = summNal - Number($(".summMinus").html());
        $("#SummNalOstatok").html(number_format(summNal, 2, '.', ' '));

        //Общая сумма без аренды
        $("#allsumm").html(number_format(summ, 2, '.', ' '));

        //Итоговые сумма
        $(".itogSummInputNal").each(function(){
            summ += Number($(this).val());
            summNal += Number($(this).val());
        });
        //console.log(summ);

        $("#itogSummShow").html(number_format(summ, 2, '.', ' '));
        $("#itogSumm").val(number_format(summ, 2, '.', ' '));

        //Остаток наличных по кассе минус расходы + аренда
        $("#itogSummNalShow").html(number_format(summ-summBeznal, 2, '.', ' '));

    }

    //!!! Эта функция и следующая - фактически одинаковые
    //Добавление ежедневного отчёта в бд
    function fl_createDailyReport_add(){
        //console.log($("#allsumm").html().replace(/\s{2,}/g, ''));

        //убираем ошибки
        hideAllErrors ();

        let link = "fl_createDailyReport_add_f.php";

        let filial_id = $("#SelectFilial").val();

        let reqData = {
            date: $("#iWantThisDate2").val(),
            filial_id: filial_id,
            itogSumm: $("#itogSumm").val(),
            arenda: $("#arendaNal").val(),
            zreport: $("#zreport").val(),
            allsumm: $("#allsumm").html(),

            SummNal: $("#SummNal").html(),
            SummBeznal: $("#SummBeznal").html(),

            SummNalStomCosm: $("#SummNalStomCosm").html(),
            SummBeznalStomCosm: $("#SummBeznalStomCosm").html(),

            CertCount: $("#CertCount").html(),
            SummCertNal: $("#SummCertNal").html(),
            SummCertBeznal: $("#SummCertBeznal").html(),

            AbonCount: $("#AbonCount").html(),
            SummAbonNal: $("#SummAbonNal").html(),
            SummAbonBeznal: $("#SummAbonBeznal").html(),

            SolarCount: $("#SolarCount").html(),
            SummSolarNal: $("#SummSolarNal").html(),
            SummSolarBeznal: $("#SummSolarBeznal").html(),

            RealizCount: $("#RealizCount").html(),
            SummRealizNal: $("#SummRealizNal").html(),
            SummRealizBeznal: $("#SummRealizBeznal").html(),


            ortoSummNal: $("#ortoSummNal").val(),
            ortoSummBeznal: $("#ortoSummBeznal").val(),

            specialistSummNal: $("#specialistSummNal").val(),
            specialistSummBeznal: $("#specialistSummBeznal").val(),

            analizSummNal: $("#analizSummNal").val(),
            analizSummBeznal: $("#analizSummBeznal").val(),

            solarSummNal: $("#solarSummNal").val(),
            solarSummBeznal: $("#solarSummBeznal").val(),

            summMinusNal: $("#summMinusNal").html()
            /*,
            bankSummNal: $("#bankSummNal").html(),
            directorSummNal: $("#directorSummNal").html()*/
        };
        //console.log(reqData);

        let day = $("#iWantThisDate2").val().split(".")[0];
        let month = $("#iWantThisDate2").val().split(".")[1];
        let year = $("#iWantThisDate2").val().split(".")[2];

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == 'success') {
                    //console.log('success');

                    //Счётчики
                    if ($("#haveLamps").val() == 1){
                        //console.log($("#haveLamps").val());

                        let link4Lamp = "fl_createLampReport_add_f.php";

                        let dataLamp = {};

                        $(".lampCount").each(function(){
                            // console.log($(this).attr("id"));
                            // console.log($(this).val());
                            // console.log($(this).attr("id").split('_')[2]);
                            // console.log($(this).attr("id").split('_')[1]);

                            if (!($(this).attr("id").split('_')[2] in dataLamp)){
                                dataLamp[$(this).attr("id").split('_')[2]] = {}
                            }
                            dataLamp[$(this).attr("id").split('_')[2]][$(this).attr("id").split('_')[1]] = $(this).val();
                        })

                        let reqData4Lamp = {
                            date: $("#iWantThisDate2").val(),
                            filial_id: filial_id,
                            dataLamp: dataLamp
                        }
                        // console.log(reqData4Lamp);

                        $.ajax({
                            url: link4Lamp,
                            global: false,
                            type: "POST",
                            dataType: "JSON",
                            data: reqData4Lamp,
                            cache: false,
                            beforeSend: function() {
                                //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                            },
                            // действие, при ответе с сервера
                            success: function(res2){
                                // $('#errrror').html(res);
                                // console.log(res2.data);

                                //Ответ показываем от добавления самого отчёта
                                $('#data').html(res.data);

                                setTimeout(function () {
                                    //window.location.replace('stat_cashbox.php');
                                    window.location.replace('fl_consolidated_report_admin.php?filial_id='+filial_id+'&m='+month+'&y='+year);
                                    //console.log('client.php?id='+id);

                                }, 500);
                            }
                        });
                    }

                }else{
                    //console.log('error');
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Редактирование ежедневного отчёта в бд
    function fl_editDailyReport_add(report_id, day, month, year){

        //убираем ошибки
        hideAllErrors ();

        let link = "fl_editDailyReport_add_f.php";

        let filial_id = $("#SelectFilial").val();

        let reqData = {
            report_id: report_id,
            itogSumm: $("#itogSumm").val(),
            arenda: $("#arendaNal").val(),
            zreport: $("#zreport").val(),
            allsumm: $("#allsumm").html(),
            SummNal: $("#SummNal").html(),
            SummBeznal: $("#SummBeznal").html(),
            SummNalStomCosm: $("#SummNalStomCosm").html(),
            SummBeznalStomCosm: $("#SummBeznalStomCosm").html(),
            CertCount: $("#CertCount").html(),
            SummCertNal: $("#SummCertNal").html(),
            SummCertBeznal: $("#SummCertBeznal").html(),
            ortoSummNal: $("#ortoSummNal").val(),
            ortoSummBeznal: $("#ortoSummBeznal").val(),
            specialistSummNal: $("#specialistSummNal").val(),
            specialistSummBeznal: $("#specialistSummBeznal").val(),
            analizSummNal: $("#analizSummNal").val(),
            analizSummBeznal: $("#analizSummBeznal").val(),
            solarSummNal: $("#solarSummNal").val(),
            solarSummBeznal: $("#solarSummBeznal").val(),
            //summMinusNal: $("#summMinusNal").val()
            summMinusNal: $("#summMinusNal").html()
        };

        // let day = $("#iWantThisDate2").val().split(".")[0];
        // let month = $("#iWantThisDate2").val().split(".")[1];
        // let year = $("#iWantThisDate2").val().split(".")[2];

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == 'success') {
                    //console.log('success');
                    // $('#data').html(res.data);
                    // setTimeout(function () {
                    //     //window.location.replace('stat_cashbox.php');
                    //     window.location.replace('fl_consolidated_report_admin.php?filial_id='+filial_id+'&m='+month+'&y='+year);
                    //     //console.log('client.php?id='+id);
                    // }, 500);

                    //Счётчики
                    if ($("#haveLamps").val() == 1){
                        //console.log($("#haveLamps").val());

                        let link4Lamp = "fl_editLampReport_add_f.php";

                        let dataLamp = {};

                        $(".lampCount").each(function(){
                            // console.log($(this).attr("id"));
                            // console.log($(this).val());
                            // console.log($(this).attr("id").split('_')[2]);
                            // console.log($(this).attr("id").split('_')[1]);

                            if (!($(this).attr("id").split('_')[2] in dataLamp)){
                                dataLamp[$(this).attr("id").split('_')[2]] = {}
                            }
                            dataLamp[$(this).attr("id").split('_')[2]][$(this).attr("id").split('_')[1]] = $(this).val();
                        })

                        let reqData4Lamp = {
                            day: day,
                            month: month,
                            year: year,
                            filial_id: filial_id,
                            dataLamp: dataLamp
                        }
                        // console.log(reqData4Lamp);

                        $.ajax({
                            url: link4Lamp,
                            global: false,
                            type: "POST",
                            dataType: "JSON",
                            data: reqData4Lamp,
                            cache: false,
                            beforeSend: function() {
                                //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                            },
                            // действие, при ответе с сервера
                            success: function(res2){
                                // $('#errrror').html(res);
                                // console.log(res2.data);

                                //Ответ показываем от добавления самого отчёта
                                $('#data').html(res.data);

                                setTimeout(function () {
                                    //window.location.replace('stat_cashbox.php');
                                    window.location.replace('fl_consolidated_report_admin.php?filial_id='+filial_id+'&m='+month+'&y='+year);
                                    //console.log('client.php?id='+id);

                                }, 500);
                            }
                        });
                    }



                }else{
                    //console.log('error');
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавление рабочих часов сотрудникам на филиале
    function fl_createSchedulerReport_add(type){
        //console.log($("#allsumm").html().replace(/\s{2,}/g, ''));

        //убираем ошибки
        hideAllErrors ();

        //Куда возвращаемся
        var res_location = 'scheduler3.php';
        if (type == 11){
            res_location = 'scheduler4.php';
        }

        var errors = false;

        //Соберём данные по часам
        var workerHoursValues_arr = {};
        var workerTypesValues_arr = {};

        $(".workerHoursValue").each(function(){
            // console.log($(this).attr('worker_id'));
            // console.log($(this).attr('worker_type'));
            // console.log($(this).val());

            if (isNaN($(this).val())){
                errors = true;

                //console.log("#hours_"+$(this).attr('worker_id')+"_num_error");

                $("#hours_"+$(this).attr('worker_id')+"_num_error").html("В этом поле ошибка");
                $("#hours_"+$(this).attr('worker_id')+"_num_error").show();
            }else{
                //Часов должно быть хоть сколько-нибудь
                if (($(this).val() > 0) || (($(this).val() && (($(this).attr('worker_type') == 1) || ($(this).attr('worker_type') == 9) || ($(this).attr('worker_type') == 11) || ($(this).attr('worker_type') == 12) || ($(this).attr('worker_type') == 777))))){
                    workerHoursValues_arr[$(this).attr('worker_id')] = $(this).val();
                    workerTypesValues_arr[$(this).attr('worker_id')] = $(this).attr('worker_type');
                }else{
                    errors = true;
                }
            }
        });

        if (!errors) {
            //console.log(workerHoursValues_arr);
            //console.log(workerTypesValues_arr);

            var link = "fl_createSchedulerReport_add_f.php";

            var filial_id = $("#SelectFilial").val();

            var reqData = {
                date: $("#iWantThisDate2").val(),
                filial_id: filial_id,
                workers_hours_data: workerHoursValues_arr,
                workers_types_data: workerTypesValues_arr
            };
            //console.log(reqData);

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function () {
                    //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res.data);

                    //!!! Переделать тут нормально
                    if (res.result == 'success') {
                        //console.log('success');
                        $('#data').html(res.data);
                        setTimeout(function () {
                            //window.location.replace('stat_cashbox.php');
                            //window.location.replace('scheduler3.php?filial_id='+filial_id);
                            window.location.href = res_location+'?filial=' + filial_id;
                            //console.log('client.php?id='+id);
                        }, 500);
                    } else {
                        //console.log('error');
                        $('#errrror').html(res.data);
                        //$('#errrror').html('');
                    }
                }
            });
        }else{
            $("#errrror").html('<div class="query_neok">Ошибка, что-то заполнено не так. Часов должно быть большо 0.</div>')
        }

        setTimeout('$("#fl_createSchedulerReport_add").removeAttr("disabled")', 1500);
    }

    //Редактирование рабочих часов сотрудникам на филиале
    function fl_editSchedulerReport_add(report_ids, type){
        //console.log(report_ids);

        //убираем ошибки
        hideAllErrors ();

        //Куда возвращаемся
        var res_location = 'scheduler3.php';
        if (type == 11){
            res_location = 'scheduler4.php';
        }
        if (type == 999){
            res_location = 'scheduler5.php';
        }

        var errors = false;

        //Соберём данные по часам
        var workerHoursValues_arr = {};
        var workerTypesValues_arr = {};

        // $(".workerHoursValue").each(function(){
        //     // console.log($(this).attr('worker_id'));
        //     // console.log(Number($(this).val().replace(',', '.')));
        //
        //     workerHoursValues_arr[$(this).attr('worker_id')] = $(this).val().replace(',', '.');
        //     workerTypesValues_arr[$(this).attr('worker_id')] = $(this).attr('worker_type');
        //
        // });

        $(".workerHoursValue").each(function(){
            // console.log($(this).attr('worker_id'));
            // console.log($(this).val());

            if (isNaN($(this).val())){
                errors = true;

                //console.log("#hours_"+$(this).attr('worker_id')+"_num_error");

                $("#hours_"+$(this).attr('worker_id')+"_num_error").html("В этом поле ошибка");
                $("#hours_"+$(this).attr('worker_id')+"_num_error").show();
            }else{
                //Часов должно быть хоть сколько-нибудь
                if (($(this).val() > 0) || (($(this).val() && (($(this).attr('worker_type') == 1) || ($(this).attr('worker_type') == 9) || ($(this).attr('worker_type') == 11) || ($(this).attr('worker_type') == 12) || ($(this).attr('worker_type') == 777))))){
                    workerHoursValues_arr[$(this).attr('worker_id')] = $(this).val();
                    workerTypesValues_arr[$(this).attr('worker_id')] = $(this).attr('worker_type');
                }else{
                    errors = true;
                }
            }
        });
        //console.log(workerHoursValues_arr);

        if (!errors) {
            var link = "fl_editSchedulerReport_add_f.php";

            var filial_id = $("#SelectFilial").val();

            var reqData = {
                report_ids: report_ids,
                date: $("#iWantThisDate2").val(),
                filial_id: filial_id,
                workers_hours_data: workerHoursValues_arr,
                workers_types_data: workerTypesValues_arr
            };
            //console.log(reqData);

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function () {
                    //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == 'success') {
                        //console.log('success');
                        $('#data').html(res.data);
                        setTimeout(function () {
                            //window.location.replace('stat_cashbox.php');
                            //window.location.replace('scheduler3.php?filial_id='+filial_id);
                            window.location.href = res_location + '?filial=' + filial_id;
                            //console.log('client.php?id='+id);
                        }, 500);
                    } else {
                        //console.log('error');
                        $('#errrror').html(res.data);
                        //$('#errrror').html('');
                    }
                }
            });
        }else{
            $("#errrror").html('<div class="query_neok">Ошибка, что-то заполнено не так. Часов должно быть большо 0.</div>')
        }

        setTimeout('$("#fl_editSchedulerReport_add").removeAttr("disabled")', 1500);
    }

    //Удалить часы за смену по id
    function fl_deleteSchedulerReportItem(report_id){
        //console.log(report_id);

        var rys = false;

        rys = confirm("Вы хотите удалить часы сотрудника за смену. \n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "scheduler_report_del_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    report_id: report_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    if (res.result == "success") {
                        location.reload();
                        //console.log(res.data);
                    }
                    if (res.result == "error") {
                        //alert(res.data);
                    }
                    //console.log(data.data);

                }
            });
        }
    }



    //Подсчет итогов за месяц
    function fl_getDailyReportsSummAllMonth(filial_id, month, year){


        //Все выплаты
        var link = "fl_get_giveouts_f.php";

        var reqData = {
            filial_id: filial_id,
            month: month,
            year: year
        };
        //console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                console.log(res);
                //$('#errrror').html(res.subtractions_j);
                // console.log(res.subtractions_j);
                //console.log(res.subtractions_j.length);

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#errrror').html(res.data);

                    //var prev_month_filial_summ = number_format((res.prev_month_filial_summ), 2, '.', ' ');
                    var prev_month_filial_summ = Number(res.prev_month_filial_summ);

                    //var subtractionsSumm_arr = [];

                    var SummPrepayment = 0, SummHolidayPay = 0, SummHospitalPay = 0, SummSalary = 0, SummRefund = 0, SummWithdraw = 0;

                    var subtractions = res.subtractions_j;

                    if (subtractions.length > 0){
                        for(var i = 0; i < subtractions.length; i++){
                            // console.log(subtractions[i].type);
                            // if (subtractionsSumm_arr.indexOf(subtractions[i].type) == -1) {
                            //     //console.log(subtractions[i].type);
                            //     subtractionsSumm_arr[subtractions[i].type] = 0;
                            // }
                            // //Плюсуем
                            // if (subtractions[i].type == 1) {
                            //     console.log(parseFloat(subtractionsSumm_arr[subtractions[i].type]));
                            //     console.log(parseFloat(subtractions[i].summ));
                            //     console.log(subtractionsSumm_arr[subtractions[i].type] + parseFloat(subtractions[i].summ));
                            //     console.log('//////////////////////////');
                            //
                            //     subtractionsSumm_arr[subtractions[i].type] = parseFloat(subtractionsSumm_arr[subtractions[i].type]) + parseFloat(subtractions[i].summ);
                            //     console.log(subtractionsSumm_arr[subtractions[i].type]);
                            //     console.log('---------------');
                            //     console.log(subtractionsSumm_arr);
                            // }

                            if (subtractions[i].type == 1){
                                SummPrepayment += parseFloat(subtractions[i].summ)
                            }
                            if (subtractions[i].type == 2){
                                SummHolidayPay += parseFloat(subtractions[i].summ)
                            }
                            if (subtractions[i].type == 3){
                                SummHospitalPay += parseFloat(subtractions[i].summ)
                            }
                            if (subtractions[i].type == 7){
                                SummSalary += parseFloat(subtractions[i].summ)
                            }
                        }
                    }

                    //Выдачи (возвраты) денег пациентам
                    var withdraws = res.withdraw_j;

                    if (withdraws.length > 0){
                        for(var i = 0; i < withdraws.length; i++){

                            SummWithdraw += parseFloat(withdraws[i].summ)

                        }
                    }
                    // console.log(SummPrepayment);
                    // console.log(SummHolidayPay);
                    // console.log(SummHospitalPay);
                    // console.log(SummSalary);
                    // console.log(SummRefund);
                    // console.log(SummWithdraw);


                    //
                    $("#itogSummAllMonth").html(0);
                    $("#arendaAllMonth").html(0);               $("#arendaAllMonthItog").html(0);       $("#arendaAllMonthItog2").html(0);
                    $("#zReportAllMonth").html(0);
                    $("#allSummAllMonth").html(0);
                    $("#SummNalAllMonth").html(0);              $("#SummNalAllMonthItog").html(0);      $("#SummNalAllMonthItog2").html(0);
                    $("#SummBeznalAllMonth").html(0);           $("#SummBeznalAllMonthItog").html(0);
                    $("#SummNalStomCosmMonth").html(0);
                    $("#SummBeznalStomCosmAllMonth").html(0);
                    $("#SummCertNalAllMonth").html(0);
                    $("#SummCertBeznalAllMonth").html(0);
                    $("#ortoSummNalAllMonth").html(0);
                    $("#ortoSummBeznalAllMonth").html(0);
                    $("#specialistSummNalAllMonth").html(0);
                    $("#specialistSummBeznalAllMonth").html(0);
                    $("#analizSummNalAllMonth").html(0);
                    $("#analizSummBeznalAllMonth").html(0);
                    $("#solarSummNalAllMonth").html(0);
                    $("#solarSummBeznalAllMonth").html(0);
                    $("#summMinusNalAllMonth").html(0);

                    $("#summGiveoutInBank").html(0);
                    $("#summGiveoutDirector").html(0);

                    $("#itogSummNalAllMonth").html(0);
                    $("#SummNalStomCosmAllMonth").html(0);



                    $("#prev_month_filial_summ").html(number_format((prev_month_filial_summ), 2, '.', ' '));


                    //- Итог общий
                    $(".itogSumm").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var itogSummAllMonth = Number($("#itogSummAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#itogSummAllMonth").html(number_format((itogSummAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- Итог общий нал
                    $(".itogSummNal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var itogSummNalAllMonth = Number($("#itogSummNalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#itogSummNalAllMonth").html(number_format((itogSummNalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- Аренда
                    $(".arenda").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var arendaAllMonth = Number($("#arendaAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#arendaAllMonth").html(number_format((arendaAllMonth + thisSumm), 2, '.', ' '));

                            $("#arendaAllMonthItog").html(number_format((arendaAllMonth + thisSumm), 2, '.', ' '));
                            $("#arendaAllMonthItog2").html(number_format((arendaAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- z-отчет
                    $(".zReport").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var zReportAllMonth = Number($("#zReportAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#zReportAllMonth").html(number_format((zReportAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- общая сумма
                    $(".allSumm").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var allSummAllMonth = Number($("#allSummAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#allSummAllMonth").html(number_format((allSummAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- сумма нал
                    $(".SummNal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var SummNalAllMonth = Number($("#SummNalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#SummNalAllMonth").html(number_format((SummNalAllMonth + thisSumm), 2, '.', ' '));

                            $("#SummNalAllMonthItog").html(number_format((SummNalAllMonth + thisSumm), 2, '.', ' '));
                            $("#SummNalAllMonthItog2").html(number_format((SummNalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- сумма безнал
                    $(".SummBeznal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var SummBeznalAllMonth = Number($("#SummBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#SummBeznalAllMonth").html(number_format((SummBeznalAllMonth + thisSumm), 2, '.', ' '));

                            $("#SummBeznalAllMonthItog").html(number_format((SummBeznalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- ордеры нал
                    $(".SummNalStomCosm").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var SummNalStomCosmAllMonth = Number($("#SummNalStomCosmAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#SummNalStomCosmAllMonth").html(number_format((SummNalStomCosmAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- ордеры безнал
                    $(".SummBeznalStomCosm").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var SummBeznalStomCosmAllMonth = Number($("#SummBeznalStomCosmAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#SummBeznalStomCosmAllMonth").html(number_format((SummBeznalStomCosmAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- сертификаты нал
                    $(".SummCertNal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var SummCertNalAllMonth = Number($("#SummCertNalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#SummCertNalAllMonth").html(number_format((SummCertNalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- сертификаты безнал
                    $(".SummCertBeznal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var SummCertBeznalAllMonth = Number($("#SummCertBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#SummCertBeznalAllMonth").html(number_format((SummCertBeznalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- орто нал
                    $(".ortoSummNal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var ortoSummNalAllMonth = Number($("#ortoSummNalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#ortoSummNalAllMonth").html(number_format((ortoSummNalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- орто безнал
                    $(".ortoSummBeznal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var ortoSummBeznalAllMonth = Number($("#ortoSummBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#ortoSummBeznalAllMonth").html(number_format((ortoSummBeznalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- специалисты нал
                    $(".specialistSummNal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var specialistSummNalAllMonth = Number($("#specialistSummNalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#specialistSummNalAllMonth").html(number_format((specialistSummNalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- специалисты безнал
                    $(".specialistSummBeznal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var specialistSummBeznalAllMonth = Number($("#specialistSummBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#specialistSummBeznalAllMonth").html(number_format((specialistSummBeznalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- анализы нал
                    $(".analizSummNal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var analizSummNalAllMonth = Number($("#analizSummNalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#analizSummNalAllMonth").html(number_format((analizSummNalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- анализы безнал
                    $(".analizSummBeznal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var analizSummBeznalAllMonth = Number($("#analizSummBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#analizSummBeznalAllMonth").html(number_format((analizSummBeznalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- солярий нал
                    $(".solarSummNal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var solarSummNalAllMonth = Number($("#solarSummNalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#solarSummNalAllMonth").html(number_format((solarSummNalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- солярий безнал
                    $(".solarSummBeznal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var solarSummBeznalAllMonth = Number($("#solarSummBeznalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#solarSummBeznalAllMonth").html(number_format((solarSummBeznalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- расход
                    $(".summMinusNal").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));
                        //console.log(Number($(this).html()));
                        //console.log(Number($(this).html().replace(/\s{1,}/g, '')));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var summMinusNalAllMonth = Number($("#summMinusNalAllMonth").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#summMinusNalAllMonth").html(number_format((summMinusNalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- Выдачи в банк
                    $(".giveout_inBank").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));
                        //console.log(Number($(this).html()));
                        //console.log(Number($(this).html().replace(/\s{1,}/g, '')));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var summMinusNalAllMonth = Number($("#summGiveoutInBank").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#summGiveoutInBank").html(number_format((summMinusNalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });
                    //- Выдачи АНу
                    $(".giveout_director").each(function(){
                        //console.log($(this).html().replace(/\s{1,}/g, ''));
                        //console.log(Number($(this).html()));
                        //console.log(Number($(this).html().replace(/\s{1,}/g, '')));

                        if (!isNaN(Number($(this).html().replace(/\s{1,}/g, '')))) {
                            var summMinusNalAllMonth = Number($("#summGiveoutDirector").html().replace(/\s{1,}/g, ''));
                            var thisSumm = Number($(this).html().replace(/\s{1,}/g, ''));

                            $("#summGiveoutDirector").html(number_format((summMinusNalAllMonth + thisSumm), 2, '.', ' '));

                        }
                    });

                    //- Вся выручка
                    $("#summAllMonth").html(number_format(
                        (Number(
                                $("#SummNalAllMonthItog").html().replace(/\s{1,}/g, '')
                            )
                            +
                            Number(
                                $("#arendaAllMonthItog").html().replace(/\s{1,}/g, '')
                            )
                            +
                            Number(
                                $("#SummBeznalAllMonthItog").html().replace(/\s{1,}/g, '')
                            )
                        )
                        , 2, '.', ' ')
                    );
                    //- Все расходы
                    $("#summMinusAllMonth").html(number_format(
                        (Number(
                                $("#summMinusNalAllMonth").html().replace(/\s{1,}/g, '')
                            )
                            +
                            Number(
                                $("#summGiveoutInBank").html().replace(/\s{1,}/g, '')
                            )
                            +
                            Number(
                                $("#summGiveoutDirector").html().replace(/\s{1,}/g, '')
                            )
                        )
                        , 2, '.', ' ')
                    );
                    //- Итог наличка
                    $("#ostatokNalAllMonth").html(number_format(
                        (Number(
                                $("#SummNalAllMonthItog2").html().replace(/\s{1,}/g, '')
                            )
                            +
                            Number(
                                $("#arendaAllMonthItog2").html().replace(/\s{1,}/g, '')
                            )
                            -
                            Number(
                                $("#summMinusAllMonth").html().replace(/\s{1,}/g, '')
                            )
                            +
                            prev_month_filial_summ
                        )
                        , 2, '.', ' ')
                    );

                    //Выводим выдачи
                    $("#SummPrepaymentGiveout").html(number_format((SummPrepayment), 2, '.', ' '));
                    $("#SummHolidayPayGiveout").html(number_format((SummHolidayPay), 2, '.', ' '));
                    $("#SummHospitalPayGiveout").html(number_format((SummHospitalPay), 2, '.', ' '));
                    $("#SummSalaryGiveout").html(number_format((SummSalary), 2, '.', ' '));
                    //$("#SummRefundGiveout").html(number_format((SummRefund), 2, '.', ' '));
                    $("#SummWithdrawGiveout").html(number_format((SummWithdraw), 2, '.', ' '));


                    $("#SummGiveoutMonth").html(number_format((SummPrepayment + SummHolidayPay + SummHospitalPay + SummSalary + SummRefund + SummWithdraw), 2, '.', ' '));

                    // console.log(Number($("#ostatokNalAllMonth").html().replace(/\s{1,}/g, '')));
                    // console.log(Number($("#SummGiveoutMonth").html().replace(/\s{1,}/g, '')));
                    // console.log(Number(prev_month_filial_summ));

                    $("#ostatokFinalNalAllMonth").html(number_format(
                        (Number(
                                $("#ostatokNalAllMonth").html().replace(/\s{1,}/g, '')
                            )
                        )
                        , 2, '.', ' ')
                    );

                    $("#ostatokFinalNalAllMonth2").html(number_format(
                        (Number(
                                $("#ostatokNalAllMonth").html().replace(/\s{1,}/g, '')
                            )
                         )
                        , 2, '.', ' ')
                    );



                    //Выдачи из кассы (подробно за месяц)
                    var giveouts_j = res.giveouts_j;

                    $("#giveout_cash").html(giveouts_j);


                    //Промежуточные (примерные) итоги показываем
                    //$("#interimReport").html();
                    $("#interimReport").show();

                }else{
                    //console.log('error');
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Функция сравнения с реальностью сохраненных ежеднеынх отчетов (Если были изменения)
    ///!!! не используем
    //function checkConsolidateReports(date, filial_id){
        //console.log(date+'/'+filial_id);
    //}

    //Получение отчёта по какому-то дню из филиала и заполнение отчета
    function fl_getDailyReports(thisObj){

        //Дата
        var date = (thisObj.find(".reportDate").html().replace(/\s{2,}/g, ''));
        //console.log(date);
        //console.log(getTodayDate());

        //Блоки, где будут:
        //- Итог общий
        var itogSumm = (thisObj.find(".itogSumm"));
        //- Итог общий нал
        var itogSummNal = (thisObj.find(".itogSummNal"));
        //- Аренда
        var arenda = (thisObj.find(".arenda"));
        //- z-отчет
        var zReport = (thisObj.find(".zReport"));
        //- общая сумма
        var allSumm = (thisObj.find(".allSumm"));
        //- сумма нал
        var SummNal = (thisObj.find(".SummNal"));
        //- сумма безнал
        var SummBeznal = (thisObj.find(".SummBeznal"));
        //- сумма нал стом+косм
        var SummNalStomCosm = (thisObj.find(".SummNalStomCosm"));
        //- сумма безнал стом+косм
        var SummBeznalStomCosm = (thisObj.find(".SummBeznalStomCosm"));
        //- сертификаты нал
        var SummCertNal = (thisObj.find(".SummCertNal"));
        //- сертификаты безнал
        var SummCertBeznal = (thisObj.find(".SummCertBeznal"));
        //- орто нал
        var ortoSummNal = (thisObj.find(".ortoSummNal"));
        //- орто безнал
        var ortoSummBeznal = (thisObj.find(".ortoSummBeznal"));
        //- специалисты нал
        var specialistSummNal = (thisObj.find(".specialistSummNal"));
        //- специалисты безнал
        var specialistSummBeznal = (thisObj.find(".specialistSummBeznal"));
        //- анализы нал
        var analizSummNal = (thisObj.find(".analizSummNal"));
        //- анализы безнал
        var analizSummBeznal = (thisObj.find(".analizSummBeznal"));
        //- солярий нал
        var solarSummNal = (thisObj.find(".solarSummNal"));
        //- солярий безнал
        var solarSummBeznal = (thisObj.find(".solarSummBeznal"));
        //- расход
        var summMinusNal = (thisObj.find(".summMinusNal"));

        //- Выдачи в банк
        var giveout_inBank = (thisObj.find(".giveout_inBank"));
        //- Выдачи АНу
        var giveout_director = (thisObj.find(".giveout_director"));

        //убираем ошибки
        hideAllErrors ();

        var link = "fl_getDailyReports_f.php";

        var reqData = {
            date: date,
            filial_id: $("#SelectFilial").val()
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //$('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);
                //console.log(res.count);
                //console.log(date);
                //console.log(res.real_summs);

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);
                    //console.log(res.count);
                    // console.log(res.data);
                    // console.log(res.giveout_bank);

                    if (res.count > 0){
                        //console.log(res.data);
                        //console.log(Object.size(res.data));
                        //console.log(Object.size(res.data));

                        thisObj.css({
                            "color": "#333"
                        });

                        //Закрываю, потому что буду отслеживать по count
                        //if (Object.size(res.data) > 0){}

                        var data = res.data[0];

                        //Если массив отчета не пустой
                        //if (date == getTodayDate()){
                        if (Object.size(data) > 0){



                            itogSumm.html               (number_format(data.itogSumm, 2, '.', ' ')).css({"text-align": "right"});
                            arenda.html                 (number_format(data.arenda, 0, '.', ' ')).css({"text-align": "right"});
                            zReport.html                (number_format(data.zreport, 2, '.', ' ')).css({"text-align": "right", "color": "rgb(18, 0, 255)"});
                            allSumm.html                (number_format(data.summ, 2, '.', ' ')).css({"text-align": "right"});
                            SummNal.html                (number_format(data.nal, 0, '.', ' ')).css({"text-align": "right"});
                            SummBeznal.html             (number_format(data.beznal, 0, '.', ' ')).css({"text-align": "right"});
                            SummNalStomCosm.html        (number_format(data.cashbox_nal, 0, '.', ' ')).css({"text-align": "right"});
                            SummBeznalStomCosm.html     (number_format(data.cashbox_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            SummCertNal.html            (number_format(data.cashbox_cert_nal, 0, '.', ' ')).css({"text-align": "right"});
                            SummCertBeznal.html         (number_format(data.cashbox_cert_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            ortoSummNal.html            (number_format(data.temp_orto_nal, 0, '.', ' ')).css({"text-align": "right"});
                            ortoSummBeznal.html         (number_format(data.temp_orto_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            specialistSummNal.html      (number_format(data.temp_specialist_nal, 0, '.', ' ')).css({"text-align": "right"});
                            specialistSummBeznal.html   (number_format(data.temp_specialist_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            analizSummNal.html          (number_format(data.temp_analiz_nal, 0, '.', ' ')).css({"text-align": "right"});
                            analizSummBeznal.html       (number_format(data.temp_analiz_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            solarSummNal.html           (number_format(data.temp_solar_nal, 0, '.', ' ')).css({"text-align": "right"});
                            solarSummBeznal.html        (number_format(data.temp_solar_beznal, 0, '.', ' ')).css({"text-align": "right"});
                            summMinusNal.html           (number_format(data.temp_giveoutcash, 2, '.', ' ')).css({"text-align": "right"});

                            //Итог наличные
                            var itog_summ_nal = Number(data.nal) + Number(data.arenda) - Number(data.temp_giveoutcash);
                            // console.log(data.nal);
                            // console.log(data.arenda);
                            // console.log(data.temp_giveoutcash);

                            itogSummNal.html            (number_format(itog_summ_nal, 0, '.', ' ')).css({"text-align": "right"});

                            //Прописываем статус отчета
                            $(thisObj).find(".reportDate").attr('status', data.status);
                            //И id
                            $(thisObj).find(".reportDate").attr('report_id', data.id);


                            //Теперь хотим сравнить с реальностью и поставить метку, если не совпадает
                            //checkConsolidateReports(date, $("#SelectFilial").val());

                            if ((Number(data.nal) != Number(res.real_summs['nal'])) || (Number(data.beznal) != Number(res.real_summs['beznal']))){
                                // console.log($(thisObj).find(".reportDate").html());
                                // console.log(Number(data.nal));
                                // console.log(Number(res.real_summs['nal']));
                                // console.log('---------------');
                                // console.log(Number(data.beznal));
                                // console.log(Number(res.real_summs['beznal']));
                                // console.log('/////////////////////////');

                                $(thisObj).find(".reportDate").css({
                                            "background-color": "rgba(47, 186, 239, 0.7)"
                                        });
                            }

                        }else{

                            thisObj.html('<div class="cellTime cellsTimereport reportDate" status="0" report_id="0" style="text-align: center; cursor: pointer; color: #333;">'+date+'</div>' +
                            '<div class="cellText" style="color: rgb(48, 185, 91); font-weight: normal; padding-left: 35px;"><i>Отчёт был заполнен и добавлен в архив, для изменений обратитесь к руководителю.</i></div>');

                        }

                        //Меняем цвет, если проверено
                        if (data.status == 7) {
                            $(thisObj).css({"background-color": "rgba(216, 255, 196, 0.98)"});
                            //блокируем ссылки
                            summMinusNal.css("pointer-events", "none");
                        }

                    }else{
                        //console.log(res.count);

                        itogSumm.html('-');
                        itogSummNal.html('-');
                        arenda.html('-');
                        zReport.html('-');
                        allSumm.html('-');
                        SummNal.html('-');
                        SummBeznal.html('-');
                        SummCertNal.html('-');
                        SummCertBeznal.html('-');
                        ortoSummNal.html('-');
                        ortoSummBeznal.html('-');
                        specialistSummNal.html('-');
                        specialistSummBeznal.html('-');
                        analizSummNal.html('-');
                        analizSummBeznal.html('-');
                        solarSummNal.html('-');
                        solarSummBeznal.html('-');
                        summMinusNal.html('-');
                    }

                    //Выдачи
                    if (res.giveout_bank > 0) {
                        giveout_inBank.html(number_format(res.giveout_bank, 0, '.', ' ')).css({"text-align": "right"});
                    }else{
                        giveout_inBank.html('-');
                    }
                    if (res.giveout_director > 0) {
                        giveout_director.html(number_format(res.giveout_director, 0, '.', ' ')).css({"text-align": "right"});
                    }else{
                        giveout_director.html('-');
                    }

                    //console.log(data);
                    //Если есть объект
                    if (data !== undefined) {
                        //Если в объекте есть ключ
                        //if ('status' in data) {
                            //Если ключ равен значению
                            if (data.status == 7) {
                                //блокируем ссылки

                                // console.log(giveout_inBank);
                                // console.log(giveout_director);
                                //console.log(giveout_inBank.html());
                                //console.log(giveout_director.html());

                                //!!! 2019-08-12 открыл ссылки на банк и АН
                                //giveout_inBank.css("pointer-events", "none");
                                //giveout_director.css("pointer-events", "none");
                            }
                        //}
                    }

                }else{
                    //console.log('error');
                    $('#errrror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Удаление ежедневного отчёта администраторов
    function fl_delete_consRepEdit(id){

        var reqData = {
            report_id: id
        };

        var link = "fl_deleteDailyReport_f.php";

        var rys = false;

        rys = confirm("Вы действительно хотите удалить отчёт?");

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                        //console.log(res.data);
                    }
                    if (res.result == "error") {
                        alert(res.data);
                    }
                    //console.log(data.data);

                }
            });
        }
    }

    //Установить статус проверено в ежедневный отчет администраторов
    function fl_check_consRepAdm(id){

        var reqData = {
            report_id: id
        };

        var link = "fl_checkDailyReport_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function (res) {
                //console.log(res);

                if (res.result == "success") {
                    location.reload();
                    //console.log(res.data);
                }
                if (res.result == "error") {
                    alert(res.data);
                }
                //console.log(data.data);

            }
        });

    }

    //Снять статус проверено в ежедневный отчет администраторов
    function fl_uncheck_consRepEdit(id){

        var reqData = {
            report_id: id
        };

        var link = "fl_uncheckDailyReport_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function (res) {
                //console.log(res);

                if (res.result == "success") {
                    location.reload();
                    //console.log(res.data);
                }
                if (res.result == "error") {
                    alert(res.data);
                }
                //console.log(data.data);

            }
        });

    }

    //Добавить ежедневный отчет администраторов
    function fl_add_consRepAdm(event){
        //console.log(event);

        var target = $(event.target);
        console.log(target);

        /*var reqData = {
            report_id: id
        };

        var link = "fl_uncheckDailyReport_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function (res) {
                //console.log(res);

                if (res.result == "success") {
                    location.reload();
                    //console.log(res.data);
                }
                if (res.result == "error") {
                    alert(res.data);
                }
                //console.log(data.data);

            }
        });*/

    }

    //Редактирование оклада / добавление новой строчки
    var el = document.getElementById("currentSalary"), newInput;

    if(el) {
        el.addEventListener("click", function () {
            //console.log(el);

            $("#addSalaryOptions").html("<span id='newSalarySave' class='button_tiny' style='color: rgb(125, 125, 125); font-size: 11px; cursor: pointer;'><i class='fa fa-check' aria-hidden='true' style='color: green;' title='Сохранить'></i> Применить</span> <span id='newSalaryCancel' class='button_tiny' style='color: rgb(125, 125, 125); font-size: 11px; cursor: pointer;'><i class='fa fa-times' aria-hidden='true' style='color: red;'></i> Отменить</span>");
            $("#addSalaryDate").show();
            $("#salaryText").html("Введите новое значение:");

            var thisVal = this.innerHTML;
            var newVal = thisVal;

            var inputs = this.getElementsByTagName("input");
            if (inputs.length > 0) return;
            if (!newInput) {

                newInput = document.createElement("input");
                newInput.type = "text";
                newInput.maxLength = 10;
                newInput.setAttribute("size", 20);
                newInput.style.width = "80px";
                newInput.style.fontSize = "18px";

                //Клик вне поля
                //newInput.addEventListener("blur", function () {

                $("body").on("click", "#newSalaryCancel", function (event) {
                    //console.log("blur");

                    //$("#textAfterSalary").html("руб.");
                    $("#addSalaryOptions").html("");
                    $("#addSalaryDate").hide();
                    $("#salaryText").html("Текущий оклад:");

                    newInput.parentNode.innerHTML = thisVal;
                    newVal = thisVal;
                });
                //}, false);

                $("body").on("click", "#newSalarySave", function (event) {
                    //alert();

                    newVal = parseInt(newInput.value, 10);

                    var link = $("#pass").val()+".php";
                    //console.log(link);

                    if (link == "fl_add_new_salary_f.php") {
                        var reqData = {
                            worker_id: $("#worker_id").val(),
                            date_from: $("#iWantThisDate2").val(),
                            /*category_id: $("#category_id").val(),*/
                            summ: newVal
                        };
                    }

                    if (link == "fl_add_new_salary_category_f.php") {
                        var reqData = {
                            category_id: $("#category_id").val(),
                            filial_id: $("#filial_id").val(),
                            permission_id: $("#permission_id").val(),
                            date_from: $("#iWantThisDate2").val(),
                            summ: newVal
                        };
                    }
                    //console.log(reqData);

                    $.ajax({
                        url: link,
                        global: false,
                        type: "POST",
                        dataType: "JSON",
                        data: reqData,
                        cache: false,
                        beforeSend: function () {
                            //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                        },
                        // действие, при ответе с сервера
                        success: function (res) {
                            if (res.result == "success") {
                                //console.log(res.data);

                                setTimeout(function () {
                                    location.reload();
                                }, 1000);

                            }

                        }
                    });

                });

            }

            //newInput.value = this.firstChild.innerHTML;
            newInput.value = thisVal;
            this.innerHTML = "";
            //this.appendChild(buttonDiv);
            this.appendChild(newInput);
            //newInput.innerHTML = ('<i class="fa fa-check" aria-hidden="true"></i>');
            newInput.focus();
            newInput.select();

        }.bind(el), false);
    }

    //Редактирование налога / добавление новой строчки
    //Для изменений в процентах персональных
    var changeTax_elems = document.getElementsByClassName("changeCurrentTax"), newInput;
    //console.log(elems);

    if (changeTax_elems.length > 0) {
        for (var i = 0; i < changeTax_elems.length; i++) {
            var el = changeTax_elems[i];
            el.addEventListener("click", function () {
                //var thisID = this.id;
                var workerID = this.getAttribute("worker_id");
                //console.log(this.getAttribute("worker_id"));
                //var catID = this.getAttribute("cat_id");
                //console.log(this.getAttribute("cat_id"));
                //var typeID = this.getAttribute("type_id");
                //console.log(this.getAttribute("type_id"));

                var thisVal = this.innerHTML;
                var newVal = thisVal;
                //console.log(this);
                //console.log(workerID);
                //console.log(catID);
                //console.log(typeID);
                //console.log(thisVal);
                //console.log(isNaN(thisVal));

                var inputs = this.getElementsByTagName("input");
                if (inputs.length > 0) return;
                if (!newInput) {

                    /*buttonDiv = document.createElement("div");
                     //buttonDiv.innerHTML = '<i class="fa fa-check" aria-hidden="true" title="Применить" style="margin-right: 4px;"></i> <i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>';
                     buttonDiv.innerHTML = '<i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>';
                     buttonDiv.style.position = "absolute";
                     buttonDiv.style.right = "-9px";
                     buttonDiv.style.top = "1px";
                     buttonDiv.style.fontSize = "12px";
                     buttonDiv.style.color = "green";
                     buttonDiv.style.border = "1px solid #BFBCB5";
                     buttonDiv.style.backgroundColor = "#FFF";
                     buttonDiv.style.padding = "0 6px";

                     buttonDiv.id = "changePersonalPercentCatdefault";*/

                    newInput = document.createElement("input");
                    newInput.type = "text";
                    newInput.maxLength = 10;
                    newInput.setAttribute("size", 20);
                    newInput.style.width = "50px";
                    newInput.addEventListener("blur", function () {
                        //console.log(newInput.parentNode.getAttribute("worker_id"));

                        workerID = newInput.parentNode.getAttribute("worker_id");
                        //catID = newInput.parentNode.getAttribute("cat_id");
                        //typeID = newInput.parentNode.getAttribute("type_id");

                        //Попытка обработать клика на кнопке для сброса на значения по умолчанию - провалилась, всегда сбрасывается на по умолчанию
                        //var changePersonalPercentCatdefault = document.getElementById("changePersonalPercentCatdefault");
                        //console.log(changePersonalPercentCatdefault.innerHTML);

                        //changePersonalPercentCatdefault.addEventListener("click", fl_changePersonalPercentCatdefault(workerID, catID, typeID), false);

                        //Новые данные
                        //if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (newInput.value > 100) || (isNaN(parseInt(newInput.value, 10)))) {
                        if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (isNaN(parseInt(newInput.value, 10)))) {
                            //newInput.parentNode.innerHTML = 0;
                            newInput.parentNode.innerHTML = thisVal;
                            newVal = thisVal;
                        } else {
                            newInput.parentNode.innerHTML = number_format(parseFloat(newInput.value, 10), 2, '.', '');
                            newVal = number_format(parseFloat(newInput.value, 10), 2, '.', '');
                        }
                        //console.log(this);
                        //console.log(workerID);

                        //console.log(thisVal == newVal);

                        if (Number(thisVal) != Number(newVal)) {
                            //console.log(newVal);

                            $.ajax({
                                url: "fl_change_personal_tax_f.php",
                                global: false,
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    worker_id: workerID,
                                    //cat_id: catID,
                                    //type: typeID,
                                    val: newVal
                                },
                                cache: false,
                                beforeSend: function () {
                                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                                },
                                // действие, при ответе с сервера
                                success: function (res) {
                                    if (res.result == "success") {
                                        //console.log(res);

                                        $('#infoDiv').html(res.data);
                                        $('#infoDiv').show();
                                        setTimeout(function () {
                                            $('#infoDiv').hide('slow');
                                            $('#infoDiv').html();
                                        }, 1000);

                                        //location.reload();
                                    }

                                }
                            });
                        }
                    }, false);
                }

                //newInput.value = this.firstChild.innerHTML;
                newInput.value = thisVal;
                this.innerHTML = "";
                //this.appendChild(buttonDiv);
                this.appendChild(newInput);
                //newInput.innerHTML = ('<i class="fa fa-check" aria-hidden="true"></i>');
                newInput.focus();
                newInput.select();
            }.bind(el), false);
        }
    }

    /*$("body").on("click", "#click_id", function(){
        alert('1234');
    });*/

    //Редактирование надбавки / добавление новой строчки
    var changeSur_elems = document.getElementsByClassName("changeCurrentSur"), newInput;
    //console.log(elems);

    if (changeSur_elems.length > 0) {
        for (var i = 0; i < changeSur_elems.length; i++) {
            var el = changeSur_elems[i];
            el.addEventListener("click", function () {
                //var thisID = this.id;
                var workerID = this.getAttribute("worker_id");
                //console.log(this.getAttribute("worker_id"));
                //var catID = this.getAttribute("cat_id");
                //console.log(this.getAttribute("cat_id"));
                //var typeID = this.getAttribute("type_id");
                //console.log(this.getAttribute("type_id"));

                var thisVal = this.innerHTML;
                var newVal = thisVal;
                //console.log(this);
                //console.log(workerID);
                //console.log(catID);
                //console.log(typeID);
                //console.log(thisVal);
                //console.log(isNaN(thisVal));

                var inputs = this.getElementsByTagName("input");
                if (inputs.length > 0) return;
                if (!newInput) {

                    /*buttonDiv = document.createElement("div");
                     //buttonDiv.innerHTML = '<i class="fa fa-check" aria-hidden="true" title="Применить" style="margin-right: 4px;"></i> <i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>';
                     buttonDiv.innerHTML = '<i class="fa fa-refresh" aria-hidden="true" title="По умолчанию" style="color: red;"></i>';
                     buttonDiv.style.position = "absolute";
                     buttonDiv.style.right = "-9px";
                     buttonDiv.style.top = "1px";
                     buttonDiv.style.fontSize = "12px";
                     buttonDiv.style.color = "green";
                     buttonDiv.style.border = "1px solid #BFBCB5";
                     buttonDiv.style.backgroundColor = "#FFF";
                     buttonDiv.style.padding = "0 6px";

                     buttonDiv.id = "changePersonalPercentCatdefault";*/

                    newInput = document.createElement("input");
                    newInput.type = "text";
                    newInput.maxLength = 10;
                    newInput.setAttribute("size", 20);
                    newInput.style.width = "50px";
                    newInput.addEventListener("blur", function () {
                        //console.log(newInput.parentNode.getAttribute("worker_id"));

                        workerID = newInput.parentNode.getAttribute("worker_id");
                        //catID = newInput.parentNode.getAttribute("cat_id");
                        //typeID = newInput.parentNode.getAttribute("type_id");

                        //Попытка обработать клика на кнопке для сброса на значения по умолчанию - провалилась, всегда сбрасывается на по умолчанию
                        //var changePersonalPercentCatdefault = document.getElementById("changePersonalPercentCatdefault");
                        //console.log(changePersonalPercentCatdefault.innerHTML);

                        //changePersonalPercentCatdefault.addEventListener("click", fl_changePersonalPercentCatdefault(workerID, catID, typeID), false);

                        //Новые данные
                        //if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (newInput.value > 100) || (isNaN(parseInt(newInput.value, 10)))) {
                        if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (isNaN(parseInt(newInput.value, 10)))) {
                            //newInput.parentNode.innerHTML = 0;
                            newInput.parentNode.innerHTML = thisVal;
                            newVal = thisVal;
                        } else {
                            newInput.parentNode.innerHTML = number_format(parseFloat(newInput.value, 10), 2, '.', '');
                            newVal = number_format(parseFloat(newInput.value, 10), 2, '.', '');
                        }
                        //console.log(this);
                        //console.log(workerID);

                        //console.log(thisVal == newVal);

                        if (Number(thisVal) != Number(newVal)) {
                            //console.log(newVal);

                            $.ajax({
                                url: "fl_change_personal_surcharge_f.php",
                                global: false,
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    worker_id: workerID,
                                    //cat_id: catID,
                                    //type: typeID,
                                    val: newVal
                                },
                                cache: false,
                                beforeSend: function () {
                                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                                },
                                // действие, при ответе с сервера
                                success: function (res) {
                                    if (res.result == "success") {
                                        //console.log(res);

                                        $('#infoDiv').html(res.data);
                                        $('#infoDiv').show();
                                        setTimeout(function () {
                                            $('#infoDiv').hide('slow');
                                            $('#infoDiv').html();
                                        }, 1000);

                                        //location.reload();
                                    }

                                }
                            });
                        }
                    }, false);
                }

                //newInput.value = this.firstChild.innerHTML;
                newInput.value = thisVal;
                this.innerHTML = "";
                //this.appendChild(buttonDiv);
                this.appendChild(newInput);
                //newInput.innerHTML = ('<i class="fa fa-check" aria-hidden="true"></i>');
                newInput.focus();
                newInput.select();
            }.bind(el), false);
        }
    }

    //Функция удаления оклада
    function deleteThisSalary(salary_id, type){
        //console.log(type);

        var rys = false;

        rys = confirm("Вы хотите удалить оклад. \nЭто необратимо.\nПо умолчанию будет оклад с более поздней датой.\n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "salary_del_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: salary_id,
                    type: type
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    if (res.result == "success") {
                        location.reload();
                        //console.log(res.data);
                    }
                    if (res.result == "error") {
                        //alert(res.data);
                    }
                    //console.log(data.data);

                }
            });
        }
    }

    //Функция удаления цены из прайса
    function deleteThisPrice(price_id, insure){
        //console.log(type);

        var rys = false;

        rys = confirm("Вы хотите удалить цену. \nЭто необратимо.\nПо умолчанию будет цена с более поздней датой.\n\nВы уверены?");

        if (rys) {
            $.ajax({
                url: "price_del_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: price_id,
                    insure: insure
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    if (res.result == "success") {
                        location.reload();
                        //console.log(res.data);
                    }
                    if (res.result == "error") {
                        //alert(res.data);
                    }
                    //console.log(data.data);

                }
            });
        }
    }


    //Добавляем/редактируем в базу выплату в банку
    function fl_Ajax_add_in_bank(mode, reqData){
        //console.log(reqData);

        var date_arr = reqData['date'].split(".");
        var date_str = "&m="+date_arr[1]+"&y="+date_arr[2];

        var link = "fl_addInBank_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data: reqData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
                //console.log(res.data);
                //$('#data').html(res)

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    blockWhileWaiting (true);
                    document.location.href = "fl_consolidated_report_admin.php?filial_id=" + reqData.filial_id + date_str;

                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу выплату динектору
    function  fl_Ajax_add_to_director(mode, reqData){
        //console.log(reqData);

        var date_arr = reqData['date'].split(".");
        var date_str = "&m="+date_arr[1]+"&y="+date_arr[2];

        var link = "fl_addToDirector_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data: reqData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
                //console.log(res.data);
                //$('#data').html(res)

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    blockWhileWaiting (true);
                    document.location.href = "fl_consolidated_report_admin.php?filial_id=" + reqData.filial_id + date_str;

                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Промежуточная функция для выплаты в банк
    function fl_showAjaxAddInBank (mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var filial_id = $('#SelectFilial').val();
        var date = $("#iWantThisDate2").val();
        var comment = $('#comment').val();
        var summ = $('#summ').val();

        var reqData = {
            filial_id: filial_id,
            date: date,
            summ: summ,
            comment: comment
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {summ: summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                    fl_Ajax_add_in_bank(mode, reqData);

                    // в случае ошибок в форме
                }else{
                    // перебираем массив с ошибками
                    for(var errorField in res.text_error){
                        // выводим текст ошибок
                        $('#'+errorField+'_error').html(res.text_error[errorField]);
                        // показываем текст ошибок
                        $('#'+errorField+'_error').show();
                        // обводим инпуты красным цветом
                        // $('#'+errorField).addClass('error_input');
                    }
                    $('#errror').html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
                }
            }
        })
    }

    //Промежуточная функция для выплаты директору
    function fl_showAjaxAddToDirector (mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var filial_id = $('#SelectFilial').val();
        var date = $("#iWantThisDate2").val();
        var comment = $('#comment').val();
        var summ = $('#summ').val();

        var reqData = {
            filial_id: filial_id,
            date: date,
            summ: summ,
            comment: comment
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {summ: summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                    fl_Ajax_add_to_director(mode, reqData);

                    // в случае ошибок в форме
                }else{
                    // перебираем массив с ошибками
                    for(var errorField in res.text_error){
                        // выводим текст ошибок
                        $('#'+errorField+'_error').html(res.text_error[errorField]);
                        // показываем текст ошибок
                        $('#'+errorField+'_error').show();
                        // обводим инпуты красным цветом
                        // $('#'+errorField).addClass('error_input');
                    }
                    $('#errror').html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
                }
            }
        })
    }




