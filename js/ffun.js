

    //Ждем ждём ожидание ждать
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

    //Удалить табель
    function fl_deleteTabelItem(tabel_id){
        //console.log(id);

        var rys = false;

        rys = confirm("Вы хотите удалить табель. \nЭто необратимо. Все РЛ будут откреплены.\nВсе прикрепленные документы будут удалены\n\nВы уверены?");

        if (rys) {

            $.ajax({
                url: "fl_tabel_del_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: tabel_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(data.data);
                    //location.reload();
                    if(res.result == "success"){
                        //!!! переадресация на вкладку, может когда-нибудь организую
                        //http://localhost/gitasmedica/fl_tabels.php#tabs-5_324
                        window.location.href = "fl_tabels.php";
                    }else{
                        alert(res.data)
                    }
                }
            });

        }
    }

    //Удалить расчет
    function fl_deleteCalculateItem(id, client_id, invoice_id){
        //console.log(id);

        var rys = false;

        rys = confirm("Удалить расчетный лист?");

        if (rys) {

            $.ajax({
                url: "fl_check_calculate_in_tabel_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    calculate_id: id,
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if(res.result == "success"){
                        if (res.data == 0){
                            console.log(res);

                            $.ajax({
                                url: "fl_calculate_del_f.php",
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
                                success: function (data) {
                                    /*if(data.result == "success"){

                                     }*/
                                    //console.log(data.data);
                                    //location.reload();
                                    window.location.href = "invoice.php?id=" + invoice_id;
                                }
                            });

                        }
                    }
                    if(res.result == "error"){
                        alert("Расчётный лист добавлен в табель #"+res.data+".\n\nНельзя удалить.\n\nОбратитесь к руководителю.");
                        $("#tabel_info").html("<div class='query_neok'><a href='fl_tabel.php?id="+res.data+"' class='ahref'>Перейти в табель #"+res.data+"</a></div>");
                    }
                }
            });
        }
    }

    //Удалить Прочую выдачу/расход
    function deletePaidoutsTempItem(id){
        //console.log(id);

        var rys = false;

        rys = confirm("Удалить Выдачу?");

        if (rys) {


            $.ajax({
                url: "fl_paidouts_temp_item_del_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (data) {

                    location.reload();

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


    //Для изменений в нормах часов персональных
    let changePersonalNormaHours_elems = document.getElementsByClassName("changePersonalNormaHours"), newInputNormaHours;
    //console.log(elems);

    if (changePersonalNormaHours_elems.length > 0) {
        for (let i = 0; i < changePersonalNormaHours_elems.length; i++) {

            let el = changePersonalNormaHours_elems[i];

            el.addEventListener("click", function () {
                let workerID = this.getAttribute("worker_id");
                let normaID = this.getAttribute("norma_id");
                // let typeID = this.getAttribute("type_id");

                let thisVal = this.innerHTML;
                let newVal = thisVal;

                let inputs = this.getElementsByTagName("input");
                if (inputs.length > 0) return;
                if (!newInputNormaHours) {

                    newInputNormaHours = document.createElement("input");
                    newInputNormaHours.type = "text";
                    newInputNormaHours.maxLength = 5;
                    newInputNormaHours.setAttribute("size", 20);
                    newInputNormaHours.style.width = "40px";
                    newInputNormaHours.addEventListener("blur", function () {

                        workerID = newInputNormaHours.parentNode.getAttribute("worker_id");
                        normaID = newInputNormaHours.parentNode.getAttribute("norma_id");
                        // typeID = newInputNormaHours.parentNode.getAttribute("type_id");

                        //Новые данные
                        if ((newInputNormaHours.value == "") || (isNaN(newInputNormaHours.value)) || (newInputNormaHours.value < 0) || (isNaN(parseInt(newInputNormaHours.value, 10)))) {
                            newInputNormaHours.parentNode.innerHTML = thisVal;
                            newVal = thisVal;
                        } else {
                            newInputNormaHours.parentNode.innerHTML = parseInt(newInputNormaHours.value, 10);
                            newVal = parseInt(newInputNormaHours.value, 10);
                        }

                        if (Number(thisVal) != Number(newVal)) {

                            $.ajax({
                                url: "fl_change_personal_norma_hours_f.php",
                                global: false,
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    worker_id: workerID,
                                    norma_id: normaID,
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

                //newInputNormaHours.value = this.firstChild.innerHTML;
                newInputNormaHours.value = thisVal;
                this.innerHTML = "";
                //this.appendChild(buttonDiv);
                this.appendChild(newInputNormaHours);
                //newInputNormaHours.innerHTML = ('<i class="fa fa-check" aria-hidden="true"></i>');
                newInputNormaHours.focus();
                newInputNormaHours.select();
            }.bind(el), false);
        }
    }

    //Функция для поочередного вывода на экран табелей для печати
    function fl_printCheckedWorkersTabels (){
        //console.log (calcIDForTabelINarr());


        wait(function(runNext){

            blockWhileWaiting (true);

            setTimeout(function(){
                runNext(calcIDForTabelINarr());
            }, 500);

        }).wait(function(runNext, workersIDs_arr){
            //используем аргументы из предыдущего вызова
            //console.log(workersIDs_arr.main_data)

            setTimeout(function(){

                var link = "fl_tabel_print_all.php";

                //console.log($('#SelectMonth').val());
                //console.log($('#SelectYear').val());

                var month = $('#SelectMonth').val();
                var year = $('#SelectYear').val();
                var office = $('#SelectFilialp').val();

                hideAllErrors ();
                $('#rezult').html('');


                workersIDs_arr.main_data.forEach(function(w_id, i, arr) {
                    //console.log(w_id);

                    var reqData = {
                        worker_id: w_id,
                        month: month,
                        year: year,
                        office: office
                    };

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
                            //console.log(res.tabel_ids);

                            if (res.result == "success") {
                                //console.log(res);
                                //console.log(JSON.parse(res.tabel_ids));

                                $('#rezult').append(res.data);

                                var tabel_ids = JSON.parse(res.tabel_ids);

                                tabel_ids.forEach(function(tabel_id, j, arr2) {
                                    fl_tabulation (tabel_id);
                                    //console.log(tabel_id);
                                })

                            } else if (res.result == "empty") {
                                //$('#errror').html('<div class="query_neok">Ошибка #61. Нет данных.</div>');
                            } else {
                                $('#errror').html(res.data);
                            }
                        }
                    });
                });

                runNext();

            }, 1500);

        }).wait(function(runNext){
            //console.log(1);

            setTimeout(function(){
                var elems = document.getElementsByClassName('rezult_item');
                //console.log(elems);
                var arr = $.makeArray(document.getElementsByClassName('rezult_item'));
                //console.log(arr);

                arr.sort(function (a, b) {
                    a = $(a).attr('fio');
                    //console.log(a);
                    b = $(b).attr('fio');
                    //console.log(b);
                    return a.localeCompare(b);
                });
                //console.log(arr);

                for(var i=0; i<arr.length; i++){
                    //console.log(arr[i]);
                    //console.log(i);
                    //console.log((i+1)% 3);
                    //console.log(typeof (arr[i]));
                    //console.log(arr.classList.contains("rezult_item"));

                    if ((i+1)% 3 == 0){
                        //console.log(i);
                        //console.log(arr[i]);
                        //console.log(arr[i].classList.contains("rezult_item"));

                        arr[i].classList.add("rezult_item3print");

                    }
                }

                $(arr).appendTo("#rezult");
                //console.log(arr.length);


            }, 1500);

            blockWhileWaiting (false);

        });
    }

    //Собираем ID отмеченных РЛ в массив
    function calcIDForTabelINarr() {
        var ids_arr = {};
        var chkBoxData_arr = {};
        var calcIDForTabel_arr = {};
        calcIDForTabel_arr.data = [];
        calcIDForTabel_arr.main_data = [];

        $(".chkBoxCalcs").each(function(){
            if ($(this).attr("checked")){

                ids_arr = $(this).attr("name").split("_");
                //console.log(ids_arr[1]);

                //chkBoxData_arr  = $(this).attr("chkBoxData").split("_");
                //console.log(chkBoxData_arr);

                //var calcIDForTabel = ids_arr[1];

                calcIDForTabel_arr.data = $(this).attr("chkBoxData");
                calcIDForTabel_arr.main_data[calcIDForTabel_arr.main_data.length] = ids_arr[1];
                //console.log(ids_arr[1]);

            }
        });

        //console.log(calcIDForTabel_arr);

        return calcIDForTabel_arr;
    }

    //
    function fl_addNewTabelIN (newTabel){

        wait(function(runNext){

            setTimeout(function(){
                runNext(calcIDForTabelINarr());
            }, 1500);

        }).wait(function(runNext, calcIDForTabel_arr){
            //используем аргументы из предыдущего вызова
            //console.log(calcIDForTabel_arr)

            $.ajax({
                url: "fl_addCalcsIDsINSessionForTabel.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    calcArr: calcIDForTabel_arr
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        //console.log(res);
                        if (newTabel) {
                            //document.location.href = "fl_addNewTabel.php";
                            var openedWindow = iOpenNewWindow('fl_addNewTabel.php', 'newTabelwindow', 'width=800, height=800, scrollbars=yes,resizable=yes,menubar=no,toolbar=yes,status=yes');
                        }else{
                            //console.log(12333);
                            var openedWindow = iOpenNewWindow("fl_addINExistTabel.php", 'oldTabelwindow', 'width=800, height=800, scrollbars=yes,resizable=yes,menubar=no,toolbar=yes,status=yes');

                        }

                    }

                }
            });

        });
    }


    //
    function menuForAddINNewTabel(res, type_id, worker_id, filial_id, newTabel, noch, clear, dopData){
        // console.log(res);
        // console.log(newTabel);
        // console.log(noch);
        // console.log(dopData);
        // console.log(JSON.stringify(dopData));

        var buttonsStr = '';

        if (newTabel) {
            buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="fl_addNewTabel2(' + type_id + ', ' + worker_id + ', ' + filial_id + ')">';
        }else{
            buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="fl_addInExistTabel2(' + type_id + ', ' + worker_id + ', ' + filial_id + ')">';
        }
        //Если создаём пустой табель
        if (clear){
            buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="fl_addNewTabelClear(' + type_id + ', ' + worker_id + ', ' + filial_id + ')">';
        }

        //Если оформляем ночь
        if (noch){
            buttonsStr = "<input type='button' class='b' value='Далее' onclick='fl_addNewNoch(" + type_id + ", " + worker_id + ", " + filial_id + ", " + (JSON.stringify(dopData)) + ")'>";
        }

        // Создаем меню:
        var menu = $('<div/>', {
            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        }).css({
            "top": "-170px",
            "height": "fit-content",
            "width": "45%",
            "background-color": "rgb(195, 194, 194)"
        })
            .appendTo('#overlay')
            .append(
                $('<div/>')
                    .css({
                        /*"height": "100%",*/
                        "border": "1px solid #AAA",
                        "position": "relative",
                        "background-color": "rgb(245, 245, 245)",
                        "padding": "10px"
                    })
                    //.append('<span style="margin: 5px;"><i>Новый табель</i></span>')
                    .append(
                        $('<div/>')
                            .css({
                                /*"position": "absolute",*/
                                "width": "100%",
                                "margin": "auto",
                                "top": "-10px",
                                "left": "0",
                                "bottom": "0",
                                "right": "0",
                                "height": "50%"
                            })
                            .append('<div style="margin: 0px;">'+res+'</div>')
                    )
                    .append(
                        $('<div/>')
                            .css({
                                /*"position": "absolute",*/
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
    //Ajax_change_shed
    //
    function menuForAddINExistNewTabel(){

    }


    //Добавить расчетные листы в новый табель, попутно создать этот новый табель
    function fl_addNewTabelIN2 (newTabel, type_id, worker_id, filial_id){

        /*if (newTabel) {
            var link = "fl_getCalcsFromSession_f.php";
        }else{
            var link = "fl_getCalcsFromSessionForExistTabel_f.php";
        }*/

        var link = "fl_getCalcsFromSession_f.php";

        var reqData = {
            newTabel: newTabel?1:0
        };
        //console.log(reqData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success: function (res) {
                //console.log (res);
                //console.log (res.length);

                if (res.length > 0) {
                    $('#overlay').show();

                    menuForAddINNewTabel(res, type_id, worker_id, filial_id, newTabel, false, false, {});
                }else{
                    $('#errrror').html('<div class="query_neok">Ошибка #34. Ничего не выбрано. Обновите выбор РЛ</div>');
                }
            }
        })
    }

    //Функция создания пустого табеля
    function fl_addNewClearTabelIN (newTabel, type_id, worker_id, filial_id){

        var link = "fl_menuForClearTabel_f.php";

        var reqData = {
            type_id: type_id,
            worker_id: worker_id,
            filial_id: filial_id,
            newTabel: newTabel?1:0
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success: function (res) {
                //console.log (res);
                //console.log (res.length);

                if (res.length > 0) {
                    $('#overlay').show();

                    menuForAddINNewTabel(res, type_id, worker_id, filial_id, newTabel, false, true, {});
                }else{
                    $('#errrror').html('<div class="query_neok">Ошибка #52. Что-то пошло не так.</div>');
                }
            }
        })
    }

    //Рассчет ночи
    function fl_addNoch (noch, type_id, worker_id, filial_id){

        /*if (newTabel) {
            var link = "fl_getCalcsFromSession_f.php";
        }else{
            var link = "fl_getCalcsFromSessionForExistTabel_f.php";
        }*/

        var link = "fl_getCalcsFromSession_f.php";

        var reqData = {
            newTabel: 0
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success: function (res) {
                // console.log (res);
                // console.log (res.length);

                if (res.length > 0) {
                    $('#overlay').show();

                    menuForAddINNewTabel(res, type_id, worker_id, filial_id, 0, noch, false, {});
                }else{
                    $('#errrror').html('<div class="query_neok">Ошибка #48. Ничего не выбрано. Обновите выбор РЛ</div>');
                }
            }
        })
    }

    //Рассчет ночи 2.0
    function fl_addReportNoch (day, month, year, type_id, worker_id, filial_id, filial_summ, zp_summ, invoice_ids){
        // console.log(day);
        // console.log(month);
        // console.log(year);
        // console.log(type_id);
        // console.log(worker_id);
        // console.log(filial_id);
        // console.log(filial_summ);
        // console.log(zp_summ);
        // console.log(invoice_ids);

        var link = "fl_getTabels_noch_f.php";

        var dopData = {
            day: day,
            month: month,
            year: year,
            summ: zp_summ
        };
        //console.log(dopData);

        var reqData = {
            type_id: type_id,
            worker_id: worker_id,
            filial_id: filial_id,
            dopData: dopData
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success: function (res) {
                // console.log (res);
                // console.log (res.length);

                if (res.length > 0) {
                    $('#overlay').show();

                    menuForAddINNewTabel(res, type_id, worker_id, filial_id, 0, true, false, dopData);
                }else{
                    $('#errrror').html('<div class="query_neok">Ошибка #49. Нет табелей. Табель ассистенту можно добавить в <a href="fl_tabels2.php" class="ahref">Отчёте по часам</a></div>');
                }
            }
        })
    }

    //Добавляем в базу табель из сессии
    function fl_addNewTabel(){

        var link = "fl_tabel_add_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    tabelMonth: $("#tabelMonth").val(),
                    tabelYear: $("#tabelYear").val(),
                    summCalcs: $(".summCalcsNPaid").html()
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res.data);

                if(res.result == "success"){
                    //document.location.href = "fl_tabels.php";
                    window.close('newTabelwindow');
                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }

    //Добавляем в базу новый табель и РЛ в него из сессии
    function fl_addNewTabel2(type_id, worker_id, filial_id){
        //console.log($(".summCalcsForTabel").html());

        var link = "fl_tabel_add2_f.php";

        var reqData = {
            tabelMonth: $("#tabelMonth").val(),
            tabelYear: $("#tabelYear").val(),
            summCalcs: $(".summCalcsForTabel").html()
        };

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
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res);

                    //document.location.href = "fl_tabels.php";
                    //window.close('newTabelwindow');

                    $("#overlay").hide();
                    $(".center_block").remove();

                    setTimeout(function () {
                        refreshOnlyThisTab($("#refreshID_"+type_id+"_"+worker_id+"_"+filial_id+""), type_id, worker_id, filial_id);
                    }, 1000);


                }else{
                    //console.log(res);

                    $('#errror').html(res.data);
                    $("#overlay").hide();
                    $(".center_block").remove();
                }
            }
        });
    }

    //Добавляем в базу новый ПУСТОЙ табель без РЛ
    function fl_addNewTabelClear(type_id, worker_id, filial_id){
        //console.log($(".summCalcsForTabel").html());

        var link = "fl_tabel_add3_f.php";

        var reqData = {
            type_id: type_id,
            worker_id:  worker_id,
            filial_id: filial_id,
            tabelMonth: $("#tabelMonth").val(),
            tabelYear: $("#tabelYear").val()
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
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == "success"){
                    console.log(res);

                    //document.location.href = "fl_tabels.php";
                    //window.close('newTabelwindow');

                    $("#overlay").hide();
                    $(".center_block").remove();

                    setTimeout(function () {
                        refreshOnlyThisTab($("#refreshID_"+type_id+"_"+worker_id+"_"+filial_id+""), type_id, worker_id, filial_id);
                    }, 1000);


                }else{
                    //console.log(res);

                    $('#errror').html(res.data);
                    $("#overlay").hide();
                    $(".center_block").remove();
                }
            }
        });
    }

    //Добавляем в существующий табель РЛ из сессии
    function fl_addInExistTabel(){

        var link = "fl_add_in_tabel_f.php";
        //console.log(link);

        var tabelForAdding = $('input[name=tabelForAdding]:checked').val();
        //console.log(tabelForAdding);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    tabelForAdding: tabelForAdding
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == "success"){
                    //document.location.href = "fl_tabels.php";
                    window.close('oldTabelwindow');
                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }

    //Добавляем в существующий табель РЛ из сессии
    function fl_addInExistTabel2(type_id, worker_id, filial_id){

        var link = "fl_add_in_tabel2_f.php";
        //console.log(link);

        var tabelForAdding = $('input[name=tabelForAdding]:checked').val();
        var tabel_noch_mark = $('input[name=tabelForAdding]:checked').attr("tabel_noch_mark");

        //console.log(tabelForAdding);
        //console.log($('input[name=tabelForAdding]:checked').attr("tabel_noch_mark"));

        var reqData = {
            summCalcs: $(".summCalcsForTabel").html(),
            tabel_noch_mark: tabel_noch_mark,
            tabelForAdding: tabelForAdding
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
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res);

                    //document.location.href = "fl_tabels.php";
                    //window.close('newTabelwindow');

                    $("#overlay").hide();
                    $(".center_block").remove();

                    setTimeout(function () {
                        refreshOnlyThisTab($("#refreshID_"+type_id+"_"+worker_id+"_"+filial_id+""), type_id, worker_id, filial_id);
                    }, 1000);


                }else{
                    $('#errror').html(res.data);
                    //console.log(res);
                }
            }
        });
    }

    //Добавляем в базу рассчет ночи
    function fl_addNewNoch(type_id, worker_id, filial_id, dopData){
        //console.log($(".summCalcsForTabel").html());
        //console.log(dopData);

        var link = "fl_add_new_noch2_f.php";

        var tabelForAdding = $('input[name=tabelForAdding]:checked').val();

        var reqData = {
            type_id: type_id,
            worker_id: worker_id,
            filial_id: filial_id,
            dopData: dopData,
            tabelForAdding: tabelForAdding
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
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res);

                    setTimeout(function () {
                        location.reload()
                    }, 100);

                }else{
                    //console.log(res);

                    $('#errror').html(res.data);
                    $("#overlay").hide();
                    $(".center_block").remove();
                }
            }
        });
    }

    //Добавляем в базу новый ночной табель и сразу же добавляем туда отчёт за указанную дату
    function fl_addNewNochTabel(type_id, worker_id, filial_id, dopData){
        // console.log(type_id);
        // console.log(worker_id);
        // console.log(filial_id);
        // console.log(dopData);

        var link = "fl_add_new_noch_tabel_f.php";

        //var tabelForAdding = $('input[name=tabelForAdding]:checked').val();

        var reqData = {
            type_id: type_id,
            worker_id: worker_id,
            filial_id: filial_id,
            dopData: dopData
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
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res);

                    setTimeout(function () {
                        location.reload()
                    }, 100);

                }else{
                    //console.log(res);

                    $('#errror').html(res.data);
                    $("#overlay").hide();
                    $(".center_block").remove();
                }
            }
        });
    }



    //Удаляем все выделенные РЛ из программы в разделе Важный отчет
    function fl_deleteMarkedCalculates (thisObj){
        //console.log(thisObj);
        //console.log(thisObj.parent());

        wait(function(runNext){

            setTimeout(function(){
                runNext(calcIDForTabelINarr());
            }, 1500);

        }).wait(function(runNext, calcIDForTabel_arr){
            //используем аргументы из предыдущего вызова
            //console.log(calcIDForTabel_arr);
            //console.log(typeof (calcIDForTabel_arr));
            //console.log(calcIDForTabel_arr.main_data.length);

            if (calcIDForTabel_arr.main_data.length > 0) {
                var rys = false;

                rys = confirm("Вы хотите удалить выделенные РЛ. \nЭто необратимо. Все РЛ будут полностью удалены\nиз программы.\n\nВы уверены?");

                if (rys) {
                    $.ajax({
                        url: "fl_deleteCalcsByIDsFromDB.php",
                        global: false,
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            calcArr: calcIDForTabel_arr.main_data
                        },
                        cache: false,
                        beforeSend: function () {
                            //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                        },
                        // действие, при ответе с сервера
                        success: function (res) {
                            //console.log(res);

                            if (res.result == "success") {
                                //console.log(res);

                                var tableArr = calcIDForTabel_arr.data.split('_');
                                /*console.log(tableArr[1]);
                                 console.log(tableArr[2]);
                                 console.log(tableArr[3]);*/

                                refreshOnlyThisTab(thisObj, tableArr[1],tableArr[2],tableArr[3]);
                            }
                        }
                    });
                }
            }
        });
    }

    //Перерасчет зп (если меняли процент) во всех выделенных РЛ из программы в разделе Важный отчет
    function fl_reloadPercentsMarkedCalculates (thisObj){
        //console.log(thisObj);
        //console.log(thisObj.parent());

        wait(function(runNext){

            setTimeout(function(){
                runNext(calcIDForTabelINarr());
            }, 1500);

        }).wait(function(runNext, calcIDForTabel_arr){
            //используем аргументы из предыдущего вызова
            //console.log(calcIDForTabel_arr);

            if (calcIDForTabel_arr.main_data.length > 0) {

                if (calcIDForTabel_arr.main_data.length > 10){
                    alert("Рассчитать можно не более 10 РЛ за раз.");
                }else {
                    var rys = false;

                    rys = confirm("Вы собираетесь перерасчитать выделенные РЛ. \n\nВы уверены?");

                    if (rys) {
                        $.ajax({
                            url: "fl_reloadPercentsMarkedCalculates.php",
                            global: false,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                calcArr: calcIDForTabel_arr
                            },
                            cache: false,
                            beforeSend: function () {
                                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                            },
                            // действие, при ответе с сервера
                            success: function (res) {
                                //console.log(res);

                                if (res.result == "success") {
                                    //console.log(res);

                                    var tableArr = calcIDForTabel_arr.data.split('_');

                                    refreshOnlyThisTab(thisObj, tableArr[1],tableArr[2],tableArr[3]);
                                }
                            }
                        });
                    }
                }
            }
        });
    }

    //Показываем блок с ночными сменами
    function Ajax_NightSmenaAddINTabel (tabel_id, nightSmenaCount){
        //console.log(tabel_id);

        var link = "fl_add_night_smena_in_tabel_f.php";
        //console.log(link);

        var Data = {
            tabel_id: tabel_id,
            count: nightSmenaCount
        };

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
                    $('#errror').html(res.data);
                }
            }
        });
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

    //Показываем блок с ночными сменами
    function Ajax_emptySmenaAddINTabel (tabel_id, emptySmens){
        //console.log(tabel_id);

        var link = "fl_add_empty_smena_in_tabel_f.php";
        //console.log(link);

        var Data = {
            tabel_id: tabel_id,
            count: emptySmens
        };

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
                    $('#errror').html(res.data);
                }
            }
        });
    }

    //Показываем блок с ночными сменами
    function showNightSmenaAddINTabel (tabel_id, nightSmenaCount){
        //console.log(tabel_id);
        $('#overlay').show();

        var buttonsStr = '<input type="button" class="b" value="Добавить" onclick="Ajax_NightSmenaAddINTabel('+tabel_id+', '+nightSmenaCount+')">';

        /*if (mode == 'edit'){
            buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add(\'edit\')">';
        }*/

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
                    .append('<span style="margin: 5px;"><i>Проверьте и нажмите добавить</i></span>')
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
                                "height": "50%"
                            })
                            .append('<div style="margin: 10px;">Кол-во ночных смен: <span class="calculateInsInvoice">'+nightSmenaCount+'</span></div>')
                            .append('<div style="margin: 10px;">Общая сумма: <span class="calculateInvoice">'+nightSmenaCount*1000+'</span> руб.</div>')
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

    //Показываем блок с "пустыми" сменами
    function showEmptySmenaAddINTabel (tabel_id){
        //console.log(tabel_id);

        var emptySmens = $('#emptySmens').val();
        //console.log(emptySmens);

        if (emptySmens.length > 0) {

            if (!isNaN(emptySmens)) {

                if (emptySmens > 0) {

                    emptySmens = Number(emptySmens);

                    $('#overlay').show();

                    var buttonsStr = '<input type="button" class="b" value="Добавить" onclick="Ajax_emptySmenaAddINTabel('+tabel_id+', '+emptySmens+')">';

                    /*if (mode == 'edit'){
                     buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add(\'edit\')">';
                     }*/

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
                                .append('<span style="margin: 5px;"><i>Проверьте и нажмите добавить</i></span>')
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
                                            "height": "50%"
                                        })
                                        .append('<div style="margin: 10px;">Кол-во "пустых" смен: <span class="calculateInsInvoice">' + emptySmens + '</span></div>')
                                        .append('<div style="margin: 10px;">Общая сумма: <span class="calculateInvoice">' + emptySmens * 500 + '</span> руб.</div>')
                                )
                                .append(
                                    $('<div/>')
                                        .css({
                                            "position": "absolute",
                                            "bottom": "2px",
                                            "width": "100%"
                                        })
                                        .append(buttonsStr +
                                            '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                                        )
                                )
                        );


                    menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
                }
            }
        }

    }

    //Удаляем РЛ из табеля
    function fl_deleteCalculateFromTabel(tabel_id, calculate_id){

        var link = "fl_deleteCalcFromTabel_f.php";

        var rys = false;

        rys = confirm("Вы хотите удалить РЛ из табеля. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    tabel_id: tabel_id,
                    calculate_id: calculate_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Удаляем ночной отчет из табеля
    function fl_deleteNightFromTabel(tabel_id, tabel_night_id){

        var link = "fl_deleteNightFromTabel_f.php";

        var rys = false;

        rys = confirm("Вы хотите удалить отчёт по ночи из табеля. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    tabel_id: tabel_id,
                    tabel_night_id: tabel_night_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Удаляем Вычет из табеля
    function fl_deleteDeductionFromTabel(tabel_id, deduction_id){

        var link = "fl_deleteDeductionFromTabel_f.php";

        var rys = false;

        rys = confirm("Вы хотите удалить Вычет из табеля. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    tabel_id: tabel_id,
                    deduction_id: deduction_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Удаляем надбавку из табеля
    function fl_deleteSurchargeFromTabel(tabel_id, surcharge_id){

        var link = "fl_deleteSurchargeFromTabel_f.php";

        var rys = false;

        rys = confirm("Вы хотите удалить Надбавку из табеля. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    tabel_id: tabel_id,
                    surcharge_id: surcharge_id
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Удаляем выплату из табеля
    function fl_deletePaidoutFromTabel(tabel_id, paidout_id){

        var link = "fl_deletePaidoutFromTabel_f.php";

        var rys = false;

        rys = confirm("Вы хотите удалить Выплату из табеля. \n\nВы уверены?");
        //console.log(885);

        if (rys) {

            var noch = $('#noch').val();

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    tabel_id: tabel_id,
                    paidout_id: paidout_id,

                    noch: noch
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Добавляем/редактируем в базу вычет из табеля
    function  fl_Ajax_deduction_add(deduction_id, tabel_id, mode, deductionData, link_res){

        var link = "fl_deduction_add_f.php";

        if (mode == 'edit'){
            link = "fl_deduction_edit_f.php";
        }

        deductionData['deduction_id'] = deduction_id;

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data:deductionData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
                //console.log(res.data);

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    blockWhileWaiting (true);

                    document.location.href = link_res+"?id="+tabel_id;
                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу надбавку в табель
    function  fl_Ajax_surcharge_add(surcharge_id, tabel_id, mode, surchargeData, link_res){

        var link = "fl_surcharge_add_f.php";

        if (mode == 'edit'){
            link = "fl_surcharge_edit_f.php";
        }

        surchargeData['surcharge_id'] = surcharge_id;

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data:surchargeData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
                //console.log(res.data);

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    blockWhileWaiting (true);

                    document.location.href = link_res+"?id="+tabel_id;
                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу выплату в табель
    function  fl_Ajax_paidout_add(paidout_id, tabel_id, mode, paidoutData, link_res, variant){

        if (variant == 1) {
            var link = "fl_paidout_add_f.php";
            if (mode == 'edit') {
                link = "fl_paidout_edit_f.php";
            }
        }

        if (variant == 2) {
            var link = "fl_paidout_add2_f.php";
            if (mode == 'edit') {
                link = "fl_paidout_edit2_f.php";
            }
        }

        paidoutData['paidout_id'] = paidout_id;
        //console.log(paidoutData);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data: paidoutData,

            cache: false,
            beforeSend: function() {
                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success:function(res){
                //console.log(paidoutData['deploy']);
                //console.log(res.data);
                //$('#data').html(res)

                if(res.result == 'success') {
                    //console.log('success');
                    //$('#data').html(res.data);

                    blockWhileWaiting (true);

                    if (paidoutData['deploy']) {

                        wait(function(runNext){

                            setTimeout(function(){

                                deployTabel(tabel_id);

                                runNext();

                            }, 500);

                        }).wait(function(){

                            setTimeout(function(){

                                if ($('#ref').val() === undefined) {
                                    document.location.href = link_res + "?id=" + tabel_id;
                                }else{
                                    document.location.href = $('#ref').val();
                                }

                            }, 500);

                        });

                        // setTimeout(function(){
                        //     //console.log($('#ref').val());
                        //
                        //     deployTabel(tabel_id);
                        //
                        //     if ($('#ref').val() === undefined) {
                        //         document.location.href = link_res + "?id=" + tabel_id;
                        //     }else{
                        //         document.location.href = $('#ref').val();
                        //     }
                        //
                        // }, 100);
                    }else {
                        ///console.log($('#ref').val());

                        if ($('#ref').val() === undefined) {
                            document.location.href = link_res + "?id=" + tabel_id;
                        }else{
                            document.location.href = $('#ref').val();
                        }
                    }
                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                    //$('#errrror').html('');
                }
            }
        });
    }

    //Добавляем/редактируем в базу временную тестовую выплату в бд
    function  fl_Ajax_paidout_another_add(paidoutData){
        //console.log(paidoutData);

        var link = "fl_paidout_another_add_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",

            data: paidoutData,

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
                    //window.location.replace("fl_tabel.php?id="+tabel_id);

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

    //Промежуточная функция для вычета
    function fl_showDeductionAdd (deduction_id, tabel_id, type, link, mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var deduction_summ = $('#deduction_summ').val();
        var descr = $('#descr').val();

        var deductionData = {
            tabel_id: tabel_id,
            type: type,
            deduction_summ: deduction_summ,
            descr: descr
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {deduction_summ: deduction_summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                    fl_Ajax_deduction_add(deduction_id, tabel_id, mode, deductionData, link);

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

    //Промежуточная функция для надбавки
    function fl_showSurchargeAdd (surcharge_id, tabel_id, type, link, mode){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var surcharge_summ = $('#surcharge_summ').val();
        var descr = $('#descr').val();

        var surchargeData = {
            tabel_id:tabel_id,
            type:type,
            surcharge_summ:surcharge_summ,
            descr:descr
        };

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {surcharge_summ:surcharge_summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                    fl_Ajax_surcharge_add(surcharge_id, tabel_id, mode, surchargeData, link);

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

    //Промежуточная функция для выплаты
    function fl_showPaidoutAdd (paidout_id, tabel_id, type, worker_id, month, year, link, mode, deploy, variant){
        //console.log(mode);
        //deploy - провести или нет
        //variant - какой вариант использовать. либо где по позициям или по всем суммам

        //убираем ошибки
        hideAllErrors ();

        var filials_subtractions = {};
        //Соберём суммы для вычетов со всех филиалов
        $('.filial_subtraction').each(function(){
            if ($(this).val() > 0) {
                filials_subtractions[$(this).attr('filial_id')] = Number($(this).val());
            }
        });
        // console.log(filials_subtractions);
        // console.log(JSON.stringify(filials_subtractions));

        var paidout_summ = $('#paidout_summ').val();
        var descr = $('#descr').val();
        var noch = $('#noch').val();
        var filial_id = $('#SelectFilial').val();

        var paidoutData = {
            tabel_id: tabel_id,
            type: type,
            worker_id: worker_id,
            month: month,
            year: year,
            paidout_summ: paidout_summ,
            noch: noch,
            descr:descr,
            filial_id: filial_id,
            deploy: deploy,
            subtractions: filials_subtractions
        };
            //console.log(paidoutData);

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: {paidout_summ: paidout_summ},

            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            success:function(res){
                if(res.result == 'success'){

                        fl_Ajax_paidout_add(paidout_id, tabel_id, mode, paidoutData, link, variant);

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

    //Промежуточная функция для тестовой выплаты
    function fl_showPaidoutAnotherAdd(){

        //убираем ошибки
        hideAllErrors ();

        if ($('#SelectFilial').val() == 0){
            $("#errror").html('<div class="query_neok">Не выбран филиал</div>');
        }else {

            var paidoutData = {
                month: $('#iWantThisMonth').val(),
                year: $('#iWantThisYear').val(),
                worker: $('#search_client2').val(),
                paidout_summ: $('#paidout_summ').val(),
                paidout_id: $('#SelectType').val(),
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

                data: {paidout_summ: $('#paidout_summ').val()},

                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (res) {
                    if (res.result == 'success') {

                        fl_Ajax_paidout_another_add(paidoutData);

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

    //Промежуточная функция для добавылния отзыва
    function fl_showReviewAdd(mode){

        //убираем ошибки
        hideAllErrors ();

        if ($('#SelectFilial').val() == 0){
            $("#errror").html('<div class="query_neok">Не выбран филиал</div>');
        }else {
            if ($('#search_client2').val().length > 0) {
                if ($('#search_client3').val().length > 0) {
                    if ($('#review_text').val().length > 0) {
                        if ($('#sites').val().length > 0) {
                            let review_id = 0;

                            let link = "review_add_f.php";

                            if (mode == 'edit') {
                                link = "review_edit_f.php";
                                review_id = $("#review_id").val();
                            }

                            let reqData = {
                                date: $('#iWantThisDate').val(),
                                worker_name: $('#search_client2').val(),
                                filial_id: $('#SelectFilial').val(),
                                review_text: $('#review_text').val(),
                                added_name: $('#search_client3').val(),
                                sites: $('#sites').val(),
                                status: status,
                                review_id: review_id
                            };
                            console.log(reqData);


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
                                    //console.log(res);

                                    $('.center_block').remove();
                                    $('#overlay').hide();

                                    if(res.result == "success"){
                                        //$('#data').hide();
                                        $('#data').html(res.data);

                                        setTimeout(function () {
                                            //!!! переход window.location.href - это правильное использование
                                            window.location.href = 'reviews.php';
                                        }, 300);
                                    }else{
                                        $('#errror').html(res.data);
                                    }
                                }
                            });
                        }else{
                            $("#errror").html('<div class="query_neok">Укажите сайты</div>');
                        }
                    }else{
                        $("#errror").html('<div class="query_neok">Отзыв не может быть пустым</div>');
                    }
                }else{
                    $("#errror").html('<div class="query_neok">Выберите того, кто добавил</div>');
                }
            }else{
                $("#errror").html('<div class="query_neok">Выберите врача</div>');
            }
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

    //Провести табель
    function deployTabel (tabel_id){
        //console.log(tabel_id);
        //console.log(Number($("#summItog").html()));


        //убираем ошибки
        hideAllErrors ();

        var deployData = {
            tabel_id:tabel_id
        };

        var link = "fl_deployTabel_f.php";


        var rys = false;

        rys = confirm("Вы собираетесь провести табель.\nПосле этого изменить его не получится.\nВы уверены?");
        //console.log(885);

        if (rys) {

            //if (Number($("#summItog").html()) == 0) {

                $.ajax({
                    url: link,
                    global: false,
                    type: "POST",
                    dataType: "JSON",

                    data: deployData,

                    cache: false,
                    beforeSend: function () {
                        //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                    },
                    // действие, при ответе с сервера
                    success: function (res) {
                        //console.log(res);

                        if (res.result == "success") {
                            //console.log(res);
                            //console.log($('#ref').val());
                            //location.reload();

                            if ($('#ref').val() === undefined) {
                                window.location.replace("fl_tabel.php?id="+tabel_id);
                            }else{
                                window.location.replace($('#ref').val());
                            }

                        } else {
                            //$('#errror').html(res.data);
                            alert("Сумма табеля не равна нулю [ "+parseInt(res.data)+" ] ! Невозможно провести.");
                        }
                    }
                });
            // }else{
            //     alert("Сумма табеля не равна нулю! Невозможно провести.");
            // }
        }
    }

    //Снять отметку о Проведении табеля
    function deployTabelDelete (tabel_id){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var Data = {
            tabel_id:tabel_id
        };

        var link = "fl_deployTabel_delete_f.php";

        var rys = false;

        rys = confirm("Вы уверены, что хотите\nснять отметку о проведении табеля?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",

                data: Data,

                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        //console.log(res);
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Удалить ночные смены из табеля
    function nightSmenaTabelDelete (tabel_id){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var Data = {
            tabel_id:tabel_id
        };

        var link = "fl_nightSmenaTabel_delete_f.php";

        var rys = false;

        rys = confirm("Вы уверены, что хотите удалить \nночные смены из табеля?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",

                data: Data,

                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        //console.log(res);
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
        }
    }

    //Удалить пустые смены из табеля
    function emptySmenaTabelDelete (tabel_id){
        //console.log(mode);

        //убираем ошибки
        hideAllErrors ();

        var Data = {
            tabel_id:tabel_id
        };

        var link = "fl_emptySmenaTabel_delete_f.php";

        var rys = false;

        rys = confirm("Вы уверены, что хотите удалить \n\"пустые\" смены из табеля?");
        //console.log(885);

        if (rys) {

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",

                data: Data,

                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function (res) {
                    //console.log(res);

                    if (res.result == "success") {
                        //console.log(res);
                        location.reload();
                    } else {
                        $('#errror').html(res.data);
                    }
                }
            });
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

    //Добавление в сессию данных по рассчетным листам, которые надо добавить, (ID)
    function fl_addCalcsIDsINSessionForTabel(calc_id_arr, add_status, type, worker_id, filial_id){
        //console.log(calc_id_arr);
        //console.log(add_status);
        //console.log(type);
        //console.log(worker_id);
        //console.log(filial_id);

        var link = "fl_addCalcsIDsINSessionForTabel2.php";

        var reqData = {
            calc_id_arr: calc_id_arr,
            add_status: add_status,
            type: type,
            worker_id: worker_id,
            filial_id: filial_id
        };

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
            success: function (res) {
                //console.log (res);

                if (res.result == "success") {
                    //console.log (res);

                }else{
                    //--
                }
            }
        })

    }

    //Чистим все отмеченные checkbox и сессионный данные
    function clearAllChecked(){

        fl_addCalcsIDsINSessionForTabel([], 0, 0, 0, 0);

        $('input:checked').prop('checked', false);

        $('.calculateBlockItem').each(function() {
            if ($(this).attr("worker_mark") == 1){
                $(this).css({'background-color': '#FFF'});
            }else{
                $(this).css({'background-color': 'rgba(255, 141, 141, 0.2)'});
            }
        });
    }



    $(document).ready(function() {
        //console.log(123);


        //Рабочий пример клика на элементе после подгрузки загрузки его в DOM
        $("body").on("click", ".chkBoxCalcs", function(){
            var checked_status = $(this).prop("checked");
            //console.log(checked_status);
            //console.log($(this).parent());
            //console.log($(this).parent().parent().parent().attr("doctor_mark"));

            var add_status = 0;
            var calc_id_arr = [];

            //Меняем цвет блока
            if (checked_status){
                $(this).parent().parent().parent().css({"background-color": "#83DB53"});
                add_status = 1;
            }else{
                    //console.log($(this).parent().parent().parent().attr("worker_mark"));

                if ($(this).parent().parent().parent().attr("worker_mark") == 1) {
                    $(this).parent().parent().parent().css({"background-color": "rgb(255, 255, 255);"});
                }else{
                    $(this).parent().parent().parent().css({"background-color": "rgba(255, 141, 141, 0.2);"});
                }
            }

            //Получим ID расчетного листа
            //console.log($(this).attr("name").split("_"));
            //!!! Лишнее действие, надо бы посмотреть и переделать потом без split, чтоб данные писались в DOM сразу в виде чистого ID
            var ids_arr = $(this).attr("name").split("_");
            //Массив с ID расчетных листов
            calc_id_arr.push(ids_arr[1]);
            //console.log(calc_id);
            //console.log(checked_status);

            //Дополнительные данные
            var data_arr = $(this).attr("chkBoxData").split("_");

            //Добавим в сессию данные
            fl_addCalcsIDsINSessionForTabel(calc_id_arr, add_status, data_arr[1], data_arr[2], data_arr[3]);

        });


        $("body").on("click", ".checkAll", function(){

            var add_status = 0;
            var calc_id_arr = [];

            var checked_status = $(this).is(":checked");
            var thisId = $(this).attr("id");

            $("."+thisId).each(function() {
                if (checked_status){
                    $(this).prop("checked", true);
                    $(this).parent().parent().parent().css({"background-color": "#83DB53"});
                    add_status = 1;
                }else{
                    $(this).prop("checked", false);
                    if ($(this).parent().parent().parent().attr("worker_mark") == 1) {
                        $(this).parent().parent().parent().css({"background-color": "rgb(255, 255, 255);"});
                    }else{
                        $(this).parent().parent().parent().css({"background-color": "rgba(255, 141, 141, 0.2);"});
                    }
                }

                //Получим ID расчетного листа
                var ids_arr = $(this).attr("name").split("_");
                //Массив с ID расчетных листов
                calc_id_arr.push(ids_arr[1]);

            });

            //Дополнительные данные
            var data_arr = $(this).attr("chkBoxData").split("_");

            //Добавим в сессию данные
            fl_addCalcsIDsINSessionForTabel(calc_id_arr, add_status, data_arr[1], data_arr[2], data_arr[3]);

        });

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

    //Получаем табели
    function getTabelsfunc (thisObj, reqData){
        //console.log (reqData);

        $.ajax({
            url:"fl_get_tabels_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",

            data: reqData,

            cache: false,
            beforeSend: function() {
                thisObj.html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...<br>загрузка табелей</span></div>");
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


                    //Костыль для конкретного персонала, который не входит в другие группы
                    var workers_target_arr = [1, 9, 12, 777];
                    //console.log(permission);
                    //console.log(workers_target_arr.indexOf(Number(permission)));

                    if (workers_target_arr.indexOf(Number(permission)) !== -1){
                        permission = 999;
                    }

                    if (res.status == 1){
                        thisObj.html(res.data);

                        //Показываем оповещения на фио и филиале
                        /*$("#tabs_notes2_"+permission+"_"+worker).show();
                         $("#tabs_notes2_"+permission+"_"+worker+"_"+office).show();*/
                        //console.log("#tabs_notes_"+permission+"_"+worker+"_"+office);
                        if (res.notDeployCount > 0){
                            $("#tabs_notes2_"+permission+"_"+worker).css("display", "inline-block");
                            $("#tabs_notes2_"+permission+"_"+worker+"_"+office).css("display", "inline-block");
                        }else{
                            //$("#tabs_notes2_"+permission+"_"+worker).css("display", "none");
                            $("#tabs_notes2_"+permission+"_"+worker+"_"+office).css("display", "none");
                        }

                        //
                        thisObj.parent().find(".summTabelNPaid").html(res.summCalc);

                    }

                    // console.log($("#tabs_notes2_"+permission+"_"+worker+"_"+office).css("display"));
                    // console.log($("#tabs_notes_"+permission+"_"+worker+"_"+office).css("display"));


                    // console.log($("#tabs_notes2_"+permission+"_"+worker+"_"+office).css("display"));
                    // console.log($("#tabs_notes_"+permission+"_"+worker+"_"+office).css("display"));
                    // console.log(permission);
                    // console.log(worker);
                    // console.log(office);
                    // console.log($('#tabs_notes2_'+permission+'_'+worker+'_'+office).css("display"));
                    // console.log($('#tabs_notes2_'+permission+'_'+worker+'_'+office).css("display") == 'none');
                    // console.log((($('#tabs_notes2_'+permission+'_'+worker+'_'+office).css("display") == 'none')&&($('#tabs_notes_'+permission+'_'+worker+'_'+office).css("display") == 'none')));

                    // var index = $('#tabs_w'+permission+'_'+worker+' a[href="#tabs-'+permission+'_'+worker+'_'+office+'"]').parent().index();
                    //
                    // //console.log('Index: ' + index);
                    //
                    // if (($('#tabs_notes2_'+permission+'_'+worker+'_'+office).css("display") == 'none')
                    //     &&
                    //     ($('#tabs_notes_'+permission+'_'+worker+'_'+office).css("display") == 'none'))
                    // {
                    //     $('#tabs_w'+permission+'_'+worker).tabs( "option", "disabled", [ index ] );
                    // }

                    //!!!Если и нужна блокировка закладок, то запускать не отсюда, а в самом конце,
                    //а значит переделать саму функцию
                    //disableTabs (permission, worker);

                    if (res.status == 0){
                        thisObj.html("Нет данных по табелям");


                        //!!! доделать тут чтоб правильно прятались или нет вкладки
                        //Спрячем пустые вкладки, где нет данных

                        //!!! пока костыль такой
                        if (reqData['own_tabel']){
                            //console.log($("#filial_"+reqData['office']).css("display"));

                            //$("#filial_"+reqData['office']).hide();


                            // $("#filial_"+reqData['office']).css({
                            //     "pointer-events": "none",
                            //     "cursor": "default",
                            //     "background-color": "rgba(140, 137, 137, 0.7)",
                            // })
                        }
                    }


                }

                if(res.result == "error"){
                    thisObj.html(res.data);
                }
            }
        });
    }


    //Обновим данные в табеле, но только в данной вкладке
    function refreshOnlyThisTab(thisObj, permission_id, worker_id, office_id){
        //console.log(permission_id+' _ '+worker_id+' _ '+office_id);
        //console.log(thisObj.parent());

        hideAllErrors ();

        var now = new Date();
        var year = now.getFullYear();
        var month = now.getMonth();
        month = Number(month)+1;
        if (Number(month) < 10){
            month = "0"+month;
        }
        //console.log(month);

        var needCalcObj = thisObj.parent().find('.tableDataNPaidCalcs');
        var needTabelObj = thisObj.parent().find('.tableTabels');


        var reqData = {
            permission: permission_id,
            worker: worker_id,
            office: office_id,
            month: month,
            year: year,
            own_tabel: false
        };

        getCalculatesfunc (needCalcObj, reqData);

        getTabelsfunc (needTabelObj, reqData);

    }

    //Расчет табеля, подстановки данных
    function fl_tabulation (tabel_id){
        //console.log();

        var pay_plus = 0;
        var pay_minus = 0;
        var pay_plus_part = 0;
        var pay_minus_part = 0;

        wait(function(runNext){

            setTimeout(function(){

                $('.pay_plus_part1_'+tabel_id).each(function() {
                    pay_plus_part += Number($(this).html());
                    //console.log(Number($(this).html()));
                });
                //console.log(pay_plus_part);

                runNext(pay_plus_part);

            }, 100);

        }).wait(function(runNext, pay_plus_part){
            //используем аргументы из предыдущего вызова

            $('.pay_plus1_'+tabel_id).html(pay_plus_part);
            pay_plus += pay_plus_part;
            pay_plus_part = 0;

            setTimeout(function(){

                $('.pay_minus_part1_'+tabel_id).each(function() {
                    pay_minus_part += Number($(this).html());
                    //console.log(Number($(this).html()));
                });
                //console.log(pay_minus_part);

                runNext(pay_plus, pay_plus_part, pay_minus_part);

            }, 100);

        }).wait(function(runNext, pay_plus, pay_plus_part, pay_minus_part){
            //используем аргументы из предыдущего вызова

            $('.pay_minus1_'+tabel_id).html(pay_minus_part);
            pay_minus += pay_minus_part;
            pay_minus_part = 0;

            setTimeout(function(){

                $('.pay_plus_part2_'+tabel_id).each(function() {
                    pay_plus_part += Number($(this).html());
                    //console.log(Number($(this).html()));
                });
                //console.log(pay_plus_part);

                runNext(pay_plus, pay_minus, pay_plus_part, pay_minus_part);

            }, 100);

        }).wait(function(runNext, pay_plus, pay_minus, pay_plus_part, pay_minus_part){
            //используем аргументы из предыдущего вызова

            $('.pay_plus2_'+tabel_id).html(pay_plus_part);
            pay_plus += pay_plus_part;

            setTimeout(function(){

                $('.pay_minus_part2_'+tabel_id).each(function() {
                    pay_minus_part += Number($(this).html());
                    //console.log(Number($(this).html()));
                });
                //console.log(pay_plus_part);

                runNext(pay_plus, pay_minus, pay_plus_part, pay_minus_part);

            }, 100);

        }).wait(function(runNext, pay_plus, pay_minus, pay_plus_part, pay_minus_part){

            $('.pay_minus2_'+tabel_id).html(pay_minus_part);
            pay_minus += pay_minus_part;

            $('.pay_must_'+tabel_id).html(pay_plus - pay_minus);

        });
    }

    //Приказ №8 перерасчёт - этап 2 реализация
    function fl_prikazNomerVosem_JustDoIt(tabel_id, newPercent, controlCategories){
        //console.log (tabel_id);
        //console.log (newPercent);
        //console.log (controlCategories);

        let link = "fl_prikazNomerVosem_JustDoIt_f.php";

        let reqData = {
            tabel_id: tabel_id,
            newPercent: newPercent,
            controlCategories: controlCategories
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //прячем кнопки
                $('#prikazButtons').hide();

                $('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if (res.result == 'success') {
                    let calc_ids_arr = Array.from(res.data);
                    //console.log(calc_ids_arr);

                    //!!! Хороший пример паузы в цикле (пауза в цикле) через рекурсию
                    //Не использовать, если есть вариант, что массив изменится во время
                    //И если обязательно индексы цифровые и по порядку
                    if (calc_ids_arr.length > 0) {

                        let foo = function (i) {
                            $("#prikazNomerVosem").html("<i>Обновляем данные для РЛ</i>: #<b>" + calc_ids_arr[i] + "</b><br>");

                            window.setTimeout(function () {
                                //console.log(calc_ids_arr[i]);

                                link = "fl_reloadPercentsMarkedCalculates.php";

                                reqData.tabel_id = tabel_id;
                                reqData.newPercent = newPercent;
                                reqData.controlCategories = controlCategories;

                                //Так как функция, находящаяся в fl_reloadPercentsMarkedCalculates.php
                                //Работает по-ебаному (лень просто переделывать, лепим костыли),
                                //а именно: ей нужно скормить перемменную вида chkBox_5_400_16
                                //В которой хранятся тип (стом, косм...)/5, ID работника/400, филиал/16)
                                //Поэтому создадим такую ебаную переменную reqData.data =)

                                reqData.data = 'chkBox_6_000_00';

                                reqData.main_data = [];

                                reqData.main_data[reqData.main_data.length] = calc_ids_arr[i];

                                //По каждому из id пересчитываем РЛ
                                $.ajax({
                                    url: link,
                                    global: false,
                                    type: "POST",
                                    dataType: "JSON",
                                    data: {
                                        calcArr: reqData
                                    },
                                    cache: false,
                                    beforeSend: function () {
                                    },
                                    // действие, при ответе с сервера
                                    success: function (res) {
                                        //console.log(res);

                                        if (res.result == "success") {
                                            //$("#prikazNomerVosem").append("<i>Новый РЛ</i>: <b>"+res.newCalcID+"</b> <i>создан<br></i>");
                                        }
                                    }
                                });

                                if (i < calc_ids_arr.length - 1) {
                                    foo(i + 1);
                                } else {
                                    //По окончании цикла, который выше, чего-то делаем
                                    //console.log("Обновляем сумму табеля.");

                                    $("#prikazNomerVosem").html("Обновляем сумму табеля.");

                                    link = "fl_updateTabelBalance_f.php";

                                    //А тут мне пришлось создать отдельный файл с функцией, которая тупо передаёт
                                    //дальше ID табеля и тот пересчитывает свою сумму.
                                    //По каждому из id пересчитываем РЛ
                                    $.ajax({
                                        url: link,
                                        global: false,
                                        type: "POST",
                                        dataType: "JSON",
                                        data: {
                                            tabel_id: tabel_id
                                        },
                                        cache: false,
                                        beforeSend: function () {
                                        },
                                        // действие, при ответе с сервера
                                        success: function (res) {
                                            //console.log(res);

                                            if (res.result == "success") {
                                                location.reload();
                                            } else {
                                                //console.log(res.data);
                                            }
                                        }
                                    });
                                }
                            }, 1000);
                        };
                        foo(0);
                    }
                }else{
                    $("#prikazNomerVosem").html("<i style='color: red;'>Нет РЛ, подходящих для перерасчёта</i>");
                }
            }
        });
    }

    //Приказ по Ботоксу перерасчёт - этап 2 подготовка
    function fl_prikazNomerBotoks_JustDoIt(tabel_id, /*newPercent, */controlCategories){
        console.log (tabel_id);
        //console.log (newPercent);
        console.log (controlCategories);

        let link = "fl_prikazNomerBotoks_JustDoIt_f.php";

        let reqData = {
            tabel_id: tabel_id,
            /*newPercent: newPercent,*/
            controlCategories: controlCategories
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function() {
                //прячем кнопки
                $('#prikazButtons').hide();

                $('#waitProcess').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5); margin: auto;'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                console.log(res);

                // if (res.result == 'success') {
                //     let calc_ids_arr = Array.from(res.data);
                //     //console.log(calc_ids_arr);
                //
                //     //!!! Хороший пример паузы в цикле (пауза в цикле) через рекурсию
                //     //Не использовать, если есть вариант, что массив изменится во время
                //     //И если обязательно индексы цифровые и по порядку
                //     if (calc_ids_arr.length > 0) {
                //
                //         let foo = function (i) {
                //             $("#prikazNomerVosem").html("<i>Обновляем данные для РЛ</i>: #<b>" + calc_ids_arr[i] + "</b><br>");
                //
                //             window.setTimeout(function () {
                //                 //console.log(calc_ids_arr[i]);
                //
                //                 link = "fl_reloadPercentsMarkedCalculates.php";
                //
                //                 reqData.tabel_id = tabel_id;
                //                 reqData.newPercent = newPercent;
                //                 reqData.controlCategories = controlCategories;
                //
                //                 //Так как функция, находящаяся в fl_reloadPercentsMarkedCalculates.php
                //                 //Работает по-ебаному (лень просто переделывать, лепим костыли),
                //                 //а именно: ей нужно скормить перемменную вида chkBox_5_400_16
                //                 //В которой хранятся тип (стом, косм...)/5, ID работника/400, филиал/16)
                //                 //Поэтому создадим такую ебаную переменную reqData.data =)
                //
                //                 reqData.data = 'chkBox_6_000_00';
                //
                //                 reqData.main_data = [];
                //
                //                 reqData.main_data[reqData.main_data.length] = calc_ids_arr[i];
                //
                //                 //По каждому из id пересчитываем РЛ
                //                 $.ajax({
                //                     url: link,
                //                     global: false,
                //                     type: "POST",
                //                     dataType: "JSON",
                //                     data: {
                //                         calcArr: reqData
                //                     },
                //                     cache: false,
                //                     beforeSend: function () {
                //                     },
                //                     // действие, при ответе с сервера
                //                     success: function (res) {
                //                         //console.log(res);
                //
                //                         if (res.result == "success") {
                //                             //$("#prikazNomerVosem").append("<i>Новый РЛ</i>: <b>"+res.newCalcID+"</b> <i>создан<br></i>");
                //                         }
                //                     }
                //                 });
                //
                //                 if (i < calc_ids_arr.length - 1) {
                //                     foo(i + 1);
                //                 } else {
                //                     //По окончании цикла, который выше, чего-то делаем
                //                     //console.log("Обновляем сумму табеля.");
                //
                //                     $("#prikazNomerVosem").html("Обновляем сумму табеля.");
                //
                //                     link = "fl_updateTabelBalance_f.php";
                //
                //                     //А тут мне пришлось создать отдельный файл с функцией, которая тупо передаёт
                //                     //дальше ID табеля и тот пересчитывает свою сумму.
                //                     //По каждому из id пересчитываем РЛ
                //                     $.ajax({
                //                         url: link,
                //                         global: false,
                //                         type: "POST",
                //                         dataType: "JSON",
                //                         data: {
                //                             tabel_id: tabel_id
                //                         },
                //                         cache: false,
                //                         beforeSend: function () {
                //                         },
                //                         // действие, при ответе с сервера
                //                         success: function (res) {
                //                             //console.log(res);
                //
                //                             if (res.result == "success") {
                //                                 location.reload();
                //                             } else {
                //                                 //console.log(res.data);
                //                             }
                //                         }
                //                     });
                //                 }
                //             }, 1000);
                //         };
                //         foo(0);
                //     }
                // }else{
                //     $("#prikazNomerVosem").html("<i style='color: red;'>Нет РЛ, подходящих для перерасчёта</i>");
                // }
            }
        });

    }

    //Приказ №8 перерасчёт - этап 1 подготовка
    function prikazNomerVosem(worker_id, tabel_id){

        let rys = true;

        rys = confirm("Внимание\nВсе расчётные листы в табеле и общая сумма\nбудут пересчитаны в соответствии\nс приказом №8.\n\nВы уверены?");

        if (rys) {
            //console.log(worker_id);

            let link = "fl_prikazNomerVosem.php";

            let reqData = {
                worker_id: worker_id,
                tabel_id: tabel_id
            };

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
                    //console.log(res);

                    //$("#prikazNomerVosem").html(res);

                    if(res.result == "success"){
                        //console.log(JSON.stringify(res.controlCategories));

                        $('#overlay').show();

                        let buttonsStr = '<input type="button" class="b" value="Применить" onclick="fl_prikazNomerVosem_JustDoIt('+tabel_id+', '+res.newPaymentPercent+', '+JSON.stringify(res.controlCategories)+');">';

                        // Создаем меню:
                        let menu = $('<div/>', {
                            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
                        }).css({"height": "200px"})
                            .appendTo('#overlay')
                            .append(
                                $('<div/>')
                                    .css({
                                        "height": "100%",
                                        "border": "1px solid #AAA",
                                        "position": "relative"
                                    })
                                    .append('<span style="margin: 5px;"><i>Проверьте и нажмите применить</i></span>')
                                    .append(
                                        $('<div/>')
                                            .css({
                                                "position": "absolute",
                                                "width": "100%",
                                                "margin": "auto",
                                                "top": "40px",
                                                "left": "0",
                                                "bottom": "0",
                                                "right": "0",
                                                "height": "80%"
                                            })
                                            .append('<div id="waitProcess">' +
                                                '<div style="margin: 5px; font-size: 90%;">Общая сумма выручки: <span class="calculateInsInvoice">'+res.allSumm+'</span> руб.</div>' +
                                                '<div style="margin: 5px; font-size: 90%;">Сумма за эпиляции: <span class="calculateInvoice">'+res.controlCategoriesSumm+'</span> руб. (<span class="calculateInvoice">'+res.controlPercent+'%</span>)</div>' +
                                                '<div style="margin: 20px; font-size: 90%;">Новый процент за эпиляции: <span class="calculateOrder">'+res.newPaymentPercent+' %</span> </div>' +
                                                '</div>' +
                                                '<div id="prikazNomerVosem" style="margin: 10px;"></div>')
                                    )
                                    .append(
                                        $('<div/ id="prikazButtons">')
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

                    }else{
                        console.log(res);

                    }
                }
            });
        }
    }

    //Приказ по Ботоксу перерасчёт - этап 1 подготовка
    function prikazNomerBotoks(worker_id, tabel_id){
        // alert ('Я еще не готов');

        let rys = true;

        //rys = confirm("Внимание\nВсе расчётные листы в табеле и общая сумма\nбудут пересчитаны\n\nВы уверены?");

        if (rys) {
            //console.log(worker_id);

            let link = "fl_prikazNomerBotoks.php";

            let reqData = {
                worker_id: worker_id,
                tabel_id: tabel_id
            };

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
                    //console.log(res);

                    $("#prikazNomerVosem").html(res);

                    if(res.result == "success"){
                        // console.log(JSON.stringify(res.controlCategories));

                        $('#overlay').show();

                        //let buttonsStr = '<input type="button" class="b" value="Применить" onclick="fl_prikazNomerBotoks_JustDoIt('+tabel_id+', '+JSON.stringify(res.controlCategories)+');">';
                        let buttonsStr = '';

                        // Создаем меню:
                        let menu = $('<div/>', {
                            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
                        }).css({"height": "200px"})
                            .appendTo('#overlay')
                            .append(
                                $('<div/>')
                                    .css({
                                        "height": "100%",
                                        "border": "1px solid #AAA",
                                        "position": "relative"
                                    })
                                    .append('<span style="margin: 5px;"><i>Проверьте и нажмите применить</i></span>')
                                    .append(
                                        $('<div/>')
                                            .css({
                                                "position": "absolute",
                                                "width": "100%",
                                                "margin": "auto",
                                                "top": "40px",
                                                "left": "0",
                                                "bottom": "0",
                                                "right": "0",
                                                "height": "80%"
                                            })
                                            .append('<div id="waitProcess">' +
                                                '<div style="margin: 5px; font-size: 90%;">Всего сделано ботокса (на всех филиалах): <span class="calculateInvoice">'+res.controlCategoriesSummCount+'</span> шт.<br> на сумму: <span class="calculateInsInvoice">'+res.controlCategoriesSumm+'</span> руб.</div>' +
                                                // '<div style="margin: 5px; font-size: 90%;">Сумма за эпиляции: <span class="calculateInvoice">'+res.controlCategoriesSumm+'</span> руб. (<span class="calculateInvoice">'+res.controlPercent+'%</span>)</div>' +
                                                // '<div style="margin: 20px; font-size: 90%;">Новый процент за эпиляции: <span class="calculateOrder">'+res.newPaymentPercent+' %</span> </div>' +
                                                '</div>' +
                                                '<div id="prikazNomerVosem" style="margin: 10px;"></div>')
                                    )
                                    .append(
                                        $('<div/ id="prikazButtons">')
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

                    }else{
                        console.log(res);

                    }
                }
            });
        }
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

        //Памятка
        // 1 - аванс
        // 2 - отпускной
        // 3 - больничный
        // 4 - на карту
        // 7 - зп
        // 5 - ночь

        //Все выплаты ЗП и тп
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
                            -
                            Number(
                                $("#SummGiveoutMonth").html().replace(/\s{1,}/g, '')
                            )
                        )
                        , 2, '.', ' ')
                    );

                    $("#ostatokFinalNalAllMonth2").html(number_format(
                        (Number(
                                $("#ostatokNalAllMonth").html().replace(/\s{1,}/g, '')
                            )
                            -
                            Number(
                                $("#SummGiveoutMonth").html().replace(/\s{1,}/g, '')
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
    var changeTax_elems = document.getElementsByClassName("changeCurrentTax"), newInputTax;
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
                if (!newInputTax) {

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

                    newInputTax = document.createElement("input");
                    newInputTax.type = "text";
                    newInputTax.maxLength = 10;
                    newInputTax.setAttribute("size", 20);
                    newInputTax.style.width = "50px";
                    newInputTax.addEventListener("blur", function () {
                        //console.log(newInputTax.parentNode.getAttribute("worker_id"));

                        workerID = newInputTax.parentNode.getAttribute("worker_id");
                        //catID = newInputTax.parentNode.getAttribute("cat_id");
                        //typeID = newInputTax.parentNode.getAttribute("type_id");

                        //Попытка обработать клика на кнопке для сброса на значения по умолчанию - провалилась, всегда сбрасывается на по умолчанию
                        //var changePersonalPercentCatdefault = document.getElementById("changePersonalPercentCatdefault");
                        //console.log(changePersonalPercentCatdefault.innerHTML);

                        //changePersonalPercentCatdefault.addEventListener("click", fl_changePersonalPercentCatdefault(workerID, catID, typeID), false);

                        //Новые данные
                        //if ((newInputTax.value == "") || (isNaN(newInputTax.value)) || (newInputTax.value < 0) || (newInputTax.value > 100) || (isNaN(parseInt(newInputTax.value, 10)))) {
                        if ((newInputTax.value == "") || (isNaN(newInputTax.value)) || (newInputTax.value < 0) || (isNaN(parseInt(newInputTax.value, 10)))) {
                            //newInputTax.parentNode.innerHTML = 0;
                            newInputTax.parentNode.innerHTML = thisVal;
                            newVal = thisVal;
                        } else {
                            newInputTax.parentNode.innerHTML = number_format(parseFloat(newInputTax.value, 10), 2, '.', '');
                            newVal = number_format(parseFloat(newInputTax.value, 10), 2, '.', '');
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

                //newInputTax.value = this.firstChild.innerHTML;
                newInputTax.value = thisVal;
                this.innerHTML = "";
                //this.appendChild(buttonDiv);
                this.appendChild(newInputTax);
                //newInputTax.innerHTML = ('<i class="fa fa-check" aria-hidden="true"></i>');
                newInputTax.focus();
                newInputTax.select();
            }.bind(el), false);
        }
    }

    /*$("body").on("click", "#click_id", function(){
        alert('1234');
    });*/

    //Редактирование надбавки / добавление новой строчки
    var changeSur_elems = document.getElementsByClassName("changeCurrentSur"), newInputSur;
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
                if (!newInputSur) {

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

                    newInputSur = document.createElement("input");
                    newInputSur.type = "text";
                    newInputSur.maxLength = 10;
                    newInputSur.setAttribute("size", 20);
                    newInputSur.style.width = "50px";
                    newInputSur.addEventListener("blur", function () {
                        //console.log(newInputSur.parentNode.getAttribute("worker_id"));

                        workerID = newInputSur.parentNode.getAttribute("worker_id");
                        //catID = newInputSur.parentNode.getAttribute("cat_id");
                        //typeID = newInputSur.parentNode.getAttribute("type_id");

                        //Попытка обработать клика на кнопке для сброса на значения по умолчанию - провалилась, всегда сбрасывается на по умолчанию
                        //var changePersonalPercentCatdefault = document.getElementById("changePersonalPercentCatdefault");
                        //console.log(changePersonalPercentCatdefault.innerHTML);

                        //changePersonalPercentCatdefault.addEventListener("click", fl_changePersonalPercentCatdefault(workerID, catID, typeID), false);

                        //Новые данные
                        //if ((newInputSur.value == "") || (isNaN(newInputSur.value)) || (newInputSur.value < 0) || (newInputSur.value > 100) || (isNaN(parseInt(newInputSur.value, 10)))) {
                        if ((newInputSur.value == "") || (isNaN(newInputSur.value)) || (newInputSur.value < 0) || (isNaN(parseInt(newInputSur.value, 10)))) {
                            //newInputSur.parentNode.innerHTML = 0;
                            newInputSur.parentNode.innerHTML = thisVal;
                            newVal = thisVal;
                        } else {
                            newInputSur.parentNode.innerHTML = number_format(parseFloat(newInputSur.value, 10), 2, '.', '');
                            newVal = number_format(parseFloat(newInputSur.value, 10), 2, '.', '');
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

                //newInputSur.value = this.firstChild.innerHTML;
                newInputSur.value = thisVal;
                this.innerHTML = "";
                //this.appendChild(buttonDiv);
                this.appendChild(newInputSur);
                //newInputSur.innerHTML = ('<i class="fa fa-check" aria-hidden="true"></i>');
                newInputSur.focus();
                newInputSur.select();
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

    //Функция изменения процента от выручки
    function Ajax_revenue_percent_change (table, type, filial_id, category){

        var value = $("#revenuePercent").val();
        value =  value.replace(',', '.');
        //console.log(value);

        if (!isNaN(value)) {
            if (value.length > 0){

                // if (table == 'solar') {
                //     var link = "fl_revenue_percent_solar_change_f.php";
                // }else{
                //     if (table == 'realiz'){
                //         var link = "fl_revenue_percent_realiz_change_f.php";
                //     }else{
                //         var link = "fl_revenue_percent_change_f.php";
                //     }
                // }

                var link = "fl_revenue_percent_change_f.php";

                var reqData = {
                    table: table,
                    permission: type,
                    filial_id: filial_id,
                    category: category,
                    value: value
                };

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
                    success: function (res) {
                        //console.log (res);

                        if (res.result == "success") {
                            setTimeout(function () {
                                location.reload()
                            }, 200);
                        } else {

                        }
                    }
                })

            }else {
            }
        }else{
            //console.log(value);
        }
    }

    //Показывает окно для изменения процента от выручки
    function revenuePercentChangeShow (table, haveValue, type, type_name, filial_id, filial_name, category, category_name, value){
        //console.log(mode);
        $('#overlay').show();


        var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_revenue_percent_change(\''+table+'\', '+type+', '+filial_id+', '+category+')">';

        // if (haveValue){
        // }

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
                    .append('<div style="margin: 5px;"><i><b>'+filial_name+'</b></i></div>')
                    .append('<div style="margin: 5px;">'+type_name+'</div>')
                    .append('<div style="margin: 5px;">Категория: <i>'+category_name+'</i></div>')
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
                            .append('<div style="margin: 50px;"><input type="text" id="revenuePercent" value="'+value+'">%</div>')
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

        $("#revenuePercent").focus();
    }

    //Показывает окно с процентами
    function showPersonalPecentHere (worker_id, worker_name){
        //console.log(mode);
        $('#overlay').show();

        var res_str = '<table style="font-size: 80%;">'+
        '<tr><td>Наим.</td><td>Работа</td><td>Материал</td><td>Фикс.</td></tr>';

        $(".percentCatItem").each(function() {
            var cat_id = $(this).attr("cat_id");
            var cat_name = ($(this).html());
            // console.log(cat_id);
            // console.log(cat_name);

            var cpp1 = $(".cpp_1_"+worker_id+"_"+cat_id).html();
            var cpp2 = $(".cpp_2_"+worker_id+"_"+cat_id).html();
            var cpp3 = $(".cpp_3_"+worker_id+"_"+cat_id).html();

            res_str += '<tr>';
            res_str += '<td style="text-align: left; border-bottom: 1px solid rgb(119, 95, 95);">' + cat_name + ' :</td>';
            if (cpp1 > 0)
            res_str += '<td style="border-bottom: 1px solid rgb(119, 95, 95);">' + cpp1 + '%</td>';
            if (cpp2 > 0)
            res_str += '<td style="border-bottom: 1px solid rgb(119, 95, 95);">' + cpp2 + '%</td>';
            if (cpp3 > 0)
            res_str += '<td style="border-bottom: 1px solid rgb(119, 95, 95);">' + cpp3 + ' руб.</td>';
            res_str += '</tr>';

        });

        res_str += '</table>';

        // Создаем меню:
        var menu = $('<div/>', {
            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        }).css({
            "bottom": "auto", /*Выравниваем вверх*/
            "height": "auto", /*Выравниваем высоту*/
            "width": "50%" /*Выравниваем ширину*/
        })
            .appendTo('#overlay')
            .append(
                $('<div/>')
                    .css({
                        "height": "100%",
                        "border": "1px solid #AAA",
                        "position": "relative"
                    })
                    .append('<div style="margin: 5px;"><i><b>'+worker_name+'</b></i></div>')
                    .append('<div style="margin: 5px;">'+res_str +'</div>')
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
                                "height": "50%"
                            })
                            .append('<div style="margin: 50px;"><input type="text" id="revenuePercent" value=">%</div>')
                    )
                    .append(
                        $('<div/>')
                            .css({
                                /*"position": "absolute",*/
                                "bottom": "2px",
                                "width": "100%"
                            })
                            .append(
                                '<input type="button" class="b" value="Закрыть" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                            )
                    )
            );

        menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

        $("#revenuePercent").focus();
    }


    //Рассчёт общих часов сотрудников за месяц
    function calculateWorkerHours(){
        $(".workerItem").each(function() {
            var worker_id = ($(this).attr("worker_id"));
            //console.log(worker_id);

            var summHours = 0;

            $(".dayHours_"+worker_id).each(function() {
                summHours += parseFloat($(this).html(), 10) || 0;
                //summHours += $(this).html();
                //console.log($(this).html());
                //console.log(parseInt($(this).html(), 10));
            });
            //console.log(summHours);

            //Выведем кол-во часов
            $("#allMonthHours_"+worker_id).html(number_format(summHours, 2, '.', ' '));

            //Берем норму смен этого месяца для этого сотрудника
            //!!! Хотя норма для всех одинакова по сути... короче бред тут каждый раз брать одно и то же с разных мест

            var normaSmen = parseInt($("#allMonthNorma_"+worker_id).html(), 10) || 0;
            //console.log(normaSmen);

            var hoursMonthPercent = 0;

            if (normaSmen == 0){
            }else {
                hoursMonthPercent = summHours * 100 / normaSmen;
            }

            $("#hoursMonthPercent_"+worker_id).html(number_format(hoursMonthPercent, 2, '.', ' '));

            $("#schedulerResult_"+worker_id).css({
                "background-image": "linear-gradient(to right, " + Colorize(Number(hoursMonthPercent.toFixed(0)), 1) + " " + Number(hoursMonthPercent.toFixed(0)) + "%, rgba(255, 255, 255, 0) 0%)"
            });
            //console.log("linear-gradient(to right, " + Colorize(hoursMonthPercent.toFixed(0)) + " " + hoursMonthPercent.toFixed(0) + "%, rgba(255, 255, 255, 0) 0%)");

        });
    }

    //Рассчёт общих дней сотрудников за месяц
    function calculateWorkerDays(){
        $(".workerItem").each(function() {
            var worker_id = ($(this).attr("worker_id"));
            //console.log(worker_id);

            //var summHours = 0;
            var summDays = 0;

            $(".dayHours_"+worker_id).each(function() {
                //summHours += parseFloat($(this).html(), 10) || 0;
                //summHours += $(this).html();
                //console.log($(this).html());
                //console.log(parseInt($(this).html(), 10));
                summDays += 1 || 0;
            });
            //console.log(summHours);
            //console.log(summDays);

            //Выведем кол-во часов
            // $("#allMonthHours_"+worker_id).html(summHours);
            $("#allMonthHours_"+worker_id).html(summDays);

            //Берем норму смен этого месяца для этого сотрудника
            //!!! Хотя норма для всех одинакова по сути... короче бред тут каждый раз брать одно и то же с разных мест

            var normaSmen = parseInt($("#allMonthNorma_"+worker_id).html(), 10) || 0;
            //console.log(normaSmen);

            var hoursMonthPercent = 0;

            if (normaSmen == 0){
            }else {
                hoursMonthPercent = summHours * 100 / normaSmen;
            }

            $("#hoursMonthPercent_"+worker_id).html(number_format(hoursMonthPercent, 2, '.', ' '));

            $("#schedulerResult_"+worker_id).css({
                "background-image": "linear-gradient(to right, " + Colorize(Number(hoursMonthPercent.toFixed(0)), 1) + " " + Number(hoursMonthPercent.toFixed(0)) + "%, rgba(255, 255, 255, 0) 0%)"
            });
            //console.log("linear-gradient(to right, " + Colorize(hoursMonthPercent.toFixed(0)) + " " + hoursMonthPercent.toFixed(0) + "%, rgba(255, 255, 255, 0) 0%)");

        });
    }

    //Получение выручек всех филиалов и расчет зп
    function fl_calculateZP (month, year, typeW){
        // console.log(month);
        // console.log(year);
        // console.log(typeW);

        var link = "fl_calculateZP_f.php";

        var reqData = {
            month: month,
            year: year,
            typeW: typeW
        };

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
            success: function (res) {
                //console.log (res.data_solar);

                if (res.result == "success") {
                    //console.log (res);

                    //Выручка
                    $(".filialMoney").each(function(){
                        //console.log($(this).attr("filial_id"));

                        var filial_id = $(this).attr("filial_id");
                        var worker_id = $(this).attr("w_id");

                        //Если есть прикрепление к филиалу
                        if (filial_id > 0){
                            //console.log(filial_id);
                            //console.log(res.data[filial_id]);

                            $(this).html(number_format(res.data[filial_id], 2, '.', ' '));

                            $("#w_id_"+worker_id).attr("filialMoney", number_format(res.data[filial_id], 2, '.', ''));
                        }else{
                            //$(this).html('<span style="color: rgb(243, 0, 0);">не прикреплен</span>');
                            $(this).html('0.00');

                            $("#w_id_"+worker_id).attr("filialMoney", 0);
                        }
                    });

                    //Солярий
                    $(".filialSolar").each(function(){
                        //console.log($(this).attr("filial_id"));
                        //console.log(res.data_solar[filial_id]);

                        var filial_id = $(this).attr("filial_id");
                        var worker_id = $(this).attr("w_id");

                        //Если есть прикрепление к филиалу и только для 17 филиала (Черн)
                        if ((filial_id > 0) && (filial_id == 17)){
                            //console.log(filial_id);
                            //console.log(res.data[filial_id]);
                            if (typeof(res.data_solar[filial_id]) != "undefined" && res.data_solar[filial_id] !== null) {
                                $(this).html(number_format(res.data_solar[filial_id]['solar'], 2, '.', ' '));

                                $("#w_id_" + worker_id).attr("filialSolar", number_format(res.data_solar[filial_id]['solar'], 2, '.', ''));
                            }else{
                                $(this).html('0.00');

                                $("#w_id_"+worker_id).attr("filialSolar", 0);
                            }
                        }/*else{
                            //$(this).html('<span style="color: rgb(243, 0, 0);">не прикреплен</span>');
                            $(this).html('0.00');

                            $("#w_id_"+worker_id).attr("filialSolar", 0);
                        }*/
                    });

                    //Реализация
                    $(".filialRealiz").each(function(){
                        //console.log($(this).attr("filial_id"));

                        var filial_id = $(this).attr("filial_id");
                        var worker_id = $(this).attr("w_id");

                        //Если есть прикрепление к филиалу и только для 17 филиала (Черн)
                        if ((filial_id > 0) && (filial_id == 17)){
                            //console.log(filial_id);
                            //console.log(res.data[filial_id]);
                            if (typeof(res.data_solar[filial_id]) != "undefined" && res.data_solar[filial_id] !== null) {
                                $(this).html(number_format(res.data_solar[filial_id]['realiz'], 2, '.', ' '));

                                $("#w_id_" + worker_id).attr("filialRealiz", number_format(res.data_solar[filial_id]['realiz'], 2, '.', ''));
                            }else{
                                $(this).html('0.00');

                                $("#w_id_"+worker_id).attr("filialRealiz", 0);
                            }
                        }/*else{
                            //$(this).html('<span style="color: rgb(243, 0, 0);">не прикреплен</span>');
                            $(this).html('0.00');

                            $("#w_id_"+worker_id).attr("filialRealiz", 0);
                        }*/
                    });

                    //Абонементы
                    $(".filialAbon").each(function(){
                        //console.log($(this).attr("filial_id"));

                        var filial_id = $(this).attr("filial_id");
                        var worker_id = $(this).attr("w_id");

                        //Если есть прикрепление к филиалу и только для 17 филиала (Черн)
                        if ((filial_id > 0) && (filial_id == 17)){
                            //console.log(filial_id);
                            //console.log(res.data[filial_id]);
                            if (typeof(res.data_solar[filial_id]) != "undefined" && res.data_solar[filial_id] !== null) {
                                $(this).html(number_format(res.data_solar[filial_id]['abon'], 2, '.', ' '));

                                $("#w_id_" + worker_id).attr("filialAbon", number_format(res.data_solar[filial_id]['abon'], 2, '.', ''));
                            }else{
                                $(this).html('0.00');

                                $("#w_id_"+worker_id).attr("filialAbon", 0);
                            }
                        }/*else{
                            //$(this).html('<span style="color: rgb(243, 0, 0);">не прикреплен</span>');
                            $(this).html('0.00');

                            $("#w_id_"+worker_id).attr("filialAbon", 0);
                        }*/
                    });

                    $(".itogZP").each(function(){
                        //console.log("itogZP");

                        var worker_id = $(this).attr("w_id");

                        var oklad = Number($(this).attr("oklad"));
                        var w_percentHours = Number($(this).attr("w_percentHours"));
                        var worker_revenue_percent = Number($(this).attr("worker_revenue_percent"));
                        var filialMoney = Number($(this).attr("filialMoney"));

                        var worker_revenue_solar_percent = Number($(this).attr("worker_revenue_solar_percent"));
                        var filialSolar = Number($(this).attr("filialSolar"));
                        var worker_revenue_realiz_percent = Number($(this).attr("worker_revenue_realiz_percent"));
                        var filialRealiz = Number($(this).attr("filialRealiz"));
                        var worker_revenue_abon_percent = Number($(this).attr("worker_revenue_abon_percent"));
                        var filialAbon = Number($(this).attr("filialAbon"));

                        //if (worker_id == 518) {
                            // console.log(w_percentHours);
                            // console.log(worker_revenue_solar_percent);
                            // console.log(filialSolar);
                        //}

                        if (w_percentHours > 0){

                            var zp_temp = 0;
                            var revenue_summ = 0;
                            var revenue_solar_summ = 0;
                            var revenue_realiz_summ = 0;
                            var revenue_abon_summ = 0;

                            //Администраторы
                            // if (typeW == 4) {
                            //     zp_temp = (oklad * w_percentHours) / 100;
                            // }
                            //Ассистенты
                            // if (typeW == 7) {
                            //     var norma_smen = Number($("#w_norma_"+worker_id).html());
                            //     //console.log(norma_smen);
                            //     zp_temp = (oklad * norma_smen * w_percentHours) / 100;
                            // }

                            zp_temp = (oklad * w_percentHours) / 100;

                            revenue_summ = (((filialMoney / 100) * worker_revenue_percent) / 100) * w_percentHours;
                            // if (worker_id == 518) {
                            //     console.log(filialMoney);
                            //     console.log(worker_revenue_percent);
                            //     console.log(w_percentHours);
                            //     console.log(revenue_summ);
                            // }

                            revenue_solar_summ = (((filialSolar / 100) * worker_revenue_solar_percent) / 100) * w_percentHours;
                            revenue_realiz_summ = (((filialRealiz / 100) * worker_revenue_realiz_percent) / 100) * w_percentHours;
                            revenue_abon_summ = (((filialAbon / 100) * worker_revenue_abon_percent) / 100) * w_percentHours;
                            //console.log(revenue_abon_summ);


                            //var itogZP = zp_temp + revenue_summ;
                            var itogZP = zp_temp + revenue_summ + revenue_solar_summ + revenue_realiz_summ + revenue_abon_summ;
                            //console.log(itogZP);

                            $("#zp_temp_"+worker_id).html(number_format(zp_temp, 2, '.', ''));
                            $("#w_revenue_summ_"+worker_id).html(number_format(revenue_summ, 2, '.', ''));

                            $("#w_revenue_solar_summ_"+worker_id).html(number_format(revenue_solar_summ, 2, '.', ''));
                            $("#w_revenue_realiz_summ_"+worker_id).html(number_format(revenue_realiz_summ, 2, '.', ''));
                            $("#w_revenue_abon_summ_"+worker_id).html(number_format(revenue_abon_summ, 2, '.', ''));
                            //console.log("#zp_temp_"+worker_id);
                            $(this).html(number_format(itogZP, 0, '.', ''));
                        }else{
                            $(this).html(number_format(itogZP, 0, '.', ''));
                        }

                        //Раскрасим часы рабочие
                        $("#w_hours_"+worker_id).css({
                            "background-image": "linear-gradient(to right, " + Colorize(Number(w_percentHours.toFixed(0)), .5) + " " + Number(w_percentHours.toFixed(0)) + "%, rgba(255, 255, 255, 0) 0%)"
                        });
                    })

                }else{
                    //--
                }
            }
        })
    }

    //Рассчет зп для fl_tabels3.php
    //для санитарок, дворников, уборщиц
    function fl_calculateZP2 (month, year, typeW){
        // console.log(month);
        // console.log(year);
        // console.log(typeW);

        $(".itogZP").each(function(){

            var worker_id = $(this).attr("w_id");

            var oklad = Number($(this).attr("oklad"));
            var w_percentHours = Number($(this).attr("w_percentHours"));
            var worker_revenue_percent = Number($(this).attr("worker_revenue_percent"));
            var filialMoney = Number($(this).attr("filialMoney"));
            //console.log(w_percentHours);

            if (w_percentHours > 0){

                var zp_temp = 0;
                var revenue_summ = 0;

                //Администраторы
                // if (typeW == 4) {
                //     zp_temp = (oklad * w_percentHours) / 100;
                // }
                //Ассистенты
                // if (typeW == 7) {
                //     var norma_smen = Number($("#w_norma_"+worker_id).html());
                //     //console.log(norma_smen);
                //     zp_temp = (oklad * norma_smen * w_percentHours) / 100;
                // }

                zp_temp = (oklad * w_percentHours) / 100;
                //console.log(zp_temp);

                //revenue_summ = (((filialMoney / 100) * worker_revenue_percent) / 100) * w_percentHours;
                //console.log(revenue_summ);

                var itogZP = zp_temp + revenue_summ;
                //console.log(itogZP);

                $("#zp_temp_"+worker_id).html(number_format(zp_temp, 2, '.', ''));
                $("#w_revenue_summ_"+worker_id).html(number_format(revenue_summ, 2, '.', ''));
                //console.log("#zp_temp_"+worker_id);
                $(this).html(number_format(itogZP, 0, '.', ''));
            }else{
                $(this).html(number_format(itogZP, 0, '.', ''));
            }

            //Раскрасим часы рабочие
            $("#w_hours_"+worker_id).css({
                "background-image": "linear-gradient(to right, " + Colorize(Number(w_percentHours.toFixed(0)), .5) + " " + Number(w_percentHours.toFixed(0)) + "%, rgba(255, 255, 255, 0) 0%)"
            });
        })


        // $(".itogZP").each(function(){
        //
        //     //var worker_id = $(this).attr("w_id");
        //
        //     var oklad = Number($(this).attr("oklad"));
        //     //console.log(oklad);
        //
        //     var itogZP = oklad;
        //     //console.log(itogZP);
        //
        //     $(this).html(number_format(itogZP, 0, '.', ''));
        // })
    }


    //Получение табелей за этот месяц
    function fl_getAllTabels (month, year, typeW){
        // console.log(month);
        // console.log(year);

        var link = "fl_getAllTabels_f.php";

        var reqData = {
            month: month,
            year: year,
            typeW: typeW
        };

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
            success: function (res) {
                //console.log (res);

                if (res.result == "success") {
                    //console.log (res.data);

                    for(var worker_id in res.data){
                        // console.log (res.data[worker_id]);
                        // console.log (res.data[worker_id]['summ']);
                        // console.log (Number($('#w_id_' + worker_id).html()));
                        // console.log (res.data[worker_id]['summ'] == Number($('#w_id_' + worker_id).html()));


                        if (res.data[worker_id]['status'] == 7){
                            $("#worker_" + worker_id).html("<a href='fl_tabel.php?id=" + res.data[worker_id]['id'] + "' class='ahref'><i class='fa fa-file-text' aria-hidden='true' style='color: rgba(13,236,109,0.98); font-size: 130%;' title='Табель проведён'></i></a> " +
                                "");
                        }else {
                            if (res.data[worker_id]['summ'] == Number($('#w_id_' + worker_id).html())) {
                                $("#worker_" + worker_id).html("<a href='fl_tabel.php?id=" + res.data[worker_id]['id'] + "' class='ahref'><i class='fa fa-file-text' aria-hidden='true' style='color: rgba(215, 34, 236, 0.98); font-size: 130%;' title='Табель не проведён'></i></a> " +
                                    "");
                            } else {
                                $("#worker_" + worker_id).html("<a href='fl_tabel.php?id=" + res.data[worker_id]['id'] + "' class='ahref'><i class='fa fa-file-text' aria-hidden='true' style='color: rgba(236,31,0,0.98); font-size: 130%;' title='Обновите данные табели'></i></a> " +
                                    "<i class='fa fa-refresh' aria-hidden='true' style='color: rgb(218, 133, 9); font-size: 100%; cursor: pointer;' title='Обновить' onclick=\'refreshTabelForWorkerFromSchedulerReport("+res.data[worker_id]['id']+", "+worker_id+", true);\'></i>");
                            }
                        }
                    }

                }else{
                    //--
                }
            }
        })
    }

    //Поиск на странице отсутствующих табелей и попытка создать их
    function addAllTabelsForWorkerFromSchedulerReport(){

        $(".fa-plus").each(function() {
            //console.log($(this).attr('onclick'));

            var str_temp = $(this).attr('onclick');
            // console.log(str_temp);

            if (str_temp !== undefined){

                //var str = str_temp.split('(')[1];
                var str = str_temp.substring(str_temp.indexOf('(')+1, str_temp.indexOf(')'));
                //console.log(str.replace(/\s/g, ''));

                var worker_id = str.split(',')[0].replace(/\s/g, '');
                var filial_id = str.split(',')[1].replace(/\s/g, '');
                var type_id = str.split(',')[2].replace(/\s/g, '');

                // console.log(worker_id);
                // console.log(filial_id);
                // console.log(type_id);

                if (Number($("#w_id_" + worker_id).html()) > 0) {
                    addNewTabelForWorkerFromSchedulerReport(worker_id, filial_id, type_id);
                }

            }

            //пример обрезки строки, красиво
            // var str = "50ml+$100";
            // var a = str.split('+')[0]; // 50ml
            // var b = str.split('+')[1]; // $100
        })
    }

    //Добавление нового табеля админа, ассиста, ...
    function addNewTabelForWorkerFromSchedulerReport(worker_id, filial_id, type, all=true){
        // console.log(tabel_id);
        // console.log(worker_id);
        // console.log($("#w_id_"+worker_id).attr("oklad"));
        // console.log($("#w_id_"+worker_id).attr("w_percenthours"));
        // console.log($("#w_id_"+worker_id).attr("worker_revenue_percent"));
        //  console.log(Number($("#zp_temp_" + worker_id).html()));
        // console.log($("#w_id_"+worker_id).attr("filialmoney"));
        // console.log($("#w_id_"+worker_id).attr("worker_category_id"));
        // console.log($("#w_id_"+worker_id).attr("w_hours"));
        // console.log(Number($("#w_id_"+worker_id).html()));

        if (!all) {
            var rys = false;

            rys = confirm("Добавить новый табель?");
        }else{
            // rys = confirm("1");
        }

        if (rys || all) {

            var link = "fl_addNewTabelForWorkerFromSchedulerReport_f.php";

            var reqData = {
                worker_id: worker_id,
                filial_id: filial_id,
                type: type,
                month: $("#iWantThisMonth").val(),
                year: $("#iWantThisYear").val(),
                oklad: $("#w_id_" + worker_id).attr("oklad"),
                w_percenthours: $("#w_id_" + worker_id).attr("w_percenthours"),
                worker_revenue_percent: $("#w_id_" + worker_id).attr("worker_revenue_percent"),
                worker_revenue_solar_percent: $("#w_id_" + worker_id).attr("worker_revenue_solar_percent"),
                worker_revenue_realiz_percent: $("#w_id_" + worker_id).attr("worker_revenue_realiz_percent"),
                worker_revenue_abon_percent: $("#w_id_" + worker_id).attr("worker_revenue_abon_percent"),
                per_from_salary: Number($("#zp_temp_" + worker_id).html()),
                filialmoney: $("#w_id_" + worker_id).attr("filialmoney"),
                filialsolar: $("#w_id_" + worker_id).attr("filialSolar"),
                filialrealiz: $("#w_id_" + worker_id).attr("filialRealiz"),
                filialabon: $("#w_id_" + worker_id).attr("filialAbon"),
                w_revenue_summ: Number($("#w_revenue_summ_"+worker_id).html()),
                worker_category_id: $("#w_id_" + worker_id).attr("worker_category_id"),
                w_hours: $("#w_id_" + worker_id).attr("w_hours"),
                summ: Number($("#w_id_" + worker_id).html())
            };
            // console.log(reqData);

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
                success: function (res) {
                    //console.log (res);
                    //$("#errrror").html(res);

                    if (res.result == "success") {
                        //console.log(res.data);

                        location.reload();

                        //fl_getAllTabels ($("#iWantThisMonth").val(), $("#iWantThisYear").val(), type);
                    } else {
                        $("#errrror").html(res.data);
                        $('html, body').scrollTop(0);
                    }
                }
            })
        }

    }

    //Обновление данных табеля
    function refreshTabelForWorkerFromSchedulerReport(tabel_id, worker_id, confirmation){
        // console.log(tabel_id);
        // console.log(worker_id);
        // console.log($("#w_id_"+worker_id).attr("oklad"));
        // console.log($("#w_id_"+worker_id).attr("w_percenthours"));
        // console.log($("#w_id_"+worker_id).attr("worker_revenue_percent"));
        //  console.log(Number($("#zp_temp_" + worker_id).html()));
        // console.log($("#w_id_"+worker_id).attr("filialmoney"));
        // console.log($("#w_id_"+worker_id).attr("worker_category_id"));
        // console.log($("#w_id_"+worker_id).attr("w_hours"));
        // console.log(Number($("#w_id_"+worker_id).html()));

        var rys = false;

        if (confirmation) {
            rys = confirm("Вы собираетесь обновить данные в табеле. \n\nВы уверены?");
        }else{
            rys = true;
            blockWhileWaiting (true);
        }
        if (rys) {

            var link = "fl_refreshTabelForWorkerFromSchedulerReport_f.php";

            var reqData = {
                tabel_id: tabel_id,
                worker_id: worker_id,
                oklad: $("#w_id_" + worker_id).attr("oklad"),
                w_percenthours: $("#w_id_" + worker_id).attr("w_percenthours"),
                worker_revenue_percent: $("#w_id_" + worker_id).attr("worker_revenue_percent"),
                worker_revenue_solar_percent: $("#w_id_" + worker_id).attr("worker_revenue_solar_percent"),
                worker_revenue_realiz_percent: $("#w_id_" + worker_id).attr("worker_revenue_realiz_percent"),
                worker_revenue_abon_percent: $("#w_id_" + worker_id).attr("worker_revenue_abon_percent"),
                per_from_salary: Number($("#zp_temp_" + worker_id).html()),
                filialmoney: $("#w_id_" + worker_id).attr("filialmoney"),
                filialsolar: $("#w_id_" + worker_id).attr("filialSolar"),
                filialrealiz: $("#w_id_" + worker_id).attr("filialRealiz"),
                filialabon: $("#w_id_" + worker_id).attr("filialAbon"),
                worker_category_id: $("#w_id_" + worker_id).attr("worker_category_id"),
                w_hours: $("#w_id_" + worker_id).attr("w_hours"),
                summ: Number($("#w_id_" + worker_id).html())
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
                success: function (res) {
                    // console.log (res);

                    if (res.result == "success") {
                        //console.log(res.data);

                        location.reload();
                    } else {
                        //--
                    }
                }
            })
        }
    }

    //Поиск на странице табелей, требующих обновления и о попытка обновления данных
    function refreshAllTabelsForWorkerFromSchedulerReport(){
        $(".fa-refresh").each(function() {
            //console.log($(this).attr('onclick'));

            var str_temp = $(this).attr('onclick');
            // console.log(str_temp);

            if (str_temp !== undefined){

                //var str = str_temp.split('(')[1];
                var str = str_temp.substring(str_temp.indexOf('(')+1, str_temp.indexOf(')'));
                //console.log(str.replace(/\s/g, ''));

                var tabel_id = str.split(',')[0].replace(/\s/g, '');
                var worker_id = str.split(',')[1].replace(/\s/g, '');

                // console.log(tabel_id);
                // console.log(worker_id);

                refreshTabelForWorkerFromSchedulerReport(tabel_id, worker_id, false);

            }

            //пример обрезки строки, красиво
            // var str = "50ml+$100";
            // var a = str.split('+')[0]; // 50ml
            // var b = str.split('+')[1]; // $100
        })
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

    //Функция для отчета подсчета среднего за период
    function fl_mainReportAverage() {
        //console.log($("#month_count").val());
        // console.log($("#month_start").val());
        // console.log($("#year_start").val());
        // console.log($("#month_end").val());
        // console.log($("#year_end").val());
        // console.log($("#SelectFilial").val());

        var just_do_it = false;

        //убираем ошибки
        hideAllErrors();

        if (Number($("#year_start").val()) > Number($("#year_end").val())) {
            $('#errrror').html('<span class="query_neok">Ошибка в указанном периоде.</span>');
        } else {
            if (Number($("#year_start").val()) == Number($("#year_end").val())) {
                if (Number($("#month_start").val()) > Number($("#month_end").val())) {
                    $('#errrror').html('<span class="query_neok">Ошибка в указанном периоде.</span>');
                } else {
                    just_do_it = true;
                }
            } else {
                just_do_it = true;
            }
        }

        //Если с датами все более менее, то выполняем
        if (just_do_it) {

            var link = "fl_mainReportAverage_getDates_f.php";

            var reqData = {
                month_start: $("#month_start").val(),
                year_start: $("#year_start").val(),
                month_end: $("#month_end").val(),
                year_end: $("#year_end").val(),
                filial_id: $("#SelectFilial").val()
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
                success: function (res) {
                    //console.log (res);

                    if (res.result == "success") {
                        //console.log(res.data);

                        $("#res_table_tmpl").html(res.res_str);

                        $("#date_start_name").html(res.date_start_name);
                        $("#date_end_name").html(res.date_end_name);
                        $("#filial_name").html(res.filial_name);
                        $("#filial_name2").html(res.filial_name);


                        var months_arr = Array.from(res.months_arr);
                        //console.log(months_arr);

                        //!!! Хороший пример паузы в цикле (пауза в цикле) через рекурсию
                        //Не использовать, если есть вариант, что массив изменится во время
                        //И если обязательно индексы цифровые и по порядку
                        if (months_arr.length > 0) {

                            var foo = function (i) {
                                $('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");

                                window.setTimeout(function () {
                                    //console.log(months_arr[i]);

                                    link = "fl_mainReportAverage_getData_f.php";
                                    // link = "fl_reloadPercentsMarkedCalculates.php";

                                    reqData.date = months_arr[i];
                                    //console.log(reqData);

                                    //По каждому индексу даты: делаем...
                                    $.ajax({
                                        url: link,
                                        global: false,
                                        type: "POST",
                                        dataType: "JSON",
                                        data: reqData,
                                        cache: false,
                                        beforeSend: function () {
                                        },
                                        // действие, при ответе с сервера
                                        success: function (res) {
                                            // console.log(res);

                                            if (res.result == "success") {
                                                // console.log(months_arr[i] + ' ***');
                                                //console.log(res);
                                                //console.log(res.permission_summs);
                                                //console.log(res.giveoutcash_j);

                                                $(".allMoney_"+res.month+"_"+res.year).html(Number(res.nal + res.beznal + res.arenda + res.insure_summ));

                                                $(".bank_"+res.month+"_"+res.year).html(Number(res.bank_summ));
                                                $(".director_"+res.month+"_"+res.year).html(Number(res.director_summ));
                                                $(".nal_"+res.month+"_"+res.year).html(Number(res.nal));
                                                $(".beznal_"+res.month+"_"+res.year).html(Number(res.beznal + res.insure_summ));
                                                $(".arenda_"+res.month+"_"+res.year).html(Number(res.arenda));

                                                //var permission_summs = Array.from(res.permission_summs);
                                                //  console.log(5 in res.permission_summs);

                                                if (5 in res.permission_summs){
                                                    $(".zpStom_" + res.month + "_" + res.year).html(Number(res.permission_summs[5]));
                                                }
                                                if (6 in res.permission_summs){
                                                    $(".zpCosm_" + res.month + "_" + res.year).html(Number(res.permission_summs[6]));
                                                }
                                                if (10 in res.permission_summs){
                                                    $(".zpSomat_" + res.month + "_" + res.year).html(Number(res.permission_summs[10]));
                                                }
                                                if (7 in res.permission_summs){
                                                    $(".zpAssist_" + res.month + "_" + res.year).html(Number(res.permission_summs[7]));
                                                }
                                                if (4 in res.permission_summs){
                                                    $(".zpAdm_" + res.month + "_" + res.year).html(Number(res.permission_summs[4]));
                                                }

                                                //zpSanitUborDvor
                                                var zpSanitUborDvor = 0;
                                                if (13 in res.permission_summs){
                                                    zpSanitUborDvor +=  Number(res.permission_summs[13]);
                                                }
                                                if (14 in res.permission_summs){
                                                    zpSanitUborDvor +=  Number(res.permission_summs[14]);
                                                }
                                                if (15 in res.permission_summs){
                                                    zpSanitUborDvor +=  Number(res.permission_summs[15]);
                                                }
                                                $(".zpSanitUborDvor_"+res.month+"_"+res.year).html(zpSanitUborDvor);

                                                if (11 in res.permission_summs){
                                                    $(".zpZavh_" + res.month + "_" + res.year).html(Number(res.permission_summs[11]));
                                                }

                                                //zpPom
                                                var zpPom = 0;
                                                if (9 in res.permission_summs){
                                                    zpPom +=  Number(res.permission_summs[9]);
                                                }
                                                if (12 in res.permission_summs){
                                                    zpPom +=  Number(res.permission_summs[12]);
                                                }
                                                $(".zpPom_"+res.month+"_"+res.year).html(zpPom);

                                                $(".remont_"+res.month+"_"+res.year).html(res.giveoutcash_j[3]);
                                            }
                                        }
                                    });

                                    if (i < months_arr.length-1){
                                        foo(i + 1);
                                    } else {
                                        //По окончании цикла, который выше, чего-то делаем
                                        //console.log("Обновляем суммы.");

                                        window.setTimeout(function () {
                                            var date_arr = {};
                                            var m_count = 0;

                                            var allMoney = 0;
                                            var arenda = 0;
                                            var nal = 0;
                                            var beznal = 0;
                                            var zpStom = 0;
                                            var zpCosm = 0;
                                            var zpSomat = 0;
                                            var zpAssist = 0;
                                            var zpAdm = 0;
                                            var zpSanitUborDvor = 0;
                                            var zpZavh = 0;
                                            var zpPom = 0;
                                            var remont = 0;

                                            $(".need_date").each(function () {
                                                //console.log($(this).attr("need_date"));

                                                date_arr = $(this).attr("need_date").split("_");

                                                var m = date_arr[0];
                                                var y = date_arr[1];

                                                m_count++;

                                                $(".allMoney_" + m + "_" + y).each(function () {
                                                    allMoney += Number($(this).html());
                                                });

                                                $(".arenda_" + m + "_" + y).each(function () {
                                                    arenda += Number($(this).html());
                                                });

                                                $(".nal_" + m + "_" + y).each(function () {
                                                    nal += Number($(this).html());
                                                });

                                                $(".beznal_" + m + "_" + y).each(function () {
                                                    beznal += Number($(this).html());
                                                });

                                                $(".zpStom_" + m + "_" + y).each(function () {
                                                    zpStom += Number($(this).html());
                                                });

                                                $(".zpCosm_" + m + "_" + y).each(function () {
                                                    zpCosm += Number($(this).html());
                                                });

                                                $(".zpSomat_" + m + "_" + y).each(function () {
                                                    zpSomat += Number($(this).html());
                                                });

                                                $(".zpAssist_" + m + "_" + y).each(function () {
                                                    zpAssist += Number($(this).html());
                                                });

                                                $(".zpAdm_" + m + "_" + y).each(function () {
                                                    zpAdm += Number($(this).html());
                                                });

                                                $(".zpSanitUborDvor_" + m + "_" + y).each(function () {
                                                    zpSanitUborDvor += Number($(this).html());
                                                });

                                                $(".zpZavh_" + m + "_" + y).each(function () {
                                                    zpZavh += Number($(this).html());
                                                });

                                                $(".zpPom_" + m + "_" + y).each(function () {
                                                    zpPom += Number($(this).html());
                                                });

                                                $(".remont_" + m + "_" + y).each(function () {
                                                    remont += Number($(this).html());
                                                });

                                            });

                                            // console.log(m_count);
                                            // console.log(allMoney);
                                            // console.log(arenda);


                                            //Выводим результат
                                            $(".allMoney_summ").html(allMoney);
                                            $(".allMoney_average").html(number_format(allMoney/m_count, 2, '.', ''));

                                            $(".arenda_summ").html(arenda);
                                            $(".arenda_average").html(number_format(arenda/m_count, 2, '.', ''));

                                            $(".nal_summ").html(nal);
                                            $(".nal_average").html(number_format(nal/m_count, 2, '.', ''));

                                            $(".beznal_summ").html(beznal);
                                            $(".beznal_average").html(number_format(beznal/m_count, 2, '.', ''));

                                            $(".zpStom_summ").html(zpStom);
                                            $(".zpStom_average").html(number_format(zpStom/m_count, 2, '.', ''));

                                            $(".zpCosm_summ").html(zpCosm);
                                            $(".zpCosm_average").html(number_format(zpCosm/m_count, 2, '.', ''));

                                            $(".zpSomat_summ").html(zpSomat);
                                            $(".zpSomat_average").html(number_format(zpSomat/m_count, 2, '.', ''));

                                            $(".zpAssist_summ").html(zpAssist);
                                            $(".zpAssist_average").html(number_format(zpAssist/m_count, 2, '.', ''));

                                            $(".zpAdm_summ").html(zpAdm);
                                            $(".zpAdm_average").html(number_format(zpAdm/m_count, 2, '.', ''));

                                            $(".zpSanitUborDvor_summ").html(zpSanitUborDvor);
                                            $(".zpSanitUborDvor_average").html(number_format(zpSanitUborDvor/m_count, 2, '.', ''));

                                            $(".zpZavh_summ").html(zpZavh);
                                            $(".zpZavh_average").html(number_format(zpZavh/m_count, 2, '.', ''));

                                            $(".zpPom_summ").html(zpPom);
                                            $(".zpPom_average").html(number_format(zpPom/m_count, 2, '.', ''));

                                            $(".remont_summ").html(remont);
                                            $(".remont_average").html(number_format(remont/m_count, 2, '.', ''));

                                            $('#errrror').html('');
                                        }, 500);
                                    }
                                }, 1000);
                            };
                            foo(0);
                        }



                    } else {
                        //--
                    }
                }
            })
        }
    }

    //Функция для отчета по категориям 3
    function fl_mainReportCategory3() {
        // console.log($("#SelectCats").val());
        // console.log($("#SelectCats").val() == null);
        // console.log($("#month_start").val());
        // console.log($("#year_start").val());
        // console.log($("#month_end").val());
        // console.log($("#year_end").val());
        // console.log($("#SelectFilial").val());

        let just_do_it = false;

        //убираем ошибки
        hideAllErrors();

        if (Number($("#year_start").val()) > Number($("#year_end").val())) {
            $('#errrror').html('<span class="query_neok">Ошибка в указанном периоде.</span>');
        } else {
            if (Number($("#year_start").val()) == Number($("#year_end").val())) {
                if (Number($("#month_start").val()) > Number($("#month_end").val())) {
                    $('#errrror').html('<span class="query_neok">Ошибка в указанном периоде.</span>');
                } else {
                    just_do_it = true;
                }
            } else {
                just_do_it = true;
            }
        }

        if (($("#SelectCats").val() == null)){
            $('#errrror').html('<span class="query_neok">Не выбраны категории.</span>');
            just_do_it = false;
        }

        //Если с датами все более менее, то выполняем
        if (just_do_it) {

            let link = "fl_mainReportCategory3_getDates_f.php";

            let reqData = {
                cats: $("#SelectCats").val(),
                month_start: $("#month_start").val(),
                year_start: $("#year_start").val(),
                month_end: $("#month_end").val(),
                year_end: $("#year_end").val(),
                filial_id: $("#SelectFilial").val(),
                worker: $("#search_client4").val()
            }
            // console.log(reqData);

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: reqData,
                cache: false,
                beforeSend: function () {
                    $('#res_table_tmpl').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (res) {
                    // console.log (res.query);

                    if (res.result == "success") {
                        //console.log(res.data);

                        $("#res_table_tmpl").html(res.data);

                    } else {
                        //--
                        $('#res_table_tmpl').html(res.data);
                    }
                }
            })
        }
    }

    //Промежуточная функция добавления
    function fl_showAddFunction(path, task_id, mode){

        //убираем ошибки
        hideAllErrors ();

        if ($('#search_client2').val().length > 0) {
            let task_id = 0;

            let link = "individuals2_add_f.php";

            if (mode == 'edit') {
                link = "individuals2_edit_f.php";
                task_id = $("#task_id").val();
            }

            let reqData = {
                date: $('#iWantThisDate').val(),
                worker_name: $('#search_client2').val(),
                plan_text: $('#plan_text').val(),
                rings_count: $('#rings_count').val(),
                rings_review: $('#rings_review_text').val(),
                work_w_patients: $('#work_w_patients_text').val(),
                error_correction: $('#error_correction_text').val(),
                ring_stat: $('#ring_stat_text').val(),
                task_id: task_id
            };
            // console.log(reqData);

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
                    //console.log(res);

                    $('.center_block').remove();
                    $('#overlay').hide();

                    if(res.result == "success"){
                        //$('#data').hide();
                        $('#data').html(res.data);

                        setTimeout(function () {
                            //!!! переход window.location.href - это правильное использование
                            window.location.href = path+'.php';
                        }, 300);
                    }else{
                        $('#errror').html(res.data);
                    }
                }
            });
        }else{
            $("#errror").html('<div class="query_neok">Выберите сотрудника</div>');
        }
    }



