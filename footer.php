<?php

//footer.php
//

	echo '

	<div class="md-overlay"></div>
	</section>
	<div class="no_print"> 
		<!--</section>-->
		<footer>

		</footer>
	</div>
	
    <!--для печати-->	
    <style type="text/css" media="print">
      div.no_print {display: none; }
      .never_print_it {display: none; }
      #scrollUp {display: none; }
    </style> 
    ';
			
	echo'
	

	
		<script src="js/functions.js"></script>
		
		
		
		<script src="js/ffun.js"></script>
		
		
		<script src="js/calendar_kdg.js"></script>
		
		<script src="js/classie.js"></script>
		<script src="js/modalEffects.js"></script>
		<script>
			// this is important for IEs
			var polyfilter_scriptpath = \'/js/\';
		</script>
		<script src="js/cssParser.js"></script>
		<!--<script src="js/css-filters-polyfill.js"></script>-->
		
        <script>
            $( "#tabs_w" ).tabs();

//            $(".vert-nav li").hover(
//                function() {
//                    $("ul", this).slideDown(110);
//                    //$(".have_new-zapis_main").hide();
//                },
//                function() {
//                    $("ul", this).slideUp(110);
//                    //$(".have_new-zapis_main").show();
//                }
//            );

            $(document).ready(function(){
            	 $(document).attr("title", $("#doc_title").html());
            	 //console.log($("#doc_title").html());
            });
           
        
            //$(document).click(function(e){
            //	var elem = $(".point"); 
            //	if(e.target!=elem[0]&&!elem.has(e.target).length){
            //		elem.hide(); 
                    //elem.remove();
                    //$(this).remove();
            //	} 
            //})
            
            
        </script>
        
                        
        <script>
            //Запрет контекстного меню  
            const Video = document.getElementsByTagName("video")[0];
            //console.log(Video)
            
            if (Video !== undefined){
                Video.oncontextmenu = function() {return false;};
            }
            
        </script>

        <!--<script type="text/javascript">
            var total = 0;
            function add_new_image(worker){
                total++;
                $(\'<tr>\')
                .attr(\'id\',\'tr_image_\'+total)
                .css({lineHeight:\'20px\'})
                .append (
                    $(\'<td>\')
                    .css({paddingRight:\'5px\',width:\'190px\'})
                    .append(
                        $(\'<input type="text" />\')
                        .css({width:\'200px\'})
                        .attr(\'id\',\'td_title_\'+total)
                        .attr(\'class\',\'remove_add_search\')
                        .attr(\'name\',\'input_title_\'+total)
                    )		
                    
                )
                
                .append (
                    $(\'<td>\')
                    .css({paddingRight:\'5px\',width:\'200px\'})
                    .append(
                        $(\'<input type="text" size="50" name="" placeholder="" value="" autocomplete="off" />\')
                            .attr(\'id\',\'td_worker_\'+total)
                            .attr(\'class\',\'remove_add_search\')
                    )
                )	
                
                .append (
                    $(\'<td>\')
                    .css({width:\'60px\'})
                    .append (
                        $(\'<span id="progress_\'+total+\'"><a  href="#" onclick="$(\\\'#tr_image_\'+total+\'\\\').remove();" class="ico_delete"><img src="./img/delete.png" alt="del" border="0"></a></span>\')
                    )
                )
                .appendTo(\'#table_container\');
                
            }
            //$(document).ready(function() {
            //	add_new_image();
            //});
        </script>	-->


			<!--</div>-->
		</body>
	</html>';

    //var_dump(microtime(true) - $script_start);
    echo '<div class="no_print" style=" margin-left: 20px; font-size: 80%;">Страница загружена за: '.(number_format((microtime(true) - $script_start), 2, '.', '')).' сек.</div>';
    echo '<div class="no_print" style="float: right; margin-right: 120px; margin-bottom: 20px; font-size: 80%;">В любой непонятной ситуации жми Ctrl+F5 &#169;</div>';

?>