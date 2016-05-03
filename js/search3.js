$(function(){
	    
	//Живой поиск
	$('.who3').bind("change keyup input click", function() {
		if(this.value.length > 2){
			$.ajax({
				url: "FastSearchNameW.php", //Путь к обработчику
				//statbox:"status",
				type:"POST",
				data:
				{
					'searchdata3':this.value
				},
				response: 'text',
				success: function(data){
					$(".search_result3").html(data).fadeIn(); //Выводим полученые данные в списке
				}
			})
	    }else{
			var elem1 = $("#search_result3"); 
			elem1.hide(); 
		}
	})
	    
	$(".search_result3").hover(function(){
		$(".who3").blur(); //Убираем фокус с input
	})
	    
    //При выборе результата поиска, прячем список и заносим выбранный результат в input
    $(".search_result3").on("click", "li", function(){
        s_user = $(this).text();
		$(".who3").val(s_user);
        //$(".who").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно
        $(".search_result3").fadeOut();
    })
	//Если click за пределами результатов поиска - убираем эти результаты
	$(document).click(function(e){
		var elem = $("#search_result3"); 
		if(e.target!=elem[0]&&!elem.has(e.target).length){
			elem.hide(); 
		} 
	})
})