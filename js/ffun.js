
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
    function showPaymentAdd(mode){
        //console.log(mode);

        var Summ = document.getElementById("summ").value;

        //проверка данных на валидность
        $.ajax({
            url:"ajax_test.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    summ:Summ,
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
                       Ajax_payment_add('add');
                    }

                    if (mode == 'edit'){
                        Ajax_payment_add('edit');
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
                    cert_id: cert_id,
                    summ: Summ,
                    date_in: date_in,
                    comment: comment,
                },
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
                    summ:Summ,
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

    //Добавляем/редактируем в базу оплату
    function Ajax_payment_add(mode){
        //console.log(mode);

        var payment_id = 0;

        var link = "payment_add_f.php";

        if (mode == 'edit'){
            link = "payment_edit_f.php";
            payment_id = document.getElementById("payment_id").value;
        }

        var Summ = document.getElementById("summ").value;
        var invoice_id = document.getElementById("invoice_id").value;

        var client_id = document.getElementById("client_id").value;
        var date_in = document.getElementById("date_in").value;
        //console.log(date_in);

        var comment = document.getElementById("comment").value;
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
                    summ: Summ,
                    date_in: date_in,
                    comment: comment,
                },
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
    //Выборка касса
    function Ajax_show_result_stat_cashbox(){

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

        $.ajax({
            url:"ajax_show_result_cashbox_f.php",
            global: false,
            type: "POST",
            data:
                {
                    datastart: document.getElementById("datastart").value,
                    dataend: document.getElementById("dataend").value,

                    filial: document.getElementById("filial").value,

                    summtype: summtype,

                    /*zapisTypeAll: zapisTypeAll,
                    zapisTypeStom: zapisTypeStom,
                    zapisTypeCosm: zapisTypeCosm,*/

                    certificatesShow: certificatesShow,

                },
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

        $.ajax({
            url:"payment_del_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    id: id,
                    client_id: client_id,
                    invoice_id: invoice_id,
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                if(res.result == "success"){
                    location.reload();
                    //console.log(res.data);
                }
                if(res.result == "error"){
                    alert(res.data);
                }
                //console.log(data.data);

            }
        });
    }

    //Удалить расчет
    function fl_deleteCalculateItem(id, client_id, invoice_id){
        //console.log(id);

        $.ajax({
            url:"fl_calculate_del_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    id: id,
                    client_id: client_id,
                    invoice_id: invoice_id,
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){
                /*if(data.result == "success"){

                }*/
                //console.log(data.data);
                //location.reload();
                window.location.href = "invoice.php?id="+invoice_id;
            }
        });
    }

    //Сбросить проценты персональные на по умолчанию
    //function fl_changePersonalPercentCatdefault(workerID, catID, typeID){
    function fl_changePersonalPercentCatdefault(workerID){
        /*console.log(workerID);
        console.log(catID);
        console.log(typeID);*/

        var rys = false;

        var rys = confirm("Сбросить на значения по умолчанию?");

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
                    newInput.maxLength = 3;
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
                        if ((newInput.value == "") || (isNaN(newInput.value)) || (newInput.value < 0) || (newInput.value > 100) || (isNaN(parseInt(newInput.value, 10)))) {
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
                                    val: newVal,
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
        ;
    };