/*
 * jQuery.liveFilter
 *
 * Copyright (c) 2009 Mike Merritt
 *
 * Forked by Lim Chee Aun (cheeaun.com)
 * 
 */
 
(function($){
	$.fn.liveFilter = function(inputEl, filterEl, options){
		//console.log(inputEl);
        //console.log(filterEl);
		//console.log(options);

		var defaults = {
			filterChildSelector: null,
            forPriceInInvoice: false,
			filter: function(el, val){
				return $(el).text().toUpperCase().indexOf(val.toUpperCase()) >= 0;
			},
			before: function(){},
			after: function(){}
		};
		var options = $.extend(defaults, options);
		
		var el = $(this).find(filterEl);
		//console.log(el);

		if (options.filterChildSelector) el = el.find(options.filterChildSelector);

		var filter = options.filter;
		$(inputEl).keyup(function(){
			var val = $(this).val();
			var contains = el.filter(function(){
				return filter(this, val);
			});
			var containsNot = el.not(contains);
			if (options.filterChildSelector){
				contains = contains.parents(filterEl);
				containsNot = containsNot.parents(filterEl).hide();
			}
			
			options.before.call(this, contains, containsNot);

			//console.log(options.filterChildSelector);

			contains.show();
			containsNot.hide();

			if (options.forPriceInInvoice) {
                $("#lasttree").find("ul").slideDown(400).parents("li").children("div.drop").css({'background-position':"-11px 0"});
                contains.css({'background': "rgba(157, 255, 71, 0.8)"});
                //containsNot.css({'background': ""});
            }

			if (val === '') {
				contains.show();
				containsNot.show();

                if (options.forPriceInInvoice) {
                    $("#lasttree").find("ul").slideUp(400).parents("li").children("div.drop").css({'background-position':"0 0"});
                    contains.css({'background': ""});
                    containsNot.css({'background': ""});
                }
			}
			
			options.after.call(this, contains, containsNot);
		});
	}
})(jQuery);
