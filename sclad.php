<?php

//sclad.php
//

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($items['see_all'] == 1) || ($items['see_own'] == 1) || $god_mode){
			include_once 'functions.php';
            include_once 'DBWork.php';

			//тип (космет/стомат/...)
			if (isset($_GET['who'])){
				if ($_GET['who'] == 'stom'){
					$who = '&who=stom';
					$whose = 'Стоматология ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
				}elseif($_GET['who'] == 'cosm'){
					$who = '&who=cosm';
					$whose = 'Косметология ';
					$selected_stom = ' ';
					$selected_cosm = ' selected';
					$datatable = 'scheduler_cosm';
					$kabsForDoctor = 'cosm';
					$type = 6;
				}else{
					$who = '&who=stom';
					$whose = 'Стоматология ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
					$_GET['who'] = 'stom';
				}
			}else{
				$who = '&who=stom';
				$whose = 'Стоматология ';
				$selected_stom = ' selected';
				$selected_cosm = ' ';
				$datatable = 'scheduler_stom';
				$kabsForDoctor = 'stom';
				$type = 5;
				$_GET['who'] = 'stom';
			}

			echo '
				<header>
					<h1>Склад</h1>
						<!--<div>
							<span style="font-size: 80%; color: #AAA">Перейти к прайсу страховой</span><br>';
			echo '
							<select name="insurecompany" id="insurecompany">
								<option value="0">Выберите страховую</option>';
			$insures_j = SelDataFromDB('spr_insure', '', '');

			if ($insures_j != 0){
				for ($i=0;$i<count($insures_j);$i++){
                    echo "<option value='".$insures_j[$i]['id']."'>".$insures_j[$i]['name']."</option>";
			    }
			}
			echo '
							</select>
							<span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick="iWantThisInsurePrice()"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
						</div>-->
				</header>';

			//переменная, чтоб вкл/откл редактирование
			echo '
				<script>
					var iCanManage = false;
				</script>';

			echo '
                <div id="data">
                    <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
                    </ul>';
			/*echo '
								<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
									<a href="?who=stom" class="b">Стоматологи</a>
									<a href="?who=cosm" class="b">Косметологи</a>
								</li>';*/

//			if (($items['add_new'] == 1) || $god_mode){
//				echo '
//					<a href="add_pricelist_item.php" class="b">Добавить позицию</a>';
//				echo '
//					<a href="add_pricelist_group.php?'.$who.'" class="b">Добавить группу/подгруппу</a>';
//			}
//
//            if (($items['edit'] == 1) || $god_mode){
//                echo '
//								<div class="no_print">
//								<li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
//									<div style="cursor: pointer;" onclick="manageScheduler()">
//										<span style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">Управление</span> <i class="fa fa-cog" title="Настройки"></i>
//									</div>
//    						        <div id="DIVdelCheckedItems" style="display: none; width: 400px; margin-bottom: 10px; border: 1px dotted #BFBCB5; padding: 20px 10px 10px; background-color: #EEE;">
//    						            Переместить выбранные позиции в группу<br>
//    	    							<!--<input type="button" class="b" value="Удалить" onclick="if (iCanManage) Ajax_change_shed()">-->
//    	    							<input type="button" class="b" value="Переместить" onclick="showMoveCheckedItems();">
//    	    							<div id="errrror"></div>
//    							    </div>
//								</li>
//								</div>';
//                //managePriceList
//            }

			/*echo '
								<p style="margin: 5px 0; padding: 2px;">
									Быстрый поиск:
									<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
								</p>';*/
			/*echo '
								<li class="cellsBlock" style="font-weight:bold; width: auto;">
									<div class="cellPriority" style="text-align: center"></div>
									<div class="cellName" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;">Наименование</div>
									<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">Цена, руб.</div>
								</li>';*/

			//$services_j = SelDataFromDB('spr_pricelist_template', 'services', $type);
			//var_dump ($services_j);

//			$arr = array();
//			$rez = array();
//			$arr4 = array();
//			$rez4 = array();
//			$arr3 = array();
//			$rez3 = array();

            $msql_cnnct = ConnectToDB();

            //***

            echo '
                    <div style="white-space: nowrap;">
                        <div style="display: inline-block; border: 1px solid #c5c5c5; position: relative; vertical-align: top;">
                            
                            <div style="margin: 5px 0 5px; font-size: 11px; cursor: pointer;">
                            <!--<span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>-->
                            </div>
                            
                            <div id="sclad_cat_rezult" style="width: 350px; max-width: 350px; min-width: 350px; height: 600px; overflow: hidden;">
                            
                            </div>
                        </div>
                        
                        <div style="display: inline-block; border: 1px solid #c5c5c5; position: relative;">
                            
                            <div style="margin: 5px 0 5px; font-size: 11px; cursor: pointer;">
                                <span id="cat_name_show"></span>
                            </div>
                            
                            <div id="sclad_items_rezult" style="width: 700px; max-width: 700px; min-width: 700px; height: 600px;">
                            
                            </div>

                        </div>
                    </div>';

            echo '
                    <div style="white-space: nowrap;">
                        Набор
                    </div>';

			echo '

                </div>';


			//var_dump(checkExistTreeParents ('spr_sclad_category', 2, 3));

            CloseDB ($msql_cnnct);


            echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

            echo '
            <div id="doc_title">Склад - Асмедика</div>';

            echo '

            <script type="text/javascript">
				
				$(document).ready(function() {
				    //console.log(123);
				    //$("#sclad_items_rezult").html(123);
				    
				    getScladCategories ();
				    getScladItems (0, 0, 50, true);

				});				
                
            </script>
            
            <script src="js/DragManager.js"></script>
            
            <script>


                    //!!! Правильный пример контекстного меню (правильный? точно? ну пока работает)

                    var scladCategories = document.querySelector("#sclad_cat_rezult");

                    scladCategories.addEventListener("contextmenu", event => {
                        //console.log($(event.target).attr("id"));
                        
                        event.preventDefault();
                        
//                        //Сначала очищаем у всех окраску
//                        $(".droppable, .draggable").removeClass("context_pick");
//                        //Теперь покрасим
//                        if (event.target.classList.contains("droppable")){ 
//                            $(event.target).addClass("context_pick");
//                        }

                        contextMenuShow(0, 0, event, "sclad_cat");

                    }, false);
                    
                    var scladItems = document.querySelector("#sclad_items_rezult");

                    scladItems.addEventListener("contextmenu", event => {
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

                        contextMenuShow(0, 0, event, "sclad_item");

                    }, false);

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