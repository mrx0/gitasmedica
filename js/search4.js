$(function(){
	    
	//Живой поиск
	$('.who4').bind("change keyup input click", function() {
		if(this.value.length > 2){
			$.ajax({
				url: "FastSearchNameW.php", //Путь к обработчику
				//statbox:"status",
				type:"POST",
				data:
				{
					'searchdata4':this.value
				},
				response: 'text',
				success: function(data){
					$(".search_result4").html(data).fadeIn(); //Выводим полученые данные в списке
				}
			})
	    }else{
			var elem1 = $("#search_result4"); 
			elem1.hide(); 
		}
	})
	    
	$(".search_result4").hover(function(){
		$(".who4").blur(); //Убираем фокус с input
	})
	    
    //При выборе результата поиска, прячем список и заносим выбранный результат в input
    $(".search_result4").on("click", "li", function(){
        s_user = $(this).text();
		$(".who4").val(s_user);
        //$(".who").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно
        $(".search_result4").fadeOut();
    })
	//Если click за пределами результатов поиска - убираем эти результаты
	$(document).click(function(e){
		var elem = $("#search_result4"); 
		if(e.target!=elem[0]&&!elem.has(e.target).length){
			elem.hide(); 
		} 
	})
})