$(function(){
	    
	//Живой поиск
	$('.who_fc').bind("change keyup input click", function() {
		//console.log(111);

		if(this.value.length > 0){
            $(".button_in_input").show();
        }else {
            $(".button_in_input").hide();
        }

		if(this.value.length > 2){
			$.ajax({
				url: "FastSearchNameFC.php", //Путь к обработчику
				//statbox:"status",
				type:"POST",
				data:
				{
					'searchdata':this.value
				},
				response: 'text',
				success: function(data){
					//$(".search_result_fc").html(data).fadeIn(); //Выводим полученые данные в списке
                    $("#search_result_fc2").html(data); //Выводим полученые данные в списке
				}
			})

	    }else{
			$("#search_result_fc2").html("");
			//var elemFC2 = $("#search_result_fc2"); 
			//elemFC2.hide();
		}
	})
	    
	// $(".search_result_fc").hover(function(){
	// 	$(".who_fc").blur(); //Убираем фокус с input
	// })
})

$(function(){

	//Живой поиск
	$('.who_fcert').bind("change keyup input click", function() {
		if(this.value.length > 1){
			$.ajax({
				url: "FastSearchNameFCert.php", //Путь к обработчику
				//statbox:"status",
				type:"POST",
				data:
				{
					'searchdata':this.value
				},
				response: 'text',
				success: function(data){
					//$(".search_result_fc").html(data).fadeIn(); //Выводим полученые данные в списке
                    $("#search_result_fcert2").html(data); //Выводим полученые данные в списке
				}
			})
	    }else{
            $("#search_result_fcert2").html('');
			//var elemFC2 = $("#search_result_fc2");
			//elemFC2.hide();
		}
	})

	// $(".search_result_fc").hover(function(){
	// 	$(".who_fc").blur(); //Убираем фокус с input
	// })
})

//Для сертификатов именных (поиск через input не в модальном окне)
$(function(){

	//Живой поиск
	$('.who_fcert_name').bind("change keyup input click", function() {
		//console.log(this.value);

		if(this.value.length > 1){
			$.ajax({
				url: "FastSearchNameFCertName.php", //Путь к обработчику
				//statbox:"status",
				type:"POST",
				data:
					{
						'searchdata':this.value
					},
				response: 'text',
				success: function(data){
					//$(".search_result_fc").html(data).fadeIn(); //Выводим полученые данные в списке
					$("#search_result_fcertname2").html(data); //Выводим полученые данные в списке
				}
			})
		}else{
			$("#search_result_fcertname2").html('');
			//var elemFC2 = $("#search_result_fc2");
			//elemFC2.hide();
		}
	})

	// $(".search_result_fc").hover(function(){
	// 	$(".who_fc").blur(); //Убираем фокус с input
	// })
})


$(function(){

	//Живой поиск абонемента
	$('.who_fabon').bind("change keyup input click", function() {
		if(this.value.length > 1){
			$.ajax({
				url: "FastSearchNameFAbon.php", //Путь к обработчику
				//statbox:"status",
				type:"POST",
				data:
				{
					'searchdata':this.value
				},
				response: 'text',
				success: function(data){
					//$(".search_result_fc").html(data).fadeIn(); //Выводим полученые данные в списке
                    $("#search_result_fabon2").html(data); //Выводим полученые данные в списке
				}
			})
	    }else{
            $("#search_result_fabon2").html('');
			//var elemFC2 = $("#search_result_fc2");
			//elemFC2.hide();
		}
	})

	// $(".search_result_fc").hover(function(){
	// 	$(".who_fc").blur(); //Убираем фокус с input
	// })
})

$(function(){

    //Живой поиск позиции на складе
    //Рабочий пример клика на элементе после подгрузки загрузки его в DOM
    $("body").on("change keyup input click", ".sclad_item_search", function(){
	//Так не работает, если .sclad_item_search создаётся после document ready
    //$('.sclad_item_search').bind("change keyup input click", function() {
        if(this.value.length > 0){

			let link = "FastSearchScladItem.php";

            let reqData = {
                'searchdata':this.value
            };
            // console.log(reqData);

            $.ajax({
                url: link,
                global: false,
                type: "POST",
                /*dataType: "JSON",*/
                data: reqData,
                cache: false,
                beforeSend: function() {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function(res){
                    // $('#errrror').html(res);
                     //console.log(res);

                    $(".search_result_sclad_item").html(res).show(); //Выводим полученые данные в списке
                }
            });


        }else{
            $(".search_result_sclad_item").html('');
            $(".search_result_sclad_item").hide();
        }
    });


    //Заменено на  onmouseover='$(".sclad_item_search").blur();' в самом элементе
    // $(".search_result_sclad_item").hover(function(){
    //     console.log(777);
    //     $(".sclad_item_search").blur(); //Убираем фокус с input
    // });

    //При выборе результата поиска, прячем список и заносим выбранный результат в input
    $("body").on("click", ".search_result_sclad_item li", function(){
        // console.log(this.firstChild);

        $(".sclad_item_search").val($(this).text());

        $(".search_result_sclad_item").html('');
        $(".search_result_sclad_item").hide();


        // console.log($(this).attr('id'));
        //Добавим в сессию данные
        addNewScladItemsSetINSession($(this).attr('id'));

    });

    //Если click за пределами результатов поиска - убираем эти результаты
    $(document).click(function(e){
        var elem = $(".search_result_sclad_item");

        if(e.target!=elem[0]&&!elem.has(e.target).length){
            elem.hide();
        }
    })

});

