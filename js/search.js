$(function(){
	    
	//Живой поиск
	$('.who').bind("change keyup input click", function() {
		if(this.value.length > 2){
			$.ajax({
				url: "FastSearchName.php", //Путь к обработчику
				//statbox:"status",
				type:"POST",
				data:
				{
					'searchdata':this.value
				},
				response: 'text',
				success: function(data){
					$(".search_result").html(data).fadeIn(); //Выводим полученые данные в списке
				}
			})
	    }else{
			var elem1 = $("#search_result"); 
			elem1.hide(); 
		}
	});
	    
	$(".search_result").hover(function(){
		$(".who").blur(); //Убираем фокус с input
	});
	    
    //При выборе результата поиска, прячем список и заносим выбранный результат в input
    $(".search_result").on("click", "li", function(){
        //s_user = $(this).text();
        s_user = $(this).attr("full_name");
        //console.log($(this).attr("full_name"));

		$(".who").val(s_user);
		//document.getElementById("qwe").innerHTML = "111110";
        //$(".who").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно
        $(".search_result").fadeOut();
    });
	//Если click за пределами результатов поиска - убираем эти результаты
	$(document).click(function(e){
		var elem = $("#search_result"); 
		if(e.target!=elem[0]&&!elem.has(e.target).length){
			elem.hide(); 
		} 
	})
});