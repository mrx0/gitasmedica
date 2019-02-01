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
	    
	$(".search_result_fc").hover(function(){
		$(".who_fc").blur(); //Убираем фокус с input
	})
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

	$(".search_result_fc").hover(function(){
		$(".who_fc").blur(); //Убираем фокус с input
	})
})

