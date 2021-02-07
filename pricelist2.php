<?php

//pricelist2.php
//

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';
    if (($items['see_all'] == 1) || ($items['see_own'] == 1) || $god_mode){
        include_once 'functions.php';
        include_once 'DBWork.php';

        //тип (космет/стомат/...)
//			if (isset($_GET['who'])){
//				if ($_GET['who'] == 'stom'){
//					$who = '&who=stom';
//					$whose = 'Стоматология ';
//					$selected_stom = ' selected';
//					$selected_cosm = ' ';
//					$datatable = 'scheduler_stom';
//					$kabsForDoctor = 'stom';
//					$type = 5;
//				}elseif($_GET['who'] == 'cosm'){
//					$who = '&who=cosm';
//					$whose = 'Косметология ';
//					$selected_stom = ' ';
//					$selected_cosm = ' selected';
//					$datatable = 'scheduler_cosm';
//					$kabsForDoctor = 'cosm';
//					$type = 6;
//				}else{
//					$who = '&who=stom';
//					$whose = 'Стоматология ';
//					$selected_stom = ' selected';
//					$selected_cosm = ' ';
//					$datatable = 'scheduler_stom';
//					$kabsForDoctor = 'stom';
//					$type = 5;
//					$_GET['who'] = 'stom';
//				}
//			}else{
//				$who = '&who=stom';
//				$whose = 'Стоматология ';
//				$selected_stom = ' selected';
//				$selected_cosm = ' ';
//				$datatable = 'scheduler_stom';
//				$kabsForDoctor = 'stom';
//				$type = 5;
//				$_GET['who'] = 'stom';
//			}

        echo '
				<header>
                    <div class="nav">
                        <!--<a href="sclad_prihods.php" class="b">Приходные накладные</a>-->
                    </div>
					<h1>Прайс</h1>
				</header>';

        //переменная, чтоб вкл/откл редактирование
//			echo '
//				<script>
//					var iCanManage = false;
//				</script>';

        echo '
                <div id="data">
                    <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
                    </ul>';

        //***

        echo '
                    <div style="white-space: nowrap;">
                        <div style="display: inline-block; border: 1px solid #c5c5c5; position: relative; vertical-align: top;">
                            
                            <div style="margin: 5px 0 5px; font-size: 11px; cursor: pointer;">
                            <!--<span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>-->
                            </div>
                            
                            <div id="price_cat_rezult" style="width: 350px; max-width: 350px; min-width: 350px; height: 500px; overflow: hidden;">
                            
                            </div>
                        </div>
                        
                        <div style="display: inline-block; border: 1px solid #c5c5c5; position: relative;">';

        echo '
                            <div style="box-shadow: -1px 1px 5px rgba(51, 51, 51, 0.32); position: relative;">
                                <!--<i class="fa fa-search" aria-hidden="true"></i>-->
                                <!--<input type="text" id="" name="" class="" placeholder="&#xF002;" value="">-->
                                
                                <!--!!!Пример замена placeholder на иконку fontawesome плейсхолдер-->
                                <input type="text" name="search_price_items" id="search_price_items" placeholder="&#xF002;" value="" class="" style="font-family:Verdana, FontAwesome; width: 99%;" autocomplete="off">
                                <!--<div class="button_in_input" onclick=clearSearchInput();><i class="fa fa-times" aria-hidden="true" style="color: #CCC; font-size: 130%;" title="Очистить"></i></div>-->
                            </div>';


        echo '
                            <div style="margin: 5px 0 5px; font-size: 11px; cursor: pointer;">
                                <span id="cat_name_show"></span>
                            </div>
                            
                            <div id="price_items_rezult" style="width: 700px; max-width: 700px; min-width: 700px; height: 479px;">
                            
                            </div>

                        </div>
                    </div>';

        echo '
                    <div style="white-space: nowrap;">';
        echo '
                        <div id="price_items_in_set" style="display: none; width: 700px; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

        echo '
                            <div id="errror" class="invoceHeader" style="position: relative; padding: 5px 10px;">
                                <div>
                                    <div style="">Выбрано позиций: <span id="itemInSetCount" style="">0</span> шт.</div>
                                </div>
                                <div style="font-size: 11px;">
                                    <!--<div style="display: inline-block;">
                                        <a href="price_prihod_add.php" class="ahref b">Добавить приход</a>
                                    </div>-->
                                    <!--<div style="display: inline-block;">
                                        <a href="price_transfer_add.php" class="ahref b">Добавить перемещение</a>
                                    </div>-->
                                </div>
                                <div style="position: absolute; top: 10px; right: 10px; font-size: 11px;">
                                     <div class="settings_text" onclick="deleteScladItemsFromSet();">Очистить всё</div>
                                </div>';

        echo '
                            </div>';


        echo '
                            <div id="price_items_in_set_rezult" style="max-height: 260px; overflow-y: scroll; overflow-x: hidden; float: none">
                            </div>';
        echo '
                        </div>';
        echo '
                    </div>';

        echo '

                </div>';

        echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

        echo '
            <div id="doc_title">Прайс2 - Асмедика</div>';

        echo '

            <script type="text/javascript">
				
				$(document).ready(function() {
				    //console.log(123);
				    //$("#price_items_rezult").html(123);
				    
				    getPriceCategories ();
				    getPriceItems (0, 0, 1000, true);
				    //Показать выбранные позиции
				    //fillScladItemsInSet ();

				});	

                //Живой поиск
                $("#search_price_items").bind("change keyup input click", function() {
                    //console.log($(this).val());
          
                    if($(this).val().length > 0){
                       
                        getScladItems (0, 0, 1000, true, false, -1, $(this).val());

                    }
                })

                $("#search_price_items" ).blur(function() {
                    $("#search_price_items").val("");
                });

				
				
                
            </script>
            
            <script src="js/DragManager.js"></script>
            
            <script>


                    //!!! Правильный пример контекстного меню (правильный? точно? ну пока работает)

                    let priceCategories = document.querySelector("#price_cat_rezult");

                    priceCategories.addEventListener("contextmenu", event => {
                        //console.log($(event.target).attr("id"));
                        
                        event.preventDefault();
                        
//                        //Сначала очищаем у всех окраску
//                        $(".droppable, .draggable").removeClass("context_pick");
//                        //Теперь покрасим
//                        if (event.target.classList.contains("droppable")){ 
//                            $(event.target).addClass("context_pick");
//                        }

                        contextMenuShow(0, 0, event, "price_cat");

                    }, false);
                    
                    let priceItems = document.querySelector("#price_items_rezult");

                    priceItems.addEventListener("contextmenu", event => {
                        //console.log(event.target.closest("tr"));

                        //event.preventDefault();
                       
//                        //Сначала очищаем у всех окраску
//                        $(".droppable, .draggable").removeClass("context_pick");
//                        //Теперь покрасим
//                        if (event.target.closest("tr") != null){
//                            if (event.target.closest("tr").classList.contains("draggable")){ 
//                                $(event.target.closest("tr")).addClass("context_pick");
//                            }
//                        }

                        contextMenuShow(0, 0, event, "price_item");

                    }, false);
                    
                    
                    
                    //Выбираем элементы из списка в сессии для добавления в набор внизу 
                    $("body").on("click", ".select_item", function(){
                        var checked_status = $(this).prop("checked");
//                        console.log(checked_status);

                        var item_id = $(this).attr("id").split("_")[2];
//                        console.log(item_id);
            
                        //Добавим в сессию данные
                        addPriceItemsSetINSession(item_id, checked_status);
            
                    });

            </script>
            
            ';

    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>