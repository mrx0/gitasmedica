$(function(){
	    
	//Живой поиск
	$('.who_fc').bind("change keyup input click", function() {
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
					document.getElementById("search_result_fc2").innerHTML = data; //Выводим полученые данные в списке
					//document.getElementById("search_result_fc2").innerHTML = data;
				}
			})
	    }else{
			document.getElementById("search_result_fc2").innerHTML = '';
			//var elemFC2 = $("#search_result_fc2"); 
			//elemFC2.hide(); 
		}
	})
	    
	$(".search_result_fc").hover(function(){
		$(".who_fc").blur(); //Убираем фокус с input
	})
	    
    //При выборе результата поиска, прячем список и заносим выбранный результат в input
    /*$(".search_result_fc").on("click", "li", function(){
        s_user = $(this).text();
		$(".who_fc").val(s_user);
		//document.getElementById("qwe").innerHTML = "111110";
        //$(".who").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно
        $(".search_result_fc").fadeOut();
    })*/
	//Если click за пределами результатов поиска - убираем эти результаты
	/*$(document).click(function(e){
		var elemFC2 = $("#search_result_fc2"); 
		if(e.target!=elemFC2[0]&&!elemFC2.has(e.target).length){
			elemFC2.hide(); 
		} 
	})*/
})